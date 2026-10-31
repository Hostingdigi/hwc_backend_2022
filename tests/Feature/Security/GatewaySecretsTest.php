<?php

use Tests\Support\Fixtures;

// The public payment-method list used to include the gateways' secret keys.

it('never sends gateway secrets in the public payment method list', function () {
    Fixtures::insert('paymentmethods', ['Id' => 3, 'payment_name' => 'Credit Card', 'type' => 1, 'status' => 1, 'payment_mode' => 'live',
        'api_key' => 'pk_live_public', 'api_signature' => 'sk_live_SECRET', 'test_api_key' => 'pk_test_public', 'test_api_signature' => 'sk_test_SECRET']);
    Fixtures::insert('paymentmethods', ['Id' => 6, 'payment_name' => 'Paypal', 'type' => 1, 'status' => 1, 'payment_mode' => 'live',
        'api_key' => 'paypal-client-id', 'api_signature' => 'paypal-SECRET']);

    $response = $this->getJson('/api/paymethods?type=2')->assertOk();

    expect($response->getContent())->not->toContain('SECRET')
        ->and($response->json('paymentmenthods.0'))->toHaveKeys(['api_key', 'api_signature', 'test_api_key', 'test_api_signature'])
        ->and($response->json('paymentmenthods.0.api_key'))->toBe('pk_live_public')
        ->and($response->json('paymentmenthods.0.api_signature'))->toBe('');
});
