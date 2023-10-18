<?php

use App\Http\Controllers\CartMobileController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
 */
use Illuminate\Support\Facades\Route;

/* Mobile App - Start */
Route::post('/storecustomer', 'App\Http\Controllers\CustomerMobileController@storecustomer');
Route::match(['get', 'post'], '/chkcustomeravailable', 'App\Http\Controllers\CustomerMobileController@chkcustomeravailable');
Route::post('/customerlogin', 'App\Http\Controllers\CustomerMobileController@customerlogin');
Route::match(['get', 'post'], '/customerdata', 'App\Http\Controllers\CustomerMobileController@customerdata');
Route::match(['get', 'post'], '/changecustomerpassword', 'App\Http\Controllers\CustomerMobileController@changecustomerpassword');
Route::match(['get', 'post'], '/customerforgotpassword', 'App\Http\Controllers\CustomerMobileController@customerforgotpassword');

Route::match(['get', 'post'], '/mywishlist', 'App\Http\Controllers\CustomerMobileController@mywishlist');
Route::match(['get', 'post'], '/addwishlist', 'App\Http\Controllers\CustomerMobileController@addwishlist');
Route::match(['get', 'post'], '/removefromwishlist', 'App\Http\Controllers\CustomerMobileController@removefromwishlist');

Route::match(['get', 'post'], '/orderlist', 'App\Http\Controllers\CustomerMobileController@orderlist');
Route::match(['get', 'post'], '/orderinfo', 'App\Http\Controllers\CustomerMobileController@orderinfo');

Route::post('/updatecustomerdata', 'App\Http\Controllers\CustomerMobileController@updatecustomerdata');
Route::get('/allcategory', 'App\Http\Controllers\CommonMobileController@allcategory');
Route::get('/allbrands', 'App\Http\Controllers\CommonMobileController@allbrands');
Route::get('/allcountries', 'App\Http\Controllers\CommonMobileController@allcountries');

Route::get('/allbrandswithorder', 'App\Http\Controllers\CommonMobileController@allbrandswithorder');

Route::get('/offermessages', 'App\Http\Controllers\CommonMobileController@offermessages');

Route::get('/popularbrands', 'App\Http\Controllers\CommonMobileController@popularbrands');
Route::get('/brandbyfilter/{filter}', 'App\Http\Controllers\CommonMobileController@brandbyfilter');
Route::get('/offerCategories', 'App\Http\Controllers\CommonMobileController@offerCategories');
Route::get('/homebanners', 'App\Http\Controllers\CommonMobileController@homebanners');
Route::get('/adbanners', 'App\Http\Controllers\CommonMobileController@adbanners');
Route::get('/terms', 'App\Http\Controllers\CommonMobileController@terms');
Route::post('/sendfeedback', 'App\Http\Controllers\CommonMobileController@sendfeedback');
Route::get('/mobileversions', 'App\Http\Controllers\CommonMobileController@mobileversions');

Route::get('/promotionalitems', 'App\Http\Controllers\ProductMobileController@promotionalitems');
Route::match(['get', 'post'], '/branditems', 'App\Http\Controllers\ProductMobileController@branditems');
Route::match(['get', 'post'], '/categoryitems', 'App\Http\Controllers\ProductMobileController@categoryitems');
Route::match(['get', 'post'], '/childcategories', 'App\Http\Controllers\CommonMobileController@childcategories');
Route::match(['get', 'post'], '/itemdetails', 'App\Http\Controllers\ProductMobileController@itemdetails');
Route::match(['get', 'post'], '/childcategorywithproducts', 'App\Http\Controllers\ProductMobileController@childcategorywithproducts');
Route::get('/allitems', 'App\Http\Controllers\ProductMobileController@allitems');
Route::get('/promotionalitemnames', 'App\Http\Controllers\ProductMobileController@promotionalitemnames');
Route::get('/allitemnames', 'App\Http\Controllers\ProductMobileController@allitemnames');
Route::any('/sendqa', 'App\Http\Controllers\ProductMobileController@sendqa');

Route::match(['get', 'post'], '/storeproductrating', 'App\Http\Controllers\ProductMobileController@storeproductrating');

Route::prefix('cart')->group(function () {
    Route::post('add_item', [CartMobileController::class, 'addItem']);
    Route::post('remove/{itemId?}', [CartMobileController::class, 'removeItem']);
    Route::post('update', [CartMobileController::class, 'updateItem']);
    Route::post('list', [CartMobileController::class, 'cartLists']);
});

