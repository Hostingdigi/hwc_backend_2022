<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
 */

/*Route::get('/', function () {
return view('welcome');
});*/

//Route::resource('/', 'App\Http\Controllers\HomeController');

//Auth::routes();

Route::resource('/', 'App\Http\Controllers\HomeController');
Route::match(['get', 'post'], '/newslettersubscribe', 'App\Http\Controllers\HomeController@newslettersubscribe');
Route::get('/unsubscribe/{email}', ['as' => 'unsubscribe', 'uses' => 'App\Http\Controllers\HomeController@unsubscribe']);
Route::match(['get', 'post'], '/contactus', 'App\Http\Controllers\HomeController@contactus');
Route::match(['get', 'post'], '/submitfeedback', 'App\Http\Controllers\HomeController@submitfeedback');

Route::resource('/customer', 'App\Http\Controllers\CustomerController');
Route::post('/chkcustomerexist', 'App\Http\Controllers\CustomerController@chkcustomerexist');

Route::get('/login', ['as' => 'login', 'uses' => 'App\Http\Controllers\CustomerController@login']);
Route::match(['get', 'post'], '/logincheck', 'App\Http\Controllers\CustomerController@logincheck');
Route::get('/logout', ['as' => 'logout', 'uses' => 'App\Http\Controllers\CustomerController@logout']);

Route::get('/register', ['as' => 'register', 'uses' => 'App\Http\Controllers\CustomerController@register']);
Route::get('/forgotpassword', ['as' => 'forgotpassword', 'uses' => 'App\Http\Controllers\CustomerController@forgotpassword']);
Route::match(['get', 'post'], '/sendforgotemail', 'App\Http\Controllers\CustomerController@sendforgotemail');
Route::get('/resetpassword/{username}', ['as' => 'resetpassword', 'uses' => 'App\Http\Controllers\CustomerController@resetpassword']);
Route::post('/updateresetpassword', 'App\Http\Controllers\CustomerController@updateresetpassword');

Route::post('/customer/updatepersonalinfo', 'App\Http\Controllers\CustomerController@updatepersonalinfo');
Route::post('/customer/updateprofile', 'App\Http\Controllers\CustomerController@updateprofile');
Route::post('/customer/updatepassword', 'App\Http\Controllers\CustomerController@updatepassword');

Route::get('/customer/myorders/{orderid}', 'App\Http\Controllers\CustomerController@myorders');
Route::match(['get', 'post'], '/addtofavorite', 'App\Http\Controllers\CustomerController@addtofavorite');
Route::get('/customer/favouriteproducts', 'App\Http\Controllers\CustomerController@favouriteproducts');
Route::get('/removefavproduct/{id}', 'App\Http\Controllers\CustomerController@removefavproduct');
Route::get('/orderdetails/{orderid}', 'App\Http\Controllers\CustomerController@orderdetails');
Route::get('/quotationdetails/{orderid}', 'App\Http\Controllers\CustomerController@quotationdetails');
Route::get('/approvequotation/{orderid}', 'App\Http\Controllers\CustomerController@approvequotation');
Route::get('/downloadquotation/{orderid}', 'App\Http\Controllers\CustomerController@downloadquotation');
Route::match(['get', 'post'], '/trackorder/{orderid}/{trackingno}', 'App\Http\Controllers\CustomerController@trackorder');
Route::get('/orderdeliveryinfo/{orderid}', 'App\Http\Controllers\CustomerController@orderdeliveryinfo');

Route::get('/encryptpassword', 'App\Http\Controllers\CustomerController@encryptpassword');

Route::get('/category/{category}', 'App\Http\Controllers\ProductController@index');
Route::get('/type/{category}', 'App\Http\Controllers\ProductController@types');
Route::get('/types', 'App\Http\Controllers\ProductController@alltypes');

Route::get('/prod/{product}', 'App\Http\Controllers\ProductController@productdetails');

