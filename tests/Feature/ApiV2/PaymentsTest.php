<?php

use App\Mail\TemplateMail;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Mail;
use Tests\Feature\ApiV2\ApiV2Fixtures;
use Tests\Support\Fixtures;

uses(ApiV2Fixtures::class);

// Paying API v2 orders. Gateways are faked; nothing leaves the machine.

beforeEach(function () {
    Http::preventStrayRequests();
    Mail::fake();
    $this->seedStore();
    $this->customer = $this->signIn();
    $this->putInCart($this->customer, $this->drill);
    $this->order = $this->postJson('/api/v2/orders', $this->orderPayload())->assertCreated()->json('data.id'); // S$62.50
});

afterEach(fn () => \Stripe\ApiRequestor::setHttpClient(null));

/** Fakes Stripe's HTTP API: returns $responses[path-fragment] and records requests. */
function fakeStripe(array $responses): object
{
    $client = new class($responses) implements \Stripe\HttpClient\ClientInterface {
        public array $requests = [];
        public function __construct(private array $responses) {}
        public function request($method, $absUrl, $headers, $params, $hasFile)
        {
            $this->requests[] = ['url' => $absUrl, 'params' => $params];
            foreach ($this->responses as $fragment => $body) {
                if (str_contains($absUrl, $fragment)) {
                    return [json_encode($body), 200, []];
                }
            }
            return ['{"error":{"message":"not faked"}}', 404, []];
        }
    };
    \Stripe\ApiRequestor::setHttpClient($client);

    return $client;
}

function orderRow($id)
{
    return DB::table('order_master')->where('order_id', $id)->first();
}

it('creates a Stripe PaymentIntent for the stored order total', function () {
    $stripe = fakeStripe(['/v1/payment_intents' => ['id' => 'pi_123', 'object' => 'payment_intent', 'client_secret' => 'pi_123_secret']]);

    $this->postJson("/api/v2/orders/{$this->order}/payments/stripe")->assertOk()
        ->assertJsonPath('data.client_secret', 'pi_123_secret');

    expect($stripe->requests[0]['params']['amount'])->toBe(6250)
        ->and($stripe->requests[0]['params']['metadata']['order_id'])->toBe((string) $this->order);
});

it('marks the order paid on Stripe confirmation, clears the cart and emails the customer', function () {
    fakeStripe(['/v1/payment_intents/pi_123' => ['id' => 'pi_123', 'object' => 'payment_intent', 'status' => 'succeeded', 'amount_received' => 6250]]);

    $this->postJson("/api/v2/orders/{$this->order}/payments/stripe/confirm", ['payment_intent_id' => 'pi_123'])
        ->assertOk()->assertJsonPath('data.is_paid', true);

    expect((int) orderRow($this->order)->order_status)->toBe(1)
        ->and(orderRow($this->order)->trans_id)->toBe('pi_123')
        ->and(DB::table('cart_details')->where('user_id', $this->customer->cust_id)->count())->toBe(0);
    Mail::assertQueued(TemplateMail::class, fn ($m) => str_starts_with($m->subjectLine, 'Order ') && $m->hasTo($this->customer->cust_email));
});

it('refuses a Stripe payment for the wrong amount', function () {
    fakeStripe(['/v1/payment_intents/pi_123' => ['id' => 'pi_123', 'object' => 'payment_intent', 'status' => 'succeeded', 'amount_received' => 100]]);

    $this->postJson("/api/v2/orders/{$this->order}/payments/stripe/confirm", ['payment_intent_id' => 'pi_123'])
        ->assertStatus(402)->assertJsonPath('reason', 'payment_not_confirmed');

    expect((int) orderRow($this->order)->order_status)->toBe(0);
});

it('will not start a second payment for a paid order', function () {
    DB::table('order_master')->where('order_id', $this->order)->update(['order_status' => 1]);

    $this->postJson("/api/v2/orders/{$this->order}/payments/stripe")->assertStatus(409);
});

it('confirms orders from a signed Stripe webhook', function () {
    config(['services.stripe.webhook_secret' => 'whsec_test']);
    $payload = json_encode(['id' => 'evt_1', 'object' => 'event', 'type' => 'payment_intent.succeeded', 'data' => ['object' => [
        'id' => 'pi_hook', 'object' => 'payment_intent', 'amount_received' => 6250, 'metadata' => ['order_id' => (string) $this->order],
    ]]]);
    $timestamp = time();
    $signature = 't=' . $timestamp . ',v1=' . hash_hmac('sha256', $timestamp . '.' . $payload, 'whsec_test');

    $this->call('POST', '/api/webhooks/stripe', [], [], [], ['HTTP_STRIPE_SIGNATURE' => 'bad', 'CONTENT_TYPE' => 'application/json'], $payload)->assertStatus(400);
    expect((int) orderRow($this->order)->order_status)->toBe(0);

    $this->call('POST', '/api/webhooks/stripe', [], [], [], ['HTTP_STRIPE_SIGNATURE' => $signature, 'CONTENT_TYPE' => 'application/json'], $payload)->assertOk();
    expect((int) orderRow($this->order)->order_status)->toBe(1)
        ->and(orderRow($this->order)->trans_id)->toBe('pi_hook');

    // A repeat delivery changes nothing and sends no second email
    $this->call('POST', '/api/webhooks/stripe', [], [], [], ['HTTP_STRIPE_SIGNATURE' => $signature, 'CONTENT_TYPE' => 'application/json'], $payload)->assertOk();
    Mail::assertQueuedCount(1);
});