Route::post('list-shipping-methods', [CartMobileController::class, 'ListShippingmethods']);
Route::post('get-shipping-package-price', [CartMobileController::class, 'getShippingPackagePrice']);

Route::any('/shoppingcart', 'App\Http\Controllers\CartMobileController@shoppingcart');
Route::match(['get', 'post'], '/addtoshoppingcart', 'App\Http\Controllers\CartMobileController@addtoshoppingcart');
Route::match(['get', 'post'], '/clearshoppingcart', 'App\Http\Controllers\CartMobileController@clearshoppingcart');
Route::match(['get', 'post'], '/clearcustomershoppingcart', 'App\Http\Controllers\CartMobileController@clearcustomershoppingcart');
Route::get('/removeshoppingcartitem', 'App\Http\Controllers\CartMobileController@removeshoppingcartitem');
Route::match(['get', 'post'], '/updateshoppingcart', 'App\Http\Controllers\CartMobileController@updateshoppingcart');
Route::get('/shippingmethods', 'App\Http\Controllers\CartMobileController@shippingmethods');
Route::get('/paymethods', 'App\Http\Controllers\CartMobileController@paymethods');
Route::post('/createorder', 'App\Http\Controllers\CartMobileController@createorder');
Route::post('createorder-two', 'App\Http\Controllers\CartMobileController@createorderTwo');
Route::match(['get', 'post'], '/updateorder', 'App\Http\Controllers\CartMobileController@updateorder');
Route::post('/getshipandpackingprice', 'App\Http\Controllers\CartMobileController@getshipandpackingprice');

Route::match(['get', 'post'], '/hoolahcancelpayment', 'App\Http\Controllers\CartMobileController@hoolahcancelpayment');
Route::match(['get', 'post'], '/hoolahsuccess', 'App\Http\Controllers\CartMobileController@hoolahsuccess');
Route::match(['get', 'post'], '/paypalsuccess', 'App\Http\Controllers\CartMobileController@paypalsuccess');
Route::match(['get', 'post'], '/discountcoupon', 'App\Http\Controllers\CartMobileController@discountcoupon');

Route::match(['get', 'post'], '/successorder', 'App\Http\Controllers\CartMobileController@successorder');
Route::match(['get', 'post'], '/cancelorder', 'App\Http\Controllers\CartMobileController@cancelorder');
Route::match(['get', 'post'], '/paypalpayment', 'App\Http\Controllers\CartMobileController@paypalpayment');
Route::match(['get', 'post'], '/stripepayment', 'App\Http\Controllers\CartMobileController@stripepayment');

Route::post('ios-create-payment-intend', 'App\Http\Controllers\CartMobileController@iOSCreateStripePaymentIntend');

Route::post('/createquotation', 'App\Http\Controllers\CartMobileController@createquotation');
Route::match(['get', 'post'], '/quotationlist', 'App\Http\Controllers\CustomerMobileController@quotationlist');

Route::match(['get', 'post'], '/makerepayment', 'App\Http\Controllers\CartMobileController@makerepayment');

Route::post('products/category-wise', 'App\Http\Controllers\ProductMobileController@listProductsByCategoryWise');
Route::post('products/brand-wise', 'App\Http\Controllers\ProductMobileController@listProductsByBrandWise');
Route::get('faq', 'App\Http\Controllers\CommonMobileController@listFaqs');
Route::get('new-arrivals', 'App\Http\Controllers\ProductMobileController@newArrivals');
Route::post('new-arrivals-names', 'App\Http\Controllers\ProductMobileController@newArrivalsNames');
Route::post('privacy-policy', 'App\Http\Controllers\CommonMobileController@listPrivacyPolicy');
// Test email
Route::post('send-test-email', 'App\Http\Controllers\CommonMobileController@sendTestEmail');

Route::post('request-account-deactivate', 'App\Http\Controllers\CustomerMobileController@requestAccountDeactivate');
Route::post('verify-account-deactivate', 'App\Http\Controllers\CustomerMobileController@verifyAccountDeactivate');

Route::post('create_category', 'App\Http\Controllers\ApiController@createCategory');
Route::post('create_sub_category', 'App\Http\Controllers\ApiController@createSubCategory');

/* Mobile App - End */
