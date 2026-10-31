<?php

use Illuminate\Support\Facades\DB;

// Phase 0: catalogue/order sync endpoints and send-test-email need INTEGRATION_API_KEY
// (test value: "test-integration-key", set in phpunit.xml / .env.testing).

it('rejects calls without a key', function ($method, $uri) {
    $this->json($method, $uri)->assertStatus(401)->assertJson(['status' => 'Failure', 'message' => 'Unauthorized']);
})->with([
    ['POST', '/api/create_category'],
    ['POST', '/api/create_sub_category'],
    ['POST', '/api/create_product'],
    ['POST', '/api/productStatusUpdate'],
    ['POST', '/api/stock_update'],
    ['POST', '/api/product_all_update'],
    ['GET', '/api/dailyorders'],
    ['POST', '/api/send-test-email'],
]);

it('rejects a wrong key', function () {
    $this->postJson('/api/create_category', [], ['X-Api-Key' => 'wrong'])->assertStatus(401);
});

it('accepts the key as X-Api-Key or as a Bearer token', function () {
    $payload = ['category_name' => 'Abrasives', 'status' => 1];

    $this->postJson('/api/create_category', $payload, ['X-Api-Key' => 'test-integration-key'])
        ->assertOk()->assertJson(['status' => 'Success']);

    $this->postJson('/api/create_category', ['category_name' => 'Fasteners', 'status' => 1], ['Authorization' => 'Bearer test-integration-key'])
        ->assertOk()->assertJson(['status' => 'Success']);

    expect(DB::table('types')->whereIn('EnName', ['Abrasives', 'Fasteners'])->count())->toBe(2);
});

it('refuses every call when no key is configured', function () {
    config(['services.integration.key' => null]);

    $this->postJson('/api/create_category', [], ['X-Api-Key' => ''])->assertStatus(503);
});

it('leaves the public mobile catalogue endpoints open', function () {
    $this->getJson('/api/allcountries')->assertOk();
});
