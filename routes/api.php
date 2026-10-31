<?php

use App\Http\Controllers\ApiController;
use App\Http\Controllers\CartMobileController;
use App\Http\Controllers\CommonMobileController;
use App\Http\Controllers\CustomerMobileController;
use App\Http\Controllers\ProductMobileController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Loaded under the /api prefix with the "api" middleware group
| (see bootstrap/app.php).
|
 */

/* Mobile App - Start */
Route::post('/storecustomer', [CustomerMobileController::class, 'storecustomer']);
Route::match(['get', 'post'], '/chkcustomeravailable', [CustomerMobileController::class, 'chkcustomeravailable']);
Route::post('/customerlogin', [CustomerMobileController::class, 'customerlogin']);
Route::match(['get', 'post'], '/customerdata', [CustomerMobileController::class, 'customerdata']);
Route::match(['get', 'post'], '/changecustomerpassword', [CustomerMobileController::class, 'changecustomerpassword']);
Route::match(['get', 'post'], '/customerforgotpassword', [CustomerMobileController::class, 'customerforgotpassword']);

Route::match(['get', 'post'], '/mywishlist', [CustomerMobileController::class, 'mywishlist']);
Route::match(['get', 'post'], '/addwishlist', [CustomerMobileController::class, 'addwishlist']);
Route::match(['get', 'post'], '/removefromwishlist', [CustomerMobileController::class, 'removefromwishlist']);

Route::match(['get', 'post'], '/orderlist', [CustomerMobileController::class, 'orderlist']);
Route::match(['get', 'post'], '/orderinfo', [CustomerMobileController::class, 'orderinfo']);

Route::post('/updatecustomerdata', [CustomerMobileController::class, 'updatecustomerdata']);
Route::get('/allcategory', [CommonMobileController::class, 'allcategory']);
Route::get('/allbrands', [CommonMobileController::class, 'allbrands']);
Route::get('/allcountries', [CommonMobileController::class, 'allcountries']);

Route::get('/allbrandswithorder', [CommonMobileController::class, 'allbrandswithorder']);

Route::get('/offermessages', [CommonMobileController::class, 'offermessages']);

Route::get('/popularbrands', [CommonMobileController::class, 'popularbrands']);
Route::get('/brandbyfilter/{filter}', [CommonMobileController::class, 'brandbyfilter']);
Route::get('/offerCategories', [CommonMobileController::class, 'offerCategories']);
Route::get('/homebanners', [CommonMobileController::class, 'homebanners']);
Route::get('/adbanners', [CommonMobileController::class, 'adbanners']);
Route::get('/terms', [CommonMobileController::class, 'terms']);
Route::post('/sendfeedback', [CommonMobileController::class, 'sendfeedback']);
Route::get('/mobileversions', [CommonMobileController::class, 'mobileversions']);

Route::get('/promotionalitems', [ProductMobileController::class, 'promotionalitems']);
Route::match(['get', 'post'], '/branditems', [ProductMobileController::class, 'branditems']);
Route::match(['get', 'post'], '/categoryitems', [ProductMobileController::class, 'categoryitems']);
Route::match(['get', 'post'], '/childcategories', [CommonMobileController::class, 'childcategories']);
Route::match(['get', 'post'], '/itemdetails', [ProductMobileController::class, 'itemdetails']);
Route::match(['get', 'post'], '/childcategorywithproducts', [ProductMobileController::class, 'childcategorywithproducts']);
Route::get('/allitems', [ProductMobileController::class, 'allitems']);
Route::get('/promotionalitemnames', [ProductMobileController::class, 'promotionalitemnames']);
Route::get('/allitemnames', [ProductMobileController::class, 'allitemnames']);
Route::any('/sendqa', [ProductMobileController::class, 'sendqa']);

Route::match(['get', 'post'], '/storeproductrating', [ProductMobileController::class, 'storeproductrating']);

Route::prefix('cart')->group(function () {
    Route::post('add_item', [CartMobileController::class, 'addItem']);
    Route::post('remove/{itemId?}', [CartMobileController::class, 'removeItem']);
    Route::post('update', [CartMobileController::class, 'updateItem']);
    Route::post('list', [CartMobileController::class, 'cartLists']);
});

