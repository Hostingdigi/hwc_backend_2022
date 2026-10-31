<?php

use Illuminate\Support\Facades\DB;
use Tests\Support\Fixtures;

// Characterization tests: POST /api/discountcoupon (CartMobileController@discountcoupon).
// NOTE: coupon_code / coupon_code_usage are reconstructed tables (see the
// 2026_10_31 migration), so only behaviour, not column formats, is asserted.

beforeEach(function () {
    $this->customer = Fixtures::insert('customer_master', ['cust_email' => 'buyer@example.test', 'cust_status' => 1, 'group_admin' => 0]);
    $this->product = Fixtures::insert('products', ['EnName' => 'Hammer', 'Types' => 50, 'Brand' => 7, 'ProdStatus' => 1]);
});

function coupon(array $overrides = [])
{
    return Fixtures::insert('coupon_code', $overrides + [
        'coupon_code' => 'SAVE10', 'discount_type' => 1, 'discount' => 10, 'validity' => now()->addMonth()->toDateString(),
        'nooftimes' => 1, 'customer_type' => 1, 'customer_id' => 0, 'category_id' => 0, 'brand_id' => 0, 'status' => 1,
    ]);
}

function applyCoupon($test, $code)
{
    return $test->postJson('/api/discountcoupon', ['orderinfo' => [[
        'customer_id' => $test->customer,
        'couponcode' => $code,
        'products' => [['id' => $test->product, 'qty' => 1]],
    ]]]);
}

it('accepts a valid percentage coupon', function () {
    $id = coupon();

    $response = applyCoupon($this, 'SAVE10')->assertOk();

    expect($response->json('message'))->toBe('Valid Coupon Code')
        ->and((float) $response->json('discount'))->toEqual(10.0)
        ->and($response->json('discounttext'))->toEndWith('%')
        ->and($response->json('couponid'))->toBe($id);
});

it('labels a fixed-amount coupon with a dollar sign', function () {
    coupon(['discount_type' => 2, 'discount' => 5]);

    expect(applyCoupon($this, 'SAVE10')->json('discounttext'))->toStartWith('$');
});

it('rejects an unknown or inactive code', function () {
    coupon(['status' => 0]);

    expect(applyCoupon($this, 'SAVE10')->json('message'))->toBe('Invalid Coupon Code!')
        ->and(applyCoupon($this, 'NOPE')->json('message'))->toBe('Invalid Coupon Code!');
});

it('rejects an expired coupon', function () {
    coupon(['validity' => now()->subDay()->toDateString()]);

    expect(applyCoupon($this, 'SAVE10')->json('message'))->toBe('Coupon Code Expired!');
});

it('accepts a coupon on its last valid day', function () {
    coupon(['validity' => now()->toDateString()]);

    expect(applyCoupon($this, 'SAVE10')->json('message'))->toBe('Valid Coupon Code');
});

it('enforces the per-customer usage limit (customer_type 1)', function () {
    $id = coupon(['nooftimes' => 1, 'customer_type' => 1]);
    DB::table('coupon_code_usage')->insert(['coupon_id' => $id, 'customer_id' => $this->customer, 'order_id' => 1]);

    expect(applyCoupon($this, 'SAVE10')->json('message'))->toBe('Coupon Code Usage Limit Exceeded!');
});

it('counts other customers\' uses toward a global limit (customer_type 2)', function () {
    $id = coupon(['nooftimes' => 1, 'customer_type' => 2]);
    DB::table('coupon_code_usage')->insert(['coupon_id' => $id, 'customer_id' => 999, 'order_id' => 1]);

    expect(applyCoupon($this, 'SAVE10')->json('message'))->toBe('Coupon Code Usage Limit Exceeded!');
});

it('only lets group admins use customer_type 3 coupons', function () {
    coupon(['customer_type' => 3]);

    // Not a group admin: rejected with an EMPTY message
    expect(applyCoupon($this, 'SAVE10')->json())->toMatchArray(['message' => '', 'couponid' => '']);

    DB::table('customer_master')->where('cust_id', $this->customer)->update(['group_admin' => 1]);
    expect(applyCoupon($this, 'SAVE10')->json('message'))->toBe('Valid Coupon Code');
});

it('rejects a category coupon when any cart item is outside the category, with an empty message', function () {
    coupon(['category_id' => 99]);

    expect(applyCoupon($this, 'SAVE10')->json())->toMatchArray(['message' => '', 'discount' => '', 'couponid' => '']);
});

it('accepts a category coupon when every item is in the category', function () {
    coupon(['category_id' => 50]);

    expect(applyCoupon($this, 'SAVE10')->json('message'))->toBe('Valid Coupon Code');
});

it('KNOWN BUG: brand coupons crash ($cartitem[id] is an undefined constant in PHP 8)', function () {
    coupon(['brand_id' => 7]);
    $this->withoutExceptionHandling();

    expect(fn () => applyCoupon($this, 'SAVE10'))->toThrow(Error::class, 'Undefined constant "App\Http\Controllers\id"');
});

it('KNOWN BUG: customer-specific coupons crash (undefined $customer)', function () {
    coupon(['customer_id' => $this->customer]);
    $this->withoutExceptionHandling();

    expect(fn () => applyCoupon($this, 'SAVE10'))->toThrow(ErrorException::class, 'Undefined variable $customer');
});
