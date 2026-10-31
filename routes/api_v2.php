<?php

use App\Http\Controllers\Api\V2\AuthController;
use App\Http\Controllers\Api\V2\CartController;
use App\Http\Controllers\Api\V2\CatalogueController;
use App\Http\Controllers\Api\V2\EngagementController;
use App\Http\Controllers\Api\V2\OrderController;
use App\Http\Controllers\Api\V2\PaymentController;
use App\Http\Controllers\Api\V2\ProfileController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Mobile API v2  (/api/v2/...)
|--------------------------------------------------------------------------
|
| Sanctum bearer tokens identify the customer; nothing trusts a customer id
| sent by the app. See docs/api-v2.md. The original /api endpoints keep
| working (routes/api.php) until the app has moved over.
|
*/

// Sign-up / sign-in (stricter rate limit)
Route::middleware('throttle:api-v2-auth')->group(function () {
    Route::post('auth/register', [AuthController::class, 'register']);
    Route::post('auth/login', [AuthController::class, 'login']);
    Route::post('auth/forgot-password', [AuthController::class, 'forgotPassword']);
});

// Public catalogue
Route::get('categories', [CatalogueController::class, 'categories']);
Route::get('brands', [CatalogueController::class, 'brands']);
Route::get('products', [CatalogueController::class, 'products']);
Route::get('products/{id}', [CatalogueController::class, 'product'])->whereNumber('id');
Route::get('banners', [CatalogueController::class, 'banners']);
Route::get('countries', [CatalogueController::class, 'countries']);
Route::get('pages/{key}', [CatalogueController::class, 'page'])->where('key', '[a-z0-9\-]+');
Route::get('app/version', [CatalogueController::class, 'appVersion']);
Route::get('shipping-methods', [CatalogueController::class, 'shippingMethods']);
Route::get('payment-methods', [CatalogueController::class, 'paymentMethods']);
Route::post('feedback', [EngagementController::class, 'feedback']);

// Gateway redirect landing pages (the app watches for these URLs)
Route::get('payments/{gateway}/{result}', [PaymentController::class, 'gatewayReturn'])
    ->whereIn('gateway', ['paypal', 'atome'])->whereIn('result', ['return', 'cancel']);

// Signed-in customer
Route::middleware('auth:sanctum')->group(function () {
    Route::post('auth/logout', [AuthController::class, 'logout']);

    Route::get('me', [ProfileController::class, 'show']);
    Route::patch('me', [ProfileController::class, 'update']);
    Route::put('me/password', [ProfileController::class, 'changePassword']);
    Route::post('me/deactivation', [ProfileController::class, 'requestDeactivation']);
    Route::post('me/deactivation/confirm', [ProfileController::class, 'confirmDeactivation']);

    Route::get('cart', [CartController::class, 'show']);
    Route::post('cart/items', [CartController::class, 'add']);
    Route::patch('cart/items/{line}', [CartController::class, 'update'])->whereNumber('line');
    Route::delete('cart/items/{line}', [CartController::class, 'remove'])->whereNumber('line');
    Route::delete('cart', [CartController::class, 'clear']);
    Route::post('checkout/quote', [CartController::class, 'quote']);
    Route::post('coupons/check', [CartController::class, 'checkCoupon']);

    Route::get('orders', [OrderController::class, 'index']);
    Route::post('orders', [OrderController::class, 'store']);
    Route::get('orders/{order}', [OrderController::class, 'show'])->whereNumber('order');

    Route::post('orders/{order}/payments/stripe', [PaymentController::class, 'stripeIntent'])->whereNumber('order');
    Route::post('orders/{order}/payments/stripe/confirm', [PaymentController::class, 'stripeConfirm'])->whereNumber('order');
    Route::post('orders/{order}/payments/paypal', [PaymentController::class, 'paypalCreate'])->whereNumber('order');
    Route::post('orders/{order}/payments/paypal/capture', [PaymentController::class, 'paypalCapture'])->whereNumber('order');
    Route::post('orders/{order}/payments/atome', [PaymentController::class, 'atomeCreate'])->whereNumber('order');
    Route::post('orders/{order}/payments/atome/confirm', [PaymentController::class, 'atomeConfirm'])->whereNumber('order');

    Route::get('wishlist', [EngagementController::class, 'wishlist']);
    Route::post('wishlist', [EngagementController::class, 'addToWishlist']);
    Route::delete('wishlist/{product}', [EngagementController::class, 'removeFromWishlist'])->whereNumber('product');

    Route::post('products/{product}/reviews', [EngagementController::class, 'review'])->whereNumber('product');
    Route::post('products/{product}/questions', [EngagementController::class, 'question'])->whereNumber('product');
});
