<?php

namespace App\Services;

use App\Models\Cart;
use App\Models\Country;
use App\Models\ShippingMethods;
use App\Models\PaymentSettings;
use Session;

class CartServices
{
    public function cartItems($countryCode = 'SG')
    {
        $custToken = Session::get('_token');
        $taxvals = [];
        $subTotal = 0;
        $cartData = [
            'countryCode' => $countryCode != 'SG' ? $countryCode : 'SG',
            'countryId' => 189,
            'cartItems' => null,
            'subTotal' => 0,
            'taxDetails' => null,
            'discountDetails' => null,
            'deliveryDetails' => null,
            'grandTotal' => 0,
        ];
        $cart = new Cart();

        $settings = PaymentSettings::where('Id', '1')->select('min_package_fee')->first();
        $packingFee = $cartData['countryCode'] != 'SG' ? $settings->min_package_fee : 0;

        if (Session::has('customer_id')) {
            $customer = Customer::find(Session::get('customer_id'));
            $countrydata = Country::select('countrycode')->where('countryid', $customer->cust_country)->first();
            if ($countrydata) {
                $cartData['countryCode'] = $countrydata->countrycode;
                $cartData['countryId'] = $countrydata->countryid;
            }
        }

        $cartData['deliveryDetails'] = $this->deliveryDetails();
        $deliveryTotal = $boxFees = $totalWeight = 0;
        $cartData['cartItems'] = Session::has('cartdata') ? Session::get('cartdata')[$custToken] : [];

        foreach ($cartData['cartItems'] as $key => $val) {
            $subTotal += $val['total'];
        }

        //Exclude default tax amount
        $countryDetails = Country::where('countrycode', 'SG')->first();
        $taxAmount = round((($subTotal * $countryDetails->taxpercentage) / (100+$countryDetails->taxpercentage) ),2);
        $subTotal = round($subTotal-$taxAmount, 2);

        $cartData['subTotal'] = $subTotal;
        $cartData['taxDetails'] = $this->getTax($subTotal, $cartData['countryCode']);
        //$subTotal = $cartData['subTotal'] = round(($subTotal - $cartData['taxDetails']['taxTotal']), 2);

        foreach ($cartData['cartItems'] as $key => $val) {

            $totalWeight += $val['weight'];
            $boxFees = $cart->getPackagingFee($cartData['countryCode'], $totalWeight, $subTotal, $cartData['taxDetails']['taxTotal'], 
                (!empty($cartData['deliveryDetails']['deliverymethod']) ? $cartData['deliveryDetails']['deliverymethod'] : 0), $val['shippingbox'], $val['qty']);

            if (!empty($cartData['deliveryDetails']['deliverymethod'])) {
                $deliveryTotal += $cart->shippingCost($cartData['countryCode'], $cartData['deliveryDetails']['deliverymethod'], $val['shippingbox'], $val['qty'], $subTotal, $cartData['taxDetails']['taxTotal']);
            }
        }
        $packingFee += $boxFees;
        $packingFee = round($packingFee, 2);

        $cartData['deliveryDetails']['deliveryTotal'] = $deliveryTotal;
        $cartData['packingFees'] = $packingFee;
        $cartData['discountDetails'] = $this->getDiscount($subTotal, $cartData['taxDetails']['taxTotal'], $deliveryTotal, $packingFee);
        $cartData['grandTotal'] = $this->getGrandTotal($subTotal, $cartData['taxDetails']['taxTotal'], $deliveryTotal, $packingFee, $cartData['discountDetails']['discountTotal']);

        print_r($cartData);
        return $cartData;

    }

    public function deliveryDetails()
    {
        $deliveryTotal = $deliverymethod = 0;
        $deliverytype = '';

        if (Session::has('deliverymethod')) {
            $deliverydata = ShippingMethods::where('Id', Session::get('deliverymethod'))->first();
            $deliverytype = $deliverydata->EnName;
            $deliverymethod = Session::get('deliverymethod');
        }

        return [
            'deliverymethod' => $deliverymethod,
            'title' => $deliverytype,
            'deliveryTotal' => $deliveryTotal,
        ];
    }

    public function getDiscount($subTotal = 0, $taxTotal = 0, $shippingcost = 0, $packingfee = 0)
    {
        $discounttype = $disamount = 0;
        $discountTitle = '';
        if (Session::has('couponcode')) {
            $discounttype = Session::get('discounttype');
            $disamount = Session::get('discount');
            $discountTitle = Session::get('discounttext');
            $tmptotal = $subTotal + $taxTotal + $shippingcost + $packingfee;
            if ($discounttype == 1) {
                $disamount = ($tmptotal * $disamount) / 100;
            } elseif ($discounttype == 2) {
                $disamount = $tmptotal - $disamount;
            }
        }

        return [
            'title' => $discountTitle,
            'discountTotal' => round($disamount, 2),
        ];
    }

    public function getGrandTotal($subTotal = 0, $taxTotal = 0, $deliverycost = 0, $packingfee = 0, $discount = 0)
    {
        $grandtotal = $subTotal + $taxTotal + $deliverycost + $packingfee;
        $grandtotal = $grandtotal - $discount;

        return round($grandtotal, 2);
    }

    public function getTax($subTotal = 0, $countryCode = '')
    {

        $countryDetails = Country::where('countrycode', $countryCode)->first();
        $taxPercentage = $countryDetails->taxpercentage;
        $taxLabelOnly = !empty($countryDetails->taxtitle) ? $countryDetails->taxtitle : 'Tax';

        return [
            'taxLabelOnly' => $taxLabelOnly,
            'taxLabel' => $taxLabelOnly . ' (' . $taxPercentage . '%)',
            'taxPercentage' => $taxPercentage,
            'taxTotal' => round((($subTotal * $taxPercentage) / 100), 2),
        ];
    }
}
