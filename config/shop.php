<?php

/*
| Store settings that used to be read with env() inside controllers. Reading
| env() outside config files returns null after `php artisan config:cache`,
| which (for example) silently set the overseas handling fee to 0.
*/

return [

    // Base URL for product/brand images in mobile API responses
    'img_url' => env('IMG_URL'),

    // Overseas handling fee per kg (CartServices::getHandlingFee)
    'handling_per_kg' => env('HANDLING_PER'),

    // Fuel surcharge percentage shown on the order review page
    'fuel_surcharge_percent' => env('FUEL_SUBCHARGE'),

    /*
    | The original mobile app endpoints (routes/api.php and their copies in
    | routes/web.php), replaced by /api/v2. "on" serves them with deprecation
    | headers and counts the calls (php artisan api:legacy-usage); "off" answers
    | 410 so the old app tells the customer to update. mobileversions keeps
    | working either way so the old app can still prompt for the update.
    */
    'legacy_mobile_api' => env('LEGACY_MOBILE_API', 'on'),

    // Planned switch-off date (Y-m-d), sent to the old app as a Sunset header
    'legacy_mobile_api_sunset' => env('LEGACY_MOBILE_API_SUNSET'),

];
