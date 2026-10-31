<?php

namespace App\Services;

use App\Models\InternationalShipping;

/**
 * Finds the international shipping zone for a country.
 *
 * international_shipping_methods.CountriesList is a comma-separated list of
 * country ids ("59,12,9,5,..."). The old lookup used LIKE '%,12%,', which also
 * matched 123, 127, ... so many countries were priced with another zone's rates.
 * This matches the id exactly, the way the checkout's free-shipping message
 * already did.
 */
class ShippingZones
{
    public static function forCountryId($countryId): ?InternationalShipping
    {
        $countryId = (string) (int) $countryId;
        if ($countryId === '0') {
            return null;
        }

        foreach (InternationalShipping::where('Status', 1)->orderBy('Id')->get() as $zone) {
            $ids = array_map('trim', explode(',', (string) $zone->CountriesList));
            if (in_array($countryId, $ids, true)) {
                return $zone;
            }
        }

        return null;
    }
}
