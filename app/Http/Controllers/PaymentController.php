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
use App\Services\CartServices;
use App\Services\OrderServices;
use DB;
use Illuminate\Http\Request;
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
        $cartItems = $this->cartServices->cartItems();
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

        $orderincid = 0;
        $billinginfo = Session::has('billinginfo') ? Session::get('billinginfo') : [];

        // Stripe Key
        $paymentmethod = PaymentMethods::where('id', 3)->orWhere('payment_name', 'LIKE', '%stripe')->first();
        $stripekey = $paymentmethod ? ($paymentmethod->payment_mode == 'live' ? $paymentmethod->api_key : $paymentmethod->test_api_key) : '';

        return view('public/Payment.stripe', compact('cartdata', 'orderincid', 'taxLabelOnly', 'subtotal', 'gst', 'grandtotal', 'taxtitle', 'stripekey', 'billinginfo', 'deliverycost', 'deliverytype', 'packingfee', 'discounttext', 'discount'));
    }

    public function stripePaymentProcess(Request $request)
    {
        if (!Session::has('billinginfo')) {
            return redirect('cancelpayment');
        }

        $cartItems = $this->cartServices->cartItems();
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
            $itemdetails = '<table style="width:100%; background:#f1f1f142; padding:10px;">';
            $itemdetails .= '<tr><th width="40%" style="text-align:left">Item</th><th width="15%" style="text-align:center;">Quantity</th><th width="25%" style="text-align:right">Price</th><th width="20%" style="text-align:right">Total</th></tr>';
            $itemdetails .= '<tr><td colspan="4"><hr></td></tr>';
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

                if ($cart['productoption']) {
                    $option = $cart['productoption'];
                    $productname .= '<br><span style="font-size:11px; margin-left:10px;">Option: ' . $option . '</span>';
                }
                if ($cart['weight']) {
                    $weight = $cart['weight'];
                    $productname .= '<br><span style="font-size:11px; margin-left:10px;">Weight: ' . $weight . 'Kg</span>';
                }
                if ($cart['size']) {
                    $size = $cart['size'];
                    $productname .= '<br><span style="font-size:11px; margin-left:10px;">Size: ' . $size . '</span>';
                }
                if ($cart['color']) {
                    $color = $cart['color'];
                    $productname .= '<br><span style="font-size:11px; margin-left:10px;">Color: ' . $color . '</span>';
                }

                $itemdetails .= '<tr><td>' . $productname . '</td><td style="text-align:center;">' . $cart['qty'] . '</td><td style="text-align:right">$' . number_format($cart['price'], 2) . '</td><td style="text-align:right">$' . number_format($cart['total'], 2) . '</td></tr>';
            }

            $itemdetails .= '<tr><td colspan="4"><hr></td></tr>';
            $itemdetails .= '<tr><td colspan="2"></td><td>Sub Total</td><td style="text-align:right;">$' . number_format($subtotal, 2) . '</td></tr>';
            $itemdetails .= '<tr><td colspan="4"><hr></td></tr>';
            $itemdetails .= '<tr><td colspan="2"></td><td>' . $taxtitle . '</td><td style="text-align:right;">$' . number_format($gst, 2) . '</td></tr>';
            $itemdetails .= '<tr><td colspan="4"><hr></td></tr>';
            $itemdetails .= '<tr><td colspan="2"></td><td>Shipping (' . $deliverytype . ')</td><td style="text-align:right;">$' . number_format($deliverycost, 2) . '</td></tr>';
            $itemdetails .= '<tr><td colspan="4"><hr></td></tr>';
            $itemdetails .= '<tr><td colspan="2"></td><td>Packing Fee</td><td style="text-align:right;">$' . number_format($packingfee, 2) . '</td></tr>';
            $itemdetails .= '<tr><td colspan="4"><hr></td></tr>';
            if ($discounttext != '' && $discount != 0) {
                $itemdetails .= '<tr><td colspan="2"></td><td>Discount(' . $discounttext . ')</td><td style="text-align:right;">$' . number_format($discount, 2) . '</td></tr>';
                $itemdetails .= '<tr><td colspan="4"><hr></td></tr>';
            }
            $itemdetails .= '<tr><td colspan="2"></td><td><b>Grand Total</b></td><td style="text-align:right;"><b>$' . number_format($grandtotal, 2) . '</b></td></tr>';
            $itemdetails .= '<tr><td colspan="4"><hr></td></tr>';

            $itemdetails .= '</table>';

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

            $billing = '';
            $billing .= '<p style="margin:0;">' . $billinginfo['bill_fname'] . ' ' . $billinginfo['bill_lname'] . '</p>';
            $billing .= '<p style="margin:0;">' . $billinginfo['bill_ads1'] . '</p>';
            if ($billinginfo['bill_ads2'] != '') {
                $billing .= '<p style="margin:0;">' . $billinginfo['bill_ads2'] . '</p>';
            }
            $billing .= '<p style="margin:0;">' . $billinginfo['bill_city'] . '</p>';
            $billing .= '<p style="margin:0;">' . $billinginfo['bill_state'] . ' - ' . $billinginfo['bill_zip'] . '</p>';
            $billing .= '<p style="margin:0;">' . $billinginfo['bill_country'] . '</p>';

            $shipping = '';
            $shipping .= '<p style="margin:0;">' . $billinginfo['ship_fname'] . ' ' . $billinginfo['ship_lname'] . '</p>';
            $shipping .= '<p style="margin:0;">' . $billinginfo['ship_ads1'] . '</p>';
            if ($billinginfo['ship_ads2'] != '') {
                $shipping .= '<p style="margin:0;">' . $billinginfo['ship_ads2'] . '</p>';
            }
            $shipping .= '<p style="margin:0;">' . $billinginfo['ship_city'] . '</p>';
            $shipping .= '<p style="margin:0;">' . $billinginfo['ship_state'] . ' - ' . $billinginfo['ship_zip'] . '</p>';
            $shipping .= '<p style="margin:0;">' . $billinginfo['ship_country'] . '</p>';

            $emailtemplate = EmailTemplate::where('template_type', '=', '2')->where('status', '=', '1')->first();
            if ($emailtemplate) {

                if (strlen($orderid) == 3) {
                    $orderid = date('Ymd') . '0' . $orderid;
                } elseif (strlen($orderid) == 2) {
                    $orderid = date('Ymd') . '00' . $orderid;
                } elseif (strlen($orderid) == 1) {
                    $orderid = date('Ymd') . '000' . $orderid;
                } else {
                    $orderid = date('Ymd') . $orderid;
                }

                $emailsubject = $emailtemplate->subject;
                $emailcontent = $emailtemplate->content;
                $logo = url('/') . '/front/img/logo.png';
                $logo = '<img src="' . $logo . '">';
                $emailsubject = str_replace('{companyname}', $companyname, $emailsubject);
                $emailcontent = str_replace('{companyname}', $companyname, $emailcontent);
                $emailcontent = str_replace('{customername}', $billinginfo['bill_fname'] . ' ' . $billinginfo['bill_lname'], $emailcontent);
                $emailcontent = str_replace('{logo}', $logo, $emailcontent);
                $emailcontent = str_replace('{email}', $adminemail, $emailcontent);
                $emailcontent = str_replace('{orderid}', $orderid, $emailcontent);
                $emailcontent = str_replace('{datetime}', date("M d Y H:i A"), $emailcontent);
                $emailcontent = str_replace('{billinginfo}', $billing, $emailcontent);
                $emailcontent = str_replace('{shippinginfo}', $shipping, $emailcontent);
                $emailcontent = str_replace('{paymentmethod}', $paymethodname, $emailcontent);
                $emailcontent = str_replace('{shippingmethod}', $deliverytype, $emailcontent);
                $emailcontent = str_replace('{orderdetails}', $itemdetails, $emailcontent);

                $custemail = $billinginfo['bill_email'];

                $headers = 'From: ' . $companyname . ' ' . $adminemail . '' . "\r\n";
                $headers .= 'Reply-To: ' . $adminemail . "\r\n";
                $headers .= 'X-Mailer: PHP/' . phpversion();
                $headers .= "MIME-Version: 1.0\r\n";
                $headers .= "Content-type: text/html; charset=iso-8859-1\r\n";

                @mail($custemail, $emailsubject, $emailcontent, $headers);

                @mail($adminemail, $emailsubject, $emailcontent, $headers);
            }

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
            if ($result) {
                $response = json_decode($result);
                $authtoken = $response->token;

                if ($authtoken != '') {

                    $url = $paymenturl . "/order/initiate";

                    $billmobile = $billinginfo['bill_mobile'];
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

                    $closeurl = url('/') . '/cancelpayment';
                    $returnurl = url('/') . '/success?orderid=' . $orderincid;

                    $ch = curl_init($url);
                    # Setup request to send json via POST.
                    $payload = array("consumerTitle" => "", "consumerFirstName" => $billinginfo['bill_fname'], "consumerLastName" => $billinginfo['bill_lname'], "consumerMiddleName" => "", "consumerEmail" => $billinginfo['bill_email'], "consumerPhoneNumber" => $billmobile, "shippingAddress" => array("line1" => $billinginfo['ship_ads1'], "line2" => $shipadd2, "suburb" => $shipcountryname, "postcode" => $billinginfo['ship_zip'], "countryCode" => $billinginfo['ship_country']), "billingAddress" => array("line1" => $billinginfo['bill_ads1'], "line2" => $billadd2, "suburb" => $billcountryname, "postcode" => $billinginfo['bill_zip'], "countryCode" => $billinginfo['bill_country']), "items" => array($hoolahitems), "totalAmount" => $grandtotal, "originalAmount" => $grandtotal, "taxAmount" => $gst, "cartId" => $orderid, "orderType" => "ONLINE", "shippingAmount" => $deliverycost, "shippingMethod" => "FREE", "discount" => $discount, "voucherCode" => "", "currency" => $currency, "closeUrl" => $closeurl, "returnToShopUrl" => $returnurl);

                    $payload = json_encode($payload);

                    //print_r($payload); exit;
                    curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);
                    $header_str = "Authorization: bearer " . $authtoken;
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
                }
            } else {
                $ordermaster->save();

                $order = OrderMaster::orderBy('order_id', 'desc')->select('order_id')->first();
                if ($order) {
                    $orderid = $order->order_id;
                    $orderincid = $order->order_id;
                }
            }

            $itemdetails = '<table style="width:100%; background:#f1f1f142; padding:10px;">';
            $itemdetails .= '<tr><th width="40%" style="text-align:left">Item</th><th width="15%" style="text-align:center;">Quantity</th><th width="25%" style="text-align:right">Price</th><th width="20%" style="text-align:right">Total</th></tr>';
            $itemdetails .= '<tr><td colspan="4"><hr></td></tr>';
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

                if ($cart['productoption']) {
                    $option = $cart['productoption'];
                    $productname .= '<br><span style="font-size:11px; margin-left:10px;">Option: ' . $option . '</span>';
                }
                if ($cart['weight']) {
                    $weight = $cart['weight'];
                    $productname .= '<br><span style="font-size:11px; margin-left:10px;">Weight: ' . $weight . 'Kg</span>';
                }
                if ($cart['size']) {
                    $size = $cart['size'];
                    $productname .= '<br><span style="font-size:11px; margin-left:10px;">Size: ' . $size . '</span>';
                }
                if ($cart['color']) {
                    $color = $cart['color'];
                    $productname .= '<br><span style="font-size:11px; margin-left:10px;">Color: ' . $color . '</span>';
                }

                $itemdetails .= '<tr><td>' . $productname . '</td><td style="text-align:center;">' . $cart['qty'] . '</td><td style="text-align:right">$' . number_format($cart['price'], 2) . '</td><td style="text-align:right">$' . number_format($cart['total'], 2) . '</td></tr>';
            }

            $itemdetails .= '<tr><td colspan="4"><hr></td></tr>';
            $itemdetails .= '<tr><td colspan="2"></td><td>Sub Total</td><td style="text-align:right;">$' . number_format($subtotal, 2) . '</td></tr>';
            $itemdetails .= '<tr><td colspan="4"><hr></td></tr>';
            $itemdetails .= '<tr><td colspan="2"></td><td>' . $taxtitle . '</td><td style="text-align:right;">$' . number_format($gst, 2) . '</td></tr>';
            $itemdetails .= '<tr><td colspan="4"><hr></td></tr>';
            $itemdetails .= '<tr><td colspan="2"></td><td>Shipping (' . $deliverytype . ')</td><td style="text-align:right;">$' . number_format($deliverycost, 2) . '</td></tr>';
            $itemdetails .= '<tr><td colspan="4"><hr></td></tr>';
            $itemdetails .= '<tr><td colspan="2"></td><td>Packing Fee</td><td style="text-align:right;">$' . number_format($packingfee, 2) . '</td></tr>';
            $itemdetails .= '<tr><td colspan="4"><hr></td></tr>';
            if ($discounttext != '' && $discount != 0) {
                $itemdetails .= '<tr><td colspan="2"></td><td>Discount(' . $discounttext . ')</td><td style="text-align:right;">$' . number_format($discount, 2) . '</td></tr>';
                $itemdetails .= '<tr><td colspan="4"><hr></td></tr>';
            }
            $itemdetails .= '<tr><td colspan="2"></td><td><b>Grand Total</b></td><td style="text-align:right;"><b>$' . number_format($grandtotal, 2) . '</b></td></tr>';
            $itemdetails .= '<tr><td colspan="4"><hr></td></tr>';

            $itemdetails .= '</table>';

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

            $billing = '';
            $billing .= '<p style="margin:0;">' . $billinginfo['bill_fname'] . ' ' . $billinginfo['bill_lname'] . '</p>';
            $billing .= '<p style="margin:0;">' . $billinginfo['bill_ads1'] . '</p>';
            if ($billinginfo['bill_ads2'] != '') {
                $billing .= '<p style="margin:0;">' . $billinginfo['bill_ads2'] . '</p>';
            }
            $billing .= '<p style="margin:0;">' . $billinginfo['bill_city'] . '</p>';
            $billing .= '<p style="margin:0;">' . $billinginfo['bill_state'] . ' - ' . $billinginfo['bill_zip'] . '</p>';
            $billing .= '<p style="margin:0;">' . $billinginfo['bill_country'] . '</p>';

            $shipping = '';
            $shipping .= '<p style="margin:0;">' . $billinginfo['ship_fname'] . ' ' . $billinginfo['ship_lname'] . '</p>';
            $shipping .= '<p style="margin:0;">' . $billinginfo['ship_ads1'] . '</p>';
            if ($billinginfo['ship_ads2'] != '') {
                $shipping .= '<p style="margin:0;">' . $billinginfo['ship_ads2'] . '</p>';
            }
            $shipping .= '<p style="margin:0;">' . $billinginfo['ship_city'] . '</p>';
            $shipping .= '<p style="margin:0;">' . $billinginfo['ship_state'] . ' - ' . $billinginfo['ship_zip'] . '</p>';
            $shipping .= '<p style="margin:0;">' . $billinginfo['ship_country'] . '</p>';

            $emailtemplate = EmailTemplate::where('template_type', '=', '2')->where('status', '=', '1')->first();
            if ($emailtemplate) {

                if (strlen($orderid) == 3) {
                    $orderid = date('Ymd') . '0' . $orderid;
                } elseif (strlen($orderid) == 2) {
                    $orderid = date('Ymd') . '00' . $orderid;
                } elseif (strlen($orderid) == 1) {
                    $orderid = date('Ymd') . '000' . $orderid;
                } else {
                    $orderid = date('Ymd') . $orderid;
                }

                $emailsubject = $emailtemplate->subject;
                $emailcontent = $emailtemplate->content;
                $logo = url('/') . '/front/img/logo.png';
                $logo = '<img src="' . $logo . '">';
                $emailsubject = str_replace('{companyname}', $companyname, $emailsubject);
                $emailcontent = str_replace('{companyname}', $companyname, $emailcontent);
                $emailcontent = str_replace('{customername}', $billinginfo['bill_fname'] . ' ' . $billinginfo['bill_lname'], $emailcontent);
                $emailcontent = str_replace('{logo}', $logo, $emailcontent);
                $emailcontent = str_replace('{email}', $adminemail, $emailcontent);
                $emailcontent = str_replace('{orderid}', $orderid, $emailcontent);
                $emailcontent = str_replace('{datetime}', date("M d Y H:i A"), $emailcontent);
                $emailcontent = str_replace('{billinginfo}', $billing, $emailcontent);
                $emailcontent = str_replace('{shippinginfo}', $shipping, $emailcontent);
                $emailcontent = str_replace('{paymentmethod}', $paymethodname, $emailcontent);
                $emailcontent = str_replace('{shippingmethod}', $deliverytype, $emailcontent);
                $emailcontent = str_replace('{orderdetails}', $itemdetails, $emailcontent);

                $custemail = $billinginfo['bill_email'];

                $headers = 'From: ' . $companyname . ' ' . $adminemail . '' . "\r\n";
                $headers .= 'Reply-To: ' . $adminemail . "\r\n";
                $headers .= 'X-Mailer: PHP/' . phpversion();
                $headers .= "MIME-Version: 1.0\r\n";
                $headers .= "Content-type: text/html; charset=iso-8859-1\r\n";

                @mail($custemail, $emailsubject, $emailcontent, $headers);

                //@mail($adminemail, $emailsubject, $emailcontent, $headers);
            }

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

            return view('public/Payment.paypal', compact('cartdata', 'sesid', 'subtotal', 'gst', 'grandtotal', 'taxtitle', 'apikey', 'apisignature', 'paymenturl', 'billinginfo', 'deliverycost', 'deliverytype', 'packingfee', 'discounttext', 'discount', 'currency', 'payenv', 'orderincid'));
        }
    }

    public function grabpaywebhook(Request $request)
    {
        error_log(print_r($request->all(), true));
    }

    public function grabpay()
    {
        $cartItems = $this->cartServices->cartItems();
        $cartdata = $cartItems['cartItems'];

        if (empty($cartdata)) {
            return redirect('cancelpayment');
        }

        $grandtotal = $cartItems['grandTotal'];

        //Create order
        $orderCreate = $this->orderServices->createOrder();

        if ($orderCreate['status'] == false) {
            return redirect('cancelpayment');
        }

        $userid = $orderCreate['userId'];
        DB::table('cart_details')->where('user_id', '=', $userid)->delete();

        $paysettings = PaymentSettings::where('id', 1)->select('currency_type')->first();
        $currency = $paysettings ? $paysettings->currency_type : 'SGD';

        $paymentmethod = PaymentMethods::where('id', 5)->first();
//--

        $order_id = $orderCreate['orderId'];

        Paymentlog::create([
            'pay_method' => $paymentmethod->Id,
            'order_id' => $order_id,
            'sent_values' => serialize([])
        ]);

        if ($paymentmethod) {
            $paymode = $paymentmethod->payment_mode;
            if ($paymode == 'live') {
                $clientId = $paymentmethod->api_key;
                $clientSecret = $paymentmethod->api_signature;
                $paymenturl = $paymentmethod->live_url;
                $partnerId = $paymentmethod->partner_id;
                $partnerSecret = $paymentmethod->partner_secret;
                $merchantId = $paymentmethod->merchant_id;

            } else {
                $clientId = $paymentmethod->test_api_key;
                $clientSecret = $paymentmethod->test_api_signature;
                $paymenturl = $paymentmethod->testing_url;
                $partnerId = $paymentmethod->test_partner_id;
                $partnerSecret = $paymentmethod->test_partner_secret;
                $merchantId = $paymentmethod->test_merchant_id;
            }
        }

        define('CONST_CLIENT_ID', $clientId);
        define('CONST_CLIENT_SECRET', $clientSecret);
        define('CONST_PARTNER_ID', $partnerId);
        define('CONST_PARTNER_SECRET', $partnerSecret);
        define('CONST_MERCHANT_ID', $merchantId);
        define('ENDPOINT_URL', $paymenturl);
        define('CONST_REDIRECT_URI', url('success?orderid=' . $order_id));

        $grabPayStagingURL = ENDPOINT_URL;

        //Generate HMAC Signature
        $date = gmdate("D, d M Y H:i:s \G\M\T");
        $partnerTxID = "ORD_" . $order_id;
        setcookie('partnerTxID', $partnerTxID, time() + 36000);

        $payloadtoSign = json_encode([
            'partnerTxID' => $partnerTxID,
            'partnerGroupTxID' => $partnerTxID,
            'amount' => (1.00 * 100),
            // 'amount' => $grandtotal * 100,
            'currency' => $currency,
            'merchantID' => CONST_MERCHANT_ID,
            'description' => "Order from HardwareCity",
            'hidePaymentMethods' => ["INSTALMENT", "POSTPAID", "CARD"],
        ]);

        $gpay = new GrabPayFunctions;
        $authorizationCode = $gpay->ComputeHMAC($date, $payloadtoSign);
        Session::put('gau', $authorizationCode);

        $cURLConnection = curl_init();
        curl_setopt($cURLConnection, CURLOPT_HTTPHEADER, [
            "Content-Type: application/json",
            "Authorization:$authorizationCode",
            "Date:$date",
        ]);
        curl_setopt($cURLConnection, CURLOPT_POST, 1);
        curl_setopt($cURLConnection, CURLOPT_URL, ENDPOINT_URL . 'grabpay/partner/v2/charge/init');
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
            // echo "Authorize Link:" . $authorizeLink;
            curl_close($cURLConnection);
            header('location:' . $authorizeLink);
            exit;
        }

    }

    public function success2(Request $request)
    {
        define('CONST_CLIENT_ID', 'b72377e113cd40f280fe100ad5972b99');
        define('CONST_CLIENT_SECRET', 'Gh6M7NCPaVi0aXud');
        define('CONST_PARTNER_ID', '6a223ea9-f269-4fa8-986a-be0bb0035636');
        define('CONST_PARTNER_SECRET', 'XVn5d_u6axtoiBS9');
        define('CONST_MERCHANT_ID', '0a1a11c7-16e2-42c0-acf3-711b5522d01f');
        define('ENDPOINT_URL', 'https://partner-api.grab.com/');
        //define('CONST_REDIRECT_URI', url('success?orderid=' . $orderincid));
        $orderId = '0001';
        $orderTotal = 0.25;
        $date = date('Y-m-d H:i:s');
        $payloadtoSign = [
            'partnerTxID' => "ORD_" . $orderId,
            'partnerGroupTxID' => "ORD_" . $orderId,
            'amount' => $orderTotal * 100,
            'currency' => 'SGD',
            'merchantID' => CONST_MERCHANT_ID,
            'description' => "Order from HardwareCity",
        ];
        $payloadtoSignEncoded = json_encode($payloadtoSign);
        echo $payloadtoSignEncoded . ' \n ';
        $authorizationCode = base64_encode(hash_hmac('SHA256', CONST_PARTNER_SECRET, $payloadtoSignEncoded, true));
        echo $authorizationCode . ' \n ';
        echo CONST_PARTNER_ID . ":" . $authorizationCode;

        $cURLConnection = curl_init();
        curl_setopt($cURLConnection, CURLOPT_HTTPHEADER, [
            "Content-Type: application/json",
            "Authorization:" . CONST_PARTNER_ID . ":" . $authorizationCode,
            "Date:$date",
        ]);
        curl_setopt($cURLConnection, CURLOPT_POST, 1);
        curl_setopt($cURLConnection, CURLOPT_URL, ENDPOINT_URL . 'grabpay/partner/v2/charge/init');
        curl_setopt($cURLConnection, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($cURLConnection, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($cURLConnection, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($cURLConnection, CURLOPT_POSTFIELDS, $payloadtoSign);
        $output = curl_exec($cURLConnection);

        print_r($output);
/*
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
header('location:' . $authorizeLink);
exit;
}
 */
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
            return redirect('cancelpayment');
        }

        if (!$request->has('error')) {
            OrderMaster::where('order_id', '=', $orderid)->update(array('order_status' => '1'));
            $order = OrderMaster::where('order_id', '=', $orderid)->select('order_type')->first();
            if ($order) {
                if ($order->order_type == 2) {
                    OrderMaster::where('order_id', '=', $orderid)->update(array('quotation_status' => '1'));
                }
            }
            return view('public/Payment.success');
        }
    }

    public function paypalsuccess(Request $request)
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
        $order = OrderMaster::where('order_id', '=', $orderid)->select('order_type')->first();
        if ($order) {
            if ($order->order_type == 2) {
                OrderMaster::where('order_id', '=', $orderid)->update(array('quotation_status' => '1'));
            }
        }
        return view('public/Payment.success');

    }

    public function cancelpayment()
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
        return view('public/Payment.cancelpayment');
    }

}
