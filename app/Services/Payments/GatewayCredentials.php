<?php

namespace App\Services\Payments;

use App\Models\PaymentMethods;

/**
 * Credentials for a payment gateway from its paymentmethods row (managed in the
 * admin under Settings > Payment Methods): live or test values by payment_mode.
 */
class GatewayCredentials
{
    // paymentmethods.Id values used throughout the app
    public const STRIPE = 3;
    public const PAYPAL = 6;
    public const ATOME = 7;

    /** @return array{live: bool, key: string, secret: string, url: string} */
    public static function for(int $paymentMethodId): array
    {
        $pm = PaymentMethods::where('id', $paymentMethodId)->first();
        $live = $pm && $pm->payment_mode == 'live';

        return [
            'live' => $live,
            'key' => (string) ($pm ? ($live ? $pm->api_key : $pm->test_api_key) : ''),
            'secret' => (string) ($pm ? ($live ? $pm->api_signature : $pm->test_api_signature) : ''),
            'url' => (string) ($pm ? ($live ? $pm->live_url : $pm->testing_url) : ''),
        ];
    }
}
