<?php

use Illuminate\Support\Facades\DB;
use Tests\Feature\ApiV2\ApiV2Fixtures;
use Tests\Support\Fixtures;

uses(ApiV2Fixtures::class);

beforeEach(fn () => $this->seedStore());

it('adds, updates and removes cart items with server prices', function () {
    $this->signIn();

    $line = $this->postJson('/api/v2/cart/items', ['product_id' => $this->drill, 'quantity' => 2])
        ->assertCreated()->assertJsonPath('data.subtotal', 109)->json('data.items.0.id');

    $this->postJson('/api/v2/cart/items', ['product_id' => $this->drill, 'quantity' => 1])->assertJsonPath('data.items.0.quantity', 3);
    $this->patchJson("/api/v2/cart/items/{$line}", ['quantity' => 1])->assertOk()->assertJsonPath('data.subtotal', 54.5);
    $this->deleteJson("/api/v2/cart/items/{$line}")->assertOk()->assertJsonPath('data.item_count', 0);
});

it('cannot touch another customer\'s cart line', function () {
    $other = $this->makeCustomer();
    $this->putInCart($other, $this->drill);
    $line = DB::table('cart_details')->where('user_id', $other->cust_id)->value('detail_id');

    $this->signIn();
    $this->patchJson("/api/v2/cart/items/{$line}", ['quantity' => 50])->assertNotFound();
    $this->deleteJson("/api/v2/cart/items/{$line}")->assertNotFound();

    expect((int) DB::table('cart_details')->where('detail_id', $line)->value('prod_quantity'))->toBe(1);
});

it('quotes the cart total (same numbers as the v1 app quote)', function () {
    $customer = $this->signIn();
    $this->putInCart($customer, $this->drill);

    $this->postJson('/api/v2/checkout/quote', ['country' => 'SG', 'shipping_method_id' => 2])->assertOk()
        ->assertJsonPath('data.subtotal_excl_tax', 50)
        ->assertJsonPath('data.tax', 4.5)
        ->assertJsonPath('data.shipping', 8)
        ->assertJsonPath('data.total', 62.5);
});

it('only applies a coupon in the quote if the order would accept it', function () {
    $customer = $this->signIn();
    $this->putInCart($customer, $this->drill);
    Fixtures::insert('coupon_code', ['coupon_code' => 'BRAND8', 'discount_type' => 1, 'discount' => 10, 'brand_id' => 8,
        'validity' => now()->addDay()->toDateString(), 'nooftimes' => 5, 'status' => 1]);

    $this->postJson('/api/v2/checkout/quote', ['country' => 'SG', 'shipping_method_id' => 2, 'coupon_code' => 'BRAND8'])
        ->assertJsonPath('data.discount', 0)->assertJsonPath('data.total', 62.5)
        ->assertJsonPath('data.coupon_error', 'This coupon does not apply to every item in your cart.');

    $this->postJson('/api/v2/coupons/check', ['coupon_code' => 'BRAND8'])->assertStatus(422);
});

it('places an unpaid order from the cart, priced by the server', function () {
    $customer = $this->signIn();
    $this->putInCart($customer, $this->drill, 2);

    // 2 x 54.50 = 109 incl. GST: above the S$100 free-delivery threshold, so no shipping
    $response = $this->postJson('/api/v2/orders', $this->orderPayload(['expected_total' => 109.0]))->assertCreated()
        ->assertJsonPath('data.is_paid', false)
        ->assertJsonPath('data.totals.total', 109)
        ->assertJsonPath('data.totals.shipping', 0)
        ->assertJsonPath('data.lines.0.unit_price', 54.5)
        ->assertJsonPath('data.payment_method', 'Credit Card');

    $order = DB::table('order_master')->where('order_id', $response->json('data.id'))->first();
    expect((int) $order->user_id)->toBe($customer->cust_id)
        ->and((int) DB::table('products')->where('Id', $this->drill)->value('Quantity'))->toBe(8)
        // the cart is kept until the payment is confirmed
        ->and(DB::table('cart_details')->where('user_id', $customer->cust_id)->count())->toBe(1);
});

it('refuses to place an order when the displayed total is out of date', function () {
    $customer = $this->signIn();
    $this->putInCart($customer, $this->drill);

    $this->postJson('/api/v2/orders', $this->orderPayload(['expected_total' => 1.00]))
        ->assertStatus(409)->assertJsonPath('reason', 'price_changed')->assertJsonPath('quote.total', 62.5);

    expect(DB::table('order_master')->count())->toBe(0);
});

it('explains why an order cannot be placed', function () {
    $customer = $this->signIn();
    $this->postJson('/api/v2/orders', $this->orderPayload())->assertStatus(422)->assertJsonPath('reason', 'cart_empty');

    $this->putInCart($customer, $this->drill);
    Fixtures::insert('paymentmethods', ['Id' => 4, 'payment_name' => 'Hoolah', 'type' => 1, 'status' => 1]);
    $this->postJson('/api/v2/orders', $this->orderPayload(['payment_method_id' => 4]))->assertStatus(422)->assertJsonPath('reason', 'payment_method_unavailable');

    $this->postJson('/api/v2/orders', ['shipping_method_id' => 2])->assertStatus(422)->assertJsonValidationErrors(['shipping.address1', 'payment_method_id']);
});

it('only shows customers their own orders', function () {
    $customer = $this->signIn();
    $this->putInCart($customer, $this->drill);
    $mine = $this->postJson('/api/v2/orders', $this->orderPayload())->json('data.id');
    $theirs = Fixtures::insert('order_master', ['user_id' => $customer->cust_id + 999, 'order_status' => 1, 'payable_amount' => 5, 'date_entered' => now()]);

    $this->getJson('/api/v2/orders')->assertOk()->assertJsonCount(1, 'data')->assertJsonPath('data.0.id', $mine);
    $this->getJson("/api/v2/orders/{$mine}")->assertOk()->assertJsonCount(1, 'data.lines');
    $this->getJson("/api/v2/orders/{$theirs}")->assertNotFound();
    $this->postJson("/api/v2/orders/{$theirs}/payments/stripe")->assertNotFound();
});

it('keeps a wishlist and takes reviews for moderation', function () {
    $this->signIn();

    $this->postJson('/api/v2/wishlist', ['product_id' => $this->drill])->assertCreated();
    $this->postJson('/api/v2/wishlist', ['product_id' => $this->drill])->assertCreated();
    $this->getJson('/api/v2/wishlist')->assertOk()->assertJsonCount(1, 'data');
    $this->deleteJson("/api/v2/wishlist/{$this->drill}")->assertOk();
    $this->getJson('/api/v2/wishlist')->assertJsonCount(0, 'data');

    $this->postJson("/api/v2/products/{$this->drill}/reviews", ['rating' => 5, 'comment' => 'Great'])->assertCreated();
    expect((int) DB::table('product_reviews')->where('ProdId', $this->drill)->value('status'))->toBe(0);
});
