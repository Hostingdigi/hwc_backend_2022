<?php

use App\Models\SMS;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Tests\Support\Fixtures;

// Phase 5 cutover tooling: retiring the original mobile API, preflight checks,
// scrubbing a production copy for staging. See docs/cutover.md.

describe('original mobile API', function () {
    it('serves the same body with deprecation headers and counts each call per endpoint', function () {
        config(['shop.legacy_mobile_api' => 'on']);
        $body = (string) app(\App\Http\Controllers\CommonMobileController::class)->allcountries()->getContent();

        $response = $this->get('/api/allcountries')->assertOk()
            ->assertHeader('Deprecation', 'true')
            ->assertHeaderMissing('Sunset');
        expect($response->getContent())->toBe($body)
            ->and($response->headers->get('Link'))->toContain('/api/v2>; rel="successor-version"');

        $this->get('/api/allcountries');
        $this->get('/allcountries')->assertHeader('Deprecation', 'true');

        expect(DB::table('legacy_api_hits')->where('path', 'api/allcountries')->value('hits'))->toBe(2)
            ->and(DB::table('legacy_api_hits')->where('path', 'allcountries')->value('hits'))->toBe(1);
    });

    it('records the route pattern rather than the raw URL', function () {
        $this->get('/api/brandbyfilter/a');
        $this->get('/api/brandbyfilter/b');

        expect(DB::table('legacy_api_hits')->where('path', 'api/brandbyfilter/{filter}')->value('hits'))->toBe(2);
    });

    it('announces the switch-off date as a Sunset header', function () {
        config(['shop.legacy_mobile_api_sunset' => '2027-03-31']);

        $this->get('/api/allcountries')->assertHeader('Sunset', 'Wed, 31 Mar 2027 00:00:00 GMT');
    });

    it('answers 410 on the api and root copies once switched off', function () {
        config(['shop.legacy_mobile_api' => 'off']);

        $this->getJson('/api/allcountries')->assertStatus(410)->assertJson(['response' => 'failed']);
        $this->postJson('/api/customerlogin', ['email' => 'a@b.test', 'password' => 'x'])->assertStatus(410);
        $this->postJson('/customerlogin', ['email' => 'a@b.test', 'password' => 'x'])->assertStatus(410);
        $this->postJson('/api/createorder', [])->assertStatus(410);

        // still counted, so usage after the switch-off is visible
        expect(DB::table('legacy_api_hits')->where('path', 'api/allcountries')->value('hits'))->toBe(1);
    });

    it('keeps mobileversions, the sync partner API, the PayPal button and v2 up when switched off', function () {
        config(['shop.legacy_mobile_api' => 'off']);

        $this->getJson('/api/mobileversions')->assertOk()->assertHeader('Deprecation', 'true');
        $this->getJson('/mobileversions')->assertOk();
        $this->postJson('/api/create_category', [])->assertStatus(401);
        expect($this->postJson('/api/paypal/orders/999999', [])->status())->not->toBe(410);
        $this->getJson('/api/v2/countries')->assertOk()->assertHeaderMissing('Deprecation');
    });

    it('reports usage per day and endpoint', function () {
        DB::table('legacy_api_hits')->insert([
            ['day' => now()->toDateString(), 'path' => 'api/createorder', 'hits' => 7, 'last_seen_at' => now()],
            ['day' => now()->subDay()->toDateString(), 'path' => 'api/createorder', 'hits' => 3, 'last_seen_at' => now()->subDay()],
            ['day' => now()->subDays(40)->toDateString(), 'path' => 'api/orderlist', 'hits' => 99, 'last_seen_at' => now()->subDays(40)],
        ]);

        $this->artisan('api:legacy-usage', ['--days' => 14])->assertSuccessful()
            ->expectsOutputToContain('api/createorder')
            ->doesntExpectOutputToContain('api/orderlist');
    });
});

