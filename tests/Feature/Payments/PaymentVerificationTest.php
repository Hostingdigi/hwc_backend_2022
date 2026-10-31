<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Tests\Support\Fixtures;

// Phase 0: "success" callbacks only mark an order paid after the gateway confirms it
// (App\Services\PaymentVerifier). Gateways are faked; nothing leaves the machine.

beforeEach(function () {
    Fixtures::insert('paymentmethods', ['Id' => 3, 'payment_name' => 'Credit Card', 'payment_mode' => 'test', 'test_api_signature' => 'sk_test_fake', 'status' => 1]);
    Fixtures::insert('paymentmethods', ['Id' => 6, 'payment_name' => 'Paypal', 'payment_mode' => 'test', 'test_api_key' => 'pp-client', 'test_api_signature' => 'pp-secret', 'status' => 1]);
    Fixtures::insert('paymentmethods', ['Id' => 7, 'payment_name' => 'Atome', 'payment_mode' => 'test', 'test_api_key' => 'at-key', 'test_api_signature' => 'at-secret', 'testing_url' => 'https://atome.test/v2', 'status' => 1]);

    // The shared site header reads the (single) settings row
    Fixtures::insert('settings', ['company_phone' => '+65 6000 0000', 'admin_email' => 'admin@example.test']);
});

function pendingOrder(array $overrides = [])
{
    return Fixtures::insert('order_master', $overrides + [
        'user_id' => 1, 'order_status' => 0, 'is_fulfilled' => 0, 'payable_amount' => 84.35, 'pay_method' => 'Paypal',
        'date_entered' => now(),
    ]);
}

function orderStatus($orderId)
{
    return (int) DB::table('order_master')->where('order_id', $orderId)->value('order_status');
}

function fakePaypal(array $payment)
{
    Http::fake([
        'api-m.sandbox.paypal.com/v1/oauth2/token' => Http::response(['access_token' => 'tok']),
        'api-m.sandbox.paypal.com/v1/payments/payment/*' => Http::response($payment),
    ]);
}

it('no longer marks an order paid just because the success URL was called', function ($uri) {
    $order = pendingOrder(['pay_method' => 'Hoolah']);

    $this->get($uri . '?orderid=' . $order . '&transactionid=made-up')->assertOk();

    expect(orderStatus($order))->toBe(0);
})->with(['/api/paypalsuccess', '/api/successorder', '/api/hoolahsuccess']);

it('tells the mobile app the payment is pending when it cannot be verified', function () {
    $order = pendingOrder(['pay_method' => 'Hoolah']);

    $this->getJson('/api/paypalsuccess?orderid=' . $order)
        ->assertOk()
        ->assertJson(['response' => 'failed', 'orderid' => $order]);
});

it('marks a PayPal order paid when PayPal reports it approved for the right amount', function () {
    fakePaypal(['state' => 'approved', 'transactions' => [['amount' => ['total' => '84.35']]]]);
    $order = pendingOrder();

    $this->getJson('/api/paypalsuccess?orderid=' . $order . '&transactionid=PAYID-ABC123')
        ->assertJson(['response' => 'success']);

    expect(orderStatus($order))->toBe(1)
        ->and(DB::table('order_master')->where('order_id', $order)->value('trans_id'))->toBe('PAYID-ABC123');
});

it('keeps a PayPal order pending when the paid amount does not match', function () {
    fakePaypal(['state' => 'approved', 'transactions' => [['amount' => ['total' => '1.00']]]]);
    $order = pendingOrder();

    $this->getJson('/api/paypalsuccess?orderid=' . $order . '&transactionid=PAYID-ABC123')->assertOk();

    expect(orderStatus($order))->toBe(0);
});

it('keeps a PayPal order pending when the payment was not approved', function () {
    fakePaypal(['state' => 'created', 'transactions' => [['amount' => ['total' => '84.35']]]]);
    $order = pendingOrder();

    $this->getJson('/api/paypalsuccess?orderid=' . $order . '&transactionid=PAYID-ABC123')->assertOk();

    expect(orderStatus($order))->toBe(0);
});

it('refuses to reuse one PayPal payment for a second order', function () {
    fakePaypal(['state' => 'approved', 'transactions' => [['amount' => ['total' => '84.35']]]]);
    pendingOrder(['order_status' => 1, 'trans_id' => 'PAYID-ABC123']);
    $second = pendingOrder();

    $this->getJson('/api/paypalsuccess?orderid=' . $second . '&transactionid=PAYID-ABC123');

    expect(orderStatus($second))->toBe(0);
});

it('marks an Atome order paid when Atome reports PAID', function () {
    Http::fake(['atome.test/v2/payments/*' => Http::response(['status' => 'PAID', 'amount' => 8435])]);
    $order = pendingOrder(['pay_method' => 'Atome']);

    $this->getJson('/api/successorder?orderid=' . $order)->assertOk();

    expect(orderStatus($order))->toBe(1);
    Http::assertSent(fn ($request) => $request->url() === 'https://atome.test/v2/payments/' . $order);
});

it('keeps an Atome order pending while Atome reports it unpaid', function () {
    Http::fake(['atome.test/v2/payments/*' => Http::response(['status' => 'PROCESSING', 'amount' => 8435])]);
    $order = pendingOrder(['pay_method' => 'Atome']);

    $this->getJson('/api/successorder?orderid=' . $order)->assertOk();

    expect(orderStatus($order))->toBe(0);
});

it('accepts an order that was already confirmed server-side without calling a gateway', function () {
    Http::fake();
    $order = pendingOrder(['order_status' => 1, 'trans_id' => 'ch_already_paid', 'pay_method' => 'Credit Card']);

    $this->getJson('/api/paypalsuccess?orderid=' . $order)->assertJson(['response' => 'success']);

    Http::assertNothingSent();
    expect(orderStatus($order))->toBe(1);
});

it('keeps a card order pending when there is no Stripe charge or payment intent id', function () {
    $order = pendingOrder(['pay_method' => 'Credit Card']);

    $this->getJson('/api/paypalsuccess?orderid=' . $order . '&transactionid=not-a-stripe-id');

    expect(orderStatus($order))->toBe(0);
});

it('falls back to the old behaviour in log mode, but logs the failure', function () {
    config(['services.payment_verification.mode' => 'log']);
    \Illuminate\Support\Facades\Log::shouldReceive('warning')->once()->withArgs(fn ($message) => $message === 'PAYMENT VERIFICATION FAILED');
    $order = pendingOrder(['pay_method' => 'Hoolah']);

    $this->getJson('/api/paypalsuccess?orderid=' . $order . '&transactionid=made-up');

    expect(orderStatus($order))->toBe(1);
});

it('keeps a web order pending when /success cannot verify the payment', function () {
    $order = pendingOrder(['pay_method' => 'Hoolah']);

    $this->get('/success?orderid=' . $order)
        ->assertOk()
        ->assertSee('We are confirming your payment')
        // no Google Ads purchase conversion for an unverified payment
        ->assertDontSee('Purchase conversion', false);

    expect(orderStatus($order))->toBe(0);
});
