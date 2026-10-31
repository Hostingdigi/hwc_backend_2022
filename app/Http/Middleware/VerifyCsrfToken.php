<?php

namespace App\Http\Middleware;

use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken as Middleware;

class VerifyCsrfToken extends Middleware
{
    /**
     * The URIs that should be excluded from CSRF verification.
     *
     * @var array
     */
    protected $except = [
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
    ];
}
