<?php

namespace App\Services;

use App\Models\Cart;
use App\Models\Country;
use App\Models\Customer;
use App\Models\InternationalShipping;
use App\Models\LocalShipping;
use App\Models\PaymentSettings;
use App\Models\ShippingMethods;
use Session;

class CartServices
{
    public function cartItems($countryCode = null)
    {
        $allowCust = 0;
        if (Session::has('customer_id')) {
            if (empty($countryCode)) {
                $allowCust = 1;
            } /*else if(!Session::has('billinginfo')){
        $allowCust = 1;
        }*/
        }

        $custToken = Session::get('_token');
        $taxvals = [];
        $subTotal = 0;
        $cartData = [
            'countryCode' => !empty($countryCode) ? $countryCode : 'SG',
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

        if (Session::has('customer_id') && $allowCust) {
            $customer = Customer::where('cust_id', Session::get('customer_id'))->first();

            if (!empty($customer->cust_country)) {

                $countryCodeCheck = (int) $customer->cust_country;
                $countryCodeConditions = $countryCodeCheck ? [['countryid', '=', $customer->cust_country]] :
                [['countrycode', '=', $customer->cust_country]];
                $countrydata = Country::select('countrycode')->where($countryCodeConditions)->first();
                if ($countrydata) {

                    $cartData['countryCode'] = $countrydata->countrycode;
                    $cartData['countryId'] = $countrydata->countryid;
                }
            }

        }

        $cartData['deliveryDetails'] = $this->deliveryDetails();
        $deliveryTotal = $boxFees = $totalWeight = 0;
        $cartData['cartItems'] = Session::has('cartdata') ? Session::get('cartdata')[$custToken] : [];

        foreach ($cartData['cartItems'] as $key => $val) {
            $subTotal += $val['total'];
        }

        //Extract tax amount
        $countryDetails = Country::where('countrycode', $cartData['countryCode'])->first();
        $taxAmount = round((($subTotal * $countryDetails->taxpercentage) / (100 + $countryDetails->taxpercentage)), 2);
        $subTotal = round($subTotal - $taxAmount, 2);

        $cartData['subTotal'] = $subTotal;
        $cartData['taxDetails'] = $this->getTax($subTotal, $cartData['countryCode']);
        //$subTotal = $cartData['subTotal'] = round(($subTotal - $cartData['taxDetails']['taxTotal']), 2);
        $shippingbox = '';
        $quantity = 0;
        foreach ($cartData['cartItems'] as $key => $val) {

            $totalWeight += $val['weight'];
            $quantity = $val['qty'];
            $shippingbox = $val['shippingbox'];
            $boxFees = $cart->getPackagingFee($cartData['countryCode'], $totalWeight, $subTotal, $cartData['taxDetails']['taxTotal'],
                (!empty($cartData['deliveryDetails']['deliverymethod']) ? $cartData['deliveryDetails']['deliverymethod'] : 0), $shippingbox, $quantity);
        }

        if (!empty($cartData['deliveryDetails']['deliverymethod'])) {
            $deliveryTotal = $this->getShippingCost($cartData['countryCode'], $cartData['deliveryDetails']['deliverymethod'], $shippingbox, $quantity, $subTotal, $cartData['taxDetails']['taxTotal'], $totalWeight);
            // $deliveryTotal = $cart->shippingCost($cartData['countryCode'], $cartData['deliveryDetails']['deliverymethod'], $shippingbox, $quantity, $subTotal, $cartData['taxDetails']['taxTotal'], $totalWeight);
        }
        $packingFee += $boxFees;
        $packingFee = round($packingFee, 2);

        $cartData['deliveryDetails']['deliveryTotal'] = $deliveryTotal;
        $cartData['packingFees'] = $packingFee;
        $cartData['discountDetails'] = $this->getDiscount($subTotal, $cartData['taxDetails']['taxTotal'], $deliveryTotal, $packingFee);
        $cartData['grandTotal'] = $this->getGrandTotal($subTotal, $cartData['taxDetails']['taxTotal'], $deliveryTotal, $packingFee, $cartData['discountDetails']['discountTotal']);

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

    public function getShippingCost($countrycode = '', $deliverymethod = 0, $shippingbox = '', $quantity = 1, $subtotal = 0, $gst = 0, $totalweight = 0)
    {
        $shipamt = 0;
        $initial_amt = $first_comm = $second_comm = $calculated = $dhl_extra_amount = 0;
        $countrydata = Country::where('countrycode', $countrycode)->select('countryid')->first();
        $countryid = $countrydata ? $countrydata->countryid : 0;
        $freeshippingcost = $subtotal + $gst;

        if ($countrycode == 'SG') {

            $zones = LocalShipping::where('Status', 1)->orderBy('DisplayOrder', 'asc')->first();
            $shippingmethods = ShippingMethods::where('Id', $deliverymethod)->first();

            if (($freeshippingcost >= $zones->FreeShipCost || ($shippingmethods->shipping_type == 0 && strpos($shippingmethods->EnName, 'Ninja Van Delivery') === false))) {
                //if ($freeshippingcost >= $zones->FreeShipCost || ($shippingmethods->shipping_type == 0 && strpos($shippingmethods->EnName, 'Ninja Van Delivery') === false)) {
                $shipamt = 0;
            } else {

                if ($totalweight <= 5) {
                    $shipamt = $zones->PriceRange5;
                } elseif ($totalweight <= 15) {
                    $shipamt = $zones->PriceRange15;
                } elseif ($totalweight <= 30) {
                    $shipamt = $zones->PriceRange30;
                } elseif ($totalweight > 30) {
                    $shipamt = $zones->PriceRangeAbove30;
                }
            }
        } else {

            $weight = $totalweight;
            $zones = InternationalShipping::where('Status', 1)->whereRaw("concat(',',CountriesList,',') LIKE '%," . $countryid . "%,'")->first();

            if ($zones) {
                $freeshipcost = $zones->FreeShippingCost;
                if ($freeshipcost != '0.00' && $freeshipcost > 0 && $subtotal >= $freeshipcost) {
                    $shipamt = 0;
                } else {
                    if ($weight > 300) {
                        $initial_amt = ($weight * $zones->PriceRange99999);
                    } elseif ($weight > 70) {
                        $initial_amt = ($weight * $zones->PriceRange300);
                    } elseif ($weight > 30) {
                        $initial_amt = ($weight * $zones->PriceRange70);
                    } else {
                        if ($zones->ShipCost != '') {
                            $shipcosts = @explode(",", $zones->ShipCost);
                            foreach ($shipcosts as $shipcost) {
                                $tmpshipcosts = @explode(':', $shipcost);
                                if (is_array($tmpshipcosts) && !empty($tmpshipcosts)) {
                                    if ($weight <= $tmpshipcosts[0]) {
                                        $initial_amt = $tmpshipcosts[1];
                                        break;
                                    }
                                }
                            }
                        }
                    }

                    $first_comm = (($initial_amt / 100) * 25);
                    $second_comm = ($first_comm / 100) * 8;
                    $shipamt = $first_comm + $second_comm;

                    /*DHL extra amount*/
                    if ($weight <= 2.5) {
                        $dhl_extra_amount = 0;
                    } else if ($weight <= 30) {
                        $dhl_extra_amount = 8.5;
                    } else if ($weight <= 70) {
                        $dhl_extra_amount = 27.5;
                    } else if ($weight <= 300) {
                        $dhl_extra_amount = 79.5;
                    } else if ($weight > 300) {
                        $dhl_extra_amount = 304.5;
                    }

                    $shipamt = $initial_amt + $shipamt + $dhl_extra_amount;
                }
            }
        }

        return $shipamt;
    }
}
