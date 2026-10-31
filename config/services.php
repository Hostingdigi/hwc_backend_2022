<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Third Party Services
    |--------------------------------------------------------------------------
    |
    | This file is for storing the credentials for third party services such
    | as Resend, Postmark, AWS, and more. This file provides the de facto
    | location for this type of information, allowing packages to have
    | a conventional file to locate the various service credentials.
    |
    */

    'postmark' => [
        'key' => env('POSTMARK_API_KEY'),
    ],

    'resend' => [
        'key' => env('RESEND_API_KEY'),
    ],

    'ses' => [
        'key' => env('AWS_ACCESS_KEY_ID'),
        'secret' => env('AWS_SECRET_ACCESS_KEY'),
        'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),
    ],

    'slack' => [
        'notifications' => [
            'bot_user_oauth_token' => env('SLACK_BOT_USER_OAUTH_TOKEN'),
            'channel' => env('SLACK_BOT_USER_DEFAULT_CHANNEL'),
        ],
    ],

    // Shared key for the catalogue/order sync endpoints in routes/api.php (ApiController)
    'integration' => [
        'key' => env('INTEGRATION_API_KEY'),
    ],

    // App\Services\PaymentVerifier: "enforce" keeps unverified orders pending, "log" only logs
    'payment_verification' => [
        'mode' => env('PAYMENT_VERIFICATION_MODE', 'enforce'),
    ],

    // Mobile createorder: "enforce" refuses an order whose app-sent total differs
    // from the server quote; "log" accepts it (old behaviour) but logs the mismatch
    'mobile_pricing' => [
        'mode' => env('MOBILE_PRICING_MODE', 'enforce'),
    ],

    // cXML punchout: the SharedSecret the buyer's procurement system sends in
    // Header/Sender/Credential. Setup requests are refused while this is empty.
    'punchout' => [
        'shared_secret' => env('PUNCHOUT_SHARED_SECRET'),
    ],

    // Stripe keys stay in the admin (Payment Methods); only the webhook signing
    // secret lives here. Stripe dashboard > Developers > Webhooks, event
    // payment_intent.succeeded, URL https://<your domain>/api/webhooks/stripe
    'stripe' => [
        'webhook_secret' => env('STRIPE_WEBHOOK_SECRET'),
    ],

    'recaptcha' => [
        'site_key' => env('GOOGLE_CAPTCHA_SITE_KEY'),
        'secret' => env('GOOGLE_CAPTCHA_SECRET_KEY'),
    ],

];
