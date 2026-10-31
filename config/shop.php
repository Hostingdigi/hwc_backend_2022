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

];
