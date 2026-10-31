<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Tests\Support\Fixtures;

// Web checkout + v1 app PayPal button on PayPal Orders v2 (PayPalButtonController).

beforeEach(function () {
    Http::preventStrayRequests();
    Fixtures::insert('paymentmethods', ['Id' => 6, 'payment_name' => 'Paypal', 'status' => 1, 'payment_mode' => 'test', 'test_api_key' => 'pp-client', 'test_api_signature' => 'pp-secret']);
    $this->order = Fixtures::insert('order_master', ['user_id' => 1, 'order_status' => 0, 'payable_amount' => 84.35, 'pay_method' => 'Paypal', 'date_entered' => now()]);
});

function fakePayPalApi($orderId, $captureValue = '84.35')
{
    Http::fake([
        'api-m.sandbox.paypal.com/v1/oauth2/token' => Http::response(['access_token' => 'tok']),
        'api-m.sandbox.paypal.com/v2/checkout/orders/PPX/capture' => Http::response(['status' => 'COMPLETED', 'purchase_units' => [[
            'custom_id' => (string) $orderId,
            'payments' => ['captures' => [['id' => 'CAPX', 'status' => 'COMPLETED', 'amount' => ['value' => $captureValue]]]],
        ]]]),
        'api-m.sandbox.paypal.com/v2/checkout/orders' => Http::response(['id' => 'PPX', 'links' => []]),
    ]);
}

it('creates the PayPal order for the stored total, not a browser-chosen amount', function () {
    fakePayPalApi($this->order);

    $this->postJson("/api/paypal/orders/{$this->order}", ['amount' => '0.01'])->assertOk()->assertJson(['id' => 'PPX']);

    Http::assertSent(fn ($r) => str_ends_with($r->url(), '/v2/checkout/orders') && $r['purchase_units'][0]['amount']['value'] === '84.35');
});

it('returns the success URL with the capture id for the web and app flows', function ($return, $path) {
    fakePayPalApi($this->order);

    $this->postJson("/api/paypal/orders/{$this->order}/capture/{$return}", ['paypal_order_id' => 'PPX'])->assertOk()
        ->assertJson(['redirect' => url($path) . '?orderid=' . $this->order . '&paymentId=CAPX']);
})->with([['web', '/success'], ['app', '/successorder']]);

it('refuses an underpaid capture', function () {
    fakePayPalApi($this->order, '1.00');

    $this->postJson("/api/paypal/orders/{$this->order}/capture/web", ['paypal_order_id' => 'PPX'])->assertStatus(402);
});

it('will not create a PayPal order for a paid order', function () {
    DB::table('order_master')->where('order_id', $this->order)->update(['order_status' => 1]);

    $this->postJson("/api/paypal/orders/{$this->order}")->assertStatus(409);
});

it('marks the web order paid when /success receives the v2 capture id', function () {
    Fixtures::insert('settings', ['company_phone' => '1', 'admin_email' => 'a@example.test']);
    Http::fake([
        'api-m.sandbox.paypal.com/v1/oauth2/token' => Http::response(['access_token' => 'tok']),
        'api-m.sandbox.paypal.com/v2/payments/captures/CAPX' => Http::response(['status' => 'COMPLETED', 'amount' => ['value' => '84.35']]),
    ]);

    $this->get('/success?orderid=' . $this->order . '&paymentId=CAPX');

    expect((int) DB::table('order_master')->where('order_id', $this->order)->value('order_status'))->toBe(1);
});

it('no longer loads the deprecated checkout.js anywhere', function () {
    $hits = [];
    foreach (glob(resource_path('views/public/Payment/*.blade.php')) as $view) {
        if (str_contains(file_get_contents($view), 'paypalobjects.com/api/checkout.js')) {
            $hits[] = basename($view);
        }
    }

    expect($hits)->toBe([]);
});
