<?php

namespace App\Services;

use App\Models\OrderDetails;
use App\Models\OrderMaster;
use App\Models\PaymentMethods;
use App\Models\Product;
use App\Models\Country;
use App\Models\Couponcode;
use App\Models\Customer;
use App\Services\CartServices;
use App\Models\PaymentSettings;
use App\Models\SessionCart;
use App\Models\OrderPayment;
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

        if(Session::get('customer_id')==30548 || 1==1){
			$cust_id = Session::get('customer_id');
			$SesCartObj = SessionCart::where('cust_id',$cust_id )->first();
			$billinginfo = json_decode($SesCartObj->billinginfo,true);
			$paymentmethod = ($SesCartObj->paymentmethod !='') ? $SesCartObj->paymentmethod : 0;
			$deliverymethod = $SesCartObj->deliverymethod;
			$if_items_unavailabel = $SesCartObj->if_unavailable;
			$delivery_instructions = $SesCartObj->delivery_instructions;
			$fuelcharge_percentage =  $SesCartObj->fuelcharge_percentage;
			$fuelcharges =  $SesCartObj->fuelcharges;
			$handlingfee =  $SesCartObj->handlingfee;
            $gst = $SesCartObj->gst;
            $taxPercentage = $SesCartObj->taxPercentage;
            $taxtitle = $SesCartObj->taxtitle;
			$taxLabelOnly = $SesCartObj->taxLabelOnly;
			$deliverytype = $SesCartObj->deliverytype;
            $packingfee = $SesCartObj->packingfee;
			$deliverycost = $SesCartObj->deliverycost;
			$discount = $SesCartObj->discount;
            $discounttext = $SesCartObj->discounttext;

		}
        
        $cartItems = $this->cartServices->cartItems($billinginfo['ship_country'] ?? null);
        $cartdata = $cartItems['cartItems'];
        $subtotal = $cartItems['subTotal'];

        /*$gst = $cartItems['taxDetails']['taxTotal'];
        $taxtitle = $cartItems['taxDetails']['taxLabel'];
        $taxLabelOnly = $cartItems['taxDetails']['taxLabelOnly'];
        $deliverytype = $cartItems['deliveryDetails']['title'];
        $packingfee = $cartItems['packingFees'];
        $deliverycost = $cartItems['deliveryDetails']['deliveryTotal'];
        $discount = $cartItems['discountDetails']['discountTotal'];
        $discounttext = $cartItems['discountDetails']['title']; */

        $grandtotal = $subtotal + $gst + $deliverycost + $packingfee+ $fuelcharges + $handlingfee;
		$grandtotal = $grandtotal - $discount;
        $grandtotal = $grandtotal;
        
        $grandtotal = $grandtotal;

        //$billinginfo = Session::get('billinginfo');
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

        $fuelSettings = PaymentSettings::where('Id', '1')->select('fuelcharge_percentage')->first();
        
        $isUnFulfilledOrderExist = OrderMaster::where(['user_id' => $userid,'is_fulfilled' => 0])->latest()->first();

        $orderData = [
            'shipping_cost' => $deliverycost,
            'fuelcharge_percentage' => $billinginfo['ship_country'] != 'SG' ? ($fuelSettings ? $fuelSettings->fuelcharge_percentage : 0) : 0,
            'fuelcharges' => $fuelcharges,
            'handlingfee' => $handlingfee,
            'packaging_fee' => $packingfee,
            'ship_method' => $deliverymethod,
            'tax_label' => $taxLabelOnly,
            'tax_percentage' => $taxPercentage,
            'tax_collected' => $gst,
            'payable_amount' => $this->roundDecimal($grandtotal),
            'discount_amount' => $discount,
            'discount_id' => $couponid,
            'pay_method' => $paymethodname,
            'ship_fname' => $billinginfo['ship_fname'],
            'ship_lname' => $billinginfo['ship_lname'],
            'ship_email' => $billinginfo['ship_email'],
            'ship_mobile' => $billinginfo['ship_mobile'],
            'ship_ads1' => $billinginfo['ship_ads1'],
            'ship_ads2' => $billinginfo['ship_ads2'],
            'ship_country' => $billinginfo['ship_country'],
            'ship_city' => $billinginfo['ship_city'],
            'ship_state' => $billinginfo['ship_state'],
            'ship_zip' => $billinginfo['ship_zip'],
            'bill_compname' => $billinginfo['bill_compname'],
            'bill_fname' => $billinginfo['bill_fname'],
            'bill_lname' => $billinginfo['bill_lname'],
            'bill_email' => $billinginfo['bill_email'],
            'bill_mobile' => $billinginfo['bill_mobile'],
            'bill_ads1' => $billinginfo['bill_ads1'],
            'bill_ads2' => $billinginfo['bill_ads2'],
            'bill_city' => $billinginfo['bill_city'],
            'bill_state' => $billinginfo['bill_state'],
            'bill_zip' => $billinginfo['bill_zip'],
            'bill_country' => $billinginfo['bill_country'],
            'if_items_unavailabel' => $if_items_unavailabel,
            'delivery_instructions' => $delivery_instructions,
        ];

        if ($isUnFulfilledOrderExist) {
            $orderid = $isUnFulfilledOrderExist->order_id;
            $ordermaster = OrderMaster::where('order_id', $orderid)->update($orderData + [
                'is_fulfilled' => 0,
                'user_id' => $userid,
                'order_status' => 0,
                'date_entered' => now()
            ]);
            if (!$ordermaster) return $orderReturnData;
        }else{
            $ordermaster = OrderMaster::create($orderData + [
                'is_fulfilled' => 0,
                'user_id' => $userid,
                'order_status' => 0,
                'date_entered' => now()
            ]);
            if (!$ordermaster) return $orderReturnData;
            $orderid = $ordermaster->id;
        }

        //payment log
        OrderPayment::create($orderData + [
            'order_id' => $orderid,
            'payment_status' => 0
        ]);

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
    
    public function roundDecimal($value)
    {
        $addition = 0;
        if (strpos($value, '.') !== false) {
            list($whole, $decimal) = explode('.', $value);
            
            if(!empty($decimal[1])){
                $v = $decimal[1];
                if($v>=1 && $v<5): $addition = 5-$v;
                elseif($v>=6 && $v<10): $addition = 10-$v;
                endif;
                $value = strlen($decimal)>2 ? $whole.'.'.($decimal[0].''. $decimal[1]) : $value;
            }
        }
        return $addition!=0 ? ($value+($addition/100)) : $value;
    }
    
    public function manipulateOrderNumber($orderId,$orderDate){
        $orderIdLength = strlen($orderId);
        $leadZeros = [
            1 => '000',
            2 => '00',
            3 => '0',
        ];
        $formattedOrderId = date('Ymd', strtotime($orderDate)).($leadZeros[$orderIdLength] ?? '').$orderId;
        return $formattedOrderId;
    }

}
