<?php

use App\Http\Middleware\BlockPunchoutRoutes;
use App\Http\Middleware\CheckStatus;
use App\Http\Middleware\EnsureAdminIsLoggedIn;
use App\Http\Middleware\VerifyIntegrationApiKey;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->web(append: [
            BlockPunchoutRoutes::class,
        ]);

        // Same as Laravel 8's "throttle:api" on the api group. The "api" limiter
        // is defined in AppServiceProvider.
        $middleware->throttleApi();

        $middleware->alias([
            'checkStatus' => CheckStatus::class,
            'admin.session' => EnsureAdminIsLoggedIn::class,
            'integration.key' => VerifyIntegrationApiKey::class,
        ]);

        $middleware->validateCsrfTokens(except: [
            // cXML punchout: posted by the buyer's procurement system
            'punchout/setup',

            // Payment gateway callbacks / redirects back to the site
            'success',
            'atomecallback',
            'grabpaywebhook',
            'grabpay-complete',

            // Legacy mobile-app endpoints that are also registered on the web
            // routes (no session/CSRF token on the app side). The /api copies
            // are not affected by CSRF at all.
            'storecustomer',
            'chkcustomeravailable',
            'customerlogin',
            'customerdata',
            'changecustomerpassword',
            'customerforgotpassword',
            'mywishlist',
            'addwishlist',
            'removefromwishlist',
            'orderlist',
            'orderinfo',
            'updatecustomerdata',
            'sendfeedback',
            'branditems',
            'categoryitems',
            'childcategories',
            'itemdetails',
            'childcategorywithproducts',
            'sendqa',
            'storeproductrating',
            'addtoshoppingcart',
            'updateshoppingcart',
            'createorder',
            'updateorder',
            'getshipandpackingprice',
            'hoolahcancelpayment',
            'hoolahsuccess',
            'paypalsuccess',
            'discountcoupon',
            'successorder',
            'cancelorder',
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        // Laravel's default: JSON errors only when the client asks for JSON
        // (Accept: application/json), as in Laravel 8.
    })->create();