it('pays with PayPal Orders v2: create for the stored total, then capture', function () {
    Http::fake([
        'api-m.sandbox.paypal.com/v1/oauth2/token' => Http::response(['access_token' => 'tok']),
        'api-m.sandbox.paypal.com/v2/checkout/orders/PP123/capture' => Http::response(['id' => 'PP123', 'status' => 'COMPLETED', 'purchase_units' => [[
            'custom_id' => (string) $this->order,
            'payments' => ['captures' => [['id' => 'CAP1', 'status' => 'COMPLETED', 'amount' => ['value' => '62.50', 'currency_code' => 'SGD']]]],
        ]]]),
        'api-m.sandbox.paypal.com/v2/checkout/orders' => Http::response(['id' => 'PP123', 'links' => [['rel' => 'approve', 'href' => 'https://paypal.test/approve']]]),
    ]);

    $this->postJson("/api/v2/orders/{$this->order}/payments/paypal")->assertOk()
        ->assertJsonPath('data.paypal_order_id', 'PP123')->assertJsonPath('data.approve_url', 'https://paypal.test/approve');
    Http::assertSent(fn ($r) => str_ends_with($r->url(), '/v2/checkout/orders') && $r['purchase_units'][0]['amount']['value'] === '62.50');

    $this->postJson("/api/v2/orders/{$this->order}/payments/paypal/capture", ['paypal_order_id' => 'PP123'])
        ->assertOk()->assertJsonPath('data.is_paid', true);
    expect(orderRow($this->order)->trans_id)->toBe('CAP1');
});

it('refuses a PayPal capture that belongs to another order', function () {
    Http::fake([
        'api-m.sandbox.paypal.com/v1/oauth2/token' => Http::response(['access_token' => 'tok']),
        'api-m.sandbox.paypal.com/v2/checkout/orders/PP999/capture' => Http::response(['status' => 'COMPLETED', 'purchase_units' => [[
            'custom_id' => '999999',
            'payments' => ['captures' => [['id' => 'CAP9', 'status' => 'COMPLETED', 'amount' => ['value' => '62.50']]]],
        ]]]),
    ]);

    $this->postJson("/api/v2/orders/{$this->order}/payments/paypal/capture", ['paypal_order_id' => 'PP999'])->assertStatus(402);
    expect((int) orderRow($this->order)->order_status)->toBe(0);
});

it('pays with Atome: create returns the payment URL, confirm checks Atome', function () {
    DB::table('order_master')->where('order_id', $this->order)->update(['pay_method' => 'Atome']);
    Http::fake([
        'atome.test/v2/payments/' . $this->order => Http::response(['status' => 'PAID', 'amount' => 6250]),
        'atome.test/v2/payments' => Http::response(['appPaymentUrl' => 'https://atome.test/pay/abc']),
    ]);

    $this->postJson("/api/v2/orders/{$this->order}/payments/atome")->assertOk()->assertJsonPath('data.payment_url', 'https://atome.test/pay/abc');
    $this->postJson("/api/v2/orders/{$this->order}/payments/atome/confirm")->assertOk()->assertJsonPath('data.is_paid', true);
});

it('reports a gateway outage as 502 without exposing details', function () {
    Http::fake(['api-m.sandbox.paypal.com/*' => Http::response([], 500)]);

    $this->postJson("/api/v2/orders/{$this->order}/payments/paypal")->assertStatus(502)
        ->assertJsonPath('message', 'PayPal is not available right now. Please try another payment method.');
});

it('records coupon usage once the order is paid', function () {
    $coupon = Fixtures::insert('coupon_code', ['coupon_code' => 'SAVE10', 'discount_type' => 1, 'discount' => 10,
        'validity' => now()->addDay()->toDateString(), 'nooftimes' => 5, 'status' => 1]);
    $this->putInCart($this->customer, $this->drill);
    $order = $this->postJson('/api/v2/orders', $this->orderPayload(['coupon_code' => 'SAVE10']))->assertCreated()->json('data.id');
    $total = (int) round((float) orderRow($order)->payable_amount * 100);
    fakeStripe(['/v1/payment_intents/pi_c' => ['id' => 'pi_c', 'object' => 'payment_intent', 'status' => 'succeeded', 'amount_received' => $total]]);

    expect(DB::table('coupon_code_usage')->where('coupon_id', $coupon)->count())->toBe(0);
    $this->postJson("/api/v2/orders/{$order}/payments/stripe/confirm", ['payment_intent_id' => 'pi_c'])->assertOk();
    expect(DB::table('coupon_code_usage')->where('coupon_id', $coupon)->count())->toBe(1);
});