Route::get('/promotions', ['as' => 'promotions', 'uses' => 'App\Http\Controllers\ProductController@promotions']);
Route::get('/new-arrival', ['as' => 'newarrivals', 'uses' => 'App\Http\Controllers\ProductController@newarrivals']);

Route::match(['get', 'post'], '/brands', 'App\Http\Controllers\ProductController@brands');
Route::match(['get', 'post'], '/brand/{brand}', 'App\Http\Controllers\ProductController@branditems');
Route::post('/setOptionPrice', 'App\Http\Controllers\ProductController@setOptionPrice');
Route::match(['get', 'post'], '/search', ['as' => 'search', 'uses' => 'App\Http\Controllers\ProductController@search']);
Route::post('/productrating', 'App\Http\Controllers\ProductController@productrating');
Route::post('/enquiryemailus', 'App\Http\Controllers\ProductController@enquiryemailus');
Route::post('/getproductslist', 'App\Http\Controllers\ProductController@getproductslist');
Route::post('/gettypeslist', 'App\Http\Controllers\ProductController@gettypeslist');
Route::post('/submitqa', 'App\Http\Controllers\ProductController@submitqa');
Route::get('/updateLatestProducts/{tablename}', 'App\Http\Controllers\ProductController@updateLatestProducts');

Route::resource('/cart', 'App\Http\Controllers\CartController');
Route::match(['get', 'post'], '/addtocart', 'App\Http\Controllers\CartController@addtocart');
Route::get('/clearcart', 'App\Http\Controllers\CartController@clearcart');
Route::get('/addtocartstatus', 'App\Http\Controllers\CartController@addtocartstatus');
Route::get('/removecartitem/{key}/{productid}', 'App\Http\Controllers\CartController@removecartitem');
Route::post('/updatecart', 'App\Http\Controllers\CartController@updatecart');
Route::get('/checkout', 'App\Http\Controllers\CartController@checkout');
Route::match(['get', 'post'], '/placeorder', 'App\Http\Controllers\CartController@placeorder');
Route::match(['get', 'post'], '/processpayment', 'App\Http\Controllers\CartController@processpayment');
Route::match(['get', 'post'], '/paymentform', 'App\Http\Controllers\CartController@paymentform');
Route::match(['get', 'post'], '/getdeliverymethods', 'App\Http\Controllers\CartController@getdeliverymethods');
Route::match(['get', 'post'], '/updatecartqty', 'App\Http\Controllers\CartController@updatecartqty');
Route::match(['get', 'post'], '/quotation', 'App\Http\Controllers\CartController@quotation');
Route::get('/invoice/{orderid}', 'App\Http\Controllers\CartController@invoice');
Route::get('/makepayment/{orderid}', 'App\Http\Controllers\CartController@makepayment');
Route::post('/applycouponcode', 'App\Http\Controllers\CartController@applycouponcode');
Route::get('/cancelcoupon', 'App\Http\Controllers\CartController@cancelcoupon');

Route::match(['get', 'post'], '/stripe', 'App\Http\Controllers\PaymentController@stripe');
Route::match(['get', 'post'], '/stripePaymentProcess', 'App\Http\Controllers\PaymentController@stripePaymentProcess');
Route::match(['get', 'post'], '/success', 'App\Http\Controllers\PaymentController@success');
Route::match(['get', 'post'], '/hoolah', 'App\Http\Controllers\PaymentController@hoolah');
Route::match(['get', 'post'], '/paypal', 'App\Http\Controllers\PaymentController@paypal');
Route::match(['get', 'post'], '/atomecallback', 'App\Http\Controllers\PaymentController@atomecallback');
Route::match(['get', 'post'], '/atome', 'App\Http\Controllers\PaymentController@atome');
Route::match(['get', 'post'], '/grabpay', 'App\Http\Controllers\PaymentController@grabpay');
Route::get('/cancelpayment', 'App\Http\Controllers\PaymentController@cancelpayment');
Route::match(['get', 'post'], '/grabpaywebhook', 'App\Http\Controllers\PaymentController@grabpaywebhook');