describe('app:preflight', function () {
    beforeEach(function () {
        DB::table('paymentmethods')->delete();
    });

    it('fails when a gateway outside production is live, so staging cannot take real payments', function () {
        Fixtures::insert('paymentmethods', ['Id' => 3, 'payment_name' => 'Card', 'status' => 1, 'payment_mode' => 'live',
            'api_key' => 'pk', 'api_signature' => 'sk']);

        $this->artisan('app:preflight')->assertFailed()
            ->expectsOutputToContain('staging would take real payments');
    });

    it('passes on a test-mode staging setup', function () {
        Fixtures::insert('paymentmethods', ['Id' => 3, 'payment_name' => 'Card', 'status' => 1, 'payment_mode' => 'test',
            'test_api_key' => 'pk_test', 'test_api_signature' => 'sk_test']);

        $this->artisan('app:preflight')->assertSuccessful()->expectsOutputToContain('All checks passed');
    });

    it('in production, fails on debug mode, test gateways and a missing Stripe webhook secret', function () {
        $this->app['env'] = 'production';
        config(['app.debug' => true, 'services.stripe.webhook_secret' => '']);
        Fixtures::insert('paymentmethods', ['Id' => 3, 'payment_name' => 'Card', 'status' => 1, 'payment_mode' => 'test',
            'test_api_key' => 'pk_test', 'test_api_signature' => 'sk_test']);

        $this->artisan('app:preflight')->assertFailed()
            ->expectsOutputToContain('error pages show code')
            ->expectsOutputToContain('payments go to the test gateway')
            ->expectsOutputToContain('STRIPE_WEBHOOK_SECRET empty');
    });

    it('sees the cron through the scheduled heartbeat', function () {
        Cache::forget('scheduler:last_run');
        $this->artisan('app:preflight')->expectsOutputToContain('never ran');

        Artisan::call('app:heartbeat');
        $this->artisan('app:preflight')->expectsOutputToContain('last ran');
    });
});

describe('app:scrub-staging', function () {
    beforeEach(function () {
        $this->customer = Fixtures::insert('customer_master', ['cust_email' => 'real.person@gmail.com', 'cust_status' => 1]);
        $this->order = Fixtures::insert('order_master', ['ship_email' => 'real.person@gmail.com', 'bill_email' => 'boss@company.sg']);
        DB::table('paymentmethods')->delete();
        Fixtures::insert('paymentmethods', ['Id' => 3, 'payment_name' => 'Card', 'status' => 1, 'payment_mode' => 'live']);
        DB::table('password_resets')->insert(['email' => 'real.person@gmail.com', 'token' => 'x', 'created_at' => now()]);
    });

    it('switches gateways to test and makes customer addresses undeliverable', function () {
        $this->artisan('app:scrub-staging', ['--force' => true])->assertSuccessful();

        expect(DB::table('paymentmethods')->where('Id', 3)->value('payment_mode'))->toBe('test')
            ->and(DB::table('customer_master')->where('cust_id', $this->customer)->value('cust_email'))
            ->toBe("customer_master-{$this->customer}-cust_email@example.invalid")
            ->and(DB::table('order_master')->where('order_id', $this->order)->value('bill_email'))->toEndWith('@example.invalid')
            ->and(DB::table('order_master')->where('order_id', $this->order)->value('ship_email'))->toEndWith('@example.invalid')
            ->and(DB::table('password_resets')->count())->toBe(0);

        // running it twice changes nothing more
        $this->artisan('app:scrub-staging', ['--force' => true])->assertSuccessful();
        expect(DB::table('customer_master')->where('cust_id', $this->customer)->value('cust_email'))
            ->toBe("customer_master-{$this->customer}-cust_email@example.invalid");
    });

    it('refuses to run in production', function () {
        $this->app['env'] = 'production';

        $this->artisan('app:scrub-staging', ['--force' => true])->assertFailed();

        expect(DB::table('customer_master')->where('cust_id', $this->customer)->value('cust_email'))->toBe('real.person@gmail.com')
            ->and(DB::table('paymentmethods')->where('Id', 3)->value('payment_mode'))->toBe('live');
    });

    it('does nothing unless confirmed', function () {
        $this->artisan('app:scrub-staging')->expectsConfirmation(
            'Rewrite customer emails and switch gateways to test mode in database "'.DB::connection()->getDatabaseName().'"?', 'no'
        )->assertFailed();

        expect(DB::table('paymentmethods')->where('Id', 3)->value('payment_mode'))->toBe('live');
    });
});

it('keeps the Twilio credentials out of the code and skips SMS when they are not configured', function () {
    expect(file_get_contents(app_path('Models/SMS.php')))->not->toContain('AC91d9285115')->not->toContain('8b016a2dac');

    config(['services.twilio.sid' => null, 'services.twilio.token' => null]);
    expect((new SMS)->sendSMS('81234567', 'SG', 1, 'Shipped'))->toBeNull();
});
