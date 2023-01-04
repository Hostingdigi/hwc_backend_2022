<?php

namespace App\Services;

use App\Models\Country;
use App\Models\Customer;
use App\Models\OrderDetails;
use App\Models\OrderMaster;
use App\Models\PaymentMethods;
use App\Models\Product;
use App\Services\CartServices;
use Session;

class OrderServices
{
    protected $cartServices = null;

    public function __construct(CartServices $cartServices)
    {
        $this->cartServices = $cartServices;
    }

    public function createOrder()
    {
        $orderReturnData = [
            'status' => false,
            'orderId' => null,
        ];

        $billinginfo = Session::has('billinginfo') ? Session::get('billinginfo') : [];
        $cartItems = $this->cartServices->cartItems($billinginfo['bill_country'] ?? null);

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

        $billinginfo = Session::get('billinginfo');
        $countrydata = Country::where('countrycode', $billinginfo['bill_country'])->select('countryid')->first();
        $countryid = $countrydata ? $countrydata->countryid : 0;
        $orderid = $userid = null;
        if (Session::has('customer_id')) {
            $userid = Session::get('customer_id');
        } else {
            $chkcustomer = Customer::where('cust_email', $billinginfo['bill_email'])->select('cust_id')->first();
            if (!$chkcustomer) {
                $userid = Customer::insertGetId(['cust_firstname' => $billinginfo['bill_fname'], 'cust_lastname' => $billinginfo['bill_lname'], 'cust_email' => $billinginfo['bill_email'], 'cust_address1' => $billinginfo['bill_ads1'], 'cust_address2' => $billinginfo['bill_ads2'], 'cust_city' => $billinginfo['bill_city'], 'cust_state' => $billinginfo['bill_state'], 'cust_country' => $countryid, 'cust_zip' => $billinginfo['bill_zip'], 'cust_phone' => $billinginfo['bill_mobile'], 'cust_status' => 0]);
            } else {
                $userid = $chkcustomer->cust_id;
            }
        }

        if (empty($userid)) {
            return $orderReturnData;
        }

        $paymentmethod = Session::has('paymentmethod') ? Session::get('paymentmethod') : 0;
        $paymethod = PaymentMethods::where('id', '=', $paymentmethod)->first();
        $paymethodname = $paymethod ? $paymethod->payment_name : '';

        $couponid = 0;
        if (Session::has('couponcode')) {
            $couponcode = Session::get('couponcode');
            $coupondata = Couponcode::where([['coupon_code', '=', $couponcode], ['status', '=', '1']])->first();
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
        $ordermaster['date_entered'] = date('Y-m-d H:i:s');
        $order = $ordermaster->save();

        if (!$order) {
            return $orderReturnData;
        }

        $order = OrderMaster::orderBy('order_id', 'desc')->select('order_id')->first();
        $orderid = $order->order_id;

        foreach ($cartdata as $cart) {

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

            $qty = 0;
            $product = Product::where('Id', $cart['productId'])->select('Quantity')->first();
            if ($product->Quantity > $cart['qty']) {
                $qty = $product->Quantity - $cart['qty'];
            }
            Product::where('Id', $cart['productId'])->update(['Quantity' => $qty]);
        }

        $orderReturnData['status'] = true;
        $orderReturnData['orderId'] = $orderid;
        $orderReturnData['userId'] = $userid;

        return $orderReturnData;
    }

}
