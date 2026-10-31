<?php

use App\Http\Controllers\Api\V2\StripeWebhookController;
use App\Http\Middleware\AuthorizeAdminModule;
use App\Http\Middleware\BlockPunchoutRoutes;
use App\Http\Middleware\CheckStatus;
use App\Http\Middleware\EnsureAdminIsLoggedIn;
use App\Http\Middleware\LegacyMobileApi;
use App\Http\Middleware\VerifyIntegrationApiKey;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Support\Facades\Route;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
        then: function () {
            // Mobile API v2: its own per-customer rate limit instead of the v1 "api"
            // group's single shared bucket.
            Route::prefix('api/v2')
                ->middleware([SubstituteBindings::class, 'throttle:api-v2'])
                ->group(base_path('routes/api_v2.php'));

            // Gateway webhooks: verified by signature, no rate limit, no session/CSRF
            Route::post('api/webhooks/stripe', StripeWebhookController::class)->middleware(SubstituteBindings::class);
        },
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
            'admin.module' => AuthorizeAdminModule::class,
            'integration.key' => VerifyIntegrationApiKey::class,
            'legacy.mobile' => LegacyMobileApi::class,
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
            'paypalsuccess',
            'discountcoupon',
            'successorder',
            'cancelorder',
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        // API v2 and webhooks always answer in JSON. Everything else keeps Laravel's
        // default (JSON only when the client asks for it), as in Laravel 8.
        $exceptions->shouldRenderJsonWhen(
            fn (Request $request) => $request->is('api/v2/*', 'api/webhooks/*') || $request->expectsJson(),
        );
    })->create();
