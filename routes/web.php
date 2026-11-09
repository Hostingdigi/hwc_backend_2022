<?php

use App\Http\Controllers\Admin;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\CartMobileController;
use App\Http\Controllers\CommonMobileController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\CustomerMobileController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ProductMobileController;
use App\Http\Controllers\PunchoutController;
use App\Http\Middleware\CheckStatus;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Loaded with the "web" middleware group (see bootstrap/app.php).
|
 */

Route::middleware([CheckStatus::class])->group(function(){
    
    Route::post('/punchout/setup', [PunchoutController::class, 'setup']);
    Route::get('/punchout/login', [PunchoutController::class, 'login']);
    Route::get('/punchout/checkout', [PunchoutController::class, 'checkout']);
    Route::get('/punchout/final_cxml', [PunchoutController::class, 'checkoutTest']);

    Route::get('/', [HomeController::class, 'index']);
    
    Route::match(['get', 'post'], '/newslettersubscribe', [HomeController::class, 'newslettersubscribe']);
    Route::get('/unsubscribe/{email}', [HomeController::class, 'unsubscribe'])->name('unsubscribe');
    
    Route::match(['get', 'post'], 'contactus', [HomeController::class, 'contactus']);
    Route::match(['get', 'post'], 'submitfeedback', [HomeController::class, 'submitfeedback']);
    
    Route::resource('/customer', CustomerController::class)->except(['create', 'edit']);
    Route::post('/chkcustomerexist', [CustomerController::class, 'chkcustomerexist']);
    
    Route::get('/login', [CustomerController::class, 'login'])->name('login');
    Route::post('/logincheck', [CustomerController::class, 'logincheck'])->name('logincheck');
    Route::get('/logout', [CustomerController::class, 'logout'])->name('logout');
    
    Route::get('/register', [CustomerController::class, 'register'])->name('register');
    Route::get('/forgotpassword', [CustomerController::class, 'forgotpassword'])->name('forgotpassword');
    Route::match(['get', 'post'], '/sendforgotemail', [CustomerController::class, 'sendforgotemail']);
    Route::get('/resetpassword/{token}', [CustomerController::class, 'resetpassword'])->name('resetpassword');
    Route::post('/updateresetpassword', [CustomerController::class, 'updateresetpassword']);
    
    Route::post('/customer/updatepersonalinfo', [CustomerController::class, 'updatepersonalinfo']);
    Route::post('/customer/updateprofile', [CustomerController::class, 'updateprofile']);
    Route::post('/customer/updatepassword', [CustomerController::class, 'updatepassword']);
    
    Route::match(['get', 'post'], '/addtofavorite', [CustomerController::class, 'addtofavorite']);
    Route::get('/removefavproduct/{id}', [CustomerController::class, 'removefavproduct']);
    Route::get('/orderdetails/{orderid}', [CustomerController::class, 'orderdetails']);
    Route::get('/quotationdetails/{orderid}', [CustomerController::class, 'quotationdetails']);
    Route::get('/approvequotation/{orderid}', [CustomerController::class, 'approvequotation']);
    Route::get('/downloadquotation/{orderid}', [CustomerController::class, 'downloadquotation']);
    Route::match(['get', 'post'], '/trackorder/{orderid}/{trackingno}', [CustomerController::class, 'trackorder']);
    Route::get('/orderdeliveryinfo/{orderid}', [CustomerController::class, 'orderdeliveryinfo']);
    
    Route::get('/encryptpassword', [CustomerController::class, 'encryptpassword']);
    
    Route::get('category/{category}', [ProductController::class, 'productsByCategory']);
    Route::get('/type/{category}', [ProductController::class, 'types']);
    Route::get('/types', [ProductController::class, 'alltypes']);
    
    Route::get('/prod/{product}', [ProductController::class, 'productdetails']);
    
    Route::get('promotions', [ProductController::class, 'promotions'])->name('promotions');
    Route::get('new-arrival', [ProductController::class, 'newarrivals'])->name('newarrivals');
    
    Route::match(['get', 'post'], '/brands', [ProductController::class, 'brands']);
    Route::match(['get', 'post'], 'brand/{brand}', [ProductController::class, 'branditems']);
    Route::post('/setOptionPrice', [ProductController::class, 'setOptionPrice']);
    Route::match(['get', 'post'], '/search', [ProductController::class, 'search'])->name('search');
    Route::post('/productrating', [ProductController::class, 'productrating']);
    Route::post('/enquiryemailus', [ProductController::class, 'enquiryemailus']);
    Route::post('/getproductslist', [ProductController::class, 'getproductslist']);
    Route::post('/gettypeslist', [ProductController::class, 'gettypeslist']);
    Route::post('/submitqa', [ProductController::class, 'submitqa']);
    
    Route::resource('/cart', CartController::class)->only(['index']);
    Route::match(['get', 'post'], '/addtocart', [CartController::class, 'addtocart']);
    Route::get('/clearcart', [CartController::class, 'clearcart']);
    Route::get('/addtocartstatus', [CartController::class, 'addtocartstatus']);
    Route::get('/removecartitem/{key}/{productid}', [CartController::class, 'removecartitem']);
    Route::post('/updatecart', [CartController::class, 'updatecart']);
    Route::get('/checkout', [CartController::class, 'checkout']);
    Route::match(['get', 'post'], '/placeorder', [CartController::class, 'placeorder']);
    Route::match(['get', 'post'], '/paymentform', [CartController::class, 'paymentform']);
    Route::match(['get', 'post'], '/getdeliverymethods', [CartController::class, 'getdeliverymethods']);
    Route::match(['get', 'post'], '/updatecartqty', [CartController::class, 'updatecartqty']);
    Route::match(['get', 'post'], '/quotation', [CartController::class, 'quotation']);
    Route::get('/invoice/{orderid}', [CartController::class, 'invoice']);
    Route::get('/makepayment/{orderid}', [CartController::class, 'makepayment']);
    Route::post('/applycouponcode', [CartController::class, 'applycouponcode']);
    Route::get('/cancelcoupon', [CartController::class, 'cancelcoupon']);
    
    Route::match(['get', 'post'], '/stripe', [PaymentController::class, 'stripe']);
    Route::match(['get', 'post'], '/stripePaymentProcess', [PaymentController::class, 'stripePaymentProcess']);
    Route::match(['get', 'post'], '/success', [PaymentController::class, 'success']);
    Route::match(['get', 'post'], '/paypal', [PaymentController::class, 'paypal']);
    Route::match(['get', 'post'], '/atomecallback', [PaymentController::class, 'atomecallback']);
    Route::match(['get', 'post'], '/atome', [PaymentController::class, 'atome']);
    Route::match(['get', 'post'], '/grabpay', [PaymentController::class, 'grabpay']);
    Route::get('/cancelpayment', [PaymentController::class, 'cancelpayment']);
    Route::match(['get', 'post'], '/grabpaywebhook', [PaymentController::class, 'grabpaywebhook']);
    Route::match(['get', 'post'], '/grabpay-complete', [PaymentController::class, 'grabpayComplete']);

});
// Mobile App - Start
// Original mobile app, replaced by /api/v2 (see App\Http\Middleware\LegacyMobileApi)
// Still answers when LEGACY_MOBILE_API=off, so the old app can prompt for the update
Route::get('/mobileversions', [CommonMobileController::class, 'mobileversions'])->middleware('legacy.mobile:always');

