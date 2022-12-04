<?php

namespace App\Models;

use App\Models\Country;
use App\Models\InternationalShipping;
use App\Models\LocalShipping;
use App\Models\PaymentSettings;
use App\Models\ShippingMethods;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Session;

class Cart extends Model
{
    use HasFactory;

    public function getSubTotal()
    {

        $custToken = Session::get('_token');

        $subtotal = 0;
        if (Session::has('cartdata')) {
            $cartdata = Session::get('cartdata');
            if (!empty($cartdata)) {
                foreach ($cartdata[$custToken] as $key => $val) {
                    $subtotal = (float) $subtotal + (float) $val['total'];
                }
            }
        }

        return number_format($subtotal, 2);
    }

    public function getGST($subtotal = 0, $countryid = '')
    {
        $countrys = [];
        $taxtitle = $taxval = '';
        $gst = 0;
        $countryid = empty($countryid) ? 'SG' : $countryid;
        if ($countryid != '') {
            $countrys = Country::where('countrycode', '=', $countryid)->first();
            if ($countrys) {
                if ($countrys->taxtitle != '') {
                    $taxtitle = $countrys->taxtitle . '*' . $countrys->taxtitle . ' (' . (float) $countrys->taxpercentage . '%)';
                } else {
                    $taxtitle = 'Tax*Tax (' . $countrys->taxpercentage . '%)';
                }
                $taxval = $countrys->taxpercentage;
                $gst = (float) str_replace(',', '', $subtotal) * (float) $taxval;
            }
        } else {
            $tax = 7;
            $gst = (float) str_replace(',', '', $subtotal) * (float) $tax;
        }
        $gst = $gst / 100;
        if ($countryid != '') {
            return $taxtitle . "|" . number_format($gst, 2);
        } else {
            return number_format($gst, 2);
        }

    }

    public function getGrandTotal($subtotal = 0, $gst = 0, $deliverycost = 0, $packingfee = 0, $discount = 0)
    {
        $grandtotal = (float) str_replace(',', '', $subtotal) + (float) str_replace(',', '', $gst) + (float) str_replace(',', '', $deliverycost) + (float) str_replace(',', '', $packingfee);

        $grandtotal = (float) $grandtotal - (float) str_replace(',', '', $discount);

        return number_format($grandtotal, 2);
    }

    public function getCartSubTotal($customerid = 0)
    {

        $custToken = $customerid;

        $subtotal = 0;
        if (Session::has('cartdata')) {
            $cartdata = Session::get('cartdata');
            if (!empty($cartdata)) {
                foreach ($cartdata[$custToken] as $key => $val) {
                    $subtotal = (float) $subtotal + (float) $val['total'];
                }
            }
        }

        return number_format($subtotal, 2);
    }

    public function getDeliveryMethod($deliverymethod = 0)
    {
        $deliverycost = 0;
        $deliverytype = '';
        $deliverydata = ShippingMethods::where('Id', '=', $deliverymethod)->first();
        if ($deliverydata) {
            $deliverytype = $deliverydata->EnName;
        }
        return $deliverytype;
    }

    public function getPackagingFee($countrycode = '', $totalweight = '', $subtotal = 0, $gst = 0, $deliverymethod = 0, $shippingbox = '', $quantity = 1)
    {
        $countryid = $packingfee = $addamount = $minimumpackagefee = $free_shipping_amount = $shipamt = $shipweightamt = $tmppackingfee = 0;

        $zones = [];
        if ($countrycode != '') {

            $settings = PaymentSettings::where('Id', '=', '1')->first();

            $free_shipping_amount = $settings->free_shipping_amount;

            $weight = $this->internationalShippingBoxWeight($shippingbox);

            $weight = $weight * $quantity;

            $countrydata = Country::where('countrycode', '=', $countrycode)->select('countryid')->first();
            if ($countrydata) {
                $countryid = $countrydata->countryid;
            }
            $freeshippingcost = (float) str_replace(',', '', $subtotal) + (float) str_replace(',', '', $gst);
            if ($countrycode == 'SG') {
                $zones = LocalShipping::where('Status', '=', '1')->orderBy('DisplayOrder', 'asc')->first();
                $shippingmethods = ShippingMethods::where('Id', '=', $deliverymethod)->first();

                if ($shippingmethods && ($freeshippingcost >= $free_shipping_amount || ($shippingmethods->shipping_type == 0 && strpos($shippingmethods->EnName, 'Ninja Van Delivery') === false))) {
                    $shipamount = 0;
                    $shipweightamt = 0;
                    $packingfee = 0;
                } else {

                    $shipamount = 0;
                    $shipweightamt = 0;
                    $packingfee = 0;
                    /*if($weight <= 5) {
                    $packingfee = $zones->PriceRange5;
                    } elseif($weight <= 15) {
                    $packingfee = $zones->PriceRange15;
                    } elseif($weight <= 30) {
                    $packingfee = $zones->PriceRange30;
                    } elseif($weight > 30) {
                    $packingfee = $zones->PriceRangeAbove30;
                    } */
                    /*if($shippingbox == 'P') {
                $packingfee = $quantity * $settings->P_package_fee;
                } elseif($shippingbox == 'XXL') {
                $packingfee = $quantity * $settings->XXL_package_fee;
                } elseif($shippingbox == 'XL') {
                $packingfee = $quantity * $settings->XL_package_fee;
                } elseif($shippingbox == 'L') {
                $packingfee = $quantity * $settings->L_package_fee;
                } elseif($shippingbox == 'M') {
                $packingfee = $quantity * $settings->M_package_fee;
                } elseif($shippingbox == 'S') {
                $packingfee = $quantity * $settings->S_package_fee;
                } elseif($shippingbox == 'XS') {
                $packingfee = $quantity * $settings->XS_package_fee;
                } elseif($shippingbox == 'XXS') {
                $packingfee = $quantity * $settings->XXS_package_fee;
                }
                $packingfee = ceil($packingfee);    */
                }
            } else {
                //$packingfee = $minimumpackagefee;
                if ($shippingbox == 'P') {
                    $packingfee = $quantity * $settings->P_package_fee;
                } elseif ($shippingbox == 'XXL') {
                    $packingfee = $quantity * $settings->XXL_package_fee;
                } elseif ($shippingbox == 'XL') {
                    $packingfee = $quantity * $settings->XL_package_fee;
                } elseif ($shippingbox == 'L') {
                    $packingfee = $quantity * $settings->L_package_fee;
                } elseif ($shippingbox == 'M') {
                    $packingfee = $quantity * $settings->M_package_fee;
                } elseif ($shippingbox == 'S') {
                    $packingfee = $quantity * $settings->S_package_fee;
                } elseif ($shippingbox == 'XS') {
                    $packingfee = $quantity * $settings->XS_package_fee;
                } elseif ($shippingbox == 'XXS') {
                    $packingfee = $quantity * $settings->XXS_package_fee;
                }
                $packingfee = ceil($packingfee);
            }
        }

        return $packingfee;
    }

