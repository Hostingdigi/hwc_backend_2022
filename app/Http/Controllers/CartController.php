<?php

namespace App\Http\Controllers;

use App\Models\Bannerads;
use App\Models\Cart;
use App\Models\Country;
use App\Models\Couponcode;
use App\Models\CouponCodeUsage;
use App\Models\Customer;
use App\Models\EmailTemplate;
use App\Models\InternationalShipping;
use App\Models\OrderDetails;
use App\Models\OrderMaster;
use App\Models\PaymentMethods;
use App\Models\PaymentSettings;
use App\Models\Price;
use App\Models\Product;
use App\Models\ProductOptions;
use App\Models\Settings;
use App\Models\ShippingMethods;
use App\Services\CartServices;
use Illuminate\Http\Request;
use Session;

class CartController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    protected $cartServices = null;

    public function __construct(CartServices $cartServices)
    {
        $this->cartServices = $cartServices;
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        $cartItems = $this->cartServices->cartItems();
        $cartdata = $cartItems['cartItems'];
        $subtotal = $cartItems['subTotal'];
        $grandtotal = $cartItems['grandTotal'];
        $couponcode = Session::has('couponcode') ? Session::get('couponcode') : '';
        $gst = $cartItems['taxDetails']['taxTotal'];
        $taxtitle = $cartItems['taxDetails']['taxLabel'];
        $taxLabelOnly = $cartItems['taxDetails']['taxLabelOnly'];

        return view('public/Cart.index', compact('cartdata', 'subtotal', 'gst', 'grandtotal', 'taxtitle', 'couponcode', 'taxLabelOnly'));
    }

    public function addtocart(Request $request)
    {
        $productid = $request->prodid;
        $optionid = $request->optionid;

        $optionprice = 0;
        $qty = 1;
        if (isset($request->qty)) {
            $qty = $request->qty;
        }

        $cart = $cartdata = [];
        $cartcount = 1;
        $exist = 0;
        $prodoption = '';
        $sesid = Session::get('_token');
        $product = Product::where('Id', '=', $productid)->first();
        if ($product) {
            if (Session::has('cartdata')) {
                $cartdata = Session::get('cartdata');
                $cartcount = count($cartdata[$sesid]) + 1;
                if (!empty($cartdata)) {
                    foreach ($cartdata as $key => $val) {
                        if (is_array($val)) {
                            foreach ($val as $datakey => $dataval) {
                                if ($dataval['productId'] == $productid) {
                                    $exist = 1;
                                    if ($qty >= $cartdata[$key][$datakey]['qty']) {
                                        $cartdata[$key][$datakey]['total'] = ($cartdata[$key][$datakey]['qty'] + $qty) * $cartdata[$key][$datakey]['price'];
                                        $cartdata[$key][$datakey]['qty'] = $cartdata[$key][$datakey]['qty'] + $qty;
                                        $cartdata[$key][$datakey]['weight'] = ($cartdata[$key][$datakey]['qty'] + $qty) * $cartdata[$key][$datakey]['weight'];
                                    } else {
                                        $cartdata[$key][$datakey]['total'] = $qty * $cartdata[$key][$datakey]['price'];
                                        $cartdata[$key][$datakey]['qty'] = $qty;
                                        $cartdata[$key][$datakey]['weight'] = $qty * $product->Weight;
                                    }
                                }
                            }
                        }
                    }
                }
            }
            if ($exist == 0) {
                $ses_productid = $productid;
                $ses_productname = $product->EnName;
                $ses_qty = $qty;
                $price = new \App\Models\Price();
                //$productprice = $price->getPrice($productid);
                $productprice = $price->getDiscountPrice($productid);
                if ($optionid > 0) {
                    $options = ProductOptions::where('Id', '=', $optionid)->first();
                    $optionprice = $options->Price;
                    $optionprice = $price->getOptionPrice($productid, $optionid);
                    $prodoption = $options->Title;
                }

                $productprice = $productprice + $optionprice;

                $ses_productprice = $productprice;
                $ses_total = $qty * $productprice;
                $cart['productId'] = $ses_productid;
                $cart['productName'] = $ses_productname;
                $cart['qty'] = $ses_qty;
                $cart['price'] = $ses_productprice;
                $cart['total'] = $ses_total;
                $cart['image'] = $product->Image;
                $cart['productoption'] = $prodoption;
                $cart['option_id'] = $optionid;
                $cart['weight'] = $product->Weight;
                $cart['productWeight'] = $product->Weight;
                $cart['productcode'] = $product->Code;
                $cart['color'] = $product->Color;
                $cart['size'] = $product->Size;
                $cart['shippingbox'] = $product->ShippingBox;
                $cartdata[$sesid][$cartcount] = $cart;
            }
            Session::put('cartdata', $cartdata);
            echo count($cartdata[$sesid]);
        } else {
            echo '0';
        }
    }

    public function removecartitem($key, $productid)
    {
        $sesid = Session::get('_token');
        $cartdata = Session::get('cartdata');
        unset($cartdata[$sesid][$key]);
        Session::put('cartdata', $cartdata);
        if (!empty($cartdata[$sesid])) {
            $product = Product::where('Id', '=', $productid)->select('EnName')->first();
            return redirect('/cart')->with('success', '"' . $product->EnName . '" successfully removed from your shopping cart!');
        } else {
            Session::forget('cartdata');
            return redirect('/cart');
        }
    }

    public function addtocartstatus(Request $request)
    {
        $redirecturl = $request->returnurl;
        $productid = $request->pid;
        $status = $request->status;
        $product = Product::where('Id', '=', $productid)->select('EnName')->first();
        if ($status == 'success') {
            return redirect($redirecturl)->with('success', '"' . $product->EnName . '" successfully added in your shopping cart!');
        } elseif ($status == 'notavailable') {
            return redirect($redirecturl)->with('message', 'Required quantity not available for this item!');
        } else {
            return redirect($redirecturl)->with('message', 'Something went wrong while add product in your shopping cart!');
        }
    }

    public function clearcart()
    {
        Session::forget('cartdata');
        Session::forget('discount');
        Session::forget('discounttext');
        Session::forget('couponcode');
        Session::forget('discounttype');
        return redirect('/');
    }

    public function updatecart(Request $request)
    {

        $qtyfield = $itemqty = '';
        $product = [];
        $cartitems = $request->cartitems;
        $sesid = Session::get('_token');
        if (Session::has('cartdata')) {
            $cartdata = Session::get('cartdata');
            $cartcount = count($cartdata) + 1;
            $ci = 0;
            if (!empty($cartdata)) {
                foreach ($cartdata as $key => $val) {
                    if (is_array($val)) {
                        foreach ($val as $datakey => $dataval) {
                            if (in_array($dataval['productId'], $cartitems)) {
                                $productid = $cartitems[$ci];
                                $product = Product::where('Id', '=', $productid)->select('Weight')->first();
                                $qtyfield = 'qty' . $dataval['productId'];
                                $itemqty = $request->{$qtyfield};
                                if ($cartdata[$key][$datakey]['qty'] != $itemqty) {
                                    $cartdata[$key][$datakey]['total'] = $itemqty * $cartdata[$key][$datakey]['price'];
                                    $cartdata[$key][$datakey]['qty'] = $itemqty;
                                    $cartdata[$key][$datakey]['weight'] = $itemqty * $product->Weight;
                                }
                            }
                            ++$ci;
                        }
                    }
                }
            }
        }
        Session::put('cartdata', $cartdata);
        return redirect('/cart')->with('success', 'Your shopping cart successfully updated!');
    }

    public function updatecartqty(Request $request)
    {
        $qtyfield = $itemqty = '';
        $product = [];
        $prodid = $request->prodid;
        $qty = $request->qty;
        $itemtotal = 0;
        $sesid = Session::get('_token');
        if (Session::has('cartdata')) {
            $cartdata = Session::get('cartdata');
            $cartcount = count($cartdata) + 1;
            $ci = 0;
            if (!empty($cartdata)) {
                foreach ($cartdata as $key => $val) {
                    if (is_array($val)) {
                        foreach ($val as $datakey => $dataval) {
                            if ($dataval['productId'] == $prodid) {
                                $product = Product::where('Id', '=', $prodid)->select('Weight')->first();
                                $itemqty = $qty;
                                if ($cartdata[$key][$datakey]['qty'] != $itemqty) {
                                    $cartdata[$key][$datakey]['total'] = $itemqty * $cartdata[$key][$datakey]['price'];
                                    $itemtotal = number_format(($itemqty * $cartdata[$key][$datakey]['price']), 2);
                                    $cartdata[$key][$datakey]['qty'] = $itemqty;
                                    $cartdata[$key][$datakey]['weight'] = $itemqty * $product->Weight;
                                } else {
                                    $itemtotal = number_format(($itemqty * $cartdata[$key][$datakey]['price']), 2);
                                }
                            }
                            ++$ci;
                        }
                    }
                }
            }
        }
        Session::put('cartdata', $cartdata);
        $cart = new Cart();
        $subtotal = $cart->getSubTotal();

        $countryid = 0;
        $country = '';
        if (Session::has('customer_id')) {
            $userid = Session::get('customer_id');
            $customer = Customer::where('cust_id', '=', $userid)->first();
            $countryid = $customer->cust_country;
            $countrydata = Country::where('countryid', '=', $countryid)->select('countrycode')->first();
            if ($countrydata) {
                $country = $countrydata->countrycode;
            }
        }
        $taxes = $cart->getGST($subtotal, $country);

        if ($country != '') {
            $taxvals = @explode("|", $taxes);
            $taxtitle = $taxvals[0];
            $gst = $taxvals[1];
        } else {
            $gst = $taxes;
        }
        $discounttype = $disamount = 0;
        if (Session::has('couponcode')) {
            $discounttype = Session::get('discounttype');
            $disamount = Session::get('discount');
        }

        $discount = $cart->getDiscount($subtotal, $gst, 0, 0, $discounttype, $disamount);

        $grandtotal = $cart->getGrandTotal($subtotal, $gst, 0, 0, $discount);

        //$grandtotal = $cart->getGrandTotal($subtotal, $gst);
        echo $itemtotal . '|' . $subtotal . '|' . $gst . '|' . $grandtotal . '|' . $discount;
    }

    public function checkout()
    {

        if (!Session::has('cartdata')) {
            return redirect('/cart');
        }

        $cartItems = $this->cartServices->cartItems();
        $cartdata = $cartItems['cartItems'];
        $subtotal = $cartItems['subTotal'];
        $grandtotal = $cartItems['grandTotal'];
        $gst = $cartItems['taxDetails']['taxTotal'];
        $taxtitle = $cartItems['taxDetails']['taxLabel'];
        $taxLabelOnly = $cartItems['taxDetails']['taxLabelOnly'];
        $country = $cartItems['countryCode'];
        $countryid = $cartItems['countryId'];
        $discount = $cartItems['discountDetails']['discountTotal'];
        $discounttext = $cartItems['discountDetails']['title'];
        $customer = Session::has('customer_id') ? Customer::where('cust_id', Session::get('customer_id'))->first() : [];

        $freeshippingamount = $remaining = 0;
        $showfreedeliverymsg = $localfreedeliverymsg = $internationalfreedeliverymsg = '';

        $paysettings = PaymentSettings::select('free_shipping_amount', 'local_free_shipping_msg', 'international_free_shipping_msg')->first();

        if ($paysettings) {
            $freeshippingamount = $paysettings->free_shipping_amount;
            $localfreedeliverymsg = $paysettings->local_free_shipping_msg;
            $internationalfreedeliverymsg = $paysettings->international_free_shipping_msg;
        }

        if ($country != 'SG' && $country != '') {
            $intership = InternationalShipping::where('Status', '1')->get();
            if ($intership) {
                foreach ($intership as $intershipping) {
                    $intercountry = $intershipping->CountriesList;
                    if ($intercountry) {
                        $intercountrys = @explode(',', $intercountry);
                        if (in_array($countryid, $intercountrys)) {
                            $freeshippingamount = $intershipping->FreeShippingCost;
                            break;
                        }
                    }
                }
            }
            if ($freeshippingamount > $subtotal) {
                $remaining = $freeshippingamount - $subtotal;
            }

            $deliverymethods = ShippingMethods::where([['Status', '=', '1'], ['shipping_type', '=', '1']])->get();

            if ($remaining > 0) {
                $showfreedeliverymsg = str_replace('{amount}', $remaining, $internationalfreedeliverymsg);
            }
        } else {
            if ($freeshippingamount > $subtotal) {
                $remaining = ($freeshippingamount - $subtotal);
            }
            if ($remaining <= 0) {
                $deliverymethods = ShippingMethods::where([['Status', '=', '1'], ['shipping_type', '=', '0'], ['EnName', 'NOT LIKE', '%Ninja Van Delivery%']])->orderBy('EnName', 'desc')->get();
            } else {
                $deliverymethods = ShippingMethods::where([['Status', '=', '1'], ['shipping_type', '=', '0'], ['EnName', 'NOT LIKE', '%Free%']])->orderBy('EnName', 'desc')->get();
                $showfreedeliverymsg = str_replace('{amount}', $remaining, $localfreedeliverymsg);
            }

        }

        $countries = Country::where('country_status', '1')->orderBy('countryname', 'ASC')->get();

        return view('public/Cart.checkout', compact('customer', 'cartdata', 'sesid', 'subtotal', 'gst', 'grandtotal', 'deliverymethods', 'countries', 'taxLabelOnly', 'taxtitle', 'country', 'discounttext', 'discount', 'showfreedeliverymsg'));

    }

    public function placeorder(Request $request)
    {
        if (!Session::has('cartdata')) {
            return redirect('/cart');
        }

        $deliverymethod = $request->deliverymethod;
        $billinginfo = $shippinginfo = [];
        $billinginfo['bill_fname'] = $request->bill_fname;
        $billinginfo['bill_lname'] = $request->bill_lname;
        $billinginfo['bill_email'] = $request->bill_email;
        $billinginfo['bill_mobile'] = $request->bill_mobile;
        $billinginfo['bill_compname'] = $request->bill_compname;
        $billinginfo['bill_ads1'] = $request->bill_ads1;
        $billinginfo['bill_ads2'] = $request->bill_ads2;
        $billinginfo['bill_city'] = $request->bill_city;
        $billinginfo['bill_state'] = $request->bill_state;
        $billinginfo['bill_zip'] = $request->bill_zip;
        $billinginfo['bill_country'] = $request->bill_country;
        $country = empty($request->bill_country) ? 'SG' : $request->bill_country;
        if (isset($request->shipaddress)) {
            $billinginfo['ship_fname'] = $request->bill_fname;
            $billinginfo['ship_lname'] = $request->bill_lname;
            $billinginfo['ship_email'] = $request->bill_email;
            $billinginfo['ship_mobile'] = $request->bill_mobile;
            $billinginfo['ship_ads1'] = $request->bill_ads1;
            $billinginfo['ship_ads2'] = $request->bill_ads2;
            $billinginfo['ship_country'] = $request->bill_country;
            $billinginfo['ship_city'] = $request->bill_city;
            $billinginfo['ship_state'] = $request->bill_state;
            $billinginfo['ship_zip'] = $request->bill_zip;
        } else {
            $billinginfo['ship_fname'] = $request->ship_fname;
            $billinginfo['ship_lname'] = $request->ship_lname;
            $billinginfo['ship_email'] = $request->ship_email;
            $billinginfo['ship_mobile'] = $request->ship_mobile;
            $billinginfo['ship_ads1'] = $request->ship_ads1;
            $billinginfo['ship_ads2'] = $request->ship_ads2;
            $billinginfo['ship_country'] = $request->ship_country;
            $billinginfo['ship_city'] = $request->ship_city;
            $billinginfo['ship_state'] = $request->ship_state;
            $billinginfo['ship_zip'] = $request->ship_zip;
            $country = $request->ship_country;
        }
        Session::put('deliverymethod', $deliverymethod);
        Session::put('billinginfo', $billinginfo);
        Session::put('if_unavailable', $request->if_items_unavailabel);

        $cartItems = $this->cartServices->cartItems($country);
        $cartdata = $cartItems['cartItems'];
        $subtotal = $cartItems['subTotal'];
        $grandtotal = $cartItems['grandTotal'];
        $gst = $cartItems['taxDetails']['taxTotal'];
        $taxtitle = $cartItems['taxDetails']['taxLabel'];
        $taxLabelOnly = $cartItems['taxDetails']['taxLabelOnly'];
        $country = $cartItems['countryCode'];
        $countryid = $cartItems['countryId'];
        $discount = $cartItems['discountDetails']['discountTotal'];
        $discounttext = $cartItems['discountDetails']['title'];
        $customer = Session::has('customer_id') ? Customer::where('cust_id', Session::get('customer_id'))->first() : [];
        $deliverytype = $cartItems['deliveryDetails']['title'];
        $packingfee = $cartItems['packingFees'];
        $deliverycost = $cartItems['deliveryDetails']['deliveryTotal'];
        $paymentmethods = PaymentMethods::where('status', '1')->get();
        $ordertype = 1;

        if (Session::has('old_order_id') && Session::get('old_order_id') > 0) {
            $eorder = OrderMaster::where('order_id', Session::get('old_order_id'))->select('order_type')->first();
            if ($eorder) {
                $ordertype = $eorder->order_type;
            }
        }

        return view('public/Cart.placeorder', compact('cartdata', 'subtotal', 'gst', 'grandtotal', 'taxLabelOnly', 'paymentmethods', 'taxtitle', 'deliverycost', 'deliverytype', 'packingfee', 'discounttext', 'discount', 'ordertype'));

    }

    public function paymentform(Request $request)
    {
        $paymentmethod = $request->paymentmethod;
        $paymethod = PaymentMethods::where('id', $paymentmethod)->first();
        $paymethodname = $paymethod ? $paymethod->payment_name : 'Cash On Delivery';
        $sesid = Session::get('_token');
        $cartdata = Session::has('cartdata') ? Session::get('cartdata') : [];

        if (!empty($cartdata)) {
            Session::put('paymentmethod', $paymentmethod);
            if (stripos($paymethodname, 'Debit / Credit Card') !== false || $paymentmethod == 3) {
                //return view('public/Cart.stripe', compact('cartdata', 'subtotal', 'gst', 'grandtotal', 'taxtitle', 'sesid'));
                return redirect('/stripe');
            } elseif (stripos($paymethodname, 'hoolah') !== false || $paymentmethod == 4) {
                $billinginfo = Session::get('billinginfo');
                if (($billinginfo['bill_country'] == 'SG' || $billinginfo['bill_country'] == 'MY') && strlen($billinginfo['bill_mobile']) >= 8) {
                    return redirect('/hoolah');
                } else {
                    return redirect('/placeorder')->with('error', 'Invalid Country / Phone Number');
                }
            } elseif (stripos($paymethodname, 'Paypal') !== false || $paymentmethod == 6) {
                return redirect('/paypal');
            } elseif (stripos($paymethodname, 'grap') !== false || $paymentmethod == 5) {
                return redirect('/grabpay');
            } elseif (stripos($paymethodname, 'Atome') !== false || $paymentmethod == 7) {
                $billinginfo = Session::get('billinginfo');
                if (($billinginfo['bill_country'] == 'SG' || $billinginfo['bill_country'] == 'MY' || $billinginfo['bill_country'] == 'HK' || $billinginfo['bill_country'] == 'ID' || $billinginfo['bill_country'] == 'TH' || $billinginfo['bill_country'] == 'TW' || $billinginfo['bill_country'] == 'VN' || $billinginfo['bill_country'] == 'PH' || $billinginfo['bill_country'] == 'JP' || $billinginfo['bill_country'] == 'KR') && strlen($billinginfo['bill_mobile']) >= 8) {
                    return redirect('/atome');
                } else {
                    return redirect('/placeorder')->with('error', 'Invalid Country / Phone Number');
                }
            } else {
                return redirect('/placeorder');
            }
        }
        return redirect('/cart');
    }

    public function submitpayment(Request $request)
    {
        $paymethodname = 'Cash On Delivery';
        $cartdata = [];
        $paymentmethod = $request->paymentmethod;
        $paymethod = PaymentMethods::where('id', '=', $paymentmethod)->first();
        if ($paymethod) {
            $paymethodname = $paymethod->payment_name;
        }

        $sesid = Session::get('_token');
        if (Session::has('cartdata')) {
            $cartdata = Session::get('cartdata');
        }

        if ($cartdata) {

            $billinginfo = Session::get('billinginfo');
            $cart = new Cart();
            $subtotal = $cart->getSubTotal();
            $gst = $cart->getGST($subtotal);
            $grandtotal = $cart->getGrandTotal($subtotal, $gst);

            $userid = Session::get('customer_id');

            $ordermaster = new OrderMaster;
            $ordermaster['user_id'] = $userid;
            $ordermaster['ship_method'] = Session::get('deliverymethod');
            $ordermaster['pay_method'] = $paymethodname;
            $ordermaster['tax_collected'] = $gst;
            $ordermaster['payable_amount'] = $grandtotal;
            $ordermaster['order_status'] = '1';
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
            $ordermaster->save();

            $orderid = $ordermaster->orderid;

            foreach ($cartdata[$sesid] as $cart) {
                $orderdetails = new OrderDetails;
                $orderdetails['order_id'] = $orderid;
                $orderdetails['prod_id'] = $cart['productId'];
                $orderdetails['prod_name'] = $cart['productName'];
                $orderdetails['prod_quantity'] = $cart['qty'];
                $orderdetails['prod_unit_price'] = $cart['price'];
                $orderdetails['prod_option'] = $cart['productoption'];
                $orderdetails['Weight'] = $cart['weight'];
                $orderdetails['prod_code'] = $cart['productcode'];
                $orderdetails->save();
            }

            Customer::where('cust_id', '=', $userid)->update(array('cust_firstname' => $billinginfo['bill_fname'], 'cust_lastname' => $billinginfo['bill_lname'], 'cust_address1' => $billinginfo['bill_ads1'], 'cust_address2' => $billinginfo['bill_ads2'], 'cust_city' => $billinginfo['bill_city'], 'cust_state' => $billinginfo['bill_state'], 'cust_country' => $billinginfo['bill_country'], 'cust_zip' => $billinginfo['bill_zip'], 'cust_phone' => $billinginfo['bill_mobile']));

            Session::forget('cartdata');

            if (stripos($paymethodname, 'paypal') !== false) {

            } elseif (stripos($paymethodname, 'stripe') !== false) {

            } elseif (stripos($paymethodname, 'hoolah') !== false) {

            } else {
                return redirect('/success');
            }
        } else {
            return redirect('/');
        }
    }

    public function getdeliverymethods(Request $request)
    {
        $deliverymethods = '';
        $deliverytypes = $deliverymethodarr = [];
        $countrycode = $request->country;
        $subtotal = $request->subtotal;
        $countryid = 0;
        $freeshippingamount = $remaining = 0;
        $showfreedeliverymsg = $localfreedeliverymsg = $internationalfreedeliverymsg = '';

        $paysettings = PaymentSettings::select('free_shipping_amount', 'local_free_shipping_msg', 'international_free_shipping_msg')->first();

        if ($paysettings) {
            $freeshippingamount = $paysettings->free_shipping_amount;
            $localfreedeliverymsg = $paysettings->local_free_shipping_msg;
            $internationalfreedeliverymsg = $paysettings->international_free_shipping_msg;
        }

        $countrydata = Country::where('countrycode', '=', $countrycode)->select('countryid')->first();
        if ($countrydata) {
            $countryid = $countrydata->countryid;
        }

        if ($countrycode != 'SG' && $countrycode != '') {
            $intership = InternationalShipping::where('Status', '=', '1')->get();
            if ($intership) {
                foreach ($intership as $intershipping) {
                    $intercountry = $intershipping->CountriesList;
                    if ($intercountry) {
                        $intercountrys = @explode(',', $intercountry);
                        if (in_array($countryid, $intercountrys)) {
                            $freeshippingamount = $intershipping->FreeShippingCost;
                            break;
                        }
                    }
                }
            }
            if ($freeshippingamount > str_replace(',', '', $subtotal)) {
                $remaining = ($freeshippingamount - str_replace(',', '', $subtotal));
            }

            $deliverytypes = ShippingMethods::where('shipping_type', '=', '1')->where('Status', '=', '1')->get();

            if ($remaining > 0) {
                $showfreedeliverymsg = str_replace('{amount}', $remaining, $internationalfreedeliverymsg);
            }
        } else {
            if ($freeshippingamount > str_replace(',', '', $subtotal)) {
                $remaining = ($freeshippingamount - str_replace(',', '', $subtotal));
            }
            if ($remaining <= 0) {
                $deliverytypes = ShippingMethods::where('shipping_type', '=', '0')->where('Status', '=', '1')->where('EnName', 'NOT LIKE', '%Ninja Van Delivery%')->orderBy('EnName', 'desc')->get();
            } else {
                $deliverytypes = ShippingMethods::where('Status', '=', '1')->where('shipping_type', '=', '0')->where('EnName', 'NOT LIKE', '%Free%')->orderBy('EnName', 'desc')->get();
                $showfreedeliverymsg = str_replace('{amount}', $remaining, $localfreedeliverymsg);
            }
        }

        /*if($countrycode == 'SG') {
        $deliverytypes = ShippingMethods::where('shipping_type', '=', '0')->where('Status', '=', '1')->orderBy('EnName', 'desc')->get();
        } else {
        $deliverytypes = ShippingMethods::where('shipping_type', '=', '1')->where('Status', '=', '1')->get();
        }*/
        if ($deliverytypes) {
            foreach ($deliverytypes as $deliverytype) {
                $deliverymethodarr[] = $deliverytype->Id . '||' . $deliverytype->EnName;
            }
        }
        if (!empty($deliverymethodarr)) {
            $deliverymethods = @implode('|#|', $deliverymethodarr);
        }
        echo $deliverymethods . '|-|' . $showfreedeliverymsg;
    }

    public function makepayment($orderid)
    {
        $orders = OrderMaster::where('order_id', '=', $orderid)->first();
        $orderdetails = OrderDetails::where('order_id', '=', $orderid)->get();
        $paymentmethods = PaymentMethods::where('status', '=', '1')->get();

        $cart = $cartdata = [];
        $cartcount = 1;
        $optionprice = 0;
        $prodoption = '';
        $sesid = Session::get('_token');

        Session::put('old_order_id', $orderid);

        if ($orders) {
            Session::put('deliverymethod', $orders->ship_method);
            Session::put('if_unavailable', $orders->if_items_unavailabel);
        }

        if ($orderdetails) {
            foreach ($orderdetails as $orderdetail) {
                $product = Product::where('Id', '=', $orderdetail->prod_id)->first();

                if ($product->Quantity > 0) {
                    $ses_productid = $orderdetail->prod_id;
                    $ses_productname = $product->EnName;
                    $ses_qty = $orderdetail->prod_quantity;
                    $optionid = $orderdetail->prod_option;

                    $price = new \App\Models\Price();
                    $productprice = $price->getPrice($orderdetail->prod_id);

                    if ($optionid > 0) {
                        $options = ProductOptions::where('Id', '=', $optionid)->first();
                        $optionprice = $options->Price;
                        $prodoption = $options->Title;
                    }

                    $productprice = $productprice + $optionprice;

                    $ses_productprice = $productprice;
                    $ses_total = $ses_qty * $productprice;
                    $cart['productId'] = $ses_productid;
                    $cart['productName'] = $ses_productname;
                    $cart['qty'] = $ses_qty;
                    $cart['price'] = $ses_productprice;
                    $cart['total'] = $ses_total;
                    $cart['image'] = $product->Image;
                    $cart['productoption'] = $prodoption;
                    $cart['weight'] = $product->Weight;
                    $cart['productWeight'] = $product->Weight;
                    $cart['productcode'] = $product->Code;
                    $cart['color'] = $product->Color;
                    $cart['size'] = $product->Size;
                    $cart['shippingbox'] = $product->ShippingBox;
                    $cartdata[$sesid][$cartcount] = $cart;
                    ++$cartcount;
                }
            }
            Session::put('cartdata', $cartdata);
        }

        return redirect('/cart');
    }

    public function quotation(Request $request)
    {

        $cartdata = [];
        $orderid = $deliverycost = $packingfee = $quotationid = 0;
        $taxtitle = '';
        $sesid = Session::get('_token');
        if (Session::has('cartdata')) {
            $cartdata = Session::get('cartdata');
        }

        if ($cartdata) {

            $deliverymethod = Session::get('deliverymethod');

            $billinginfo = Session::get('billinginfo');
            $cart = new Cart();
            $subtotal = $cart->getSubTotal();
            $deliverytype = $cart->getDeliveryMethod($deliverymethod);

            $country = $billinginfo['ship_country'];

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
            //Packing Cost

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

            $userid = Session::get('customer_id');

            $couponid = 0;

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
            $ordermaster['pay_method'] = '';
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
            $ordermaster['order_type'] = '2';
            $ordermaster->save();

            $order = OrderMaster::orderBy('order_id', 'desc')->select('order_id')->first();
            if ($order) {
                $orderid = $order->order_id;
                $quotationid = $orderid;
            }

            $itemdetails = '<table style="width:100%; background:#f1f1f1; padding:10px;">';
            $itemdetails .= '<tr><th width="40%">Item</th><th width="15%" style="text-align:center;">Quantity</th><th width="25%" style="text-align:right">Price</th><th width="20%" style="text-align:right">Total</th></tr>';

            foreach ($cartdata[$sesid] as $cart) {
                $orderdetails = new OrderDetails;
                $orderdetails['order_id'] = $orderid;
                $orderdetails['prod_id'] = $cart['productId'];
                $orderdetails['prod_name'] = $cart['productName'];
                $orderdetails['prod_quantity'] = $cart['qty'];
                $orderdetails['prod_unit_price'] = $cart['price'];
                $orderdetails['prod_option'] = $cart['productoption'];
                $orderdetails['Weight'] = $cart['weight'];
                $orderdetails['prod_code'] = $cart['productcode'];
                $orderdetails->save();

                $itemdetails .= '<tr><td>' . $cart['productName'] . '</td><td style="text-align:center;">' . $cart['qty'] . '</td><td style="text-align:right">$' . number_format($cart['price'], 2) . '</td><td style="text-align:right">$' . number_format($cart['total'], 2) . '</td></tr>';
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

            Customer::where('cust_id', '=', $userid)->update(array('cust_firstname' => $billinginfo['bill_fname'], 'cust_lastname' => $billinginfo['bill_lname'], 'cust_address1' => $billinginfo['bill_ads1'], 'cust_address2' => $billinginfo['bill_ads2'], 'cust_city' => $billinginfo['bill_city'], 'cust_state' => $billinginfo['bill_state'], 'cust_country' => $billinginfo['bill_country'], 'cust_zip' => $billinginfo['bill_zip'], 'cust_phone' => $billinginfo['bill_mobile']));

            $setting = Settings::where('id', '=', '1')->first();
            if ($setting) {
                $companyname = $setting->company_name;
                $adminemail = $setting->admin_email;
                $ccemail = $setting->cc_email;
            }

            $billing = '';
            $billing .= '<p>' . $billinginfo['bill_fname'] . ' ' . $billinginfo['bill_lname'] . '</p>';
            $billing .= '<p>' . $billinginfo['bill_ads1'] . '</p>';
            $billing .= '<p>' . $billinginfo['bill_ads2'] . '</p>';
            $billing .= '<p>' . $billinginfo['bill_city'] . '</p>';
            $billing .= '<p>' . $billinginfo['bill_state'] . ' - ' . $billinginfo['bill_zip'] . '</p>';
            $billing .= '<p>' . $billinginfo['bill_country'] . '</p>';

            $shipping = '';
            $shipping .= '<p>' . $billinginfo['ship_fname'] . ' ' . $billinginfo['ship_lname'] . '</p>';
            $shipping .= '<p>' . $billinginfo['ship_ads1'] . '</p>';
            $shipping .= '<p>' . $billinginfo['ship_ads2'] . '</p>';
            $shipping .= '<p>' . $billinginfo['ship_city'] . '</p>';
            $shipping .= '<p>' . $billinginfo['ship_state'] . ' - ' . $billinginfo['ship_zip'] . '</p>';
            $shipping .= '<p>' . $billinginfo['ship_country'] . '</p>';

            $emailtemplate = EmailTemplate::where('template_type', '=', '10')->where('status', '=', '1')->first();
            if ($emailtemplate) {

                if (strlen($orderid) == 3) {
                    $orderid = '0' . $orderid;
                } elseif (strlen($orderid) == 2) {
                    $orderid = '00' . $orderid;
                } elseif (strlen($orderid) == 1) {
                    $orderid = '000' . $orderid;
                }
                $orderid = 'Q' . date('Ymd') . $orderid;

                $emailsubject = $emailtemplate->subject;
                $emailcontent = $emailtemplate->content;
                $logo = url('/') . '/front/img/logo.png';
                $logo = '<img src="' . $logo . '">';
                $emailsubject = str_replace('{companyname}', $setting->company_name, $emailsubject);
                $emailsubject = str_replace('{date}', date("d-M-Y"), $emailsubject);
                $emailcontent = str_replace('{companyname}', $setting->company_name, $emailcontent);
                $emailcontent = str_replace('{companyaddress}', nl2br($setting->company_address), $emailcontent);
                $emailcontent = str_replace('{companyfax}', $setting->company_fax, $emailcontent);
                $emailcontent = str_replace('{companygstno}', $setting->GST_res_no, $emailcontent);
                $emailcontent = str_replace('{logo}', $logo, $emailcontent);
                $emailcontent = str_replace('{email}', $adminemail, $emailcontent);
                $emailcontent = str_replace('{invoiceno}', $orderid, $emailcontent);
                $emailcontent = str_replace('{date}', date("d-M-Y"), $emailcontent);
                $emailcontent = str_replace('{orderstatus}', 'Payment Pending', $emailcontent);
                $emailcontent = str_replace('{billinginfo}', $billing, $emailcontent);
                $emailcontent = str_replace('{shippinginfo}', $shipping, $emailcontent);
                $emailcontent = str_replace('{deliverymethod}', $deliverytype, $emailcontent);
                $emailcontent = str_replace('{orderdetails}', $itemdetails, $emailcontent);

                $custemail = $billinginfo['bill_email'];

                $headers = 'From: ' . $setting->company_name . ' ' . $setting->admin_email . '' . "\r\n";
                $headers .= 'Reply-To: ' . $setting->admin_email . "\r\n";
                $headers .= 'X-Mailer: PHP/' . phpversion();
                $headers .= "MIME-Version: 1.0\r\n";
                $headers .= "Content-type: text/html; charset=iso-8859-1\r\n";

                @mail($custemail, $emailsubject, $emailcontent, $headers);

                //@mail($setting->admin_email, $emailsubject, $emailcontent, $headers);
            }

            if ($discounttext != '' && $discount != 0) {
                CouponCodeUsage::insert(array('coupon_id' => $couponid, 'customer_id' => $userid, 'order_id' => $quotationid));
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

            return redirect('/invoice/' . $quotationid)->with('success', 'Quotation Successfully Created!');

        } else {
            return redirect('/');
        }
    }

    public function invoice($orderid)
    {
        $orders = OrderMaster::where('order_id', '=', $orderid)->first();
        $orderdetails = OrderDetails::where('order_id', '=', $orderid)->get();
        $settings = Settings::where('Id', '=', '1')->first();
        $bannerads = Bannerads::where('PageId', '=', 'invoice')->where('ban_status', '=', '1')->orderBy('display_order', 'asc')->get();
        return view('public/Cart.invoice', compact('orders', 'orderdetails', 'settings', 'bannerads'));
    }

    public function applycouponcode(Request $request)
    {
        $customer = $request->customer;
        $couponcode = $request->couponcode;
        $cartitemsarr = [];
        $usage = 0;
        $countryid = $allowapply = 0;
        $chkcategory = $chkbrand = 1;
        $country = $response = '';
        $cartitems = $request->cartitems;
        if ($cartitems) {
            $cartitemsarr = @explode('|', $cartitems);
        }

        $cust = Customer::where('cust_id', '=', $customer)->first();

        $coupondata = Couponcode::where('coupon_code', '=', $couponcode)->where('status', '=', '1')->first();
        if (!empty($coupondata)) {
            $date = date('Y-m-d');

            if (strtotime($coupondata->validity) >= strtotime($date)) {

                if ($cartitemsarr) {
                    if ($coupondata->category_id > 0) {
                        foreach ($cartitemsarr as $cartitem) {
                            $chkproduct = Product::where('Id', '=', $cartitem)->where('Types', '=', $coupondata->category_id)->where('ProdStatus', '=', '1')->first();
                            if (!$chkproduct) {
                                $chkcategory = 0;
                                break;
                            }
                        }
                    }
                    if ($coupondata->brand_id > 0) {
                        foreach ($cartitemsarr as $cartitem) {
                            $chkproduct = Product::where('Id', '=', $cartitem)->where('Brand', '=', $coupondata->brand_id)->where('ProdStatus', '=', '1')->first();
                            if (!$chkproduct) {
                                $chkbrand = 0;
                                break;
                            }
                        }
                    }

                    if ($coupondata->customer_id > 0) {
                        if ($customer == $coupondata->customer_id) {
                            if ($coupondata->customer_type == 1) {
                                $usage = CouponCodeUsage::where('coupon_id', '=', $coupondata->id)->where('customer_id', '=', $customer)->count();
                            } elseif ($coupondata->customer_type == 2) {
                                $usage = CouponCodeUsage::where('coupon_id', '=', $coupondata->id)->count();
                            }
                            if ($coupondata->nooftimes > $usage) {
                                $allowapply = 1;
                            } else {
                                $response = 'Coupon Code Usage Limit Exceeded!';
                            }
                        } else {
                            $allowapply = 0;
                            $response = 'Invalid Coupon Code!';
                        }
                    } else {
                        if ($coupondata->customer_type == 3) {
                            if ($cust->group_admin == 1) {
                                $usage = CouponCodeUsage::where('coupon_id', '=', $coupondata->id)->where('customer_id', '=', $customer)->count();
                                if ($coupondata->nooftimes > $usage) {
                                    $allowapply = 1;
                                } else {
                                    $response = 'Coupon Code Usage Limit Exceeded!';
                                }
                            } else {
                                $allowapply = 0;
                            }
                        } else {
                            if ($coupondata->customer_type == 1) {
                                $usage = CouponCodeUsage::where('coupon_id', '=', $coupondata->id)->where('customer_id', '=', $customer)->count();
                            } elseif ($coupondata->customer_type == 2) {
                                $usage = CouponCodeUsage::where('coupon_id', '=', $coupondata->id)->count();
                            }
                            if ($coupondata->nooftimes > $usage) {
                                $allowapply = 1;
                            } else {
                                $response = 'Coupon Code Usage Limit Exceeded!';
                            }
                        }
                    }

                }

                /*if($coupondata->customer_type == 1) {
            $usage = CouponCodeUsage::where('coupon_id', '=', $coupondata->id)->where('customer_id', '=', $customer)->count();
            } elseif($coupondata->customer_type == 2) {
            $usage = CouponCodeUsage::where('coupon_id', '=', $coupondata->id)->count();
            } elseif($coupondata->customer_type == 3) {
            if($cust->group_admin == 1) {
            $usage = CouponCodeUsage::where('coupon_id', '=', $coupondata->id)->count();
            }
            }

            if($coupondata->customer_id > 0 && $customer == $coupondata->customer_id) {
            if($coupondata->nooftimes > $usage) {
            $allowapply = 1;
            } else {
            if($coupondata->customer_type == 3 && $coupondata->nooftimes > $usage) {
            $allowapply = 1;
            } else {
            $response = 'Coupon Code Usage Limit Exceeded!';
            }
            }
            } elseif($coupondata->customer_id == 0) {
            if($coupondata->nooftimes > $usage) {
            $allowapply = 1;
            } else {
            $response = 'Invalid Coupon Code!';
            }
            }*/
            } else {
                $response = 'Coupon Code Expired!';
            }
        } else {
            $response = 'Invalid Coupon Code!';
        }

        if ($allowapply == 1 && $chkbrand == 1 && $chkcategory == 1) {
            $cart = new Cart();
            $subtotal = $cart->getSubTotal();

            $countryid = $cust->cust_country;
            $countrydata = Country::where('countryid', '=', $countryid)->select('countrycode')->first();
            if ($countrydata) {
                $country = $countrydata->countrycode;
            }

            $taxes = $cart->getGST($subtotal, $country);

            if ($country != '') {
                $taxvals = @explode("|", $taxes);
                $taxtitle = $taxvals[0];
                $gst = $taxvals[1];
            } else {
                $gst = $taxes;
            }

            $discount = $cart->getDiscount($subtotal, $gst, 0, 0, $coupondata->discount_type, $coupondata->discount);

            $grandtotal = $cart->getGrandTotal($subtotal, $gst, 0, 0, $discount);

            $discounttext = '';
            if ($coupondata->discount_type == 1) {
                $discounttext = $coupondata->discount . '%';
            } else {
                $discounttext = '$' . $coupondata->discount;
            }

            Session::put('discount', $coupondata->discount);
            Session::put('discounttext', $discounttext);
            Session::put('couponcode', $couponcode);
            Session::put('discounttype', $coupondata->discount_type);

            $response = $discount . '|' . $subtotal . '|' . $gst . '|' . $grandtotal . '|' . $discounttext;
        } else {
            Session::forget('discount');
            Session::forget('discounttext');
            Session::forget('couponcode');
            Session::forget('discounttype');
        }

        echo $response;
    }

    public function cancelcoupon()
    {
        Session::forget('discount');
        Session::forget('discounttext');
        Session::forget('couponcode');
        Session::forget('discounttype');
        return redirect('/cart');
    }

}
