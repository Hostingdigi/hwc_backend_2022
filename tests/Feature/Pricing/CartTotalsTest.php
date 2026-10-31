<?php

use App\Services\CartServices;
use Tests\Support\Fixtures;

// Characterization tests: the full cart total the web checkout shows (CartServices::cartItems).

beforeEach(function () {
    Fixtures::insert('country', ['countryid' => 189, 'countryname' => 'Singapore', 'countrycode' => 'SG', 'taxtitle' => 'GST', 'taxpercentage' => 9]);
    Fixtures::insert('paymentsettings', ['id' => 1, 'fuelcharge_percentage' => 20, 'min_package_fee' => 5, 'free_shipping_amount' => 100]);
    Fixtures::insert('local_shipping_methods', ['Status' => 1, 'DisplayOrder' => 1, 'FreeShipCost' => 100, 'PriceRange5' => 8]);
    Fixtures::insert('types', ['TypeId' => 50, 'EnName' => 'Power Tools', 'UniqueKey' => 'power-tools']);

    $this->drill = Fixtures::insert('products', ['EnName' => 'Drill', 'Types' => 50, 'Price' => 54.50, 'ProdStatus' => 1]);
    $this->bits = Fixtures::insert('products', ['EnName' => 'Drill bits', 'Types' => 50, 'Price' => 10.90, 'ProdStatus' => 1]);
});

function putCartInSession(array $items)
{
    session(['_token' => 'cart-token', 'cartdata' => ['cart-token' => $items]]);
}

function cartLine($productId, $qty, $unitPriceInclGst, $weight)
{
    return [
        'productId' => $productId, 'qty' => $qty, 'price' => $unitPriceInclGst,
        'total' => $qty * $unitPriceInclGst, 'weight' => $weight, 'shippingbox' => '',
    ];
}

it('backs GST out of tax-inclusive prices, then re-adds it on the ex-tax subtotal', function () {
    // 54.50 + 2 x 10.90 = 76.30 incl. 9% GST -> GST 6.30, ex-tax 70.00
    putCartInSession([cartLine($this->drill, 1, 54.50, 2), cartLine($this->bits, 2, 10.90, 0.2)]);

    $cart = (new CartServices())->cartItems('SG');

    expect($cart['subTotal'])->toEqual(70.00)
        ->and($cart['taxDetails']['taxTotal'])->toEqual(6.30)
        ->and($cart['deliveryDetails']['deliveryTotal'])->toEqual(0)
        ->and($cart['packingFees'])->toEqual(0)
        ->and($cart['grandTotal'])->toEqual(76.30);
});

it('replaces the delivery cost with the category shipping charge when one matches', function () {
    Fixtures::insert('category_shipping_charges', ['name' => 'Bulky', 'category_ids' => '40,50', 'amount' => 15, 'status' => 1]);
    putCartInSession([cartLine($this->drill, 1, 54.50, 2)]);

    $cart = (new CartServices())->cartItems('SG');

    // 54.50 incl. GST -> 50.00 + 4.50, plus the 15.00 category charge
    expect($cart['deliveryDetails']['deliveryTotal'])->toEqual(15)
        ->and($cart['grandTotal'])->toEqual(69.50);
});

it('applies a percentage coupon from the session to the whole cart', function () {
    putCartInSession([cartLine($this->drill, 1, 54.50, 2)]);
    session(['couponcode' => 'SAVE10', 'discounttype' => 1, 'discount' => 10, 'discounttext' => 'Coupon discount(10%)']);

    $cart = (new CartServices())->cartItems('SG');

    // (50.00 + 4.50) * 10% = 5.45 off
    expect($cart['discountDetails']['discountTotal'])->toEqual(5.45)
        ->and($cart['grandTotal'])->toEqual(49.05);
});

it('returns zero totals for an empty cart', function () {
    putCartInSession([]);

    $cart = (new CartServices())->cartItems('SG');

    expect($cart['subTotal'])->toEqual(0)
        ->and($cart['grandTotal'])->toEqual(0);
});