Route::middleware('legacy.mobile')->group(function () {
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

    Route::get('/shoppingcart', [CartMobileController::class, 'shoppingcart']);
    Route::match(['get', 'post'], '/addtoshoppingcart', [CartMobileController::class, 'addtoshoppingcart']);
    Route::get('/clearshoppingcart', [CartMobileController::class, 'clearshoppingcart']);
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
});

// Route::get('prod_price_update', [HomeController::class, 'prodPriceUpdate']);
// Route::get('prod_option_price_update', [HomeController::class, 'prodOptionPriceUpdate']);

//Mobile App - End

Route::prefix('/admin')->group(function () {

//Route::get('/', 'App\Http\Controllers\Auth\AdminLoginController@showLoginForm')->name('admin.login');

//Route::post('/login', 'App\Http\Controllers\Auth\AdminLoginController@login')->name('admin.login.submit');

    Route::get('/', [AdminController::class, 'index']);
    Route::post('/verifylogin', [AdminController::class, 'verifylogin']);
    Route::get('/logout', [AdminController::class, 'logout']);

});

Route::prefix('/admin')->name('admin.')->middleware(['admin.session', 'admin.module'])->group(function () {

    Route::get('/dashboard', [AdminController::class, 'dashboard']);

    Route::resource('/country', Admin\CountryController::class);
    Route::post('/country/{id}/destroy', [Admin\CountryController::class, 'destroy']);
    Route::post('/country/{id}/{status}/updatestatus', [Admin\CountryController::class, 'updatestatus']);
    
    Route::prefix('category/shipping-charges')  
        ->group(function(){
            Route::get('/', [Admin\CategoryController::class,'shippingCharges']);
            Route::get('create', [Admin\CategoryController::class,'shippingChargesCreate']);
            Route::post('save', [Admin\CategoryController::class,'shippingChargesSave']);
            Route::get('edit/{id}', [Admin\CategoryController::class,'shippingChargesEdit']);
            Route::post('update', [Admin\CategoryController::class,'shippingChargesUpdate']);
            Route::post('delete/{id}', [Admin\CategoryController::class,'shippingChargesDelete']);
    });

    Route::resource('/category', Admin\CategoryController::class)->only(['index', 'create', 'store', 'edit', 'update', 'destroy']);
    Route::post('/category/{id}/destroy', [Admin\CategoryController::class, 'destroy']);
    Route::post('/category/{id}/{status}/updatestatus', [Admin\CategoryController::class, 'updatestatus']);
    Route::post('/category/bulkupdate', [Admin\CategoryController::class, 'bulkupdate']);

    Route::resource('/brands', Admin\BrandController::class)->only(['index', 'create', 'store', 'edit', 'update', 'destroy']);
    Route::post('/brands/{id}/destroy', [Admin\BrandController::class, 'destroy']);
    Route::post('/brands/{id}/{status}/updatestatus', [Admin\BrandController::class, 'updatestatus']);
    Route::post('/brands/bulkupdate', [Admin\BrandController::class, 'bulkupdate']);
    Route::get('/brands/{brandid}/groupprice', [Admin\BrandController::class, 'groupprice']);
    Route::post('/brands/addgroupprice', [Admin\BrandController::class, 'addgroupprice']);

    Route::get('/brands/groupprice/{id}/{brandid}/editgroupprice', [Admin\BrandController::class, 'editgroupprice']);
    Route::post('/brands/updategroupprice', [Admin\BrandController::class, 'updategroupprice']);
    Route::post('/brands/groupprice/{id}/{brandid}/destroygroupprice', [Admin\BrandController::class, 'destroygroupprice']);

    Route::resource('/products', Admin\ProductController::class)->only(['index', 'create', 'store', 'edit', 'update', 'destroy']);
    Route::post('/products/{id}/destroy', [Admin\ProductController::class, 'destroy']);
    Route::post('/products/{id}/{status}/updatestatus', [Admin\ProductController::class, 'updatestatus']);
    Route::post('/products/bulkupdate', [Admin\ProductController::class, 'bulkupdate']);

    Route::get('/products/{id}/quantity', [Admin\ProductController::class, 'quantity']);
    Route::post('/products/updatequantity', [Admin\ProductController::class, 'updatequantity']);
    Route::get('/products/{id}/gallery', [Admin\ProductController::class, 'gallery']);
    Route::post('/products/addgallery', [Admin\ProductController::class, 'addgallery']);
    Route::post('/products/{id}/{status}/updategallerystatus', [Admin\ProductController::class, 'updategallerystatus']);
    Route::get('/products/{id}/productoptions', [Admin\ProductController::class, 'productoptions']);
    Route::post('/products/addoptions', [Admin\ProductController::class, 'addoptions']);
    Route::get('/products/options/{id}/{prodid}/editoptions', [Admin\ProductController::class, 'editproductoptions']);
    Route::post('/products/updateoptions', [Admin\ProductController::class, 'updateoptions']);
    Route::post('/products/options/{id}/{status}/updateoptionstatus', [Admin\ProductController::class, 'updateoptionstatus']);
    Route::post('/products/options/bulkoptionupdate', [Admin\ProductController::class, 'bulkoptionupdate']);

    Route::get('/products/{id}/groupprice', [Admin\ProductController::class, 'groupprice']);
    Route::post('/products/addgroupprice', [Admin\ProductController::class, 'addgroupprice']);
    Route::get('/products/groupprice/{id}/{productid}/editgroupprice', [Admin\ProductController::class, 'editgroupprice']);
    Route::post('/products/updategroupprice', [Admin\ProductController::class, 'updategroupprice']);
    Route::post('/products/groupprice/{id}/{productid}/destroygroupprice', [Admin\ProductController::class, 'destroygroupprice']);
    Route::get('/exportproducts', [Admin\ProductController::class, 'exportproducts']);
    Route::get('/clearproductsearch', [Admin\ProductController::class, 'clearproductsearch']);

    Route::resource('/subscriber', Admin\SubscriberController::class);
    Route::post('/subscriber/{id}/destroy', [Admin\SubscriberController::class, 'destroy']);
    Route::post('/subscriber/{id}/{status}/updatestatus', [Admin\SubscriberController::class, 'updatestatus']);

    Route::resource('/local_shipping_methods', Admin\LocalShippingController::class)->only(['index', 'create', 'store', 'edit', 'update', 'destroy']);
    Route::post('/local_shipping_methods/{id}/destroy', [Admin\LocalShippingController::class, 'destroy']);
    Route::post('/local_shipping_methods/{id}/{status}/updatestatus', [Admin\LocalShippingController::class, 'updatestatus']);

    Route::resource('/international_shipping_methods', Admin\InternationalShippingController::class)->only(['index', 'create', 'store', 'edit', 'update', 'destroy']);
    Route::post('/international_shipping_methods/{id}/destroy', [Admin\InternationalShippingController::class, 'destroy']);
    Route::post('/international_shipping_methods/{id}/{status}/updatestatus', [Admin\InternationalShippingController::class, 'updatestatus']);

    Route::resource('/static_pages', Admin\PageContentController::class)->only(['index', 'create', 'store', 'edit', 'update', 'destroy']);
    Route::post('/static_pages/{id}/destroy', [Admin\PageContentController::class, 'destroy']);
    Route::post('/static_pages/{id}/{status}/updatestatus', [Admin\PageContentController::class, 'updatestatus']);
    Route::post('/static_pages/bulkupdate', [Admin\PageContentController::class, 'bulkupdate']);

    Route::resource('promotions', Admin\PromotionsController::class);
    Route::post('/promotions/{id}/destroy', [Admin\PromotionsController::class, 'destroy']);
    Route::post('/promotions/{id}/{status}/updatestatus', [Admin\PromotionsController::class, 'updatestatus']);
    Route::post('/promotions/bulkupdate', [Admin\PromotionsController::class, 'bulkupdate']);

    Route::resource('/banner_ads', Admin\BanneradsController::class);
    Route::post('/banner_ads/{id}/destroy', [Admin\BanneradsController::class, 'destroy']);
    Route::post('/banner_ads/{id}/{status}/updatestatus', [Admin\BanneradsController::class, 'updatestatus']);
    Route::post('/banner_ads/bulkupdate', [Admin\BanneradsController::class, 'bulkupdate']);

    Route::resource('/customergroup', Admin\CustomerGroupController::class)->only(['index', 'create', 'store', 'edit', 'update', 'destroy']);
    Route::post('/customergroup/{id}/destroy', [Admin\CustomerGroupController::class, 'destroy']);
    Route::post('/customergroup/{id}/{status}/updatestatus', [Admin\CustomerGroupController::class, 'updatestatus']);

    Route::resource('/emailtemplate', Admin\EmailTemplateController::class);
    Route::post('/emailtemplate/{id}/destroy', [Admin\EmailTemplateController::class, 'destroy']);
    Route::post('/emailtemplate/{id}/{status}/updatestatus', [Admin\EmailTemplateController::class, 'updatestatus']);

    Route::resource('/customer', Admin\CustomerController::class)->only(['index', 'create', 'store', 'edit', 'update', 'destroy']);
    Route::post('/customer/{id}/destroy', [Admin\CustomerController::class, 'destroy']);
    Route::post('/customer/{id}/{status}/updatestatus', [Admin\CustomerController::class, 'updatestatus']);

    Route::resource('/orders', Admin\OrderController::class)->only(['index', 'show', 'edit', 'destroy']);
    Route::post('/orders/{id}/destroy', [Admin\OrderController::class, 'destroy']);
    Route::get('/archives', [Admin\OrderController::class, 'deletedorders']);
    Route::post('/orders/bulkaction', [Admin\OrderController::class, 'bulkaction']);
    Route::get('/orders/{id}/selfcollect', [Admin\OrderController::class, 'selfcollect']);
    Route::post('/orders/updateselfcollect', [Admin\OrderController::class, 'updateselfcollect']);
    Route::get('/orders/{id}/singledelivery', [Admin\OrderController::class, 'singledelivery']);
    Route::post('/orders/updatesingledelivery', [Admin\OrderController::class, 'updatesingledelivery']);
    Route::get('/orders/{id}/multipledelivery', [Admin\OrderController::class, 'multipledelivery']);
    Route::post('/orders/updatemultipledelivery', [Admin\OrderController::class, 'updatemultipledelivery']);
    Route::get('/orders/{id}/{infoid}/deliveryinfo', [Admin\OrderController::class, 'deliveryinfo']);
    Route::post('/orders/updatedeliveryinfo', [Admin\OrderController::class, 'updatedeliveryinfo']);
    Route::get('/orders/{id}/deliverytrack', [Admin\OrderController::class, 'deliverytrack']);
    Route::get('/orders/{id}/trackorder', [Admin\OrderController::class, 'trackorder']);
    Route::get('/exportorders', [Admin\OrderController::class, 'exportorders']);
    Route::get('/archiveorder/{orderid}', [Admin\OrderController::class, 'archiveorder']);
    Route::get('/pendingorders', [Admin\OrderController::class, 'pendingorders']);
    Route::post('/pendingorders/pendingbulkaction', [Admin\OrderController::class, 'pendingbulkaction']);
    Route::get('/exportpendingorders', [Admin\OrderController::class, 'exportpendingorders']);
    Route::post('remove-order-items', [Admin\OrderController::class, 'removeOrderItems']);
    Route::post('fetch_otions_and_price', [Admin\OrderController::class, 'fetchOtionsAndPrice']);
    Route::post('add_more_items_to_order', [Admin\OrderController::class, 'addMoreItemsToOrder']);
    Route::post('update_order_item', [Admin\OrderController::class, 'updateOrderItem']);
    Route::get('search/prods', [Admin\OrderController::class, 'searchProd']);

    Route::resource('/manageadmin', Admin\ManageAdminController::class);
    Route::post('/manageadmin/{id}/destroy', [Admin\ManageAdminController::class, 'destroy']);

    Route::resource('/passwordchange', Admin\PasswordChangeController::class)->only(['index', 'store']);

    Route::resource('/subadmin_settings', Admin\SettingsController::class)->only(['index', 'update']);
    Route::post('/subadmin_settings', [Admin\SettingsController::class, 'update']);

    Route::resource('/payment_settings', Admin\PaymentSettingsController::class)->only(['index', 'update']);

    Route::resource('/shipping_methods', Admin\ShippingMethodController::class)->only(['index', 'create', 'store', 'edit', 'update', 'destroy']);
    Route::post('/shipping_methods/{id}/destroy', [Admin\ShippingMethodController::class, 'destroy']);
    Route::post('/shipping_methods/{id}/{status}/updatestatus', [Admin\ShippingMethodController::class, 'updatestatus']);

    Route::resource('/paymentmethods', Admin\PaymentMethodsController::class);
    Route::post('/paymentmethods/{id}/destroy', [Admin\PaymentMethodsController::class, 'destroy']);
    Route::post('/paymentmethods/{id}/{status}/updatestatus', [Admin\PaymentMethodsController::class, 'updatestatus']);

    Route::resource('/local_shipping_method', Admin\Local_ShippingController::class)->only(['index', 'create', 'store', 'edit', 'update', 'destroy']);
    Route::post('/local_shipping_method/{id}/destroy', [Admin\Local_ShippingController::class, 'destroy']);

    Route::resource('/page_content', Admin\Page_ContentController::class)->only(['index']);

    Route::resource('/banner_master', Admin\MastheadImageController::class);
    Route::post('/banner_master/{id}/destroy', [Admin\MastheadImageController::class, 'destroy']);
    Route::post('/banner_master/{id}/{status}/updatestatus', [Admin\MastheadImageController::class, 'updatestatus']);
    Route::post('/banner_master/bulkupdate', [Admin\MastheadImageController::class, 'bulkupdate']);

    Route::resource('/home_banner', Admin\Home_BannerController::class)->only(['index']);
    Route::resource('/advertising_banner', Admin\Advertising_BannerController::class)->only(['index']);

    Route::resource('/brand', Admin\BrandController::class)->only(['index', 'create', 'store', 'edit', 'update', 'destroy']);
    Route::post('/brand/{id}/destroy', [Admin\BrandController::class, 'destroy']);

    Route::resource('/product', Admin\ProductController::class)->only(['index', 'create', 'store', 'edit', 'update', 'destroy']);
    Route::post('/product/{id}/destroy', [Admin\ProductController::class, 'destroy']);

    Route::resource('/producttag', Admin\ProductTagController::class);
    Route::post('/producttag/{id}/destroy', [Admin\ProductTagController::class, 'destroy']);
    Route::post('/producttag/{id}/{status}/updatestatus', [Admin\ProductTagController::class, 'updatestatus']);

    Route::resource('/menu', Admin\MenuController::class);
    Route::post('/menu/{id}/destroy', [Admin\MenuController::class, 'destroy']);
    Route::post('/menu/bulkupdate', [Admin\MenuController::class, 'bulkupdate']);

    Route::resource('/announcement', Admin\AnnouncementController::class);
    Route::post('/announcement/{id}/destroy', [Admin\AnnouncementController::class, 'destroy']);
    Route::post('/announcement/{id}/{status}/updatestatus', [Admin\AnnouncementController::class, 'updatestatus']);

    Route::resource('/couponcode', Admin\CouponcodeController::class);
    Route::post('/couponcode/{id}/destroy', [Admin\CouponcodeController::class, 'destroy']);
    Route::post('/couponcode/{id}/{status}/updatestatus', [Admin\CouponcodeController::class, 'updatestatus']);

    Route::resource('/socialmedia', Admin\SocialmediaController::class)->only(['index', 'store']);

    Route::resource('/smstemplate', Admin\SMSTemplateController::class);
    Route::post('/smstemplate/{id}/destroy', [Admin\SMSTemplateController::class, 'destroy']);
    Route::post('/smstemplate/{id}/{status}/updatestatus', [Admin\SMSTemplateController::class, 'updatestatus']);

    Route::resource('/roleandrights', Admin\RoleRightsController::class);
    Route::post('/roleandrights/{id}/destroy', [Admin\RoleRightsController::class, 'destroy']);

});

Route::get('/{urlkey}', [HomeController::class, 'staticpages']);