    public function internationalShippingBoxWeight($boxsize = 'S')
    {
        $weight = 0;
        if ($boxsize == "XXS") {
            $weight = (33.7 * 18.2 * 10) / 6000;
        } elseif ($boxsize == "XS") {
            $weight = (33.6 * 32 * 5.2) / 6000;
        } elseif ($boxsize == "S") {
            $weight = (33.7 * 32.2 * 18) / 6000;
        } elseif ($boxsize == "M") {
            $weight = (33.7 * 32.2 * 34.5) / 6000;
        } else if ($boxsize == "L") {
            $weight = (41.7 * 35.9 * 36.9) / 6000;
        } else if ($boxsize == "XL") {
            $weight = (48.1 * 40.4 * 38.9) / 5000;
        } elseif ($boxsize == "XXL") {
            $weight = (54.1 * 44.4 * 40.9) / 5000;
        }
        return $weight;
    }

    public function shippingCost($countrycode = '', $deliverymethod = 0, $shippingbox = '', $quantity = 1, $subtotal = 0, $gst = 0)
    {
        $shipamt = '0.00';
        $countryid = $initial_amt = $first_comm = $second_comm = $calculated = $dhl_extra_amount = 0;
        $countrydata = Country::where('countrycode', '=', $countrycode)->select('countryid')->first();
        if ($countrydata) {
            $countryid = $countrydata->countryid;
        }

        $weight = $this->internationalShippingBoxWeight($shippingbox);

        $weight = $weight * $quantity;

        $freeshippingcost = (float) str_replace(',', '', $subtotal) + (float) str_replace(',', '', $gst);

        if ($countrycode == 'SG') {

            $zones = LocalShipping::where('Status', '=', '1')->orderBy('DisplayOrder', 'asc')->first();

            $shippingmethods = ShippingMethods::where('Id', '=', $deliverymethod)->first();

            if ($freeshippingcost >= $zones->FreeShipCost || ($shippingmethods->shipping_type == 0 && strpos($shippingmethods->EnName, 'Ninja Van Delivery') === false)) {
                $shipamt = '0.00';
            } else {
                if ($weight <= 5) {
                    $shipamt = $zones->PriceRange5;
                } elseif ($weight <= 15) {
                    $shipamt = $zones->PriceRange15;
                } elseif ($weight <= 30) {
                    $shipamt = $zones->PriceRange30;
                } elseif ($weight > 30) {
                    $shipamt = $zones->PriceRangeAbove30;
                } else {
                    $shipamt = '0.00';
                }
            }
        } else {

            $zones = InternationalShipping::where('Status', '=', '1')->whereRaw("concat(',',CountriesList,',') LIKE '%," . $countryid . "%,'")->first();

            if ($zones) {
                $freeshipcost = $zones->FreeShippingCost;
                if ($freeshipcost != '0.00' && $freeshipcost > 0 && $subtotal >= $freeshipcost) {
                    $shipamt = '0.00';
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

                    $first_comm = $initial_amt + (($initial_amt / 100) * 18);
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

                    $shipamt = $shipamt + $dhl_extra_amount;
                }
            }
        }

        return number_format($shipamt, 2);
    }

    public function getDiscount($subtotal = 0, $tax = 0, $shippingcost = 0, $packingfee = 0, $distype = 0, $dis = 0)
    {
        $disamount = 0;
        if ($distype == 1) {
            $tmptotal = (float) str_replace(',', '', $subtotal) + (float) str_replace(',', '', $tax) + (float) str_replace(',', '', $shippingcost) + (float) str_replace(',', '', $packingfee);
            $disamount = ($tmptotal * $dis) / 100;
        } elseif ($distype == 2) {
            $disamount = ((float) str_replace(',', '', $subtotal) + (float) str_replace(',', '', $tax) + (float) str_replace(',', '', $shippingcost) + (float) str_replace(',', '', $packingfee)) - $dis;
        }

        return number_format($disamount, 2);
    }

}
