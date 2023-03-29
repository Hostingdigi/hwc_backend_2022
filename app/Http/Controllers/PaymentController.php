<?php

namespace App\Http\Controllers;

use App\Models\Cart;
use App\Models\Country;
use App\Models\Couponcode;
use App\Models\CouponCodeUsage;
use App\Models\Customer;
use App\Models\EmailTemplate;
use App\Models\GrabPayFunctions;
use App\Models\OrderDetails;
use App\Models\OrderMaster;
use App\Models\Paymentlog;
use App\Models\PaymentMethods;
use App\Models\PaymentSettings;
use App\Models\Product;
use App\Models\Settings;
use App\Models\ShippingMethods;
use App\Services\CartServices;
use App\Services\OrderServices;
use DB;
use Illuminate\Http\Request;
use Mail;
use Session;
use Stripe;

class PaymentController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    protected $cartServices = null;
    protected $orderServices = null;

    public function __construct(CartServices $cartServices, OrderServices $orderServices)
    {
        $this->cartServices = $cartServices;
        $this->orderServices = $orderServices;

    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function stripe()
    {

        $billinginfo = Session::has('billinginfo') ? Session::get('billinginfo') : [];
        $cartItems = $this->cartServices->cartItems($billinginfo['ship_country'] ?? null);
        $cartdata = $cartItems['cartItems'];
        $subtotal = $cartItems['subTotal'];
        $grandtotal = $cartItems['grandTotal'];
        $gst = $cartItems['taxDetails']['taxTotal'];
        $taxtitle = $cartItems['taxDetails']['taxLabel'];
        $taxLabelOnly = $cartItems['taxDetails']['taxLabelOnly'];
        $deliverytype = $cartItems['deliveryDetails']['title'];
        $packingfee = $cartItems['packingFees'];
        $deliverycost = $cartItems['deliveryDetails']['deliveryTotal'];
        $discount = $cartItems['discountDetails']['discountTotal'];
        $discounttext = $cartItems['discountDetails']['title'];
        $fuelcharges = isset($cartItems['fuelcharges']) ? $cartItems['fuelcharges'] : 0.00;
        $handlingfee = isset($cartItems['handlingfee']) ? $cartItems['handlingfee'] : 0.00;

        $orderincid = 0;

        // Stripe Key
        $paymentmethod = PaymentMethods::where('id', 3)->orWhere('payment_name', 'LIKE', '%stripe')->first();
        $stripekey = $paymentmethod ? ($paymentmethod->payment_mode == 'live' ? $paymentmethod->api_key : $paymentmethod->test_api_key) : '';

        return view('public/Payment.stripe', compact('cartdata', 'orderincid', 'taxLabelOnly', 'subtotal', 'gst', 'grandtotal', 'taxtitle', 'stripekey', 'billinginfo', 'deliverycost', 'deliverytype', 'packingfee', 'discounttext', 'discount', 'fuelcharges', 'handlingfee'));
    }

    public function old_stripe()
    {
        /*$country = $deliverycost = $packingfee = $deliverymethod = 0;
        $taxtitle = $deliverytype = '';
        $cartdata = $taxvals = $billinginfo = [];
        $taxtitle = 'GST (7%)';
        $subtotal = $grandtotal = 0;
        $sesid = Session::get('_token');
        if(Session::has('cartdata')) {
        $cartdata = Session::get('cartdata');
        }

        $subtotal = $grandtotal = 0;
        $cart = new Cart();
        $subtotal = $cart->getSubTotal();
        //$gst = $cart->getGST($subtotal);

        // Shipping Cost

        if(Session::has('deliverymethod')) {
        $deliverymethod = Session::get('deliverymethod');
        }

        //Packing Cost

        if(Session::has('billinginfo')) {
        $billinginfo = Session::get('billinginfo');
        $country = $billinginfo['ship_country'];
        }

        // Shipping Cost

        $deliverytype = $cart->getDeliveryMethod($deliverymethod);

        $totalweight = 0;

        $taxes = $cart->getGST($subtotal, $country);

        if($country != '') {
        $taxvals = @explode("|", $taxes);
        $taxtitle = $taxvals[0];
        $gst = $taxvals[1];
        } else {
        $gst = $taxes;
        }

        $settings = PaymentSettings::where('Id', '=', '1')->select('min_package_fee')->first();
        if($country != 'SG') {
        $packingfee = $settings->min_package_fee;
        }
        $boxfees = 0;

        foreach($cartdata as $key => $val) {
        if(is_array($val)) {
        $x = 0;
        foreach($val as $datakey => $dataval) {
        $totalweight = $totalweight + $dataval['weight'];
        $shippingbox = $dataval['shippingbox'];
        $quantity = $dataval['qty'];
        $deliverycost += $cart->shippingCost($country, $deliverymethod, $shippingbox, $quantity, $subtotal, $gst);
        $boxfees = $cart->getPackagingFee($country, $totalweight, $subtotal, $gst, $deliverymethod, $shippingbox, $quantity);
        }
        }
        }

        $packingfee = number_format(($packingfee + $boxfees), 2);

        $discounttype = $disamount = 0;
        $discounttext = '';
        if(Session::has('couponcode')) {
        $discounttype = Session::get('discounttype');
        $disamount = Session::get('discount');
        $discounttext = Session::get('discounttext');
        }

        $discount = $cart->getDiscount($subtotal, $gst, $deliverycost, $packingfee, $discounttype, $disamount);

        $grandtotal = $cart->getGrandTotal($subtotal, $gst, $deliverycost, $packingfee, $discount);

        $stripekey = '';
        $paymode = 'live';
        $paymentmethod = PaymentMethods::where('id', '=', '3')->orWhere('payment_name', 'LIKE', '%stripe')->first();

        if($paymentmethod) {
        $paymode = $paymentmethod->payment_mode;
        if($paymode == 'live') {
        $stripekey = $paymentmethod->api_key;
        } else {
        $stripekey = $paymentmethod->test_api_key;
        }
        }*/

        $billinginfo = [];
        $paymethodname = $deliverytype = $emailsubject = $emailcontent = $companyname = $adminemail = $ccemail = $taxtitle = '';
        $paymentmethod = $packingfee = $deliverycost = $deliverymethod = 0;
        $sesid = Session::get('_token');
        if (Session::has('cartdata')) {
            $cartdata = Session::get('cartdata');
        }

        if (Session::has('paymentmethod')) {
            $paymentmethod = Session::get('paymentmethod');
        }

        $billinginfo = Session::get('billinginfo');

        $cart = new Cart();
        $subtotal = $cart->getSubTotal();

        $orderid = $countryid = $orderincid = 0;
        $country = '';

        $country = $billinginfo['ship_country'];

        if (Session::has('deliverymethod')) {
            $deliverymethod = Session::get('deliverymethod');
        }

        // Shipping Cost

        $deliverytype = $cart->getDeliveryMethod($deliverymethod);

        $totalweight = 0;

        $taxes = $cart->getGST($subtotal, $country);

        if ($country != '') {
            $taxvals = @explode("|", $taxes);
            $taxtitle = $taxvals[0];
            $gst = $taxvals[1];
        } else {
            $gst = $taxes;
        }

        $settings = PaymentSettings::where('Id', '=', '1')->select('min_package_fee')->first();
        if ($country != 'SG') {
            $packingfee = $settings->min_package_fee;
        }
        $boxfees = 0;

        /*foreach($cartdata as $key => $val) {
        if(is_array($val)) {
        $x = 0;
        foreach($val as $datakey => $dataval) {
        $totalweight = $totalweight + $dataval['weight'];
        $shippingbox = $dataval['shippingbox'];
        $quantity = $dataval['qty'];
        $deliverycost += $cart->shippingCost($country, $deliverymethod, $shippingbox, $quantity, $subtotal, $gst);
        $boxfees = $cart->getPackagingFee($country, $totalweight, $subtotal, $gst, $deliverymethod, $shippingbox, $quantity);
        }
        }
        }*/

        foreach ($cartdata as $key => $val) {
            if (is_array($val)) {
                $x = 0;
                $totalweight = 0;
                foreach ($val as $datakey => $dataval) {
                    $product_weight = $dataval['weight'] * $dataval['qty'];
                    $totalweight += $dataval['weight'];
                    $shippingbox = $dataval['shippingbox'];
                    $quantity = $dataval['qty'];
                    $boxfees = $cart->getPackagingFee($country, $totalweight, $subtotal, $gst, $deliverymethod, $shippingbox, $quantity);

                }
                $deliverycost = $cart->shippingCost($country, $deliverymethod, $shippingbox, $quantity, $subtotal, $gst, $totalweight);
            }
        }

        $packingfee = number_format(($packingfee + $boxfees), 2);

        $discounttype = $disamount = 0;
        $discounttext = '';
        if (Session::has('couponcode')) {
            $discounttype = Session::get('discounttype');
            $disamount = Session::get('discount');
            $discounttext = Session::get('discounttext');
        }

        $discount = $cart->getDiscount($subtotal, $gst, $deliverycost, $packingfee, $discounttype, $disamount);

        $grandtotal = $cart->getGrandTotal($subtotal, $gst, $deliverycost, $packingfee, $discount);

        $subtotal = str_replace(',', '', $subtotal);
        $deliverycost = str_replace(',', '', $deliverycost);
        $packingfee = str_replace(',', '', $packingfee);
        $gst = str_replace(',', '', $gst);
        $grandtotal = str_replace(',', '', $grandtotal);
        $discount = str_replace(',', '', $discount);

        $sesid = Session::get('_token');
        if (Session::has('cartdata')) {
            $cartdata = Session::get('cartdata');
        }

        $paymethod = PaymentMethods::where('id', '=', $paymentmethod)->first();
        if ($paymethod) {
            $paymethodname = $paymethod->payment_name;
        }

        if ($cartdata) {

            $couponid = 0;
            $userid = 0;
            if (Session::has('customer_id')) {
                $userid = Session::get('customer_id');
            } else {
                $chkcustomer = Customer::where('cust_email', '=', $billinginfo['bill_email'])->select('cust_id')->first();
                if (!$chkcustomer) {
                    Customer::insert(array('cust_firstname' => $billinginfo['bill_fname'], 'cust_lastname' => $billinginfo['bill_lname'], 'cust_email' => $billinginfo['bill_email'], 'cust_address1' => $billinginfo['bill_ads1'], 'cust_address2' => $billinginfo['bill_ads2'], 'cust_city' => $billinginfo['bill_city'], 'cust_state' => $billinginfo['bill_state'], 'cust_country' => $countryid, 'cust_zip' => $billinginfo['bill_zip'], 'cust_phone' => $billinginfo['bill_mobile'], 'cust_status' => 0));
                    $cust = Customer::where('cust_id', '>', '0')->orderBy('cust_id', 'desc')->select('cust_id')->first();
                    if ($cust) {
                        $userid = $cust->cust_id;
                    }
                } else {
                    $userid = $chkcustomer->cust_id;
                }
            }

            if (Session::has('couponcode')) {
                $couponcode = Session::get('couponcode');
                $coupondata = Couponcode::where('coupon_code', '=', $couponcode)->where('status', '=', '1')->first();
                if ($coupondata) {
                    $couponid = $coupondata->id;
                }
            }

            $ordermaster = new OrderMaster;
            $ordermaster['user_id'] = $userid;
            $ordermaster['ship_method'] = Session::get('deliverymethod');
            $ordermaster['pay_method'] = $paymethodname;
            $ordermaster['shipping_cost'] = $deliverycost;
            $ordermaster['packaging_fee'] = $packingfee;
            $ordermaster['tax_collected'] = $gst;
            $ordermaster['payable_amount'] = $grandtotal;
            $ordermaster['discount_amount'] = $discount;
            $ordermaster['discount_id'] = $couponid;
            $ordermaster['order_status'] = '0';
            $ordermaster['if_items_unavailabel'] = Session::get('if_unavailable');
            $ordermaster['delivery_instructions'] = Session::get('delivery_instructions');

            $ordermaster['bill_fname'] = $billinginfo['bill_fname'];
            $ordermaster['bill_lname'] = $billinginfo['bill_lname'];
            $ordermaster['bill_email'] = $billinginfo['bill_email'];
            $ordermaster['bill_mobile'] = $billinginfo['bill_mobile'];
            $ordermaster['bill_compname'] = $billinginfo['bill_compname'];
            $ordermaster['bill_ads1'] = $billinginfo['bill_ads1'];
            $ordermaster['bill_ads2'] = $billinginfo['bill_ads2'];
            $ordermaster['bill_city'] = $billinginfo['bill_city'];
            $ordermaster['bill_state'] = $billinginfo['bill_state'];
            $ordermaster['bill_zip'] = $billinginfo['bill_zip'];
            $ordermaster['bill_country'] = $billinginfo['bill_country'];
            $ordermaster['ship_fname'] = $billinginfo['ship_fname'];
            $ordermaster['ship_lname'] = $billinginfo['ship_lname'];
            $ordermaster['ship_email'] = $billinginfo['ship_email'];
            $ordermaster['ship_mobile'] = $billinginfo['ship_mobile'];
            $ordermaster['ship_ads1'] = $billinginfo['ship_ads1'];
            $ordermaster['ship_ads2'] = $billinginfo['ship_ads2'];
            $ordermaster['ship_country'] = $billinginfo['ship_country'];
            $ordermaster['ship_city'] = $billinginfo['ship_city'];
            $ordermaster['ship_state'] = $billinginfo['ship_state'];
            $ordermaster['ship_zip'] = $billinginfo['ship_zip'];

            if (Session::has('old_order_id')) {
                if (Session::get('old_order_id') > 0) {
                    $orderid = Session::get('old_order_id');
                    $orderincid = Session::get('old_order_id');
                    //OrderMaster::where('order_id', '=', $orderincid)->update(array('pay_method' => $paymethodname));
                    $today = date('Y-m-d H:i:s');
                    OrderMaster::where('order_id', '=', $orderincid)->update(array('pay_method' => $paymethodname, 'date_entered' => $today));
                }
            } else {
                $ordermaster->save();
                $order = OrderMaster::orderBy('order_id', 'desc')->select('order_id')->first();
                if ($order) {
                    $orderid = $order->order_id;
                    $orderincid = $order->order_id;
                }
            }

            foreach ($cartdata[$sesid] as $cart) {
                $orderdetails = new OrderDetails;
                $orderdetails['order_id'] = $orderid;
                $orderdetails['prod_id'] = $cart['productId'];
                $orderdetails['prod_name'] = $cart['productName'];
                $orderdetails['prod_quantity'] = $cart['qty'];
                $orderdetails['prod_unit_price'] = $cart['price'];
                $orderdetails['prod_option'] = $cart['productoption'];
                //$orderdetails['option_id'] = $cart['option_id'];
                $orderdetails['Weight'] = $cart['weight'];
                $orderdetails['prod_code'] = $cart['productcode'];

                if (!Session::has('old_order_id')) {
                    $orderdetails->save();
                }
                $qty = 0;
                $product = Product::where('Id', '=', $cart['productId'])->select('Quantity')->first();
                if ($product->Quantity > $cart['qty']) {
                    $qty = $product->Quantity - $cart['qty'];
                }
                Product::where('Id', '=', $cart['productId'])->update(array('Quantity' => $qty));

            }

            $countrydata = Country::where('countrycode', '=', $billinginfo['bill_country'])->select('countryid')->first();
            if ($countrydata) {
                $countryid = $countrydata->countryid;
            }

            Customer::where('cust_id', '=', $userid)->update(array('cust_firstname' => $billinginfo['bill_fname'], 'cust_lastname' => $billinginfo['bill_lname'], 'cust_address1' => $billinginfo['bill_ads1'], 'cust_address2' => $billinginfo['bill_ads2'], 'cust_city' => $billinginfo['bill_city'], 'cust_state' => $billinginfo['bill_state'], 'cust_country' => $countryid, 'cust_zip' => $billinginfo['bill_zip'], 'cust_phone' => $billinginfo['bill_mobile']));

            DB::table('cart_details')->where('user_id', '=', $userid)->delete();

            $currency = 'SGD';
            $paysettings = PaymentSettings::where('id', '=', '1')->select('currency_type')->first();
            if ($paysettings) {
                $currency = $paysettings->currency_type;
            }

            $stripesignature = '';
            $paymode = 'live';
            $paymentmethod = PaymentMethods::where('id', '=', '3')->orWhere('payment_name', 'LIKE', '%stripe')->first();

            if ($paymentmethod) {
                $paymode = $paymentmethod->payment_mode;
                if ($paymode == 'live') {
                    $stripekey = $paymentmethod->api_key;
                } else {
                    $stripekey = $paymentmethod->test_api_key;
                }
            }

        }

        return view('public/Payment.stripe', compact('cartdata', 'sesid', 'subtotal', 'gst', 'grandtotal', 'taxtitle', 'stripekey', 'billinginfo', 'deliverycost', 'deliverytype', 'packingfee', 'discounttext', 'discount', 'orderincid'));
    }

    public function stripePaymentProcess(Request $request)
    {
        if (!Session::has('billinginfo')) {
            return redirect('cancelpayment');
        }

        $billinginfo = Session::has('billinginfo') ? Session::get('billinginfo') : [];
        $cartItems = $this->cartServices->cartItems($billinginfo['ship_country'] ?? null);
        $cartdata = $cartItems['cartItems'];
        $subtotal = $cartItems['subTotal'];
        $grandtotal = $cartItems['grandTotal'];
        $gst = $cartItems['taxDetails']['taxTotal'];
        $taxtitle = $cartItems['taxDetails']['taxLabel'];
        $taxLabelOnly = $cartItems['taxDetails']['taxLabelOnly'];
        $deliverytype = $cartItems['deliveryDetails']['title'];
        $packingfee = $cartItems['packingFees'];
        $deliverycost = $cartItems['deliveryDetails']['deliveryTotal'];
        $discount = $cartItems['discountDetails']['discountTotal'];
        $discounttext = $cartItems['discountDetails']['title'];

        $paymethodname = $emailsubject = $emailcontent = $companyname = $adminemail = $ccemail = '';
        $paymentmethod = 0;
        $sesid = Session::get('_token');

        if (Session::has('paymentmethod')) {
            $paymentmethod = Session::get('paymentmethod');
        }

        $billinginfo = Session::get('billinginfo');

        //Create order
        $orderCreate = $this->orderServices->createOrder();

        if ($orderCreate['status'] == false) {
            return redirect('cancelpayment');
        }

        $userid = $orderCreate['userId'];
        DB::table('cart_details')->where('user_id', '=', $userid)->delete();

        $paysettings = PaymentSettings::where('id', 1)->select('currency_type')->first();
        $currency = $paysettings ? $paysettings->currency_type : 'SGD';

        $paymentmethod = PaymentMethods::where('id', '3')->orWhere('payment_name', 'LIKE', '%stripe')->first();
        $stripesignature = $paymentmethod ? ($paymentmethod->payment_mode == 'live' ? $paymentmethod->api_signature : $paymentmethod->test_api_signature) : '';

        Stripe\Stripe::setApiKey($stripesignature);

        $source = \Stripe\Source::create([
            "type" => "card",
            "currency" => $currency,
            "card" => [
                "number" => $request->cardnumber,
                "cvc" => $request->cvc,
                "exp_month" => $request->exp_month,
                "exp_year" => $request->exp_year,
            ],
            "owner" => [
                "email" => $billinginfo['bill_email'],
            ],
        ]);

        $shippingAddress = [
            'line1' => $billinginfo['ship_ads1'],
            'city' => $billinginfo['ship_city'],
            'state' => $billinginfo['ship_state'],
            'postal_code' => $billinginfo['ship_zip'],
            'country' => $billinginfo['ship_country'],
        ];

        $stripecustomer = \Stripe\Customer::create([
            'name' => $billinginfo['ship_fname'] . ' ' . $billinginfo['ship_lname'],
            'email' => $billinginfo['bill_email'],
            'address' => $shippingAddress,
        ]);

        $couponid = 0;
        if (Session::has('couponcode')) {
            $couponcode = Session::get('couponcode');
            $coupondata = Couponcode::where('coupon_code', '=', $couponcode)->where('status', '=', '1')->first();
            if ($coupondata) {
                $couponid = $coupondata->id;
            }
        }

        if ($stripecustomer) {

            $response = Stripe\Charge::create([
                "amount" => $grandtotal * 100,
                "currency" => $currency,
                "source" => $request->stripeToken,
                "description" => "Payment from hardwarecity.com.sg",
                "metadata" => ["order_id" => $orderCreate['orderId']],
                "source" => $source['id'],
                "customer" => $stripecustomer['id'],
                "shipping" => [
                    'name' => $billinginfo['ship_fname'] . ' ' . $billinginfo['ship_lname'],
                    'address' => $shippingAddress,
                ],
            ]);

            if ($response) {
                $transid = $response['id'];
                OrderMaster::where('order_id', $orderCreate['orderId'])->update(['trans_id' => $transid, 'order_status' => '1']);

                if (!empty($discounttext) && !empty($discount)) {
                    CouponCodeUsage::insert(['coupon_id' => $couponid, 'customer_id' => $userid, 'order_id' => $orderCreate['orderId']]);
                }
            }

            //Clear sessions
            $sessionsValues = ['cartdata', 'deliverymethod', 'if_unavailable', 'billinginfo', 'paymentmethod', 'discount', 'discounttext',
                'couponcode', 'discounttype', 'old_order_id'];
            foreach ($sessionsValues as $session) {Session::forget($session);}

            return redirect('success?orderid=' . $orderCreate['orderId']);

        }

        return redirect('cancelpayment');

    }

    public function stripePaymentProcess2(Request $request)
    {
        $billinginfo = [];
        $paymethodname = $deliverytype = $emailsubject = $emailcontent = $companyname = $adminemail = $ccemail = $taxtitle = '';
        $paymentmethod = $packingfee = $deliverycost = $deliverymethod = 0;
        $sesid = Session::get('_token');
        if (Session::has('cartdata')) {
            $cartdata = Session::get('cartdata');
        }

        if (Session::has('paymentmethod')) {
            $paymentmethod = Session::get('paymentmethod');
        }

        $billinginfo = Session::get('billinginfo');

        $cart = new Cart();
        $subtotal = $cart->getSubTotal();

        $orderid = $countryid = $orderincid = 0;
        $country = '';

        $orderid = $request->orderincid;
        $orderincid = $request->orderincid;

        $country = $billinginfo['ship_country'];

        if (Session::has('deliverymethod')) {
            $deliverymethod = Session::get('deliverymethod');
        }

        // Shipping Cost

        $deliverytype = $cart->getDeliveryMethod($deliverymethod);

        $totalweight = 0;

        $taxes = $cart->getGST($subtotal, $country);

        if ($country != '') {
            $taxvals = @explode("|", $taxes);
            $taxtitle = $taxvals[0];
            $gst = $taxvals[1];
        } else {
            $gst = $taxes;
        }

        $settings = PaymentSettings::where('Id', '=', '1')->select('min_package_fee')->first();
        if ($country != 'SG') {
            $packingfee = $settings->min_package_fee;
        }
        $boxfees = 0;

        /*foreach($cartdata as $key => $val) {
        if(is_array($val)) {
        $x = 0;
        foreach($val as $datakey => $dataval) {
        $totalweight = $totalweight + $dataval['weight'];
        $shippingbox = $dataval['shippingbox'];
        $quantity = $dataval['qty'];
        $deliverycost += $cart->shippingCost($country, $deliverymethod, $shippingbox, $quantity, $subtotal, $gst);
        $boxfees = $cart->getPackagingFee($country, $totalweight, $subtotal, $gst, $deliverymethod, $shippingbox, $quantity);
        }
        }
        }*/

        foreach ($cartdata as $key => $val) {
            if (is_array($val)) {
                $x = 0;
                $totalweight = 0;
                foreach ($val as $datakey => $dataval) {
                    $product_weight = $dataval['weight'] * $dataval['qty'];
                    $totalweight += $dataval['weight'];
                    $shippingbox = $dataval['shippingbox'];
                    $quantity = $dataval['qty'];
                    $boxfees = $cart->getPackagingFee($country, $totalweight, $subtotal, $gst, $deliverymethod, $shippingbox, $quantity);

                }
                $deliverycost = $cart->shippingCost($country, $deliverymethod, $shippingbox, $quantity, $subtotal, $gst, $totalweight);
            }
        }

        $packingfee = number_format(($packingfee + $boxfees), 2);

        $discounttype = $disamount = 0;
        $discounttext = '';
        if (Session::has('couponcode')) {
            $discounttype = Session::get('discounttype');
            $disamount = Session::get('discount');
            $discounttext = Session::get('discounttext');
        }

        $discount = $cart->getDiscount($subtotal, $gst, $deliverycost, $packingfee, $discounttype, $disamount);

        $grandtotal = $cart->getGrandTotal($subtotal, $gst, $deliverycost, $packingfee, $discount);

        $subtotal = str_replace(',', '', $subtotal);
        $deliverycost = str_replace(',', '', $deliverycost);
        $packingfee = str_replace(',', '', $packingfee);
        $gst = str_replace(',', '', $gst);
        $grandtotal = str_replace(',', '', $grandtotal);
        $discount = str_replace(',', '', $discount);

        $sesid = Session::get('_token');
        if (Session::has('cartdata')) {
            $cartdata = Session::get('cartdata');
        }

        $paymethod = PaymentMethods::where('id', '=', $paymentmethod)->first();
        if ($paymethod) {
            $paymethodname = $paymethod->payment_name;
        }

        if ($cartdata) {

            $couponid = 0;
            $userid = 0;
            if (Session::has('customer_id')) {
                $userid = Session::get('customer_id');
            } else {
                $chkcustomer = Customer::where('cust_email', '=', $billinginfo['bill_email'])->select('cust_id')->first();
                if (!$chkcustomer) {
                    Customer::insert(array('cust_firstname' => $billinginfo['bill_fname'], 'cust_lastname' => $billinginfo['bill_lname'], 'cust_email' => $billinginfo['bill_email'], 'cust_address1' => $billinginfo['bill_ads1'], 'cust_address2' => $billinginfo['bill_ads2'], 'cust_city' => $billinginfo['bill_city'], 'cust_state' => $billinginfo['bill_state'], 'cust_country' => $countryid, 'cust_zip' => $billinginfo['bill_zip'], 'cust_phone' => $billinginfo['bill_mobile'], 'cust_status' => 0));
                    $cust = Customer::where('cust_id', '>', '0')->orderBy('cust_id', 'desc')->select('cust_id')->first();
                    if ($cust) {
                        $userid = $cust->cust_id;
                    }
                } else {
                    $userid = $chkcustomer->cust_id;
                }
            }

            if (Session::has('couponcode')) {
                $couponcode = Session::get('couponcode');
                $coupondata = Couponcode::where('coupon_code', '=', $couponcode)->where('status', '=', '1')->first();
                if ($coupondata) {
                    $couponid = $coupondata->id;
                }
            }

            $ordermaster = new OrderMaster;
            $ordermaster['user_id'] = $userid;
            $ordermaster['ship_method'] = Session::get('deliverymethod');
            $ordermaster['pay_method'] = $paymethodname;
            $ordermaster['shipping_cost'] = $deliverycost;
            $ordermaster['packaging_fee'] = $packingfee;
            $ordermaster['tax_collected'] = $gst;
            $ordermaster['payable_amount'] = $grandtotal;
            $ordermaster['discount_amount'] = $discount;
            $ordermaster['discount_id'] = $couponid;
            $ordermaster['order_status'] = '0';
            $ordermaster['if_items_unavailabel'] = Session::get('if_unavailable');
            $ordermaster['delivery_instructions'] = Session::get('delivery_instructions');
            $ordermaster['bill_fname'] = $billinginfo['bill_fname'];
            $ordermaster['bill_lname'] = $billinginfo['bill_lname'];
            $ordermaster['bill_email'] = $billinginfo['bill_email'];
            $ordermaster['bill_mobile'] = $billinginfo['bill_mobile'];
            $ordermaster['bill_compname'] = $billinginfo['bill_compname'];
            $ordermaster['bill_ads1'] = $billinginfo['bill_ads1'];
            $ordermaster['bill_ads2'] = $billinginfo['bill_ads2'];
            $ordermaster['bill_city'] = $billinginfo['bill_city'];
            $ordermaster['bill_state'] = $billinginfo['bill_state'];
            $ordermaster['bill_zip'] = $billinginfo['bill_zip'];
            $ordermaster['bill_country'] = $billinginfo['bill_country'];
            $ordermaster['ship_fname'] = $billinginfo['ship_fname'];
            $ordermaster['ship_lname'] = $billinginfo['ship_lname'];
            $ordermaster['ship_email'] = $billinginfo['ship_email'];
            $ordermaster['ship_mobile'] = $billinginfo['ship_mobile'];
            $ordermaster['ship_ads1'] = $billinginfo['ship_ads1'];
            $ordermaster['ship_ads2'] = $billinginfo['ship_ads2'];
            $ordermaster['ship_country'] = $billinginfo['ship_country'];
            $ordermaster['ship_city'] = $billinginfo['ship_city'];
            $ordermaster['ship_state'] = $billinginfo['ship_state'];
            $ordermaster['ship_zip'] = $billinginfo['ship_zip'];

            /*if(Session::has('old_order_id')) {
            if(Session::get('old_order_id') > 0) {
            $orderid = Session::get('old_order_id');
            $orderincid = Session::get('old_order_id');
            OrderMaster::where('order_id', '=', $orderincid)->update(array('pay_method' => $paymethodname));

            }
            } else {
            $ordermaster->save();
            $order = OrderMaster::orderBy('order_id', 'desc')->select('order_id')->first();
            if($order) {
            $orderid = $order->order_id;
            $orderincid = $order->order_id;
            }
            }

            foreach($cartdata[$sesid] as $cart) {
            $orderdetails = new OrderDetails;
            $orderdetails['order_id'] = $orderid;
            $orderdetails['prod_id'] = $cart['productId'];
            $orderdetails['prod_name'] = $cart['productName'];
            $orderdetails['prod_quantity'] = $cart['qty'];
            $orderdetails['prod_unit_price'] = $cart['price'];
            $orderdetails['prod_option'] = $cart['productoption'];
            //$orderdetails['option_id'] = $cart['option_id'];
            $orderdetails['Weight'] = $cart['weight'];
            $orderdetails['prod_code'] = $cart['productcode'];

            if(!Session::has('old_order_id')) {
            $orderdetails->save();
            }
            $qty = 0;
            $product = Product::where('Id', '=', $cart['productId'])->select('Quantity')->first();
            if($product->Quantity > $cart['qty']) {
            $qty = $product->Quantity - $cart['qty'];
            }
            Product::where('Id', '=', $cart['productId'])->update(array('Quantity' => $qty));

            }*/

            $countrydata = Country::where('countrycode', '=', $billinginfo['bill_country'])->select('countryid')->first();
            if ($countrydata) {
                $countryid = $countrydata->countryid;
            }

            Customer::where('cust_id', '=', $userid)->update(array('cust_firstname' => $billinginfo['bill_fname'], 'cust_lastname' => $billinginfo['bill_lname'], 'cust_address1' => $billinginfo['bill_ads1'], 'cust_address2' => $billinginfo['bill_ads2'], 'cust_city' => $billinginfo['bill_city'], 'cust_state' => $billinginfo['bill_state'], 'cust_country' => $countryid, 'cust_zip' => $billinginfo['bill_zip'], 'cust_phone' => $billinginfo['bill_mobile']));

            DB::table('cart_details')->where('user_id', '=', $userid)->delete();

            $currency = 'SGD';
            $paysettings = PaymentSettings::where('id', '=', '1')->select('currency_type')->first();
            if ($paysettings) {
                $currency = $paysettings->currency_type;
            }

            $stripesignature = '';
            $paymode = 'live';
            $paymentmethod = PaymentMethods::where('id', '=', '3')->orWhere('payment_name', 'LIKE', '%stripe')->first();

            if ($paymentmethod) {
                $paymode = $paymentmethod->payment_mode;
                if ($paymode == 'live') {
                    $stripesignature = $paymentmethod->api_signature;
                } else {
                    $stripesignature = $paymentmethod->test_api_signature;
                }
            }

            Stripe\Stripe::setApiKey($stripesignature);
            /*$response = Stripe\Charge::create ([
            "amount" => $grandtotal * 100,
            "currency" => $currency,
            "source" => $request->stripeToken,
            "description" => "Payment from hardwarecity.com.sg",
            "metadata" => array("order_id" => $orderid)
            ]);

            if($response) {
            $transid = $response['id'];
            OrderMaster::where('order_id', '=', $orderincid)->update(array('trans_id' => $transid, 'order_status' => '2'));
            if($discounttext != '' && $discount != 0) {
            CouponCodeUsage::insert(array('coupon_id' => $couponid, 'customer_id' => $userid, 'order_id' => $orderincid));
            }
            }*/

            $source = \Stripe\Source::create([
                "type" => "card",
                "currency" => $currency,
                "card" => [
                    "number" => $request->cardnumber,
                    "cvc" => $request->cvc,
                    "exp_month" => $request->exp_month,
                    "exp_year" => $request->exp_year,
                ],
                "owner" => [
                    "email" => $billinginfo['bill_email'],
                ],
            ]);

            $stripecustomer = \Stripe\Customer::create([
                'name' => $billinginfo['ship_fname'] . ' ' . $billinginfo['ship_lname'],
                'email' => $billinginfo['bill_email'],
                'address' => [
                    'line1' => $billinginfo['ship_ads1'],
                    'city' => $billinginfo['ship_city'],
                    'state' => $billinginfo['ship_state'],
                    'postal_code' => $billinginfo['ship_zip'],
                    'country' => $billinginfo['ship_country'],
                ],
            ]);

            if ($stripecustomer) {
                $response = Stripe\Charge::create([
                    "amount" => $grandtotal * 100,
                    "currency" => $currency,
                    "source" => $request->stripeToken,
                    "description" => "Payment from hardwarecity.com.sg",
                    "metadata" => array("order_id" => $orderid),
                    "source" => $source['id'],
                    "customer" => $stripecustomer['id'],
                    "shipping" => [
                        'name' => $billinginfo['ship_fname'] . ' ' . $billinginfo['ship_lname'],
                        'address' => [
                            'line1' => $billinginfo['ship_ads1'],
                            'city' => $billinginfo['ship_city'],
                            'state' => $billinginfo['ship_state'],
                            'postal_code' => $billinginfo['ship_zip'],
                            'country' => $billinginfo['ship_country'],
                        ],
                    ],
                ]);

                if ($response) {
                    $transid = $response['id'];
                    OrderMaster::where('order_id', '=', $orderincid)->update(array('trans_id' => $transid, 'order_status' => '1'));

                    if ($discounttext != '' && $discount != 0) {
                        CouponCodeUsage::insert(array('coupon_id' => $couponid, 'customer_id' => $userid, 'order_id' => $orderincid));
                    }
                }
            }

            Session::forget('cartdata');
            Session::forget('deliverymethod');
            Session::forget('if_unavailable');
            Session::forget('billinginfo');
            Session::forget('paymentmethod');
            Session::forget('discount');
            Session::forget('discounttext');
            Session::forget('couponcode');
            Session::forget('discounttype');
            Session::forget('old_order_id');
        }

        return redirect('/success?orderid=' . $orderincid);
    }

    public function hoolah()
    {
        $authtoken = $username = $password = $paymenturl = $apikey = $apisignature = $billcountryname = $shipcountryname = '';

        $billinginfo = [];
        $paymethodname = $deliverytype = $emailsubject = $emailcontent = $companyname = $adminemail = $ccemail = $taxtitle = '';
        $paymentmethod = $packingfee = $deliverycost = $deliverymethod = 0;
        $sesid = Session::get('_token');
        if (Session::has('cartdata')) {
            $cartdata = Session::get('cartdata');
        }

        if (Session::has('paymentmethod')) {
            $paymentmethod = Session::get('paymentmethod');
        }

        $billinginfo = Session::get('billinginfo');
        $cartItems = $this->cartServices->cartItems($billinginfo['ship_country'] ?? null);

        $cart = new Cart();
        $subtotal = $cart->getSubTotal();

        $orderid = $countryid = $orderincid = 0;
        $country = '';

        $country = $billinginfo['ship_country'];

        if (Session::has('deliverymethod')) {
            $deliverymethod = Session::get('deliverymethod');
        }

        // Shipping Cost

        $deliverytype = $cart->getDeliveryMethod($deliverymethod);

        $totalweight = 0;

        $taxes = $cart->getGST($subtotal, $country);

        if ($country != '') {
            $taxvals = @explode("|", $taxes);
            $taxtitle = $taxvals[0];
            $gst = $taxvals[1];
        } else {
            $gst = $taxes;
        }

        $settings = PaymentSettings::where('Id', '=', '1')->select('min_package_fee')->first();
        if ($country != 'SG') {
            $packingfee = $settings->min_package_fee;
        }
        $boxfees = 0;

        foreach ($cartdata as $key => $val) {
            if (is_array($val)) {
                $x = 0;
                $totalweight = 0;
                foreach ($val as $datakey => $dataval) {
                    $product_weight = $dataval['weight'] * $dataval['qty'];
                    $totalweight += $dataval['weight'];
                    $shippingbox = $dataval['shippingbox'];
                    $quantity = $dataval['qty'];
                    $boxfees = $cart->getPackagingFee($country, $totalweight, $subtotal, $gst, $deliverymethod, $shippingbox, $quantity);

                }
                $deliverycost = $cart->shippingCost($country, $deliverymethod, $shippingbox, $quantity, $subtotal, $gst, $totalweight);
            }
        }

        $packingfee = number_format(($packingfee + $boxfees), 2);

        $discounttype = $disamount = 0;
        $discounttext = '';
        if (Session::has('couponcode')) {
            $discounttype = Session::get('discounttype');
            $disamount = Session::get('discount');
            $discounttext = Session::get('discounttext');
        }

        $discount = $cart->getDiscount($subtotal, $gst, $deliverycost, $packingfee, $discounttype, $disamount);

        $grandtotal = $cart->getGrandTotal($subtotal, $gst, $deliverycost, $packingfee, $discount);

        $subtotal = str_replace(',', '', $subtotal);
        $deliverycost = str_replace(',', '', $deliverycost);
        $packingfee = str_replace(',', '', $packingfee);
        $gst = str_replace(',', '', $gst);
        $grandtotal = str_replace(',', '', $grandtotal);
        $discount = str_replace(',', '', $discount);

        $sesid = Session::get('_token');
        if (Session::has('cartdata')) {
            $cartdata = Session::get('cartdata');
        }

        $paymethod = PaymentMethods::where('id', '=', $paymentmethod)->first();
        if ($paymethod) {
            $paymethodname = $paymethod->payment_name;
        }

        if ($cartdata) {

            $couponid = 0;
            $userid = 0;
            if (Session::has('customer_id')) {
                $userid = Session::get('customer_id');
            } else {
                $chkcustomer = Customer::where('cust_email', '=', $billinginfo['bill_email'])->select('cust_id')->first();
                if (!$chkcustomer) {
                    Customer::insert(array('cust_firstname' => $billinginfo['bill_fname'], 'cust_lastname' => $billinginfo['bill_lname'], 'cust_email' => $billinginfo['bill_email'], 'cust_address1' => $billinginfo['bill_ads1'], 'cust_address2' => $billinginfo['bill_ads2'], 'cust_city' => $billinginfo['bill_city'], 'cust_state' => $billinginfo['bill_state'], 'cust_country' => $countryid, 'cust_zip' => $billinginfo['bill_zip'], 'cust_phone' => $billinginfo['bill_mobile'], 'cust_status' => 0));
                    $cust = Customer::where('cust_id', '>', '0')->orderBy('cust_id', 'desc')->select('cust_id')->first();
                    if ($cust) {
                        $userid = $cust->cust_id;
                    }
                } else {
                    $userid = $chkcustomer->cust_id;
                }
            }

            if (Session::has('couponcode')) {
                $couponcode = Session::get('couponcode');
                $coupondata = Couponcode::where('coupon_code', '=', $couponcode)->where('status', '=', '1')->first();
                if ($coupondata) {
                    $couponid = $coupondata->id;
                }
            }

            $ordermaster = new OrderMaster;
            $ordermaster['user_id'] = $userid;
            $ordermaster['ship_method'] = Session::get('deliverymethod');
            $ordermaster['pay_method'] = $paymethodname;
            $ordermaster['shipping_cost'] = $deliverycost;
            $ordermaster['packaging_fee'] = $packingfee;
            $ordermaster['tax_collected'] = $gst;
            $ordermaster['tax_label'] = isset($cartItems['taxDetails']['taxLabel']) ? $cartItems['taxDetails']['taxLabel'] : '';
            $ordermaster['tax_percentage'] = isset($cartItems['taxDetails']['taxPercentage']) ? $cartItems['taxDetails']['taxPercentage'] : '';
            $ordermaster['payable_amount'] = $grandtotal;
            $ordermaster['discount_amount'] = $discount;
            $ordermaster['discount_id'] = $couponid;
            $ordermaster['order_status'] = '0';
            $ordermaster['if_items_unavailabel'] = Session::get('if_unavailable');
            $ordermaster['delivery_instructions'] = Session::get('delivery_instructions');
            $ordermaster['bill_fname'] = $billinginfo['bill_fname'];
            $ordermaster['bill_lname'] = $billinginfo['bill_lname'];
            $ordermaster['bill_email'] = $billinginfo['bill_email'];
            $ordermaster['bill_mobile'] = $billinginfo['bill_mobile'];
            $ordermaster['bill_compname'] = $billinginfo['bill_compname'];
            $ordermaster['bill_ads1'] = $billinginfo['bill_ads1'];
            $ordermaster['bill_ads2'] = $billinginfo['bill_ads2'];
            $ordermaster['bill_city'] = $billinginfo['bill_city'];
            $ordermaster['bill_state'] = $billinginfo['bill_state'];
            $ordermaster['bill_zip'] = $billinginfo['bill_zip'];
            $ordermaster['bill_country'] = $billinginfo['bill_country'];
            $ordermaster['ship_fname'] = $billinginfo['ship_fname'];
            $ordermaster['ship_lname'] = $billinginfo['ship_lname'];
            $ordermaster['ship_email'] = $billinginfo['ship_email'];
            $ordermaster['ship_mobile'] = $billinginfo['ship_mobile'];
            $ordermaster['ship_ads1'] = $billinginfo['ship_ads1'];
            $ordermaster['ship_ads2'] = $billinginfo['ship_ads2'];
            $ordermaster['ship_country'] = $billinginfo['ship_country'];
            $ordermaster['ship_city'] = $billinginfo['ship_city'];
            $ordermaster['ship_state'] = $billinginfo['ship_state'];
            $ordermaster['ship_zip'] = $billinginfo['ship_zip'];

            if (Session::has('old_order_id')) {
                if (Session::get('old_order_id') > 0) {
                    $orderid = Session::get('old_order_id');
                    $orderincid = Session::get('old_order_id');
                    //OrderMaster::where('order_id', '=', $orderincid)->update(array('pay_method' => $paymethodname));
                    $today = date('Y-m-d H:i:s');
                    OrderMaster::where('order_id', '=', $orderincid)->update(array('pay_method' => $paymethodname, 'date_entered' => $today));
                }
            } else {
                $ordermaster->save();

                $order = OrderMaster::orderBy('order_id', 'desc')->select('order_id')->first();
                if ($order) {
                    $orderid = $order->order_id;
                    $orderincid = $order->order_id;
                }
            }
            $hoolahitems = [];

            foreach ($cartdata[$sesid] as $cart) {
                $orderdetails = new OrderDetails;
                $orderdetails['order_id'] = $orderid;
                $orderdetails['prod_id'] = $cart['productId'];
                $orderdetails['prod_name'] = $cart['productName'];
                $orderdetails['prod_quantity'] = $cart['qty'];
                $orderdetails['prod_unit_price'] = $cart['price'];
                $orderdetails['prod_option'] = $cart['productoption'];
                //$orderdetails['option_id'] = $cart['option_id'];
                $orderdetails['Weight'] = $cart['weight'];
                $orderdetails['prod_code'] = $cart['productcode'];
                if (!Session::has('old_order_id')) {
                    $orderdetails->save();
                }

                $desc = $image = '';

                $qty = 0;
                $product = Product::where('Id', '=', $cart['productId'])->select('Quantity', 'Image', 'EnShortDesc')->first();
                if ($product->Quantity > $cart['qty']) {
                    $qty = $product->Quantity - $cart['qty'];
                    $desc = $product->EnShortDesc;
                    if ($desc == '') {
                        $desc = $cart['productName'];
                    }
                    $image = url('/') . '/uploads/product/' . $product->Image;
                }
                Product::where('Id', '=', $cart['productId'])->update(array('Quantity' => $qty));

                $productname = $cart['productName'];

                $sku = $ean = "";
                if ($cart['productcode']) {
                    $sku = $cart['productcode'];
                    $ean = $cart['productcode'];
                }

                $hoolahitems = array("name" => $productname, "description" => $desc, "sku" => $sku, "ean" => $ean, "quantity" => $cart['qty'], "originalPrice" => $cart['price'], "price" => $cart['price'], "images" => array(array("imageLocation" => $image)), "taxAmount" => "0", "discount" => "0", "detailDescription" => $desc);

            }

            $countrydata = Country::where('countrycode', '=', $billinginfo['bill_country'])->select('countryid', 'countryname')->first();
            if ($countrydata) {
                $countryid = $countrydata->countryid;
                $billcountryname = $countrydata->countryname;
            }

            $shipcountrydata = Country::where('countrycode', '=', $billinginfo['ship_country'])->select('countryid', 'countryname')->first();
            if ($shipcountrydata) {
                $shipcountryname = $shipcountrydata->countryname;
            }

            Customer::where('cust_id', '=', $userid)->update(array('cust_firstname' => $billinginfo['bill_fname'], 'cust_lastname' => $billinginfo['bill_lname'], 'cust_address1' => $billinginfo['bill_ads1'], 'cust_address2' => $billinginfo['bill_ads2'], 'cust_city' => $billinginfo['bill_city'], 'cust_state' => $billinginfo['bill_state'], 'cust_country' => $countryid, 'cust_zip' => $billinginfo['bill_zip'], 'cust_phone' => $billinginfo['bill_mobile']));

            DB::table('cart_details')->where('user_id', '=', $userid)->delete();

            $currency = 'SGD';
            $paysettings = PaymentSettings::where('id', '=', '1')->select('currency_type')->first();
            if ($paysettings) {
                $currency = $paysettings->currency_type;
            }

            $paymode = 'live';
            $paymentmethod = PaymentMethods::where('id', '=', '4')->orWhere('payment_name', 'LIKE', '%hoolah')->first();

            if ($paymentmethod) {
                $paymode = $paymentmethod->payment_mode;
                if ($paymode == 'live') {
                    $apikey = $paymentmethod->api_key;
                    $apisignature = $paymentmethod->api_signature;
                    $paymenturl = $paymentmethod->live_url;
                } else {
                    $apikey = $paymentmethod->test_api_key;
                    $apisignature = $paymentmethod->test_api_signature;
                    $paymenturl = $paymentmethod->testing_url;
                }
            }

            $url = $paymenturl . "/auth/login";
            $ch = curl_init($url);
            $payload = json_encode(array("username" => $apikey, "password" => $apisignature));
            curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);
            curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type:application/json'));
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            $result = curl_exec($ch);
            curl_close($ch);

            $logfile = $_SERVER['DOCUMENT_ROOT'] . '/public/hoolahlog.txt';

            $serialized = serialize($result);

            $fp = fopen($logfile, "a+");
            //write to the file
            fwrite($fp, "Order Id:" . $orderincid);
            fwrite($fp, "\n\n");
            fwrite($fp, $serialized);
            fwrite($fp, "\n\n");

            fclose($fp);

            if ($result) {
                $response = json_decode($result);

                print_r($response);

                $authtoken = $response->token;

                if ($authtoken != '') {

                    $url = $paymenturl . "/order/initiate";

                    $billmobile = $billinginfo['bill_mobile'];

                    if (strlen($billmobile) > 8) {
                        $billmobile = substr($billmobile, -8);
                    }

                    $orderurl = 'https://demo-js.demo-hoolah.co/';
                    if ($billinginfo['bill_country'] == 'SG') {
                        $billmobile = '+65' . $billmobile;
                        if ($paymode == 'live') {
                            $orderurl = 'https://js.secure-hoolah.co/';
                        }
                    } elseif ($billinginfo['bill_country'] == 'MY') {
                        $billmobile = '+60' . $billmobile;
                        $orderurl = 'https://my.demo-js.demo-hoolah.co/';
                        if ($paymode == 'live') {
                            $orderurl = 'https://my.js.secure-hoolah.co/';
                        }
                    }

                    $billadd2 = $shipadd2 = "";
                    if ($billinginfo['bill_ads2']) {
                        $billadd2 = $billinginfo['bill_ads2'];
                    }

                    if ($billinginfo['ship_ads2']) {
                        $shipadd2 = $billinginfo['ship_ads2'];
                    }

                    $closeurl = url('/') . '/cancelpayment?orderid=' . $orderincid;
                    $returnurl = url('/') . '/success?orderid=' . $orderincid;

                    $ch = curl_init($url);
                    # Setup request to send json via POST.
                    $payload = array("consumerTitle" => "", "consumerFirstName" => $billinginfo['bill_fname'], "consumerLastName" => $billinginfo['bill_lname'], "consumerMiddleName" => "", "consumerEmail" => $billinginfo['bill_email'], "consumerPhoneNumber" => $billmobile, "shippingAddress" => array("line1" => $billinginfo['ship_ads1'], "line2" => $shipadd2, "suburb" => $shipcountryname, "postcode" => $billinginfo['ship_zip'], "countryCode" => $billinginfo['ship_country']), "billingAddress" => array("line1" => $billinginfo['bill_ads1'], "line2" => $billadd2, "suburb" => $billcountryname, "postcode" => $billinginfo['bill_zip'], "countryCode" => $billinginfo['bill_country']), "items" => array($hoolahitems), "totalAmount" => $grandtotal, "originalAmount" => $grandtotal, "taxAmount" => $gst, "cartId" => $orderid, "orderType" => "ONLINE", "shippingAmount" => $deliverycost, "shippingMethod" => "FREE", "discount" => $discount, "voucherCode" => "", "currency" => $currency, "closeUrl" => $closeurl, "returnToShopUrl" => $returnurl);

                    $payload = json_encode($payload);

                    //print_r($payload); exit;
                    curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);
                    $header_str = "Authorization: Bearer " . $authtoken;
                    curl_setopt($ch, CURLOPT_HTTPHEADER, array(
                        "Content-Type: application/json",
                        "Accept: application/json",
                        $header_str,
                    ));
                    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                    $result = curl_exec($ch);
                    curl_close($ch);

                    $serialized = serialize($result);

                    $fp = fopen($logfile, "a+");
                    //write to the file
                    fwrite($fp, "Order Id:" . $orderincid);
                    fwrite($fp, "\n\n");
                    fwrite($fp, $serialized);
                    fwrite($fp, "\n\n");
                    fclose($fp);

                    if ($result) {
                        $response = json_decode($result);
                        print_r($response);
                        $orderContentToken = $response->orderContextToken;
                        $orderid = $response->orderId;
                        $orderuuid = $response->orderUuid;
                        if ($orderContentToken) {
                            OrderMaster::where('order_id', '=', $orderincid)->update(array('trans_id' => $orderContentToken));

                            if ($discounttext != '' && $discount != 0) {
                                CouponCodeUsage::insert(array('coupon_id' => $couponid, 'customer_id' => $userid, 'order_id' => $orderincid));
                            }
                            header('location:' . $orderurl . '?ORDER_CONTEXT_TOKEN=' . $orderContentToken . '&platform=bespoke&version=1.0.1');
                            exit;
                        }
                    }
                }
            }

            Session::forget('cartdata');
            Session::forget('deliverymethod');
            Session::forget('if_unavailable');
            Session::forget('billinginfo');
            Session::forget('paymentmethod');
            Session::forget('discount');
            Session::forget('discounttext');
            Session::forget('couponcode');
            Session::forget('discounttype');
            Session::forget('old_order_id');
        }

    }

    public function paypal()
    {

        $authtoken = $username = $password = $paymenturl = $apikey = $apisignature = $billcountryname = $shipcountryname = '';

        $billinginfo = [];
        $paymethodname = $deliverytype = $emailsubject = $emailcontent = $companyname = $adminemail = $ccemail = $taxtitle = '';
        $paymentmethod = $packingfee = $deliverycost = $deliverymethod = 0;
        $sesid = Session::get('_token');
        if (Session::has('cartdata')) {
            $cartdata = Session::get('cartdata');
        }

        if (Session::has('paymentmethod')) {
            $paymentmethod = Session::get('paymentmethod');
        }

        $billinginfo = Session::get('billinginfo');

        $cartItems = $this->cartServices->cartItems($billinginfo['ship_country'] ?? null);
        $cartdata2 = $cartItems['cartItems'];
        $subtotal = $cartItems['subTotal'];
        $taxLabelOnly = $cartItems['taxDetails']['taxLabelOnly'];
        $fuelcharges = isset($cartItems['fuelcharges']) ? $cartItems['fuelcharges'] : 0.00;
        $handlingfee = isset($cartItems['handlingfee']) ? $cartItems['handlingfee'] : 0.00;
        /*$grandtotal = $cartItems['grandTotal'];
        $gst = $cartItems['taxDetails']['taxTotal'];
        $taxtitle = $cartItems['taxDetails']['taxLabel'];

        $deliverytype = $cartItems['deliveryDetails']['title'];
        $packingfee = $cartItems['packingFees'];
        $deliverycost = $cartItems['deliveryDetails']['deliveryTotal'];
        $discount = $cartItems['discountDetails']['discountTotal'];
        $discounttext = $cartItems['discountDetails']['title'];*/

        $cart = new Cart();
        //$subtotal = $cart->getSubTotal();

        $orderid = $countryid = $orderincid = 0;
        $country = '';

        $country = $billinginfo['ship_country'];

        if (Session::has('deliverymethod')) {
            $deliverymethod = Session::get('deliverymethod');
        }

        // Shipping Cost

        $deliverytype = $cart->getDeliveryMethod($deliverymethod);

        $totalweight = 0;

        $taxes = $cart->getGST($subtotal, $country);

        if ($country != '') {
            $taxvals = @explode("|", $taxes);
            $taxtitle = $taxvals[0];
            $gst = $taxvals[1];
        } else {
            $gst = $taxes;
        }

        $settings = PaymentSettings::where('Id', '=', '1')->select('min_package_fee')->first();
        if ($country != 'SG') {
            $packingfee = $settings->min_package_fee;
        }
        $boxfees = 0;

        foreach ($cartdata as $key => $val) {
            if (is_array($val)) {
                $x = 0;
                $totalweight = 0;
                foreach ($val as $datakey => $dataval) {
                    $product_weight = $dataval['weight'] * $dataval['qty'];
                    $totalweight += $dataval['weight'];
                    $shippingbox = $dataval['shippingbox'];
                    $quantity = $dataval['qty'];
                    $boxfees = $cart->getPackagingFee($country, $totalweight, $subtotal, $gst, $deliverymethod, $shippingbox, $quantity);

                }
                $deliverycost = $cart->shippingCost($country, $deliverymethod, $shippingbox, $quantity, $subtotal, $gst, $totalweight);
            }
        }

        $packingfee = number_format(($packingfee + $boxfees), 2);

        $discounttype = $disamount = 0;
        $discounttext = '';
        if (Session::has('couponcode')) {
            $discounttype = Session::get('discounttype');
            $disamount = Session::get('discount');
            $discounttext = Session::get('discounttext');
        }

        $discount = $cart->getDiscount($subtotal, $gst, $deliverycost, $packingfee, $discounttype, $disamount);

        $grandtotal = $cart->getGrandTotal($subtotal, $gst, $deliverycost, $packingfee, $discount, $fuelcharges, $handlingfee);

        $subtotal = str_replace(',', '', $subtotal);
        $deliverycost = str_replace(',', '', $deliverycost);
        $packingfee = str_replace(',', '', $packingfee);
        $gst = str_replace(',', '', $gst);
        $grandtotal = str_replace(',', '', $grandtotal);
        $discount = str_replace(',', '', $discount);

        $sesid = Session::get('_token');
        if (Session::has('cartdata')) {
            $cartdata = Session::get('cartdata');
        }

        $paymethod = PaymentMethods::where('id', '=', $paymentmethod)->first();
        if ($paymethod) {
            $paymethodname = $paymethod->payment_name;
        }

        if ($cartdata) {

            $couponid = 0;
            $userid = 0;
            if (Session::has('customer_id')) {
                $userid = Session::get('customer_id');
            } else {
                $chkcustomer = Customer::where('cust_email', '=', $billinginfo['bill_email'])->select('cust_id')->first();
                if (!$chkcustomer) {
                    Customer::insert(array('cust_firstname' => $billinginfo['bill_fname'], 'cust_lastname' => $billinginfo['bill_lname'], 'cust_email' => $billinginfo['bill_email'], 'cust_address1' => $billinginfo['bill_ads1'], 'cust_address2' => $billinginfo['bill_ads2'], 'cust_city' => $billinginfo['bill_city'], 'cust_state' => $billinginfo['bill_state'], 'cust_country' => $countryid, 'cust_zip' => $billinginfo['bill_zip'], 'cust_phone' => $billinginfo['bill_mobile'], 'cust_status' => 0));
                    $cust = Customer::where('cust_id', '>', '0')->orderBy('cust_id', 'desc')->select('cust_id')->first();
                    if ($cust) {
                        $userid = $cust->cust_id;
                    }
                } else {
                    $userid = $chkcustomer->cust_id;
                }
            }

            if (Session::has('couponcode')) {
                $couponcode = Session::get('couponcode');
                $coupondata = Couponcode::where('coupon_code', '=', $couponcode)->where('status', '=', '1')->first();
                if ($coupondata) {
                    $couponid = $coupondata->id;
                }
            }

            $fuelSettings = PaymentSettings::where('Id', '1')->select('fuelcharge_percentage')->first();
            $ordermaster = new OrderMaster;
            $ordermaster['user_id'] = $userid;
            $ordermaster['ship_method'] = Session::get('deliverymethod');
            $ordermaster['pay_method'] = $paymethodname;
            $ordermaster['shipping_cost'] = $deliverycost;
            $ordermaster['packaging_fee'] = $packingfee;
            $ordermaster['fuelcharge_percentage'] = $billinginfo['ship_country'] != 'SG' ? ($fuelSettings ? $fuelSettings->fuelcharge_percentage : 0) : 0;
            $ordermaster['fuelcharges'] = $fuelcharges;
            $ordermaster['handlingfee'] = $handlingfee;

            $ordermaster['tax_label'] = isset($cartItems['taxDetails']['taxLabel']) ? $cartItems['taxDetails']['taxLabel'] : '';
            $ordermaster['tax_percentage'] = isset($cartItems['taxDetails']['taxPercentage']) ? $cartItems['taxDetails']['taxPercentage'] : '';
            $ordermaster['tax_collected'] = $gst;
            $ordermaster['payable_amount'] = $grandtotal;
            $ordermaster['discount_amount'] = $discount;
            $ordermaster['discount_id'] = $couponid;
            $ordermaster['order_status'] = '0';
            $ordermaster['if_items_unavailabel'] = Session::get('if_unavailable');
            $ordermaster['delivery_instructions'] = Session::get('delivery_instructions');
            $ordermaster['bill_fname'] = $billinginfo['bill_fname'];
            $ordermaster['bill_lname'] = $billinginfo['bill_lname'];
            $ordermaster['bill_email'] = $billinginfo['bill_email'];
            $ordermaster['bill_mobile'] = $billinginfo['bill_mobile'];
            $ordermaster['bill_compname'] = $billinginfo['bill_compname'];
            $ordermaster['bill_ads1'] = $billinginfo['bill_ads1'];
            $ordermaster['bill_ads2'] = $billinginfo['bill_ads2'];
            $ordermaster['bill_city'] = $billinginfo['bill_city'];
            $ordermaster['bill_state'] = $billinginfo['bill_state'];
            $ordermaster['bill_zip'] = $billinginfo['bill_zip'];
            $ordermaster['bill_country'] = $billinginfo['bill_country'];
            $ordermaster['ship_fname'] = $billinginfo['ship_fname'];
            $ordermaster['ship_lname'] = $billinginfo['ship_lname'];
            $ordermaster['ship_email'] = $billinginfo['ship_email'];
            $ordermaster['ship_mobile'] = $billinginfo['ship_mobile'];
            $ordermaster['ship_ads1'] = $billinginfo['ship_ads1'];
            $ordermaster['ship_ads2'] = $billinginfo['ship_ads2'];
            $ordermaster['ship_country'] = $billinginfo['ship_country'];
            $ordermaster['ship_city'] = $billinginfo['ship_city'];
            $ordermaster['ship_state'] = $billinginfo['ship_state'];
            $ordermaster['ship_zip'] = $billinginfo['ship_zip'];

            if (Session::has('old_order_id')) {
                if (Session::get('old_order_id') > 0) {
                    $orderid = Session::get('old_order_id');
                    $orderincid = Session::get('old_order_id');
                    //OrderMaster::where('order_id', '=', $orderincid)->update(array('pay_method' => $paymethodname));
                    $today = date('Y-m-d H:i:s');
                    OrderMaster::where('order_id', '=', $orderincid)->update(array('pay_method' => $paymethodname, 'date_entered' => $today));
                }
            } else {
                $ordermaster->save();

                $order = OrderMaster::orderBy('order_id', 'desc')->select('order_id')->first();
                if ($order) {
                    $orderid = $order->order_id;
                    $orderincid = $order->order_id;
                }
            }

            foreach ($cartdata[$sesid] as $cart) {
                $orderdetails = new OrderDetails;
                $orderdetails['order_id'] = $orderid;
                $orderdetails['prod_id'] = $cart['productId'];
                $orderdetails['prod_name'] = $cart['productName'];
                $orderdetails['prod_quantity'] = $cart['qty'];
                $orderdetails['prod_unit_price'] = $cart['price'];
                $orderdetails['prod_option'] = $cart['productoption'];
                //$orderdetails['option_id'] = $cart['option_id'];
                $orderdetails['Weight'] = $cart['weight'];
                $orderdetails['prod_code'] = $cart['productcode'];
                if (!Session::has('old_order_id')) {
                    $orderdetails->save();
                }

                $desc = $image = '';

                $qty = 0;
                $product = Product::where('Id', '=', $cart['productId'])->select('Quantity', 'Image', 'EnShortDesc')->first();
                if ($product->Quantity > $cart['qty']) {
                    $qty = $product->Quantity - $cart['qty'];
                    $desc = $product->EnShortDesc;
                    if ($desc == '') {
                        $desc = $cart['productName'];
                    }
                    $image = url('/') . '/uploads/product/' . $product->Image;
                }
                Product::where('Id', '=', $cart['productId'])->update(array('Quantity' => $qty));

                $sku = $ean = "";
                if ($cart['productcode']) {
                    $sku = $cart['productcode'];
                    $ean = $cart['productcode'];
                }

            }

            $countrydata = Country::where('countrycode', '=', $billinginfo['bill_country'])->select('countryid', 'countryname')->first();
            if ($countrydata) {
                $countryid = $countrydata->countryid;
                $billcountryname = $countrydata->countryname;
            }

            $shipcountrydata = Country::where('countrycode', '=', $billinginfo['ship_country'])->select('countryid', 'countryname')->first();
            if ($shipcountrydata) {
                $shipcountryname = $shipcountrydata->countryname;
            }

            Customer::where('cust_id', '=', $userid)->update(array('cust_firstname' => $billinginfo['bill_fname'], 'cust_lastname' => $billinginfo['bill_lname'], 'cust_address1' => $billinginfo['bill_ads1'], 'cust_address2' => $billinginfo['bill_ads2'], 'cust_city' => $billinginfo['bill_city'], 'cust_state' => $billinginfo['bill_state'], 'cust_country' => $countryid, 'cust_zip' => $billinginfo['bill_zip'], 'cust_phone' => $billinginfo['bill_mobile']));

            $setting = Settings::where('id', '=', '1')->first();
            if ($setting) {
                $companyname = $setting->company_name;
                $adminemail = $setting->admin_email;
                $ccemail = $setting->cc_email;
            }

            DB::table('cart_details')->where('user_id', '=', $userid)->delete();

            $currency = 'SGD';
            $paysettings = PaymentSettings::where('id', '=', '1')->select('currency_type')->first();
            if ($paysettings) {
                $currency = $paysettings->currency_type;
            }

            $paymode = 'live';
            $payenv = 'production';
            $paymentmethod = PaymentMethods::where('id', '=', '6')->orWhere('payment_name', 'LIKE', '%Paypal')->first();

            if ($paymentmethod) {
                $paymode = $paymentmethod->payment_mode;
                if ($paymode == 'live') {
                    $apikey = $paymentmethod->api_key;
                    $apisignature = $paymentmethod->api_signature;
                    $paymenturl = $paymentmethod->live_url;
                    $payenv = 'production';
                } else {
                    $apikey = $paymentmethod->test_api_key;
                    $apisignature = $paymentmethod->test_api_signature;
                    $paymenturl = $paymentmethod->testing_url;
                    $payenv = 'sandbox';
                }
            }

            return view('public/Payment.paypal', compact('cartdata', 'sesid', 'subtotal', 'gst', 'grandtotal', 'taxtitle', 'apikey', 'apisignature', 'paymenturl', 'billinginfo', 'deliverycost', 'deliverytype', 'packingfee', 'discounttext', 'discount', 'currency', 'payenv', 'orderincid', 'taxLabelOnly', 'handlingfee', 'fuelcharges'));
        }
    }

    public function atome()
    {

        $paymenturl = $username = $password = $paymenturl = $apikey = $apisignature = $billcountryname = $shipcountryname = '';
        $billinginfo = [];
        $paymethodname = $deliverytype = $emailsubject = $emailcontent = $companyname = $adminemail = $ccemail = $taxtitle = '';
        $paymentmethod = $packingfee = $deliverycost = $deliverymethod = 0;
        $sesid = Session::get('_token');
        if (Session::has('cartdata')) {
            $cartdata = Session::get('cartdata');
        }

        if (Session::has('paymentmethod')) {
            $paymentmethod = Session::get('paymentmethod');
        }

        $billinginfo = Session::get('billinginfo');
        $cartItems = $this->cartServices->cartItems($billinginfo['ship_country'] ?? null);
        $fuelcharges = isset($cartItems['fuelcharges']) ? $cartItems['fuelcharges'] : 0.00;
        $handlingfee = isset($cartItems['handlingfee']) ? $cartItems['handlingfee'] : 0.00;

        $cart = new Cart();
        $subtotal = $cart->getSubTotal();

        $orderid = $countryid = $orderincid = 0;
        $country = '';

        $country = $billinginfo['ship_country'];

        if (Session::has('deliverymethod')) {
            $deliverymethod = Session::get('deliverymethod');
        }

        // Shipping Cost

        $deliverytype = $cart->getDeliveryMethod($deliverymethod);

        $totalweight = 0;

        $taxes = $cart->getGST($subtotal, $country);

        if ($country != '') {
            $taxvals = @explode("|", $taxes);
            $taxtitle = $taxvals[0];
            $gst = $taxvals[1];
        } else {
            $gst = $taxes;
        }

        $gst = $cartItems['taxDetails']['taxTotal'];

        $settings = PaymentSettings::where('Id', '=', '1')->select('min_package_fee')->first();
        if ($country != 'SG') {
            $packingfee = $settings->min_package_fee;
        }
        $boxfees = 0;

        foreach ($cartdata as $key => $val) {
            if (is_array($val)) {
                $x = 0;
                foreach ($val as $datakey => $dataval) {
                    $totalweight = $totalweight + $dataval['weight'];
                    $shippingbox = $dataval['shippingbox'];
                    $quantity = $dataval['qty'];
                    $boxfees = $cart->getPackagingFee($country, $totalweight, $subtotal, $gst, $deliverymethod, $shippingbox, $quantity);
                }
                $deliverycost = $cart->shippingCost($country, $deliverymethod, $shippingbox, $quantity, $subtotal, $gst, $totalweight);
            }
        }

        $packingfee = number_format(($packingfee + $boxfees), 2);

        $discounttype = $disamount = 0;
        $discounttext = '';
        if (Session::has('couponcode')) {
            $discounttype = Session::get('discounttype');
            $disamount = Session::get('discount');
            $discounttext = Session::get('discounttext');
        }

        //$discount = $cart->getDiscount($subtotal, $gst, $deliverycost, $packingfee, $discounttype, $disamount);
        $discount = $cartItems['discountDetails']['discountTotal'];
        //$grandtotal = $cart->getGrandTotal($subtotal, $gst, $deliverycost, $packingfee, $discount);
        $grandtotal = $cartItems['grandTotal'];

        $subtotal = str_replace(',', '', $subtotal);
        $deliverycost = str_replace(',', '', $deliverycost);
        $packingfee = str_replace(',', '', $packingfee);
        $gst = str_replace(',', '', $gst);
        $grandtotal = str_replace(',', '', $grandtotal);
        $discount = str_replace(',', '', $discount);

        $sesid = Session::get('_token');
        if (Session::has('cartdata')) {
            $cartdata = Session::get('cartdata');
        }

        $paymethod = PaymentMethods::where('id', '=', $paymentmethod)->first();
        if ($paymethod) {
            $paymethodname = $paymethod->payment_name;
        }

        if ($cartdata) {

            $couponid = 0;
            $userid = 0;
            if (Session::has('customer_id')) {
                $userid = Session::get('customer_id');
            } else {
                $chkcustomer = Customer::where('cust_email', '=', $billinginfo['bill_email'])->select('cust_id')->first();
                if (!$chkcustomer) {
                    Customer::insert(array('cust_firstname' => $billinginfo['bill_fname'], 'cust_lastname' => $billinginfo['bill_lname'], 'cust_email' => $billinginfo['bill_email'], 'cust_address1' => $billinginfo['bill_ads1'], 'cust_address2' => $billinginfo['bill_ads2'], 'cust_city' => $billinginfo['bill_city'], 'cust_state' => $billinginfo['bill_state'], 'cust_country' => $countryid, 'cust_zip' => $billinginfo['bill_zip'], 'cust_phone' => $billinginfo['bill_mobile'], 'cust_status' => 0));
                    $cust = Customer::where('cust_id', '>', '0')->orderBy('cust_id', 'desc')->select('cust_id')->first();
                    if ($cust) {
                        $userid = $cust->cust_id;
                    }
                } else {
                    $userid = $chkcustomer->cust_id;
                }
            }

            if (Session::has('couponcode')) {
                $couponcode = Session::get('couponcode');
                $coupondata = Couponcode::where('coupon_code', '=', $couponcode)->where('status', '=', '1')->first();
                if ($coupondata) {
                    $couponid = $coupondata->id;
                }
            }
            $fuelSettings = PaymentSettings::where('Id', '1')->select('fuelcharge_percentage')->first();
            $ordermaster = new OrderMaster;
            $ordermaster['user_id'] = $userid;
            $ordermaster['ship_method'] = Session::get('deliverymethod');
            $ordermaster['pay_method'] = $paymethodname;
            $ordermaster['shipping_cost'] = $deliverycost;
            $ordermaster['packaging_fee'] = $packingfee;
            $ordermaster['fuelcharge_percentage'] = $billinginfo['ship_country'] != 'SG' ? ($fuelSettings ? $fuelSettings->fuelcharge_percentage : 0) : 0;
            $ordermaster['fuelcharges'] = $fuelcharges;
            $ordermaster['handlingfee'] = $handlingfee;

            $ordermaster['tax_label'] = isset($cartItems['taxDetails']['taxLabel']) ? $cartItems['taxDetails']['taxLabel'] : '';
            $ordermaster['tax_percentage'] = isset($cartItems['taxDetails']['taxPercentage']) ? $cartItems['taxDetails']['taxPercentage'] : '';
            $ordermaster['tax_collected'] = $gst;
            $ordermaster['payable_amount'] = $grandtotal;
            $ordermaster['discount_amount'] = $discount;
            $ordermaster['discount_id'] = $couponid;
            $ordermaster['order_status'] = '0';
            $ordermaster['if_items_unavailabel'] = Session::get('if_unavailable');
            $ordermaster['delivery_instructions'] = Session::get('delivery_instructions');
            $ordermaster['bill_fname'] = $billinginfo['bill_fname'];
            $ordermaster['bill_lname'] = $billinginfo['bill_lname'];
            $ordermaster['bill_email'] = $billinginfo['bill_email'];
            $ordermaster['bill_mobile'] = $billinginfo['bill_mobile'];
            $ordermaster['bill_compname'] = $billinginfo['bill_compname'];
            $ordermaster['bill_ads1'] = $billinginfo['bill_ads1'];
            $ordermaster['bill_ads2'] = $billinginfo['bill_ads2'];
            $ordermaster['bill_city'] = $billinginfo['bill_city'];
            $ordermaster['bill_state'] = $billinginfo['bill_state'];
            $ordermaster['bill_zip'] = $billinginfo['bill_zip'];
            $ordermaster['bill_country'] = $billinginfo['bill_country'];
            $ordermaster['ship_fname'] = $billinginfo['ship_fname'];
            $ordermaster['ship_lname'] = $billinginfo['ship_lname'];
            $ordermaster['ship_email'] = $billinginfo['ship_email'];
            $ordermaster['ship_mobile'] = $billinginfo['ship_mobile'];
            $ordermaster['ship_ads1'] = $billinginfo['ship_ads1'];
            $ordermaster['ship_ads2'] = $billinginfo['ship_ads2'];
            $ordermaster['ship_country'] = $billinginfo['ship_country'];
            $ordermaster['ship_city'] = $billinginfo['ship_city'];
            $ordermaster['ship_state'] = $billinginfo['ship_state'];
            $ordermaster['ship_zip'] = $billinginfo['ship_zip'];

            if (Session::has('old_order_id')) {
                if (Session::get('old_order_id') > 0) {
                    $orderid = Session::get('old_order_id');
                    $orderincid = Session::get('old_order_id');
                    //OrderMaster::where('order_id', '=', $orderincid)->update(array('pay_method' => $paymethodname));
                    OrderMaster::where('order_id', '=', $orderincid)->update(array('pay_method' => $paymethodname, 'date_entered' => date('Y-m-d H:i:s')));
                }
            } else {
                $ordermaster->save();

                $order = OrderMaster::orderBy('order_id', 'desc')->select('order_id')->first();
                if ($order) {
                    $orderid = $order->order_id;
                    $orderincid = $order->order_id;
                }
            }
            $atomeitems = [];

            foreach ($cartdata[$sesid] as $cart) {
                $orderdetails = new OrderDetails;
                $orderdetails['order_id'] = $orderid;
                $orderdetails['prod_id'] = $cart['productId'];
                $orderdetails['prod_name'] = $cart['productName'];
                $orderdetails['prod_quantity'] = $cart['qty'];
                $orderdetails['prod_unit_price'] = $cart['price'];
                $orderdetails['prod_option'] = $cart['productoption'];
                //$orderdetails['option_id'] = $cart['option_id'];
                $orderdetails['Weight'] = $cart['weight'];
                $orderdetails['prod_code'] = $cart['productcode'];
                if (!Session::has('old_order_id')) {
                    $orderdetails->save();
                }

                $desc = $image = '';

                $qty = 0;
                $product = Product::where('Id', '=', $cart['productId'])->select('Quantity', 'Image', 'EnShortDesc')->first();
                if ($product->Quantity > $cart['qty']) {
                    $qty = $product->Quantity - $cart['qty'];
                    $desc = $product->EnShortDesc;
                    if ($desc == '') {
                        $desc = $cart['productName'];
                    }
                    $image = url('/') . '/uploads/product/' . $product->Image;
                }
                Product::where('Id', '=', $cart['productId'])->update(array('Quantity' => $qty));

                $productname = $cart['productName'];

                $sku = $ean = "";
                if ($cart['productcode']) {
                    $sku = $cart['productcode'];
                    $ean = $cart['productcode'];
                }

                //$hoolahitems = array("name" => $productname, "description" => $desc, "sku" => $sku, "ean" => $ean, "quantity" => $cart['qty'], "originalPrice" => $cart['price'], "price" => $cart['price'], "images" => array(array("imageLocation" => $image)), "taxAmount" => "0", "discount" => "0", "detailDescription" => $desc);

                $atomeitems = array("itemId" => $cart['productId'], "name" => $productname, "price" => $cart['price'] * 100, "quantity" => $cart['qty'], "originalPrice" => $cart['price'] * 100);

            }

            $countrydata = Country::where('countrycode', '=', $billinginfo['bill_country'])->select('countryid', 'countryname')->first();
            if ($countrydata) {
                $countryid = $countrydata->countryid;
                $billcountryname = $countrydata->countryname;
            }

            $shipcountrydata = Country::where('countrycode', '=', $billinginfo['ship_country'])->select('countryid', 'countryname')->first();
            if ($shipcountrydata) {
                $shipcountryname = $shipcountrydata->countryname;
            }

            Customer::where('cust_id', '=', $userid)->update(array('cust_firstname' => $billinginfo['bill_fname'], 'cust_lastname' => $billinginfo['bill_lname'], 'cust_address1' => $billinginfo['bill_ads1'], 'cust_address2' => $billinginfo['bill_ads2'], 'cust_city' => $billinginfo['bill_city'], 'cust_state' => $billinginfo['bill_state'], 'cust_country' => $countryid, 'cust_zip' => $billinginfo['bill_zip'], 'cust_phone' => $billinginfo['bill_mobile']));

            DB::table('cart_details')->where('user_id', '=', $userid)->delete();

            $currency = 'SGD';
            $paysettings = PaymentSettings::where('id', '=', '1')->select('currency_type')->first();
            if ($paysettings) {
                $currency = $paysettings->currency_type;
            }

            $paymode = 'live';
            $paymentmethod = PaymentMethods::where('id', '=', '7')->orWhere('payment_name', 'LIKE', '%Atome')->first();

            if ($paymentmethod) {
                $paymode = $paymentmethod->payment_mode;
                if ($paymode == 'live') {
                    $apikey = $paymentmethod->api_key;
                    $apisignature = $paymentmethod->api_signature;
                    $paymenturl = $paymentmethod->live_url;
                } else {
                    $apikey = $paymentmethod->test_api_key;
                    $apisignature = $paymentmethod->test_api_signature;
                    $paymenturl = $paymentmethod->testing_url;
                }
            }

            $url = $paymenturl . "/auth";
            $ch = curl_init($url);
            $auth = base64_encode($apikey . ':' . $apisignature);
            $header_str = "Authorization: Basic " . $auth;
            $payload = json_encode(array("callbackUrl" => "https://hardwarecity.asia/atomecallback", "countryCode" => "SG"));
            curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);
            curl_setopt($ch, CURLOPT_HTTPHEADER, array(
                "Content-Type: application/json",
                "Accept: application/json",
                $header_str,
            ));
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            $result = curl_exec($ch);
            curl_close($ch);

            if ($result) {
                $response = json_decode($result);

                print_r($response);

                $authtoken = $response->code;

                if ($authtoken == 'SUCCESS') {

                    $url = $paymenturl . "/payments";

                    $billmobile = $billinginfo['bill_mobile'];

                    if (strlen($billmobile) > 8) {
                        $billmobile = substr($billmobile, -8);
                    }

                    $billadd2 = $shipadd2 = "";
                    if ($billinginfo['bill_ads2']) {
                        $billadd2 = $billinginfo['bill_ads2'];
                    }

                    if ($billinginfo['ship_ads2']) {
                        $shipadd2 = $billinginfo['ship_ads2'];
                    }

                    $closeurl = url('/') . '/cancelpayment?orderid=' . $orderincid;
                    $returnurl = url('/') . '/success?orderid=' . $orderincid;

                    $ch = curl_init($url);
                    # Setup request to send json via POST.

                    $payload = json_encode(array(
                        "referenceId" => $orderincid,
                        "currency" => $currency,
                        "amount" => $grandtotal * 100,
                        "callbackUrl" => url('/') . "/atomecallback",
                        "paymentResultUrl" => $returnurl,
                        "paymentCancelUrl" => $closeurl,
                        "merchantReferenceId" => $orderincid,
                        "customerInfo" => [
                            "mobileNumber" => $billmobile,
                            "fullName" => $billinginfo['bill_fname'] . ' ' . $billinginfo['bill_lname'],
                            "email" => $billinginfo['bill_email'],
                        ],
                        "shippingAddress" => [
                            "countryCode" => $billinginfo['ship_country'],
                            "lines" => [
                                $billinginfo['ship_ads1'],
                            ],
                            "postCode" => $billinginfo['ship_zip'],
                        ],
                        "billingAddress" => [
                            "countryCode" => $billinginfo['bill_country'],
                            "lines" => [
                                $billinginfo['bill_ads1'],
                            ],
                            "postCode" => $billinginfo['bill_zip'],
                        ],
                        "taxAmount" => $gst * 100,
                        "shippingAmount" => $deliverycost * 100,
                        "originalAmount" => $grandtotal * 100,
                        "items" => [$atomeitems],
                    ));

                    $url = $paymenturl . '/payments';
                    $ch = curl_init($url);
                    # Setup request to send json via POST.

                    $auth = base64_encode($apikey . ':' . $apisignature);
                    $header_str = "Authorization: Basic " . $auth;
                    curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);
                    curl_setopt($ch, CURLOPT_HTTPHEADER, array(
                        "Content-Type: application/json",
                        "Accept: application/json",
                        $header_str,
                    ));
                    # Return response instead of printing.
                    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                    # Send request.
                    $result = curl_exec($ch);
                    curl_close($ch);

                    if ($result) {
                        $response = json_decode($result);
                        print_r($response);

                        $apiPaymentUrl = $response->appPaymentUrl;

                        if ($apiPaymentUrl) {
                            //OrderMaster::where('order_id', '=', $orderincid)->update(array('trans_id' => $orderContentToken));

                            if ($discounttext != '' && $discount != 0) {
                                CouponCodeUsage::insert(array('coupon_id' => $couponid, 'customer_id' => $userid, 'order_id' => $orderincid));
                            }
                            header('location:' . $apiPaymentUrl);
                            exit;
                        }
                    }
                }
            }

            Session::forget('cartdata');
            Session::forget('deliverymethod');
            Session::forget('if_unavailable');
            Session::forget('billinginfo');
            Session::forget('paymentmethod');
            Session::forget('discount');
            Session::forget('discounttext');
            Session::forget('couponcode');
            Session::forget('discounttype');
            Session::forget('old_order_id');
        }

    }

    public function commonCurl($data)
    {
        $cURLConnection = curl_init();
        curl_setopt($cURLConnection, CURLOPT_HTTPHEADER, $data['header']);
        curl_setopt($cURLConnection, CURLOPT_POST, 1);
        curl_setopt($cURLConnection, CURLOPT_URL, $data['url']);
        curl_setopt($cURLConnection, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($cURLConnection, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($cURLConnection, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($cURLConnection, CURLOPT_POSTFIELDS, json_encode($data['payload']));
        return curl_exec($cURLConnection);
    }

    public function success(Request $request)
    {
        Session::forget('cartdata');
        Session::forget('deliverymethod');
        Session::forget('if_unavailable');
        Session::forget('billinginfo');
        Session::forget('paymentmethod');
        Session::forget('discount');
        Session::forget('discounttext');
        Session::forget('couponcode');
        Session::forget('discounttype');
        Session::forget('old_order_id');
        $orderid = $request->orderid;

        Paymentlog::where('order_id', $orderid)->update([
            'received_values' => serialize($request->all()),
            'status' => $request->has('error') ? 'failure' : 'success',
        ]);

        if ($request->has('error')) {
            Log::alert('ORDER ID : ' . $orderid . ' grabpay payment return error.{' . $request->error . '}');
            return redirect('cancelpayment', ['orderid' => $orderid]);
        }

        if (!$request->has('error')) {
            if ($request->has('code') && isset($_COOKIE['code_verifier'])) {
                //Step 1 - get token
                try {
                    $clientId = $clientSec = '';
                    $paymentmethod = PaymentMethods::where('id', '5')->first();
                    if ($paymentmethod) {
                        $clientId = $paymentmethod->test_api_signature;
                        $clientSec = $paymentmethod->api_signature;
                    }
                    $oAuthTokenResult = $this->commonCurl([
                        'url' => 'https://partner-api.grab.com/grabid/v1/oauth2/token',
                        'header' => [
                            'Content-Type: application/json',
                        ],
                        'payload' => [
                            'code' => $request->code,
                            'client_id' => $clientId,
                            'grant_type' => 'authorization_code.',
                            'redirect_uri' => url('success?grabpay=get_oauth_token'),
                            'code_verifier' => $_COOKIE['code_verifier'] ?? '',
                            'client_secret' => $clientSec,
                        ],
                    ]);

                    if ($oAuthTokenResult == false) {
                        Log::alert('ORDER ID : ' . $orderid . ' oauth token curl error.');
                        return redirect('cancelpayment', ['orderid' => $orderid]);
                    }

                    Log::alert('ORDER ID : ' . $orderid . ' oauth api response => '.json_encode($oAuthTokenResult));
                    $resultObj = json_decode($oAuthTokenResult);

                    //Step 2 - Complete charge
                    if (isset($resultObj->access_token)) {
                        if (isset($_COOKIE['partnerTxID'])) {
                            $gpay = new GrabPayFunctions;
                            $dateTime = gmdate("D, d M Y H:i:s \G\M\T");
                            $completeGrabpayPayment = $this->commonCurl([
                                'url' => 'https://partner-api.grab.com/grabpay/partner/v2/charge/complete',
                                'header' => [
                                    'Content-Type: application/json',
                                    'Authorization: ' . $resultObj->access_token,
                                    'X-GID-AUX-POP: ' . $gpay->generatePopSignature($clientSec, $resultObj->access_token, $dateTime),
                                    'Date: ' . $dateTime,
                                ],
                                'payload' => [
                                    'partnerTxID' => $_COOKIE['partnerTxID'],
                                ],
                            ]);

                            if ($completeGrabpayPayment == false) {
                                Log::alert('ORDER ID : ' . $orderid . ' grabpay complete api curl error.');
                                return redirect('cancelpayment', ['orderid' => $orderid]);
                            }

                            $completeResultObj = json_decode($completeGrabpayPayment);

                            if ((isset($completeResultObj->txStatus) && $completeResultObj->txStatus != 'success') ||
                                isset($completeResultObj->code)) {
                                Paymentlog::updateOrCreate(
                                    ['order_id' => $orderid],
                                    [
                                        'received_values' => serialize($completeResultObj),
                                        'status' => 'failure',
                                    ]);
                                Log::alert('ORDER ID : ' . $orderid . ' grabpay complete api failed.');
                                return redirect('cancelpayment', ['orderid' => $orderid]);
                            }

                        } else {
                            Log::alert('ORDER ID : ' . $orderid . ' grabpay complete api partnerTxID cookie value is not available.');
                            return redirect('cancelpayment', ['orderid' => $orderid]);
                        }
                    } else {
                        Log::alert('ORDER ID : ' . $orderid . ' grabpay oauth token api not return access_token key.');
                        return redirect('cancelpayment', ['orderid' => $orderid]);
                    }

                } catch (\Throwable $th) {
                    Log::alert('ORDER ID : ' . $orderid . ' grabpay trycatch hit. so payment is not completed.');
                    return redirect('cancelpayment', ['orderid' => $orderid]);
                }
            }

            OrderMaster::where('order_id', $orderid)->update(['order_status' => '1']);
            $order = OrderMaster::where('order_id', $orderid)->select('order_type')->first();
            if ($order) {
                if ($order->order_type == 2) {
                    OrderMaster::where('order_id', '=', $orderid)->update(array('quotation_status' => '1'));
                }

                $order = OrderMaster::where('order_id', '=', $orderid)->first();
                $orderdetails = OrderDetails::where('order_id', '=', $orderid)->get();

                $logo = url('/') . '/front/img/logo.png';
                $logo = '<img src="' . $logo . '">';

                $itemdetails = '<table style="width:100%;" border="0" cellpadding="5" cellspacing="0">';
                $itemdetails .= '<tr><th width="40%" style="text-align:left; border:1px solid #dee2e6;">Item</th><th width="15%" style="text-align:center; border:1px solid #dee2e6;">Quantity</th><th width="25%" style="text-align:right; border:1px solid #dee2e6;">Price</th><th width="20%" style="text-align:right; border:1px solid #dee2e6;">Total</th></tr>';

                $orderid = $order->order_id;
                if (strlen($orderid) == 3) {
                    $orderid = date('Ymd', strtotime($order->date_entered)) . '0' . $orderid;
                } elseif (strlen($orderid) == 2) {
                    $orderid = date('Ymd', strtotime($order->date_entered)) . '00' . $orderid;
                } elseif (strlen($orderid) == 1) {
                    $orderid = date('Ymd', strtotime($order->date_entered)) . '000' . $orderid;
                } else {
                    $orderid = date('Ymd', strtotime($order->date_entered)) . $orderid;
                }

                $tax = '';
                $taxes = Country::where('countrycode', '=', $order->ship_country)->first();
                if ($taxes) {
                    $tax = $taxes->taxtitle . ' - ' . $taxes->taxpercentage;
                }

                if ($orderdetails) {
                    foreach ($orderdetails as $orderdetail) {
                        $itemdetails .= '<tr><td style="border:1px solid #dee2e6;">' . $orderdetail->prod_name;
                        if ($orderdetail->prod_option != '') {
                            $itemdetails .= '<span style="color: #6c757d !important">Option: ' . $orderdetail->prod_option . '</span>';
                        }
                        $itemdetails .= '</td><td style="text-align:center; border:1px solid #dee2e6;">' . $orderdetail->prod_quantity . '</td>';
                        $itemdetails .= '<td style="text-align:right; border:1px solid #dee2e6;">S$' . $orderdetail->prod_unit_price . '</td>';
                        $itemdetails .= '<td style="text-align:right; border:1px solid #dee2e6;">S$' . number_format(($orderdetail->prod_quantity * $orderdetail->prod_unit_price), 2) . '</td></tr>';
                    }
                }

                $itemdetails .= '<tr><td colspan="4" style="border:1px solid #dee2e6;">&nbsp;</td></tr>';
                $itemdetails .= '<tr><td colspan="2" style="border:1px solid #dee2e6;">&nbsp;</td><td style="border:1px solid #dee2e6;">Sub Total</td><td style="text-align:right; border:1px solid #dee2e6;">S$' . number_format($order->payable_amount - ($order->shipping_cost + $order->packaging_fee + $order->tax_collected), 2) . '</td></tr>';
                $itemdetails .= '<tr><td colspan="2" style="border:1px solid #dee2e6;">&nbsp;</td><td style="border:1px solid #dee2e6;">Tax (' . $tax . '%)</td><td style="text-align:right; border:1px solid #dee2e6;">S$' . number_format($order->tax_collected, 2) . '</td></tr>';
                $itemdetails .= '<tr><td colspan="2" style="border:1px solid #dee2e6;">&nbsp;</td><td style="border:1px solid #dee2e6;">Shipping</td><td style="text-align:right; border:1px solid #dee2e6;">S$' . number_format($order->shipping_cost, 2) . '</td></tr>';
                $itemdetails .= '<tr><td colspan="2" style="border:1px solid #dee2e6;">&nbsp;</td><td style="border:1px solid #dee2e6;">Packaging Fee</td><td style="text-align:right; border:1px solid #dee2e6;">S$' . number_format($order->packaging_fee, 2) . '</td></tr>';
                if ($order->discount_amount != '0.00') {
                    $itemdetails .= '<tr><td colspan="2" style="border:1px solid #dee2e6;">&nbsp;</td><td style="border:1px solid #dee2e6;">Discount</td><td style="text-align:right; border:1px solid #dee2e6;">S$' . number_format($order->discount_amount, 2) . '</td></tr>';
                }
                $itemdetails .= '<tr><td colspan="2" style="border:1px solid #dee2e6;">&nbsp;</td><td style="border:1px solid #dee2e6;"><b>Grand Total</b></td><td style="text-align:right; border:1px solid #dee2e6;"><b>S$' . number_format($order->payable_amount, 2) . '</b></td></tr>';

                $emailsubject = $emailcontent = '';

                $companyname = $adminemail = $ccemail = $customername = $customeremail = $companydetails = $statusdetails = '';
                $shippinginfo = $billinginfo = '';
                $shipmethod = '';

                $setting = Settings::where('id', '=', '1')->first();
                if ($setting) {
                    $companyname = $setting->company_name;
                    $adminemail = $setting->admin_email;
                    $ccemail = $setting->cc_email;

                    $companydetails .= '<table><tr><td>' . nl2br($setting->company_address) . '</td></tr>';
                    $companydetails .= '<tr><td>Fax: ' . $setting->company_fax . '</td></tr>';
                    $companydetails .= '<tr><td>GST No: ' . $setting->GST_res_no . '</td></tr></table>';

                    $statusdetails .= '<table><tr><td style="vertical-align:top">Date: ' . date("d/m/Y", strtotime($order->date_entered)) . '</td></tr>';

                    if ($order->order_status == 0) {
                        $statusdetails .= '<tr><td>Status: Payment Pending</td></tr>';
                    } elseif ($order->order_status == 1) {
                        $statusdetails .= '<tr><td>Status: Paid, Shipping Pending</td></tr>';
                    }

                    $shipping = ShippingMethods::where('Id', '=', $order->ship_method)->select('EnName')->first();
                    if ($shipping) {
                        $statusdetails .= '<tr><td>Delivery Method: ' . $shipping->EnName . '</td></tr>';
                        $shipmethod = $shipping->EnName;
                    }

                    if ($order->if_items_unavailabel == 1) {
                        $statusdetails .= '<tr><td>If item(s) unavailable: Call Me</td></tr>';
                    } elseif ($order->if_items_unavailabel == 2) {
                        $statusdetails .= '<tr><td>If item(s) unavailable: Do Not Replace</td></tr>';
                    } else {
                        $statusdetails .= '<tr><td>If item(s) unavailable: Replace</td></tr>';
                    }

                    if (stripos($shipmethod, 'Self Collect') !== false) {
                        $shippinginfo .= '<table><tr><td>' . $shipmethod . '</td></tr></table>';
                    } else {
                        $shippinginfo .= '<table><tr><td>' . $order->ship_fname . ' ' . $order->ship_lname . '</td></tr>';
                        $shippinginfo .= '<tr><td>' . $order->ship_ads1 . '</td></tr>';
                        if ($order->ship_ads2) {
                            $shippinginfo .= '<tr><td>' . $order->ship_ads2 . '</td></tr>';
                        }
                        $shippinginfo .= '<tr><td>' . $order->ship_city . '</td></tr>';
                        $shippinginfo .= '<tr><td>' . $order->ship_state . ' - ' . $order->ship_zip . '</td></tr>';
                        $shippinginfo .= '<tr><td>Email: ' . $order->ship_email . '</td></tr>';
                        $shippinginfo .= '<tr><td>Mobile: ' . $order->ship_mobile . '</td></tr>';
                        $shippinginfo .= '</table>';
                    }
                    $billinginfo .= '<table><tr><td>' . $order->bill_fname . ' ' . $order->bill_lname . '</td></tr>';
                    $billinginfo .= '<tr><td>' . $order->bill_ads1 . '</td></tr>';
                    if ($order->bill_ads2) {
                        $billinginfo .= '<tr><td>' . $order->bill_ads2 . '</td></tr>';
                    }
                    $billinginfo .= '<tr><td>' . $order->bill_city . '</td></tr>';
                    $billinginfo .= '<tr><td>' . $order->bill_state . ' - ' . $order->bill_zip . '</td></tr>';
                    $billinginfo .= '<tr><td>Email: ' . $order->bill_email . '</td></tr>';
                    $billinginfo .= '<tr><td>Mobile: ' . $order->bill_mobile . '</td></tr>';
                    $billinginfo .= '</table>';

                    $emailtemplate = EmailTemplate::where('template_type', '=', '2')->where('status', '=', '1')->first();
                    if ($emailtemplate) {
                        $emailsubject = $emailtemplate->subject;
                        $emailcontent = $emailtemplate->content;
                    }

                    $customer = Customer::where('cust_id', '=', $order->user_id)->first();
                    if ($customer) {
                        $customername = $customer->cust_firstname . ' ' . $customer->cust_lastname;
                        $customeremail = $customer->cust_email;
                    }

                    $emailsubject = str_replace('{companyname}', $companyname, $emailsubject);
                    $emailsubject = str_replace('{status}', 'Confirmed', $emailsubject);
                    $emailcontent = str_replace('{companyname}', $companyname, $emailcontent);
                    $emailcontent = str_replace('{customername}', $customername, $emailcontent);
                    $emailcontent = str_replace('{logo}', $logo, $emailcontent);
                    $emailcontent = str_replace('{email}', $adminemail, $emailcontent);
                    $emailcontent = str_replace('{orderid}', $orderid, $emailcontent);
                    $emailcontent = str_replace('{companyaddress}', $companydetails, $emailcontent);
                    $emailcontent = str_replace('{statusdetails}', $statusdetails, $emailcontent);
                    $emailcontent = str_replace('{billinginfo}', $billinginfo, $emailcontent);
                    $emailcontent = str_replace('{shippinginfo}', $shippinginfo, $emailcontent);
                    $emailcontent = str_replace('{paymentmethod}', $order->pay_method, $emailcontent);
                    $emailcontent = str_replace('{orderdetails}', $itemdetails, $emailcontent);

                    $headers = 'From: ' . $companyname . ' ' . $adminemail . '' . "\r\n";
                    $headers .= 'Reply-To: ' . $adminemail . "\r\n";
                    $headers .= 'X-Mailer: PHP/' . phpversion();
                    $headers .= "MIME-Version: 1.0\r\n";
                    $headers .= "Content-type: text/html; charset=iso-8859-1\r\n";

                    #@mail($customeremail, $emailsubject, $emailcontent, $headers);
                    #@mail($adminemail, $emailsubject, $emailcontent, $headers);

                    Mail::send([], [], function ($message) use ($adminemail, $customeremail, $emailsubject, $emailcontent) {
                        $message->to([$customeremail, $adminemail])
                            ->subject($emailsubject)
                            ->from(env('MAIL_USERNAME'), env('APP_NAME'))
                            ->setBody($emailcontent, 'text/html');
                    });
                }
            }
            return view('public/Payment.success');
        }
    }

    public function success22(Request $request)
    {
        Session::forget('cartdata');
        Session::forget('deliverymethod');
        Session::forget('if_unavailable');
        Session::forget('billinginfo');
        Session::forget('paymentmethod');
        Session::forget('discount');
        Session::forget('discounttext');
        Session::forget('couponcode');
        Session::forget('discounttype');
        Session::forget('old_order_id');
        $orderid = $request->orderid;
        OrderMaster::where('order_id', '=', $orderid)->update(array('order_status' => '1'));
        $order = OrderMaster::where('order_id', '=', $orderid)->first();
        $order = OrderMaster::where('order_id', '=', $orderid)->first();
        if ($order) {
            if ($order->order_type == 2) {
                OrderMaster::where('order_id', '=', $orderid)->update(array('quotation_status' => '1'));
            }
            $orderdetails = OrderDetails::where('order_id', '=', $orderid)->get();

            $logo = url('/') . '/front/img/logo.png';
            $logo = '<img src="' . $logo . '">';

            $itemdetails = '<table style="width:100%;" border="0" cellpadding="5" cellspacing="0">';
            $itemdetails .= '<tr><th width="40%" style="text-align:left; border:1px solid #dee2e6;">Item</th><th width="15%" style="text-align:center; border:1px solid #dee2e6;">Quantity</th><th width="25%" style="text-align:right; border:1px solid #dee2e6;">Price</th><th width="20%" style="text-align:right; border:1px solid #dee2e6;">Total</th></tr>';

            $orderid = $order->order_id;
            if (strlen($orderid) == 3) {
                $orderid = date('Ymd', strtotime($order->date_entered)) . '0' . $orderid;
            } elseif (strlen($orderid) == 2) {
                $orderid = date('Ymd', strtotime($order->date_entered)) . '00' . $orderid;
            } elseif (strlen($orderid) == 1) {
                $orderid = date('Ymd', strtotime($order->date_entered)) . '000' . $orderid;
            } else {
                $orderid = date('Ymd', strtotime($order->date_entered)) . $orderid;
            }

            $tax = '';
            $taxes = Country::where('countrycode', '=', $order->ship_country)->first();
            if ($taxes) {
                $tax = $taxes->taxtitle . ' - ' . $taxes->taxpercentage;
            }

            if ($orderdetails) {
                foreach ($orderdetails as $orderdetail) {
                    $itemdetails .= '<tr><td style="border:1px solid #dee2e6;">' . $orderdetail->prod_name;
                    if ($orderdetail->prod_option != '') {
                        $itemdetails .= '<span style="color: #6c757d !important">Option: ' . $orderdetail->prod_option . '</span>';
                    }
                    $itemdetails .= '</td><td style="text-align:center; border:1px solid #dee2e6;">' . $orderdetail->prod_quantity . '</td>';
                    $itemdetails .= '<td style="text-align:right; border:1px solid #dee2e6;">S$' . $orderdetail->prod_unit_price . '</td>';
                    $itemdetails .= '<td style="text-align:right; border:1px solid #dee2e6;">S$' . number_format(($orderdetail->prod_quantity * $orderdetail->prod_unit_price), 2) . '</td></tr>';
                }
            }

            $itemdetails .= '<tr><td colspan="4" style="border:1px solid #dee2e6;">&nbsp;</td></tr>';
            $itemdetails .= '<tr><td colspan="2" style="border:1px solid #dee2e6;">&nbsp;</td><td style="border:1px solid #dee2e6;">Sub Total</td><td style="text-align:right; border:1px solid #dee2e6;">S$' . number_format($order->payable_amount - ($order->shipping_cost + $order->packaging_fee + $order->tax_collected), 2) . '</td></tr>';
            $itemdetails .= '<tr><td colspan="2" style="border:1px solid #dee2e6;">&nbsp;</td><td style="border:1px solid #dee2e6;">Tax (' . $tax . '%)</td><td style="text-align:right; border:1px solid #dee2e6;">S$' . number_format($order->tax_collected, 2) . '</td></tr>';
            $itemdetails .= '<tr><td colspan="2" style="border:1px solid #dee2e6;">&nbsp;</td><td style="border:1px solid #dee2e6;">Shipping</td><td style="text-align:right; border:1px solid #dee2e6;">S$' . number_format($order->shipping_cost, 2) . '</td></tr>';
            $itemdetails .= '<tr><td colspan="2" style="border:1px solid #dee2e6;">&nbsp;</td><td style="border:1px solid #dee2e6;">Packaging Fee</td><td style="text-align:right; border:1px solid #dee2e6;">S$' . number_format($order->packaging_fee, 2) . '</td></tr>';
            if ($order->discount_amount != '0.00') {
                $itemdetails .= '<tr><td colspan="2" style="border:1px solid #dee2e6;">&nbsp;</td><td style="border:1px solid #dee2e6;">Discount</td><td style="text-align:right; border:1px solid #dee2e6;">S$' . number_format($order->discount_amount, 2) . '</td></tr>';
            }
            $itemdetails .= '<tr><td colspan="2" style="border:1px solid #dee2e6;">&nbsp;</td><td style="border:1px solid #dee2e6;"><b>Grand Total</b></td><td style="text-align:right; border:1px solid #dee2e6;"><b>S$' . number_format($order->payable_amount, 2) . '</b></td></tr>';

            $emailsubject = $emailcontent = '';

            $companyname = $adminemail = $ccemail = $customername = $customeremail = $companydetails = $statusdetails = '';
            $shippinginfo = $billinginfo = '';
            $shipmethod = '';

            $setting = Settings::where('id', '=', '1')->first();
            if ($setting) {
                $companyname = $setting->company_name;
                $adminemail = $setting->admin_email;
                $ccemail = $setting->cc_email;

                $companydetails .= '<table><tr><td>' . nl2br($setting->company_address) . '</td></tr>';
                $companydetails .= '<tr><td>Fax: ' . $setting->company_fax . '</td></tr>';
                $companydetails .= '<tr><td>GST No: ' . $setting->GST_res_no . '</td></tr></table>';

                $statusdetails .= '<table><tr><td style="vertical-align:top">Date: ' . date("d/m/Y", strtotime($order->date_entered)) . '</td></tr>';

                if ($order->order_status == 0) {
                    $statusdetails .= '<tr><td>Status: Payment Pending</td></tr>';
                } elseif ($order->order_status == 1) {
                    $statusdetails .= '<tr><td>Status: Paid, Shipping Pending</td></tr>';
                }

                $shipping = ShippingMethods::where('Id', '=', $order->ship_method)->select('EnName')->first();
                if ($shipping) {
                    $statusdetails .= '<tr><td>Delivery Method: ' . $shipping->EnName . '</td></tr>';
                    $shipmethod = $shipping->EnName;
                }

                if ($order->if_items_unavailabel == 1) {
                    $statusdetails .= '<tr><td>If item(s) unavailable: Call Me</td></tr>';
                } elseif ($order->if_items_unavailabel == 2) {
                    $statusdetails .= '<tr><td>If item(s) unavailable: Do Not Replace</td></tr>';
                } else {
                    $statusdetails .= '<tr><td>If item(s) unavailable: Replace</td></tr>';
                }

                if (stripos($shipmethod, 'Self Collect') !== false) {
                    $shippinginfo .= '<table><tr><td>' . $shipmethod . '</td></tr></table>';
                } else {
                    $shippinginfo .= '<table><tr><td>' . $order->ship_fname . ' ' . $order->ship_lname . '</td></tr>';
                    $shippinginfo .= '<tr><td>' . $order->ship_ads1 . '</td></tr>';
                    if ($order->ship_ads2) {
                        $shippinginfo .= '<tr><td>' . $order->ship_ads2 . '</td></tr>';
                    }
                    $shippinginfo .= '<tr><td>' . $order->ship_city . '</td></tr>';
                    $shippinginfo .= '<tr><td>' . $order->ship_state . ' - ' . $order->ship_zip . '</td></tr>';
                    $shippinginfo .= '<tr><td>Email: ' . $order->ship_email . '</td></tr>';
                    $shippinginfo .= '<tr><td>Mobile: ' . $order->ship_mobile . '</td></tr>';
                    $shippinginfo .= '</table>';
                }
                $billinginfo .= '<table><tr><td>' . $order->bill_fname . ' ' . $order->bill_lname . '</td></tr>';
                $billinginfo .= '<tr><td>' . $order->bill_ads1 . '</td></tr>';
                if ($order->bill_ads2) {
                    $billinginfo .= '<tr><td>' . $order->bill_ads2 . '</td></tr>';
                }
                $billinginfo .= '<tr><td>' . $order->bill_city . '</td></tr>';
                $billinginfo .= '<tr><td>' . $order->bill_state . ' - ' . $order->bill_zip . '</td></tr>';
                $billinginfo .= '<tr><td>Email: ' . $order->bill_email . '</td></tr>';
                $billinginfo .= '<tr><td>Mobile: ' . $order->bill_mobile . '</td></tr>';
                $billinginfo .= '</table>';

                $emailtemplate = EmailTemplate::where('template_type', '=', '2')->where('status', '=', '1')->first();
                if ($emailtemplate) {
                    $emailsubject = $emailtemplate->subject;
                    $emailcontent = $emailtemplate->content;
                }

                $customer = Customer::where('cust_id', '=', $order->user_id)->first();
                if ($customer) {
                    $customername = $customer->cust_firstname . ' ' . $customer->cust_lastname;
                    $customeremail = $customer->cust_email;
                }

                $emailsubject = str_replace('{companyname}', $companyname, $emailsubject);
                $emailsubject = str_replace('{status}', 'Confirmed', $emailsubject);
                $emailcontent = str_replace('{companyname}', $companyname, $emailcontent);
                $emailcontent = str_replace('{customername}', $customername, $emailcontent);
                $emailcontent = str_replace('{logo}', $logo, $emailcontent);
                $emailcontent = str_replace('{email}', $adminemail, $emailcontent);
                $emailcontent = str_replace('{orderid}', $orderid, $emailcontent);
                $emailcontent = str_replace('{companyaddress}', $companydetails, $emailcontent);
                $emailcontent = str_replace('{statusdetails}', $statusdetails, $emailcontent);
                $emailcontent = str_replace('{billinginfo}', $billinginfo, $emailcontent);
                $emailcontent = str_replace('{shippinginfo}', $shippinginfo, $emailcontent);
                $emailcontent = str_replace('{paymentmethod}', $order->pay_method, $emailcontent);
                $emailcontent = str_replace('{orderdetails}', $itemdetails, $emailcontent);

                $headers = 'From: ' . $companyname . ' ' . $adminemail . '' . "\r\n";
                $headers .= 'Reply-To: ' . $adminemail . "\r\n";
                $headers .= 'X-Mailer: PHP/' . phpversion();
                $headers .= "MIME-Version: 1.0\r\n";
                $headers .= "Content-type: text/html; charset=iso-8859-1\r\n";

                #@mail($customeremail, $emailsubject, $emailcontent, $headers);
                #@mail($adminemail, $emailsubject, $emailcontent, $headers);

                Mail::send([], [], function ($message) use ($adminemail, $customeremail, $emailsubject, $emailcontent) {
                    $message->to([$customeremail, $adminemail])
                        ->subject($emailsubject)
                        ->from(env('MAIL_USERNAME'), env('APP_NAME'))
                        ->setBody($emailcontent, 'text/html');
                });
            }
        }

        return view('public/Payment.success');

    }

    public function cancelpayment(Request $request)
    {
        Session::forget('cartdata');
        Session::forget('deliverymethod');
        Session::forget('if_unavailable');
        Session::forget('billinginfo');
        Session::forget('paymentmethod');
        Session::forget('discount');
        Session::forget('discounttext');
        Session::forget('couponcode');
        Session::forget('discounttype');
        $orderid = $request->orderid;

        $order = OrderMaster::where('order_id', '=', $orderid)->first();
        if ($order) {
            if ($order->order_type == 2) {
                OrderMaster::where('order_id', '=', $orderid)->update(array('quotation_status' => '1'));
            }
            $orderdetails = OrderDetails::where('order_id', '=', $orderid)->get();

            $logo = url('/') . '/front/img/logo.png';
            $logo = '<img src="' . $logo . '">';

            $itemdetails = '<table style="width:100%;" border="0" cellpadding="5" cellspacing="0">';
            $itemdetails .= '<tr><th width="40%" style="text-align:left; border:1px solid #dee2e6;">Item</th><th width="15%" style="text-align:center; border:1px solid #dee2e6;">Quantity</th><th width="25%" style="text-align:right; border:1px solid #dee2e6;">Price</th><th width="20%" style="text-align:right; border:1px solid #dee2e6;">Total</th></tr>';

            $orderid = $order->order_id;
            if (strlen($orderid) == 3) {
                $orderid = date('Ymd', strtotime($order->date_entered)) . '0' . $orderid;
            } elseif (strlen($orderid) == 2) {
                $orderid = date('Ymd', strtotime($order->date_entered)) . '00' . $orderid;
            } elseif (strlen($orderid) == 1) {
                $orderid = date('Ymd', strtotime($order->date_entered)) . '000' . $orderid;
            } else {
                $orderid = date('Ymd', strtotime($order->date_entered)) . $orderid;
            }

            $tax = '';
            $taxes = Country::where('countrycode', '=', $order->ship_country)->first();
            if ($taxes) {
                $tax = $taxes->taxtitle . ' - ' . $taxes->taxpercentage;
            }

            if ($orderdetails) {
                foreach ($orderdetails as $orderdetail) {
                    $itemdetails .= '<tr><td style="border:1px solid #dee2e6;">' . $orderdetail->prod_name;
                    if ($orderdetail->prod_option != '') {
                        $itemdetails .= '<span style="color: #6c757d !important">Option: ' . $orderdetail->prod_option . '</span>';
                    }
                    $itemdetails .= '</td><td style="text-align:center; border:1px solid #dee2e6;">' . $orderdetail->prod_quantity . '</td>';
                    $itemdetails .= '<td style="text-align:right; border:1px solid #dee2e6;">S$' . $orderdetail->prod_unit_price . '</td>';
                    $itemdetails .= '<td style="text-align:right; border:1px solid #dee2e6;">S$' . number_format(($orderdetail->prod_quantity * $orderdetail->prod_unit_price), 2) . '</td></tr>';
                }
            }

            $itemdetails .= '<tr><td colspan="4" style="border:1px solid #dee2e6;">&nbsp;</td></tr>';
            $itemdetails .= '<tr><td colspan="2" style="border:1px solid #dee2e6;">&nbsp;</td><td style="border:1px solid #dee2e6;">Sub Total</td><td style="text-align:right; border:1px solid #dee2e6;">S$' . number_format($order->payable_amount - ($order->shipping_cost + $order->packaging_fee + $order->tax_collected), 2) . '</td></tr>';
            $itemdetails .= '<tr><td colspan="2" style="border:1px solid #dee2e6;">&nbsp;</td><td style="border:1px solid #dee2e6;">Tax (' . $tax . '%)</td><td style="text-align:right; border:1px solid #dee2e6;">S$' . number_format($order->tax_collected, 2) . '</td></tr>';
            $itemdetails .= '<tr><td colspan="2" style="border:1px solid #dee2e6;">&nbsp;</td><td style="border:1px solid #dee2e6;">Shipping</td><td style="text-align:right; border:1px solid #dee2e6;">S$' . number_format($order->shipping_cost, 2) . '</td></tr>';
            $itemdetails .= '<tr><td colspan="2" style="border:1px solid #dee2e6;">&nbsp;</td><td style="border:1px solid #dee2e6;">Packaging Fee</td><td style="text-align:right; border:1px solid #dee2e6;">S$' . number_format($order->packaging_fee, 2) . '</td></tr>';
            if ($order->discount_amount != '0.00') {
                $itemdetails .= '<tr><td colspan="2" style="border:1px solid #dee2e6;">&nbsp;</td><td style="border:1px solid #dee2e6;">Discount</td><td style="text-align:right; border:1px solid #dee2e6;">S$' . number_format($order->discount_amount, 2) . '</td></tr>';
            }
            $itemdetails .= '<tr><td colspan="2" style="border:1px solid #dee2e6;">&nbsp;</td><td style="border:1px solid #dee2e6;"><b>Grand Total</b></td><td style="text-align:right; border:1px solid #dee2e6;"><b>S$' . number_format($order->payable_amount, 2) . '</b></td></tr>';

            $emailsubject = $emailcontent = '';

            $companyname = $adminemail = $ccemail = $customername = $customeremail = $companydetails = $statusdetails = '';
            $shippinginfo = $billinginfo = '';
            $shipmethod = '';

            $setting = Settings::where('id', '=', '1')->first();
            if ($setting) {
                $companyname = $setting->company_name;
                $adminemail = $setting->admin_email;
                $ccemail = $setting->cc_email;

                $companydetails .= '<table><tr><td>' . nl2br($setting->company_address) . '</td></tr>';
                $companydetails .= '<tr><td>Fax: ' . $setting->company_fax . '</td></tr>';
                $companydetails .= '<tr><td>GST No: ' . $setting->GST_res_no . '</td></tr></table>';

                $statusdetails .= '<table><tr><td style="vertical-align:top">Date: ' . date("d/m/Y", strtotime($order->date_entered)) . '</td></tr>';

                if ($order->order_status == 0) {
                    $statusdetails .= '<tr><td>Status: Payment Pending</td></tr>';
                } elseif ($order->order_status == 1) {
                    $statusdetails .= '<tr><td>Status: Paid, Shipping Pending</td></tr>';
                }

                $shipping = ShippingMethods::where('Id', '=', $order->ship_method)->select('EnName')->first();
                if ($shipping) {
                    $statusdetails .= '<tr><td>Delivery Method: ' . $shipping->EnName . '</td></tr>';
                    $shipmethod = $shipping->EnName;
                }

                if ($order->if_items_unavailabel == 1) {
                    $statusdetails .= '<tr><td>If item(s) unavailable: Call Me</td></tr>';
                } elseif ($order->if_items_unavailabel == 2) {
                    $statusdetails .= '<tr><td>If item(s) unavailable: Do Not Replace</td></tr>';
                } else {
                    $statusdetails .= '<tr><td>If item(s) unavailable: Replace</td></tr>';
                }

                if (stripos($shipmethod, 'Self Collect') !== false) {
                    $shippinginfo .= '<table><tr><td>' . $shipmethod . '</td></tr></table>';
                } else {
                    $shippinginfo .= '<table><tr><td>' . $order->ship_fname . ' ' . $order->ship_lname . '</td></tr>';
                    $shippinginfo .= '<tr><td>' . $order->ship_ads1 . '</td></tr>';
                    if ($order->ship_ads2) {
                        $shippinginfo .= '<tr><td>' . $order->ship_ads2 . '</td></tr>';
                    }
                    $shippinginfo .= '<tr><td>' . $order->ship_city . '</td></tr>';
                    $shippinginfo .= '<tr><td>' . $order->ship_state . ' - ' . $order->ship_zip . '</td></tr>';
                    $shippinginfo .= '<tr><td>Email: ' . $order->ship_email . '</td></tr>';
                    $shippinginfo .= '<tr><td>Mobile: ' . $order->ship_mobile . '</td></tr>';
                    $shippinginfo .= '</table>';
                }
                $billinginfo .= '<table><tr><td>' . $order->bill_fname . ' ' . $order->bill_lname . '</td></tr>';
                $billinginfo .= '<tr><td>' . $order->bill_ads1 . '</td></tr>';
                if ($order->bill_ads2) {
                    $billinginfo .= '<tr><td>' . $order->bill_ads2 . '</td></tr>';
                }
                $billinginfo .= '<tr><td>' . $order->bill_city . '</td></tr>';
                $billinginfo .= '<tr><td>' . $order->bill_state . ' - ' . $order->bill_zip . '</td></tr>';
                $billinginfo .= '<tr><td>Email: ' . $order->bill_email . '</td></tr>';
                $billinginfo .= '<tr><td>Mobile: ' . $order->bill_mobile . '</td></tr>';
                $billinginfo .= '</table>';

                $emailtemplate = EmailTemplate::where('template_type', '=', '2')->where('status', '=', '1')->first();
                if ($emailtemplate) {
                    $emailsubject = $emailtemplate->subject;
                    $emailcontent = $emailtemplate->content;
                }

                $customer = Customer::where('cust_id', '=', $order->user_id)->first();
                if ($customer) {
                    $customername = $customer->cust_firstname . ' ' . $customer->cust_lastname;
                    $customeremail = $customer->cust_email;
                }

                $emailsubject = str_replace('{companyname}', $companyname, $emailsubject);
                $emailsubject = str_replace('{status}', 'payment not successful', $emailsubject);
                $emailcontent = str_replace('{companyname}', $companyname, $emailcontent);
                $emailcontent = str_replace('{customername}', $customername, $emailcontent);
                $emailcontent = str_replace('{logo}', $logo, $emailcontent);
                $emailcontent = str_replace('{email}', $adminemail, $emailcontent);
                $emailcontent = str_replace('{orderid}', $orderid, $emailcontent);
                $emailcontent = str_replace('{companyaddress}', $companydetails, $emailcontent);
                $emailcontent = str_replace('{statusdetails}', $statusdetails, $emailcontent);
                $emailcontent = str_replace('{billinginfo}', $billinginfo, $emailcontent);
                $emailcontent = str_replace('{shippinginfo}', $shippinginfo, $emailcontent);
                $emailcontent = str_replace('{paymentmethod}', $order->pay_method, $emailcontent);
                $emailcontent = str_replace('{orderdetails}', $itemdetails, $emailcontent);

                $headers = 'From: ' . $companyname . ' ' . $adminemail . '' . "\r\n";
                $headers .= 'Reply-To: ' . $adminemail . "\r\n";
                $headers .= 'X-Mailer: PHP/' . phpversion();
                $headers .= "MIME-Version: 1.0\r\n";
                $headers .= "Content-type: text/html; charset=iso-8859-1\r\n";

                #@mail($customeremail, $emailsubject, $emailcontent, $headers);
                #@mail($adminemail, $emailsubject, $emailcontent, $headers);

                Mail::send([], [], function ($message) use ($adminemail, $customeremail, $emailsubject, $emailcontent) {
                    $message->to([$customeremail, $adminemail])
                        ->subject($emailsubject)
                        ->from(env('MAIL_USERNAME'), env('APP_NAME'))
                        ->setBody($emailcontent, 'text/html');
                });
            }
        }

        return view('public/Payment.cancelpayment');
    }

    public function atomecallback()
    {
        //echo "hai";
    }

    public function grabpaywebhook()
    {

    }

    public function grabpay()
    {
        if (!Session::has('billinginfo')) {
            return redirect('/');
        }

        $authtoken = $username = $password = $paymenturl = $apikey = $apisignature = $billcountryname = $shipcountryname = '';
        $billinginfo = [];
        $paymethodname = $deliverytype = $emailsubject = $emailcontent = $companyname = $adminemail = $ccemail = $taxtitle = '';
        $packingfee = $deliverycost = $deliverymethod = 0;
        $sesid = Session::get('_token');
        $paymentmethod = Session::has('paymentmethod') ? Session::get('paymentmethod') : 0;
        $billinginfo = Session::get('billinginfo');

        $cart = new Cart();
        $orderid = $countryid = $orderincid = 0;
        $country = $billinginfo['ship_country'];

        $cartItems = $this->cartServices->cartItems($country ?? null);
        $cartdata = $cartItems['cartItems'];

        if (empty($cartdata)) {
            return redirect('/');
        }

        $subtotal = $cartItems['subTotal'];
        $grandtotal = $cartItems['grandTotal'];
        $gst = $cartItems['taxDetails']['taxTotal'];
        $taxtitle = $cartItems['taxDetails']['taxLabel'];
        $taxLabelOnly = $cartItems['taxDetails']['taxLabelOnly'];
        $deliverytype = $cartItems['deliveryDetails']['title'];
        $packingfee = $cartItems['packingFees'];
        $deliverycost = $cartItems['deliveryDetails']['deliveryTotal'];
        $discount = $cartItems['discountDetails']['discountTotal'];
        $discounttext = $cartItems['discountDetails']['title'];
        $fuelcharges = isset($cartItems['fuelcharges']) ? $cartItems['fuelcharges'] : 0.00;
        $handlingfee = isset($cartItems['handlingfee']) ? $cartItems['handlingfee'] : 0.00;

        if (Session::has('deliverymethod')) {
            $deliverymethod = Session::get('deliverymethod');
        }

        // Shipping Cost
        $deliverytype = $cart->getDeliveryMethod($deliverymethod);
        $totalweight = $discounttype = $disamount = 0;
        $discounttext = '';

        if (Session::has('couponcode')) {
            $discounttype = Session::get('discounttype');
            $disamount = Session::get('discount');
            $discounttext = Session::get('discounttext');
        }

        $paymethod = PaymentMethods::where('id', $paymentmethod)->first();
        if ($paymethod) {
            $paymethodname = $paymethod->payment_name;
        }

        $couponid = $userid = 0;
        if (Session::has('customer_id')) {
            $userid = Session::get('customer_id');
        } else {
            $chkcustomer = Customer::where('cust_email', $billinginfo['bill_email'])->select('cust_id')->first();
            if (!$chkcustomer) {
                Customer::insert(['cust_firstname' => $billinginfo['bill_fname'], 'cust_lastname' => $billinginfo['bill_lname'], 'cust_email' => $billinginfo['bill_email'], 'cust_address1' => $billinginfo['bill_ads1'], 'cust_address2' => $billinginfo['bill_ads2'], 'cust_city' => $billinginfo['bill_city'], 'cust_state' => $billinginfo['bill_state'], 'cust_country' => $countryid, 'cust_zip' => $billinginfo['bill_zip'], 'cust_phone' => $billinginfo['bill_mobile'], 'cust_status' => 0]);
                $cust = Customer::where('cust_id', '>', '0')->orderBy('cust_id', 'desc')->select('cust_id')->first();
                if ($cust) {
                    $userid = $cust->cust_id;
                }
            } else {
                $userid = $chkcustomer->cust_id;
            }
        }

        if (Session::has('couponcode')) {
            $couponcode = Session::get('couponcode');
            $coupondata = Couponcode::where([['coupon_code', '=', $couponcode], ['status', '=', '1']])->first();
            if ($coupondata) {
                $couponid = $coupondata->id;
            }
        }
        $fuelSettings = PaymentSettings::where('Id', '1')->select('fuelcharge_percentage')->first();
        $ordermaster = new OrderMaster;
        $ordermaster['user_id'] = $userid;
        $ordermaster['ship_method'] = Session::get('deliverymethod');
        $ordermaster['pay_method'] = $paymethodname;
        $ordermaster['shipping_cost'] = $deliverycost;
        $ordermaster['packaging_fee'] = $packingfee;
        $ordermaster['fuelcharge_percentage'] = $billinginfo['ship_country'] != 'SG' ? ($fuelSettings ? $fuelSettings->fuelcharge_percentage : 0) : 0;
        $ordermaster['fuelcharges'] = $fuelcharges;
        $ordermaster['handlingfee'] = $handlingfee;
        $ordermaster['tax_collected'] = $gst;
        $ordermaster['tax_label'] = isset($cartItems['taxDetails']['taxLabel']) ? $cartItems['taxDetails']['taxLabel'] : '';
        $ordermaster['tax_percentage'] = isset($cartItems['taxDetails']['taxPercentage']) ? $cartItems['taxDetails']['taxPercentage'] : '';
        $ordermaster['payable_amount'] = $grandtotal;
        $ordermaster['discount_amount'] = $discount;
        $ordermaster['discount_id'] = $couponid;
        $ordermaster['order_status'] = '0';
        $ordermaster['if_items_unavailabel'] = Session::get('if_unavailable');
        $ordermaster['delivery_instructions'] = Session::get('delivery_instructions');

        $ordermaster['bill_fname'] = $billinginfo['bill_fname'];
        $ordermaster['bill_lname'] = $billinginfo['bill_lname'];
        $ordermaster['bill_email'] = $billinginfo['bill_email'];
        $ordermaster['bill_mobile'] = $billinginfo['bill_mobile'];
        $ordermaster['bill_compname'] = $billinginfo['bill_compname'];
        $ordermaster['bill_ads1'] = $billinginfo['bill_ads1'];
        $ordermaster['bill_ads2'] = $billinginfo['bill_ads2'];
        $ordermaster['bill_city'] = $billinginfo['bill_city'];
        $ordermaster['bill_state'] = $billinginfo['bill_state'];
        $ordermaster['bill_zip'] = $billinginfo['bill_zip'];
        $ordermaster['bill_country'] = $billinginfo['bill_country'];
        $ordermaster['ship_fname'] = $billinginfo['ship_fname'];
        $ordermaster['ship_lname'] = $billinginfo['ship_lname'];
        $ordermaster['ship_email'] = $billinginfo['ship_email'];
        $ordermaster['ship_mobile'] = $billinginfo['ship_mobile'];
        $ordermaster['ship_ads1'] = $billinginfo['ship_ads1'];
        $ordermaster['ship_ads2'] = $billinginfo['ship_ads2'];
        $ordermaster['ship_country'] = $billinginfo['ship_country'];
        $ordermaster['ship_city'] = $billinginfo['ship_city'];
        $ordermaster['ship_state'] = $billinginfo['ship_state'];
        $ordermaster['ship_zip'] = $billinginfo['ship_zip'];
        $ordermaster->save();

        $order = OrderMaster::orderBy('order_id', 'desc')->select('order_id')->first();
        if ($order) {

            $orderid = $order->order_id;
            $orderincid = $order->order_id;

            if (!empty($couponid) && !empty($discount)) {
                CouponCodeUsage::insert(['coupon_id' => $couponid, 'customer_id' => $userid, 'order_id' => $orderid]);
            }
        }

        foreach ($cartdata as $cart) {

            $orderdetails = new OrderDetails;
            $orderdetails['order_id'] = $orderid;
            $orderdetails['prod_id'] = $cart['productId'];
            $orderdetails['prod_name'] = $cart['productName'];
            $orderdetails['prod_quantity'] = $cart['qty'];
            $orderdetails['prod_unit_price'] = $cart['price'];
            $orderdetails['prod_option'] = $cart['productoption'];
            //$orderdetails['option_id'] = $cart['option_id'];
            $orderdetails['Weight'] = $cart['weight'];
            $orderdetails['prod_code'] = $cart['productcode'];
            if (!Session::has('old_order_id')) {
                $orderdetails->save();
            }

            $desc = $image = '';
            $qty = 0;
            $product = Product::where('Id', '=', $cart['productId'])->select('Quantity', 'Image', 'EnShortDesc')->first();
            if ($product->Quantity > $cart['qty']) {
                $qty = $product->Quantity - $cart['qty'];
                $desc = $product->EnShortDesc;
                if ($desc == '') {
                    $desc = $cart['productName'];
                }
                $image = url('/uploads/product/' . $product->Image);
            }
            Product::where('Id', '=', $cart['productId'])->update(array('Quantity' => $qty));

            $productname = $cart['productName'];

            $sku = $ean = "";
            if ($cart['productcode']) {
                $sku = $cart['productcode'];
                $ean = $cart['productcode'];
            }
        }

        $countrydata = Country::where('countrycode', $billinginfo['bill_country'])->select('countryid', 'countryname')->first();
        if ($countrydata) {
            $countryid = $countrydata->countryid;
            $billcountryname = $countrydata->countryname;
        }

        $shipcountrydata = Country::where('countrycode', $billinginfo['ship_country'])->select('countryid', 'countryname')->first();
        if ($shipcountrydata) {
            $shipcountryname = $shipcountrydata->countryname;
        }

        Customer::where('cust_id', $userid)->update(['cust_firstname' => $billinginfo['bill_fname'], 'cust_lastname' => $billinginfo['bill_lname'], 'cust_address1' => $billinginfo['bill_ads1'], 'cust_address2' => $billinginfo['bill_ads2'], 'cust_city' => $billinginfo['bill_city'], 'cust_state' => $billinginfo['bill_state'], 'cust_country' => $countryid, 'cust_zip' => $billinginfo['bill_zip'], 'cust_phone' => $billinginfo['bill_mobile']]);

        DB::table('cart_details')->where('user_id', $userid)->delete();

        $paysettings = PaymentSettings::where('id', '1')->select('currency_type')->first();
        $currency = $paysettings ? $paysettings->currency_type : 'SGD';
        $partnerId = $clientId = $merchId = $partnerSec = $clientSec = '';
        $paymode = 'live';
        $paymentmethod = PaymentMethods::where('id', '5')->first();
        if ($paymentmethod) {
            $paymode = $paymentmethod->payment_mode;

            $partnerId = $paymentmethod->test_api_key;
            $clientId = $paymentmethod->test_api_signature;
            $merchId = $paymentmethod->live_url;
            $partnerSec = $paymentmethod->api_key;
            $clientSec = $paymentmethod->api_signature;
        }

        try {

            define('CONST_PARTNER_SECRET', $partnerSec);
            define('CONST_CLIENT_SECRET', $clientSec);
            define('CONST_CLIENT_ID', $clientId);
            define('CONST_PARTNER_ID', $partnerId);
            define('CONST_MERCHANT_ID', $merchId);
            define('ENDPOINT_URL', 'https://partner-api.grab.com/');
            define('CONST_REDIRECT_URI', 'https://hardwarecity.com.sg/success?orderid=' . $orderid);

            $grabPayStagingURL = ENDPOINT_URL;

            //Generate HMAC Signature
            $date = gmdate("D, d M Y H:i:s \G\M\T");
            $partnerTxID = "ORD_" . $orderid;
            setcookie('partnerTxID', $partnerTxID, time() + 36000);

            $gpay = new GrabPayFunctions;
            $payloadtoSign = json_encode([
                'partnerTxID' => $partnerTxID,
                'partnerGroupTxID' => "ORD_" . $orderid,
                'amount' => $grandtotal * 100,
                'currency' => 'SGD',
                'merchantID' => CONST_MERCHANT_ID,
                'description' => "Order from HardwareCity",
            ]);
            $authorizationCode = $gpay->ComputeHMAC($date, $payloadtoSign);
            $cURLConnection = curl_init();
            curl_setopt($cURLConnection, CURLOPT_HTTPHEADER, [
                "Content-Type: application/json",
                "Authorization:$authorizationCode",
                "Date:$date",
            ]);
            $api_url = ENDPOINT_URL . 'grabpay/partner/v2/charge/init';
            curl_setopt($cURLConnection, CURLOPT_POST, 1);
            curl_setopt($cURLConnection, CURLOPT_URL, $api_url);
            curl_setopt($cURLConnection, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($cURLConnection, CURLOPT_SSL_VERIFYHOST, false);
            curl_setopt($cURLConnection, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($cURLConnection, CURLOPT_POSTFIELDS, $payloadtoSign);

            $output = curl_exec($cURLConnection);

            Session::forget('cartdata');
            Session::forget('deliverymethod');
            Session::forget('if_unavailable');
            Session::forget('billinginfo');
            Session::forget('paymentmethod');
            Session::forget('discount');
            Session::forget('discounttext');
            Session::forget('couponcode');
            Session::forget('discounttype');
            Session::forget('old_order_id');

            if ($output === false) {
                echo 'Curl error: ' . curl_error($cURLConnection);
                return redirect('cancelpayment', ['orderid' => $orderid]);
            } else {

                $resultObj = json_decode($output);
                error_log(print_r($resultObj, true));
                $request = isset($resultObj->request) ? $resultObj->request : '';
                if (!empty($request)) {
                    $wp_session['request'] = $request;
                    setcookie('grabpay_request', $request, time() + 36000);
                }
                // else {
                //     $request = $_COOKIE['grabpay_request'];
                // }
                $authorizeLink = $gpay->getAuthorizeLink($request);
                echo "Authorize Link:" . $authorizeLink;
                curl_close($cURLConnection);
                setcookie('my_order_id', $orderid, time() + 36000);
                header('location:' . $authorizeLink);
                exit;
            }
        } catch (Exception $e) {
            return redirect('cancelpayment', ['orderid' => $orderid]);
        }

    }

    public function grabpayOld()
    {

        $authtoken = $username = $password = $paymenturl = $apikey = $apisignature = $billcountryname = $shipcountryname = '';

        $billinginfo = [];
        $paymethodname = $deliverytype = $emailsubject = $emailcontent = $companyname = $adminemail = $ccemail = $taxtitle = '';
        $paymentmethod = $packingfee = $deliverycost = $deliverymethod = 0;
        $sesid = Session::get('_token');
        if (Session::has('cartdata')) {
            $cartdata = Session::get('cartdata');
        }

        if (Session::has('paymentmethod')) {
            $paymentmethod = Session::get('paymentmethod');
        }

        $billinginfo = Session::get('billinginfo');

        $cart = new Cart();
        $subtotal = $cart->getSubTotal();

        $orderid = $countryid = $orderincid = 0;
        $country = '';

        $country = $billinginfo['ship_country'];

        if (Session::has('deliverymethod')) {
            $deliverymethod = Session::get('deliverymethod');
        }

        // Shipping Cost

        $deliverytype = $cart->getDeliveryMethod($deliverymethod);

        $totalweight = 0;

        $taxes = $cart->getGST($subtotal, $country);

        if ($country != '') {
            $taxvals = @explode("|", $taxes);
            $taxtitle = $taxvals[0];
            $gst = $taxvals[1];
        } else {
            $gst = $taxes;
        }

        $settings = PaymentSettings::where('Id', '=', '1')->select('min_package_fee')->first();
        if ($country != 'SG') {
            $packingfee = $settings->min_package_fee;
        }
        $boxfees = 0;

        foreach ($cartdata as $key => $val) {
            if (is_array($val)) {
                $x = 0;
                foreach ($val as $datakey => $dataval) {
                    $totalweight = $totalweight + $dataval['weight'];
                    $shippingbox = $dataval['shippingbox'];
                    $quantity = $dataval['qty'];
                    $deliverycost += $cart->shippingCost($country, $deliverymethod, $shippingbox, $quantity, $subtotal, $gst);
                    $boxfees = $cart->getPackagingFee($country, $totalweight, $subtotal, $gst, $deliverymethod, $shippingbox, $quantity);
                }
            }
        }

        $packingfee = number_format(($packingfee + $boxfees), 2);

        $discounttype = $disamount = 0;
        $discounttext = '';
        if (Session::has('couponcode')) {
            $discounttype = Session::get('discounttype');
            $disamount = Session::get('discount');
            $discounttext = Session::get('discounttext');
        }

        $discount = $cart->getDiscount($subtotal, $gst, $deliverycost, $packingfee, $discounttype, $disamount);

        $grandtotal = $cart->getGrandTotal($subtotal, $gst, $deliverycost, $packingfee, $discount);

        $subtotal = str_replace(',', '', $subtotal);
        $deliverycost = str_replace(',', '', $deliverycost);
        $packingfee = str_replace(',', '', $packingfee);
        $gst = str_replace(',', '', $gst);
        $grandtotal = str_replace(',', '', $grandtotal);
        $discount = str_replace(',', '', $discount);

        $sesid = Session::get('_token');
        if (Session::has('cartdata')) {
            $cartdata = Session::get('cartdata');
        }

        $paymethod = PaymentMethods::where('id', '=', $paymentmethod)->first();
        if ($paymethod) {
            $paymethodname = $paymethod->payment_name;
        }

        if ($cartdata) {

            $couponid = 0;
            $userid = 0;
            if (Session::has('customer_id')) {
                $userid = Session::get('customer_id');
            } else {
                $chkcustomer = Customer::where('cust_email', '=', $billinginfo['bill_email'])->select('cust_id')->first();
                if (!$chkcustomer) {
                    Customer::insert(array('cust_firstname' => $billinginfo['bill_fname'], 'cust_lastname' => $billinginfo['bill_lname'], 'cust_email' => $billinginfo['bill_email'], 'cust_address1' => $billinginfo['bill_ads1'], 'cust_address2' => $billinginfo['bill_ads2'], 'cust_city' => $billinginfo['bill_city'], 'cust_state' => $billinginfo['bill_state'], 'cust_country' => $countryid, 'cust_zip' => $billinginfo['bill_zip'], 'cust_phone' => $billinginfo['bill_mobile'], 'cust_status' => 0));
                    $cust = Customer::where('cust_id', '>', '0')->orderBy('cust_id', 'desc')->select('cust_id')->first();
                    if ($cust) {
                        $userid = $cust->cust_id;
                    }
                } else {
                    $userid = $chkcustomer->cust_id;
                }
            }

            if (Session::has('couponcode')) {
                $couponcode = Session::get('couponcode');
                $coupondata = Couponcode::where('coupon_code', '=', $couponcode)->where('status', '=', '1')->first();
                if ($coupondata) {
                    $couponid = $coupondata->id;
                }
            }

            $ordermaster = new OrderMaster;
            $ordermaster['user_id'] = $userid;
            $ordermaster['ship_method'] = Session::get('deliverymethod');
            $ordermaster['pay_method'] = $paymethodname;
            $ordermaster['shipping_cost'] = $deliverycost;
            $ordermaster['packaging_fee'] = $packingfee;
            $ordermaster['tax_collected'] = $gst;
            $ordermaster['payable_amount'] = $grandtotal;
            $ordermaster['discount_amount'] = $discount;
            $ordermaster['discount_id'] = $couponid;
            $ordermaster['order_status'] = '0';
            $ordermaster['if_items_unavailabel'] = Session::get('if_unavailable');
            $ordermaster['delivery_instructions'] = Session::get('delivery_instructions');

            $ordermaster['bill_fname'] = $billinginfo['bill_fname'];
            $ordermaster['bill_lname'] = $billinginfo['bill_lname'];
            $ordermaster['bill_email'] = $billinginfo['bill_email'];
            $ordermaster['bill_mobile'] = $billinginfo['bill_mobile'];
            $ordermaster['bill_compname'] = $billinginfo['bill_compname'];
            $ordermaster['bill_ads1'] = $billinginfo['bill_ads1'];
            $ordermaster['bill_ads2'] = $billinginfo['bill_ads2'];
            $ordermaster['bill_city'] = $billinginfo['bill_city'];
            $ordermaster['bill_state'] = $billinginfo['bill_state'];
            $ordermaster['bill_zip'] = $billinginfo['bill_zip'];
            $ordermaster['bill_country'] = $billinginfo['bill_country'];
            $ordermaster['ship_fname'] = $billinginfo['ship_fname'];
            $ordermaster['ship_lname'] = $billinginfo['ship_lname'];
            $ordermaster['ship_email'] = $billinginfo['ship_email'];
            $ordermaster['ship_mobile'] = $billinginfo['ship_mobile'];
            $ordermaster['ship_ads1'] = $billinginfo['ship_ads1'];
            $ordermaster['ship_ads2'] = $billinginfo['ship_ads2'];
            $ordermaster['ship_country'] = $billinginfo['ship_country'];
            $ordermaster['ship_city'] = $billinginfo['ship_city'];
            $ordermaster['ship_state'] = $billinginfo['ship_state'];
            $ordermaster['ship_zip'] = $billinginfo['ship_zip'];

            if (Session::has('old_order_id')) {
                if (Session::get('old_order_id') > 0) {
                    $orderid = Session::get('old_order_id');
                    $orderincid = Session::get('old_order_id');
                    //OrderMaster::where('order_id', '=', $orderincid)->update(array('pay_method' => $paymethodname));
                    OrderMaster::where('order_id', '=', $orderincid)->update(array('pay_method' => $paymethodname, 'date_entered' => date('Y-m-d H:i:s')));
                }
            } else {
                $ordermaster->save();

                $order = OrderMaster::orderBy('order_id', 'desc')->select('order_id')->first();
                if ($order) {
                    $orderid = $order->order_id;
                    $orderincid = $order->order_id;
                }
            }
            $hoolahitems = [];

            foreach ($cartdata[$sesid] as $cart) {
                $orderdetails = new OrderDetails;
                $orderdetails['order_id'] = $orderid;
                $orderdetails['prod_id'] = $cart['productId'];
                $orderdetails['prod_name'] = $cart['productName'];
                $orderdetails['prod_quantity'] = $cart['qty'];
                $orderdetails['prod_unit_price'] = $cart['price'];
                $orderdetails['prod_option'] = $cart['productoption'];
                //$orderdetails['option_id'] = $cart['option_id'];
                $orderdetails['Weight'] = $cart['weight'];
                $orderdetails['prod_code'] = $cart['productcode'];
                if (!Session::has('old_order_id')) {
                    $orderdetails->save();
                }

                $desc = $image = '';

                $qty = 0;
                $product = Product::where('Id', '=', $cart['productId'])->select('Quantity', 'Image', 'EnShortDesc')->first();
                if ($product->Quantity > $cart['qty']) {
                    $qty = $product->Quantity - $cart['qty'];
                    $desc = $product->EnShortDesc;
                    if ($desc == '') {
                        $desc = $cart['productName'];
                    }
                    $image = url('/') . '/uploads/product/' . $product->Image;
                }
                Product::where('Id', '=', $cart['productId'])->update(array('Quantity' => $qty));

                $productname = $cart['productName'];

                $sku = $ean = "";
                if ($cart['productcode']) {
                    $sku = $cart['productcode'];
                    $ean = $cart['productcode'];
                }

                $hoolahitems = array("name" => $productname, "description" => $desc, "sku" => $sku, "ean" => $ean, "quantity" => $cart['qty'], "originalPrice" => $cart['price'], "price" => $cart['price'], "images" => array(array("imageLocation" => $image)), "taxAmount" => "0", "discount" => "0", "detailDescription" => $desc);

            }

            $countrydata = Country::where('countrycode', '=', $billinginfo['bill_country'])->select('countryid', 'countryname')->first();
            if ($countrydata) {
                $countryid = $countrydata->countryid;
                $billcountryname = $countrydata->countryname;
            }

            $shipcountrydata = Country::where('countrycode', '=', $billinginfo['ship_country'])->select('countryid', 'countryname')->first();
            if ($shipcountrydata) {
                $shipcountryname = $shipcountrydata->countryname;
            }

            Customer::where('cust_id', '=', $userid)->update(array('cust_firstname' => $billinginfo['bill_fname'], 'cust_lastname' => $billinginfo['bill_lname'], 'cust_address1' => $billinginfo['bill_ads1'], 'cust_address2' => $billinginfo['bill_ads2'], 'cust_city' => $billinginfo['bill_city'], 'cust_state' => $billinginfo['bill_state'], 'cust_country' => $countryid, 'cust_zip' => $billinginfo['bill_zip'], 'cust_phone' => $billinginfo['bill_mobile']));

            DB::table('cart_details')->where('user_id', '=', $userid)->delete();

            $currency = 'SGD';
            $paysettings = PaymentSettings::where('id', '=', '1')->select('currency_type')->first();
            if ($paysettings) {
                $currency = $paysettings->currency_type;
            }

            $paymode = 'live';
            $paymentmethod = PaymentMethods::where('id', '=', '4')->orWhere('payment_name', 'LIKE', '%hoolah')->first();

            if ($paymentmethod) {
                $paymode = $paymentmethod->payment_mode;
                if ($paymode == 'live') {
                    $apikey = $paymentmethod->api_key;
                    $apisignature = $paymentmethod->api_signature;
                    $paymenturl = $paymentmethod->live_url;
                } else {
                    $apikey = $paymentmethod->test_api_key;
                    $apisignature = $paymentmethod->test_api_signature;
                    $paymenturl = $paymentmethod->testing_url;
                }
            }

            define('CONST_PARTNER_SECRET', 'XcxsZRu6pAlhc5K1');
            define('CONST_CLIENT_SECRET', 'Z1BODuMYLALj8NXB');

            define('CONST_CLIENT_ID', 'd3aee5aa9be84cb8ae80195c9e34efe9');
            define('CONST_PARTNER_ID', '84b39dd2-8f30-4557-8f2c-edd49984d0cb');
            define('CONST_MERCHANT_ID', 'c204830b-6331-4e6d-bc06-362be3b71424');

            define('ENDPOINT_URL', 'https://partner-api.grab.com/');
            define('CONST_REDIRECT_URI', 'https://hardwarecity.com.sg/success?orderid=' . $orderincid);

            $order_id = $orderincid;

            $order_total = '0.1';

            $grabPayStagingURL = ENDPOINT_URL;

            //Generate HMAC Signature

            //$date = gmdate("D, d M Y H:i:s", time())." GMT";

            $date = gmdate("D, d M Y H:i:s \G\M\T");

            $partnerTxID = "ORD_" . $order_id;

            setcookie('partnerTxID', $partnerTxID, time() + 36000);

            $bodyArr = array(
                'partnerTxID' => $partnerTxID,
                'partnerGroupTxID' => "ORD_" . $order_id,
                'amount' => $order_total * 100,
                'currency' => 'SGD',
                'merchantID' => CONST_MERCHANT_ID,
                'description' => "Order from HardwareCity",
            );
            $payloadtoSign = json_encode($bodyArr);

            //error_log( "Payload to Sign:".$payloadtoSign );

            //$s = hash_hmac('sha256', $message, $secret, true);

            $gpay = new GrabPayFunctions;

            $authorizationCode = $gpay->ComputeHMAC($date, $payloadtoSign);

            //$authorizationCode = base64_encode($s);

            $cURLConnection = curl_init();

            curl_setopt($cURLConnection, CURLOPT_HTTPHEADER, array(
                "Content-Type: application/json",
                "Authorization:$authorizationCode",
                "Date:$date",
            ));
            $api_url = ENDPOINT_URL . 'grabpay/partner/v2/charge/init';
            curl_setopt($cURLConnection, CURLOPT_POST, 1);
            curl_setopt($cURLConnection, CURLOPT_URL, $api_url);
            curl_setopt($cURLConnection, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($cURLConnection, CURLOPT_SSL_VERIFYHOST, false);
            curl_setopt($cURLConnection, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($cURLConnection, CURLOPT_POSTFIELDS, $payloadtoSign);

            $output = curl_exec($cURLConnection);

            if ($output === false) {
                echo 'Curl error: ' . curl_error($cURLConnection);
            } else {
                $resultObj = json_decode($output);
                error_log(print_r($resultObj, true));
                $request = isset($resultObj->request) ? $resultObj->request : '';
                if (!empty($request)) {
                    $wp_session['request'] = $request;

                    setcookie('grabpay_request', $request, time() + 36000);
                } else {
                    $request = $_COOKIE['grabpay_request'];
                }
                $authorizeLink = $gpay->getAuthorizeLink($request);
                echo "Authorize Link:" . $authorizeLink;
                curl_close($cURLConnection);
                setcookie('my_order_id', $order_id, time() + 36000);
                header('location:' . $authorizeLink);
                exit;
                //return array('result'  => 'success', 'redirect'  => $authorizeLink);
            }

            Session::forget('cartdata');
            Session::forget('deliverymethod');
            Session::forget('if_unavailable');
            Session::forget('billinginfo');
            Session::forget('paymentmethod');
            Session::forget('discount');
            Session::forget('discounttext');
            Session::forget('couponcode');
            Session::forget('discounttype');
            Session::forget('old_order_id');
        }

    }

}