// Mobile App - Start

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

Route::get('/shoppingcart', 'App\Http\Controllers\CartMobileController@shoppingcart');
Route::match(['get', 'post'], '/addtoshoppingcart', 'App\Http\Controllers\CartMobileController@addtoshoppingcart');
Route::get('/clearshoppingcart', 'App\Http\Controllers\CartMobileController@clearshoppingcart');
Route::get('/removeshoppingcartitem', 'App\Http\Controllers\CartMobileController@removeshoppingcartitem');
Route::match(['get', 'post'], '/updateshoppingcart', 'App\Http\Controllers\CartMobileController@updateshoppingcart');
Route::get('/shippingmethods', 'App\Http\Controllers\CartMobileController@shippingmethods');
Route::get('/paymethods', 'App\Http\Controllers\CartMobileController@paymethods');
Route::post('/createorder', 'App\Http\Controllers\CartMobileController@createorder');
Route::match(['get', 'post'], '/updateorder', 'App\Http\Controllers\CartMobileController@updateorder');
Route::post('/getshipandpackingprice', 'App\Http\Controllers\CartMobileController@getshipandpackingprice');

Route::match(['get', 'post'], '/hoolahcancelpayment', 'App\Http\Controllers\CartMobileController@hoolahcancelpayment');
Route::match(['get', 'post'], '/hoolahsuccess', 'App\Http\Controllers\CartMobileController@hoolahsuccess');
Route::match(['get', 'post'], '/paypalsuccess', 'App\Http\Controllers\CartMobileController@paypalsuccess');
Route::match(['get', 'post'], '/discountcoupon', 'App\Http\Controllers\CartMobileController@discountcoupon');

Route::match(['get', 'post'], '/successorder', 'App\Http\Controllers\CartMobileController@successorder');
Route::match(['get', 'post'], '/cancelorder', 'App\Http\Controllers\CartMobileController@cancelorder');

// Route::get('prod_price_update', 'App\Http\Controllers\HomeController@prodPriceUpdate');
// Route::get('prod_option_price_update', 'App\Http\Controllers\HomeController@prodOptionPriceUpdate');

//Mobile App - End

//Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');

