<?php

use App\Services\CartServices;

// Characterization tests for the parts of CartServices that need no database.

it('strips GST that is included in an item price', function ($price, $gst, $expected) {
    expect(CartServices::excludeItemPriceGst($price, $gst))->toEqualWithDelta($expected, 0.00001);
})->with([
    'SG 9%' => [109, 9, 100.00],
    '7%' => [107, 7, 100.00],
    'rounds to cents' => [10, 9, 9.17],
    'no tax' => [50, 0, 50.00],
]);

it('adds every charge and subtracts the discount for the grand total', function () {
    $total = (new CartServices())->getGrandTotal(
        100.00, // subtotal (ex tax)
        9.00,   // tax
        10.00,  // delivery
        2.00,   // packing
        5.00,   // discount
        1.00,   // fuel
        0.50    // handling
    );

    expect($total)->toEqualWithDelta(117.50, 0.00001);
});

it('rounds the grand total to cents', function () {
    expect((new CartServices())->getGrandTotal(10.004, 0.001))->toEqualWithDelta(10.01, 0.00001);
});
