<?php

use App\Services\CartServices;
use Tests\Support\Fixtures;

// Characterization tests: current behaviour of the CartServices pricing helpers.

beforeEach(function () {
    Fixtures::insert('country', ['countryid' => 189, 'countryname' => 'Singapore', 'countrycode' => 'SG', 'taxtitle' => 'GST', 'taxpercentage' => 9, 'country_status' => 1]);
    Fixtures::insert('country', ['countryid' => 129, 'countryname' => 'Malaysia', 'countrycode' => 'MY', 'taxtitle' => '', 'taxpercentage' => 0, 'country_status' => 1]);
    Fixtures::insert('paymentsettings', ['id' => 1, 'fuelcharge_percentage' => 20, 'min_package_fee' => 5, 'free_shipping_amount' => 100]);
    $this->cart = new CartServices();
});

it('charges the country tax rate on the ex-tax subtotal', function () {
    // taxpercentage is a DECIMAL column, so it comes back as the string "9.00"
    expect($this->cart->getTax(100, 'SG'))->toBe([
        'taxLabelOnly' => 'GST',
        'taxLabel' => 'GST (9.00%)',
        'taxPercentage' => '9.00',
        'taxTotal' => 9.0,
    ]);
});

it('falls back to the label "Tax" when a country has no tax title', function () {
    expect($this->cart->getTax(100, 'MY')['taxLabel'])->toBe('Tax (0.00%)');
});

it('charges no tax during a punchout session', function () {
    session(['punchout' => true]);

    $tax = $this->cart->getTax(100, 'SG');

    expect($tax['taxPercentage'])->toBe(0)
        ->and($tax['taxTotal'])->toEqual(0);
});

it('gives no discount without a coupon in the session', function () {
    expect($this->cart->getDiscount(100, 9, 10, 1))->toBe(['title' => '', 'discountTotal' => 0.0]);
});

it('applies a percentage coupon to subtotal + tax + shipping + packing', function () {
    session(['couponcode' => 'SAVE10', 'discounttype' => 1, 'discount' => 10, 'discounttext' => 'Coupon discount(10%)']);

    // (100 + 9 + 10 + 1) * 10% = 12
    expect($this->cart->getDiscount(100, 9, 10, 1))->toBe(['title' => 'Coupon discount(10%)', 'discountTotal' => 12.0]);
});

it('applies a fixed coupon as-is, even when larger than the order', function () {
    session(['couponcode' => 'FLAT', 'discounttype' => 2, 'discount' => 500, 'discounttext' => 'Coupon discount($500)']);

    expect($this->cart->getDiscount(20, 1.8, 0, 0)['discountTotal'])->toEqual(500.0);
});

it('charges no fuel surcharge in Singapore', function () {
    expect($this->cart->getFuelCharges(50, 'SG'))->toEqual(0);
});

it('charges the fuel surcharge percentage on overseas shipping', function () {
    // 20% of 50
    expect($this->cart->getFuelCharges(50, 'MY'))->toEqualWithDelta(10.0, 0.00001);
});

it('charges a handling fee per kg overseas, read from env HANDLING_PER', function () {
    // HANDLING_PER=2 in .env.testing. It is read with env() outside config, so
    // after `php artisan config:cache` it would become null and the fee 0.
    expect($this->cart->getHandlingFee(3, 'MY'))->toEqual(6)
        ->and($this->cart->getHandlingFee(3, 'SG'))->toEqual(0);
});