Route::prefix('/admin')->group(function () {

//Route::get('/', 'App\Http\Controllers\Auth\AdminLoginController@showLoginForm')->name('admin.login');

//Route::post('/login', 'App\Http\Controllers\Auth\AdminLoginController@login')->name('admin.login.submit');

    Route::resource('/', 'App\Http\Controllers\AdminController');
    Route::post('/verifylogin', 'App\Http\Controllers\AdminController@verifylogin');
    Route::get('/dashboard', 'App\Http\Controllers\AdminController@dashboard');
    Route::get('/logout', 'App\Http\Controllers\AdminController@logout');

    Route::resource('/country', 'App\Http\Controllers\Admin\CountryController');
    Route::get('/country/{id}/destroy', 'App\Http\Controllers\Admin\CountryController@destroy');
    Route::get('/country/{id}/{status}/updatestatus', 'App\Http\Controllers\Admin\CountryController@updatestatus');

    Route::resource('/category', 'App\Http\Controllers\Admin\CategoryController');
    Route::get('/category/{id}/destroy', 'App\Http\Controllers\Admin\CategoryController@destroy');
    Route::get('/category/{id}/{status}/updatestatus', 'App\Http\Controllers\Admin\CategoryController@updatestatus');
    Route::post('/category/bulkupdate', 'App\Http\Controllers\Admin\CategoryController@bulkupdate');

    Route::resource('/brands', 'App\Http\Controllers\Admin\BrandController');
    Route::get('/brands/{id}/destroy', 'App\Http\Controllers\Admin\BrandController@destroy');
    Route::get('/brands/{id}/{status}/updatestatus', 'App\Http\Controllers\Admin\BrandController@updatestatus');
    Route::post('/brands/bulkupdate', 'App\Http\Controllers\Admin\BrandController@bulkupdate');
    Route::get('/brands/{brandid}/groupprice', 'App\Http\Controllers\Admin\BrandController@groupprice');
    Route::post('/brands/addgroupprice', 'App\Http\Controllers\Admin\BrandController@addgroupprice');

    Route::get('/brands/groupprice/{id}/{brandid}/editgroupprice', 'App\Http\Controllers\Admin\BrandController@editgroupprice');
    Route::post('/brands/updategroupprice', 'App\Http\Controllers\Admin\BrandController@updategroupprice');
    Route::get('/brands/groupprice/{id}/{brandid}/destroygroupprice', 'App\Http\Controllers\Admin\BrandController@destroygroupprice');

    Route::resource('/products', 'App\Http\Controllers\Admin\ProductController');
    Route::get('/products/{id}/destroy', 'App\Http\Controllers\Admin\ProductController@destroy');
    Route::get('/products/{id}/{status}/updatestatus', 'App\Http\Controllers\Admin\ProductController@updatestatus');
    Route::post('/products/bulkupdate', 'App\Http\Controllers\Admin\ProductController@bulkupdate');

    Route::get('/products/{id}/quantity', 'App\Http\Controllers\Admin\ProductController@quantity');
    Route::post('/products/updatequantity', 'App\Http\Controllers\Admin\ProductController@updatequantity');
    Route::get('/products/{id}/gallery', 'App\Http\Controllers\Admin\ProductController@gallery');
    Route::post('/products/addgallery', 'App\Http\Controllers\Admin\ProductController@addgallery');
    Route::get('/products/{id}/{status}/updategallerystatus', 'App\Http\Controllers\Admin\ProductController@updategallerystatus');
    Route::get('/products/{id}/productoptions', 'App\Http\Controllers\Admin\ProductController@productoptions');
    Route::post('/products/addoptions', 'App\Http\Controllers\Admin\ProductController@addoptions');
    Route::get('/products/options/{id}/{prodid}/editoptions', 'App\Http\Controllers\Admin\ProductController@editproductoptions');
    Route::post('/products/updateoptions', 'App\Http\Controllers\Admin\ProductController@updateoptions');
    Route::get('/products/options/{id}/{status}/updateoptionstatus', 'App\Http\Controllers\Admin\ProductController@updateoptionstatus');
    Route::post('/products/options/bulkoptionupdate', 'App\Http\Controllers\Admin\ProductController@bulkoptionupdate');

    Route::get('/products/{id}/groupprice', 'App\Http\Controllers\Admin\ProductController@groupprice');
    Route::post('/products/addgroupprice', 'App\Http\Controllers\Admin\ProductController@addgroupprice');
    Route::get('/products/groupprice/{id}/{productid}/editgroupprice', 'App\Http\Controllers\Admin\ProductController@editgroupprice');
    Route::post('/products/updategroupprice', 'App\Http\Controllers\Admin\ProductController@updategroupprice');
    Route::get('/products/groupprice/{id}/{productid}/destroygroupprice', 'App\Http\Controllers\Admin\ProductController@destroygroupprice');
    Route::get('/exportproducts', 'App\Http\Controllers\Admin\ProductController@exportproducts');
    Route::get('/clearproductsearch', 'App\Http\Controllers\Admin\ProductController@clearproductsearch');

    Route::resource('/subscriber', 'App\Http\Controllers\Admin\SubscriberController');
    Route::get('/subscriber/{id}/destroy', 'App\Http\Controllers\Admin\SubscriberController@destroy');
    Route::get('/subscriber/{id}/{status}/updatestatus', 'App\Http\Controllers\Admin\SubscriberController@updatestatus');
    Route::post('/subscriber/bulkupdate', 'App\Http\Controllers\Admin\SubscriberController@bulkupdate');

    Route::resource('/local_shipping_methods', 'App\Http\Controllers\Admin\LocalShippingController');
    Route::get('/local_shipping_methods/{id}/destroy', 'App\Http\Controllers\Admin\LocalShippingController@destroy');
    Route::get('/local_shipping_methods/{id}/{status}/updatestatus', 'App\Http\Controllers\Admin\LocalShippingController@updatestatus');

    Route::resource('/international_shipping_methods', 'App\Http\Controllers\Admin\InternationalShippingController');
    Route::get('/international_shipping_methods/{id}/destroy', 'App\Http\Controllers\Admin\InternationalShippingController@destroy');
    Route::get('/international_shipping_methods/{id}/{status}/updatestatus', 'App\Http\Controllers\Admin\InternationalShippingController@updatestatus');

    Route::resource('/static_pages', 'App\Http\Controllers\Admin\PageContentController');
    Route::get('/static_pages/{id}/destroy', 'App\Http\Controllers\Admin\PageContentController@destroy');
    Route::get('/static_pages/{id}/{status}/updatestatus', 'App\Http\Controllers\Admin\PageContentController@updatestatus');
    Route::post('/static_pages/bulkupdate', 'App\Http\Controllers\Admin\PageContentController@bulkupdate');

    Route::resource('/promotions', 'App\Http\Controllers\Admin\PromotionsController');
    Route::get('/promotions/{id}/destroy', 'App\Http\Controllers\Admin\PromotionsController@destroy');
    Route::get('/promotions/{id}/{status}/updatestatus', 'App\Http\Controllers\Admin\PromotionsController@updatestatus');
    Route::post('/promotions/bulkupdate', 'App\Http\Controllers\Admin\PromotionsController@bulkupdate');

    Route::resource('/banner_ads', 'App\Http\Controllers\Admin\BanneradsController');
    Route::get('/banner_ads/{id}/destroy', 'App\Http\Controllers\Admin\BanneradsController@destroy');
    Route::get('/banner_ads/{id}/{status}/updatestatus', 'App\Http\Controllers\Admin\BanneradsController@updatestatus');
    Route::post('/banner_ads/bulkupdate', 'App\Http\Controllers\Admin\BanneradsController@bulkupdate');

    Route::resource('/customergroup', 'App\Http\Controllers\Admin\CustomerGroupController');
    Route::get('/customergroup/{id}/destroy', 'App\Http\Controllers\Admin\CustomerGroupController@destroy');
    Route::get('/customergroup/{id}/{status}/updatestatus', 'App\Http\Controllers\Admin\CustomerGroupController@updatestatus');

    Route::resource('/emailtemplate', 'App\Http\Controllers\Admin\EmailTemplateController');
    Route::get('/emailtemplate/{id}/destroy', 'App\Http\Controllers\Admin\EmailTemplateController@destroy');
    Route::get('/emailtemplate/{id}/{status}/updatestatus', 'App\Http\Controllers\Admin\EmailTemplateController@updatestatus');

    Route::resource('/customer', 'App\Http\Controllers\Admin\CustomerController');
    Route::get('/customer/{id}/destroy', 'App\Http\Controllers\Admin\CustomerController@destroy');
    Route::get('/customer/{id}/{status}/updatestatus', 'App\Http\Controllers\Admin\CustomerController@updatestatus');

    Route::resource('/orders', 'App\Http\Controllers\Admin\OrderController');
    Route::get('/orders/{id}/destroy', 'App\Http\Controllers\Admin\OrderController@destroy');
    Route::get('/orders/{id}/{status}/updatestatus', 'App\Http\Controllers\Admin\OrderController@updatestatus');
    Route::get('/archives', 'App\Http\Controllers\Admin\OrderController@deletedorders');
    Route::post('/orders/bulkaction', 'App\Http\Controllers\Admin\OrderController@bulkaction');
    Route::get('/orders/{id}/selfcollect', 'App\Http\Controllers\Admin\OrderController@selfcollect');
    Route::post('/orders/updateselfcollect', 'App\Http\Controllers\Admin\OrderController@updateselfcollect');
    Route::get('/orders/{id}/singledelivery', 'App\Http\Controllers\Admin\OrderController@singledelivery');
    Route::post('/orders/updatesingledelivery', 'App\Http\Controllers\Admin\OrderController@updatesingledelivery');
    Route::get('/orders/{id}/multipledelivery', 'App\Http\Controllers\Admin\OrderController@multipledelivery');
    Route::post('/orders/updatemultipledelivery', 'App\Http\Controllers\Admin\OrderController@updatemultipledelivery');
    Route::get('/orders/{id}/{infoid}/deliveryinfo', 'App\Http\Controllers\Admin\OrderController@deliveryinfo');
    Route::post('/orders/updatedeliveryinfo', 'App\Http\Controllers\Admin\OrderController@updatedeliveryinfo');
    Route::get('/orders/{id}/deliverytrack', 'App\Http\Controllers\Admin\OrderController@deliverytrack');
    Route::get('/orders/{id}/trackorder', 'App\Http\Controllers\Admin\OrderController@trackorder');
    Route::get('/exportorders', 'App\Http\Controllers\Admin\OrderController@exportorders');
    Route::get('/archiveorder/{orderid}', 'App\Http\Controllers\Admin\OrderController@archiveorder');
    Route::get('/pendingorders', 'App\Http\Controllers\Admin\OrderController@pendingorders');
    Route::post('/pendingorders/pendingbulkaction', 'App\Http\Controllers\Admin\OrderController@pendingbulkaction');
    Route::get('/exportpendingorders', 'App\Http\Controllers\Admin\OrderController@exportpendingorders');
    Route::post('remove-order-items', 'App\Http\Controllers\Admin\OrderController@removeOrderItems');

    Route::resource('/request_for_quote', 'App\Http\Controllers\Admin\QuoteController');
    Route::get('/request_for_quote/{id}/destroy', 'App\Http\Controllers\Admin\QuoteController@destroy');
    Route::get('/request_for_quote/{id}/{status}/updatestatus', 'App\Http\Controllers\Admin\QuoteController@updatestatus');

    Route::resource('/manageadmin', 'App\Http\Controllers\Admin\ManageAdminController');
    Route::get('/manageadmin/{id}/destroy', 'App\Http\Controllers\Admin\ManageAdminController@destroy');

    Route::resource('/passwordchange', 'App\Http\Controllers\Admin\PasswordChangeController');

    Route::resource('/subadmin_settings', 'App\Http\Controllers\Admin\SettingsController');
    Route::post('/subadmin_settings', 'App\Http\Controllers\Admin\SettingsController@update');

    Route::resource('/payment_settings', 'App\Http\Controllers\Admin\PaymentSettingsController');

    Route::resource('/shipping_methods', 'App\Http\Controllers\Admin\ShippingMethodController');
    Route::get('/shipping_methods/{id}/destroy', 'App\Http\Controllers\Admin\ShippingMethodController@destroy');
    Route::get('/shipping_methods/{id}/{status}/updatestatus', 'App\Http\Controllers\Admin\ShippingMethodController@updatestatus');

    Route::resource('/paymentmethods', 'App\Http\Controllers\Admin\PaymentMethodsController');
    Route::get('/paymentmethods/{id}/destroy', 'App\Http\Controllers\Admin\PaymentMethodsController@destroy');
    Route::get('/paymentmethods/{id}/{status}/updatestatus', 'App\Http\Controllers\Admin\PaymentMethodsController@updatestatus');

    Route::resource('/local_shipping_method', 'App\Http\Controllers\Admin\Local_ShippingController');
    Route::get('/local_shipping_method/{id}/destroy', 'App\Http\Controllers\Admin\Local_ShippingController@destroy');

    Route::resource('/page_content', 'App\Http\Controllers\Admin\Page_ContentController');

    Route::resource('/banner_master', 'App\Http\Controllers\Admin\MastheadImageController');
    Route::get('/banner_master/{id}/destroy', 'App\Http\Controllers\Admin\MastheadImageController@destroy');
    Route::get('/banner_master/{id}/{status}/updatestatus', 'App\Http\Controllers\Admin\MastheadImageController@updatestatus');
    Route::post('/banner_master/bulkupdate', 'App\Http\Controllers\Admin\MastheadImageController@bulkupdate');

    Route::resource('/home_banner', 'App\Http\Controllers\Admin\Home_BannerController');
    Route::resource('/advertising_banner', 'App\Http\Controllers\Admin\Advertising_BannerController');

    Route::resource('/brand', 'App\Http\Controllers\Admin\BrandController');
    Route::get('/brand/{id}/destroy', 'App\Http\Controllers\Admin\BrandController@destroy');

    Route::resource('/product', 'App\Http\Controllers\Admin\ProductController');
    Route::get('/product/{id}/destroy', 'App\Http\Controllers\Admin\ProductController@destroy');

    Route::resource('/producttag', 'App\Http\Controllers\Admin\ProductTagController');
    Route::get('/producttag/{id}/destroy', 'App\Http\Controllers\Admin\ProductTagController@destroy');
    Route::get('/producttag/{id}/{status}/updatestatus', 'App\Http\Controllers\Admin\ProductTagController@updatestatus');

    Route::resource('/emailtemplate', 'App\Http\Controllers\Admin\EmailTemplateController');
    Route::get('/emailtemplate/{id}/destroy', 'App\Http\Controllers\Admin\EmailTemplateController@destroy');
    Route::get('/emailtemplate/{id}/{status}/updatestatus', 'App\Http\Controllers\Admin\EmailTemplateController@updatestatus');

    Route::resource('/menu', 'App\Http\Controllers\Admin\MenuController');
    Route::get('/menu/{id}/destroy', 'App\Http\Controllers\Admin\MenuController@destroy');
    Route::post('/menu/bulkupdate', 'App\Http\Controllers\Admin\MenuController@bulkupdate');

    Route::resource('/announcement', 'App\Http\Controllers\Admin\AnnouncementController');
    Route::get('/announcement/{id}/destroy', 'App\Http\Controllers\Admin\AnnouncementController@destroy');
    Route::get('/announcement/{id}/{status}/updatestatus', 'App\Http\Controllers\Admin\AnnouncementController@updatestatus');

    Route::resource('/couponcode', 'App\Http\Controllers\Admin\CouponcodeController');
    Route::get('/couponcode/{id}/destroy', 'App\Http\Controllers\Admin\CouponcodeController@destroy');
    Route::get('/couponcode/{id}/{status}/updatestatus', 'App\Http\Controllers\Admin\CouponcodeController@updatestatus');

    Route::resource('/socialmedia', 'App\Http\Controllers\Admin\SocialmediaController');

    Route::resource('/smstemplate', 'App\Http\Controllers\Admin\SMSTemplateController');
    Route::get('/smstemplate/{id}/destroy', 'App\Http\Controllers\Admin\SMSTemplateController@destroy');
    Route::get('/smstemplate/{id}/{status}/updatestatus', 'App\Http\Controllers\Admin\SMSTemplateController@updatestatus');

    Route::resource('/roleandrights', 'App\Http\Controllers\Admin\RoleRightsController');
    Route::get('/roleandrights/{id}/destroy', 'App\Http\Controllers\Admin\RoleRightsController@destroy');

});

Route::get('/{urlkey}', 'App\Http\Controllers\HomeController@staticpages');