Route::post('list-shipping-methods', [CartMobileController::class, 'ListShippingmethods']);
Route::post('get-shipping-package-price', [CartMobileController::class, 'getShippingPackagePrice']);

Route::any('/shoppingcart', [CartMobileController::class, 'shoppingcart']);
Route::match(['get', 'post'], '/addtoshoppingcart', [CartMobileController::class, 'addtoshoppingcart']);
Route::match(['get', 'post'], '/clearshoppingcart', [CartMobileController::class, 'clearshoppingcart']);
Route::match(['get', 'post'], '/clearcustomershoppingcart', [CartMobileController::class, 'clearcustomershoppingcart']);
Route::get('/removeshoppingcartitem', [CartMobileController::class, 'removeshoppingcartitem']);
Route::match(['get', 'post'], '/updateshoppingcart', [CartMobileController::class, 'updateshoppingcart']);
Route::get('/shippingmethods', [CartMobileController::class, 'shippingmethods']);
Route::get('/paymethods', [CartMobileController::class, 'paymethods']);
Route::post('/createorder', [CartMobileController::class, 'createorder']);
Route::match(['get', 'post'], '/updateorder', [CartMobileController::class, 'updateorder']);
Route::post('/getshipandpackingprice', [CartMobileController::class, 'getshipandpackingprice']);

Route::match(['get', 'post'], '/paypalsuccess', [CartMobileController::class, 'paypalsuccess']);
Route::match(['get', 'post'], '/discountcoupon', [CartMobileController::class, 'discountcoupon']);

Route::match(['get', 'post'], '/successorder', [CartMobileController::class, 'successorder']);
Route::match(['get', 'post'], '/cancelorder', [CartMobileController::class, 'cancelorder']);
Route::match(['get', 'post'], '/paypalpayment', [CartMobileController::class, 'paypalpayment']);
Route::match(['get', 'post'], '/stripepayment', [CartMobileController::class, 'stripepayment']);

Route::post('ios-create-payment-intend', [CartMobileController::class, 'iOSCreateStripePaymentIntend']);

Route::post('/createquotation', [CartMobileController::class, 'createquotation']);
Route::match(['get', 'post'], '/quotationlist', [CustomerMobileController::class, 'quotationlist']);

Route::match(['get', 'post'], '/makerepayment', [CartMobileController::class, 'makerepayment']);

Route::post('products/category-wise', [ProductMobileController::class, 'listProductsByCategoryWise']);
Route::post('products/brand-wise', [ProductMobileController::class, 'listProductsByBrandWise']);
Route::get('faq', [CommonMobileController::class, 'listFaqs']);
Route::get('new-arrivals', [ProductMobileController::class, 'newArrivals']);
Route::post('new-arrivals-names', [ProductMobileController::class, 'newArrivalsNames']);
Route::post('privacy-policy', [CommonMobileController::class, 'listPrivacyPolicy']);
// Test email (sends to any address, so it is key-protected below)

Route::post('request-account-deactivate', [CustomerMobileController::class, 'requestAccountDeactivate']);
Route::post('verify-account-deactivate', [CustomerMobileController::class, 'verifyAccountDeactivate']);

Route::middleware('integration.key')->group(function () {
    Route::post('send-test-email', [CommonMobileController::class, 'sendTestEmail']);

    Route::post('create_category', [ApiController::class, 'createCategory']);
    Route::post('create_sub_category', [ApiController::class, 'createSubCategory']);

    Route::post('create_product', [ApiController::class, 'createProduct']);
    Route::post('productStatusUpdate', [ApiController::class, 'productStatusUpdate']);
    Route::post('stock_update', [ApiController::class, 'stockUpdate']);
    Route::post('product_all_update', [ApiController::class, 'productAllUpdate']);

    Route::get('dailyorders', [ApiController::class, 'listDailyOrders']);
});
/* Mobile App - End */

// PayPal JS SDK button (Orders v2) for the web checkout and the v1 app's PayPal web view
Route::middleware('throttle:api-v2')->group(function () {
    Route::post('paypal/orders/{order}', [\App\Http\Controllers\PayPalButtonController::class, 'create'])->whereNumber('order');
    Route::post('paypal/orders/{order}/capture/{return}', [\App\Http\Controllers\PayPalButtonController::class, 'capture'])
        ->whereNumber('order')->whereIn('return', ['web', 'app']);
});
