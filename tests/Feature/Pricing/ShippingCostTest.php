<?php

use App\Services\CartServices;
use Tests\Support\Fixtures;

// Characterization tests: CartServices::getShippingCost as it behaves today.

beforeEach(function () {
    Fixtures::insert('country', ['countryid' => 189, 'countryname' => 'Singapore', 'countrycode' => 'SG', 'taxpercentage' => 9]);
    Fixtures::insert('country', ['countryid' => 129, 'countryname' => 'Malaysia', 'countrycode' => 'MY', 'taxpercentage' => 0]);
    Fixtures::insert('country', ['countryid' => 12, 'countryname' => 'Australia', 'countrycode' => 'AU', 'taxpercentage' => 0]);

    // Local (SG) weight bands; free above $100 incl. GST
    Fixtures::insert('local_shipping_methods', [
        'Status' => 1, 'DisplayOrder' => 1, 'FreeShipCost' => 100,
        'PriceRange5' => 8, 'PriceRange15' => 12, 'PriceRange30' => 20, 'PriceRangeAbove30' => 35,
    ]);
    Fixtures::insert('shipping_methods', ['Id' => 2, 'EnName' => 'Courier', 'shipping_type' => 1, 'Status' => 1]);
    Fixtures::insert('shipping_methods', ['Id' => 3, 'EnName' => 'Self Collection', 'shipping_type' => 0, 'Status' => 1]);
    Fixtures::insert('shipping_methods', ['Id' => 4, 'EnName' => 'Ninja Van Delivery', 'shipping_type' => 0, 'Status' => 1]);

    // International zone for Malaysia: "weight:price" bands up to 30kg, per-kg above
    Fixtures::insert('international_shipping_methods', [
        'Zone' => 'Zone A', 'CountriesList' => '129', 'Status' => 1, 'FreeShippingCost' => 0,
        'ShipCost' => '1:20,5:40,10:60,30:100',
        'PriceRange70' => 4, 'PriceRange300' => 3, 'PriceRange99999' => 2,
    ]);

    $this->cart = new CartServices();
});

it('charges SG courier delivery by weight band', function ($weight, $expected) {
    // subtotal 50 + GST 4.50 is under the $100 free-shipping threshold
    expect((float) $this->cart->getShippingCost('SG', 2, '', 1, 50, 4.5, $weight))->toEqual($expected);
})->with([
    '<= 5kg' => [3, 8.0],
    '<= 15kg' => [10, 12.0],
    '<= 30kg' => [25, 20.0],
    '> 30kg' => [31, 35.0],
]);

it('ships free in SG once subtotal + GST reaches the threshold', function () {
    expect($this->cart->getShippingCost('SG', 2, '', 1, 95, 5, 3))->toEqual(0);
});

it('charges nothing for SG methods with shipping_type 0 (self collection)', function () {
    expect($this->cart->getShippingCost('SG', 3, '', 1, 10, 0.9, 3))->toEqual(0);
});

it('still charges Ninja Van even though its shipping_type is 0', function () {
    expect((float) $this->cart->getShippingCost('SG', 4, '', 1, 10, 0.9, 3))->toEqual(8.0);
});

it('prices light overseas parcels from the band table plus 25% + 8% commission and a DHL surcharge', function ($weight, $expected) {
    expect($this->cart->getShippingCost('MY', 2, '', 1, 50, 0, $weight))->toEqualWithDelta($expected, 0.00001);
})->with([
    // 2kg -> band 5:40; commission 40*25% = 10, +8% of that = 0.80; DHL extra 0 (<= 2.5kg)
    '2kg' => [2, 50.80],
    // 3kg -> band 40; same commission; DHL extra 8.50
    '3kg' => [3, 59.30],
    // 12kg -> band 30:100; commission 25 + 2; DHL extra 8.50
    '12kg' => [12, 135.50],
]);

it('prices heavy overseas parcels per kg', function () {
    // 50kg * 4 = 200; commission 50 + 4; DHL extra 27.50
    expect($this->cart->getShippingCost('MY', 2, '', 1, 50, 0, 50))->toEqualWithDelta(281.50, 0.00001);
});

it('ships free overseas above the zone threshold', function () {
    \Illuminate\Support\Facades\DB::table('international_shipping_methods')->update(['FreeShippingCost' => 300]);

    expect($this->cart->getShippingCost('MY', 2, '', 1, 300, 0, 3))->toEqual(0);
});

it('charges nothing for a country with no zone', function () {
    \Illuminate\Support\Facades\DB::table('international_shipping_methods')->update(['CountriesList' => '999']);

    expect($this->cart->getShippingCost('MY', 2, '', 1, 50, 0, 3))->toEqual(0);
});

it('matches the zone by exact country id, not by prefix (12 is not 129)', function () {
    // Fixed in Phase 3: the old LIKE '%,12%,' lookup put country 12 in country 129's zone
    expect($this->cart->getShippingCost('AU', 2, '', 1, 50, 0, 3))->toEqual(0);

    Fixtures::insert('international_shipping_methods', [
        'Zone' => 'Zone B', 'CountriesList' => '59,12,9', 'Status' => 1, 'FreeShippingCost' => 0,
        'ShipCost' => '1:10,5:20', 'PriceRange70' => 1, 'PriceRange300' => 1, 'PriceRange99999' => 1,
    ]);

    // 3kg -> band 20; commission 5 + 0.40; DHL extra 8.50
    expect($this->cart->getShippingCost('AU', 2, '', 1, 50, 0, 3))->toEqualWithDelta(33.90, 0.00001);
});

it('ignores trailing commas and inactive zones in the country list', function () {
    \Illuminate\Support\Facades\DB::table('international_shipping_methods')->update(['CountriesList' => '129,', 'Status' => 0]);
    expect(\App\Services\ShippingZones::forCountryId(129))->toBeNull();

    \Illuminate\Support\Facades\DB::table('international_shipping_methods')->update(['Status' => 1]);
    expect(\App\Services\ShippingZones::forCountryId(129)->Zone)->toBe('Zone A');
});
