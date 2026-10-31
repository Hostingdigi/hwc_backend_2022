<?php

namespace App\Services;

use App\Models\Cart;
use App\Models\Category;
use App\Models\CategoryShippingCharge;
use App\Models\Country;
use App\Models\Couponcode;
use App\Models\PaymentSettings;
use App\Models\Price;
use App\Models\Product;
use App\Models\ProductOptions;
use Illuminate\Support\Facades\DB;

/**
 * The mobile app's checkout total, computed on the server from the customer's
 * server-side cart (cart_details).
 *
 * This is the calculation behind POST /api/get-shipping-package-price (the total
 * the app displays), moved here unchanged so createorder can check the amount
 * the app submits against it. Known quirk kept as-is: a percentage coupon is
 * applied on a base that counts fuel and handling twice.
 */
class MobileCheckoutQuote
{
    public function __construct(private CartServices $cartServices)
    {
    }

    /**
     * @return array The fields returned by get-shipping-package-price, in order.
     */
    public function forCustomer($customerId, $country, $deliverymethod, $couponcode = ''): array
    {
        return $this->forItems(DB::table('cart_details')->where('user_id', $customerId)->get(), $country, $deliverymethod, $couponcode);
    }

    /**
     * @param  iterable  $cartdetails  objects with prod_id, prod_quantity, prod_option_id
     */
    public function forItems($cartdetails, $country, $deliverymethod, $couponcode = ''): array
    {
        $subtotal = $deliverycost = $packingfee = 0;

        $price = new Price();
        foreach ($cartdetails as $cartItem) {
            $productprice = $price->getDiscountPrice($cartItem->prod_id);
            $total = $productprice * $cartItem->prod_quantity;
            $productDeails = Product::find($cartItem->prod_id);
            $prodOptionDetails = ProductOptions::where([['Prod', '=', $cartItem->prod_id], ['Id', '=', $cartItem->prod_option_id]])->first();

            if ($cartItem->prod_option_id > 0 && !empty($prodOptionDetails)) {
                $optionprice = $price->getOptionPrice($cartItem->prod_id, $cartItem->prod_option_id) * $cartItem->prod_quantity;
                $total += $optionprice;
                $cartItem->shippingbox = $prodOptionDetails->ShippingBox;
                $cartItem->productWeight = $prodOptionDetails->Weight;
            } else {
                $cartItem->shippingbox = $productDeails->ShippingBox;
                $cartItem->productWeight = $productDeails->Weight;
            }
            $subtotal += $total;
        }

        $cart = new Cart();

        // Prices include tax: back it out, then charge it on the ex-tax subtotal
        $countryDetails = Country::where('countrycode', $country)->first();
        $taxAmount = round((($subtotal * $countryDetails->taxpercentage) / (100 + $countryDetails->taxpercentage)), 2);
        $subtotal = round($subtotal - $taxAmount, 2);

        $taxDetails = $this->cartServices->getTax($subtotal, $country);
        $gstText = $taxDetails['taxLabel'];
        $gst = $taxDetails['taxTotal'];

        if ($country != 'SG') {
            $settings = PaymentSettings::where('Id', '=', '1')->select('min_package_fee')->first();
            $packingfee = $settings->min_package_fee;
        }

        $boxfees = 0;
        $totalweight = $deliverycosttotalitem = $totaldeliverycost = 0;
        $shippingbox = '';
        $quantity = 1;

        foreach ($cartdetails as $item) {
            $totalweight += $item->productWeight;
            $shippingbox = $item->shippingbox;
            if ($shippingbox == "L" || $shippingbox == "XL" || $shippingbox == "XXL" || $shippingbox == "P") {
                $deliverycostperitem = $cart->shippingCost($country, $deliverymethod, $shippingbox, $item->prod_quantity, $subtotal, $gst, $item->productWeight);
                $deliverycosttotalitem += $deliverycostperitem * $item->prod_quantity;
            }
            $quantity = $item->prod_quantity;
            $boxfees += $cart->getPackagingFee($country, $totalweight, $subtotal, $gst, $deliverymethod, $shippingbox, $quantity);
        }

        if ($totalweight > 0) {
            $totaldeliverycost = $cart->shippingCost($country, $deliverymethod, $shippingbox, $quantity, $subtotal, $gst, $totalweight);
        }

        $deliverycost = $totaldeliverycost;

        // A category shipping charge replaces the calculated delivery cost
        $defaultDeliveryCharges = [];
        if (!in_array($deliverymethod, [1, 3, 10])) {
            foreach ($cartdetails as $val) {
                $product = Product::where('Id', $val->prod_id)->first();
                if ($product && Category::where('TypeId', $product->Types)->exists()) {
                    $charge = CategoryShippingCharge::where('status', 1)->whereRaw('FIND_IN_SET(?, category_ids)', [$product->Types])->first();
                    if ($charge && !in_array($charge->id, $defaultDeliveryCharges)) {
                        $defaultDeliveryCharges[] = $charge->id;
                    }
                }
            }
        }
        if (!empty($defaultDeliveryCharges)) {
            $deliverycost = $totaldeliverycost = CategoryShippingCharge::where('status', 1)->whereIn('id', $defaultDeliveryCharges)->sum('amount');
        }

        $packingfee = number_format(($packingfee + $boxfees), 2);

        $discounttype = $disamount = 0;
        $discountInvalid = false;
        $discounttext = $discounterrortext = '';
        if (!empty($couponcode)) {
            $coupondata = Couponcode::where('coupon_code', '=', trim($couponcode))->where('status', '=', '1')->first();
            if ($coupondata) {
                $discounttext = 'Coupon discount(' . ($coupondata->discount_type == 1 ? $coupondata->discount . '%)' : '$' . $coupondata->discount . ')');
                $discounttype = $coupondata->discount_type;
                $disamount = $coupondata->discount;
            } else {
                $discountInvalid = true;
                $discounterrortext = 'Invalid coupon code';
            }
        }
        $fuelcharges = $this->cartServices->getFuelCharges($deliverycost, $country);
        $handlingfee = $this->cartServices->getHandlingFee($totalweight, $country);
        $deliverycost += $fuelcharges + $handlingfee;

        $discount = $cart->getDiscount($subtotal, $gst, $deliverycost, $packingfee, $discounttype, $disamount, $fuelcharges, $handlingfee);
        $grandtotal = $cart->getGrandTotal($subtotal, $gst, $deliverycost, $packingfee, $discount, 0, 0);

        return [
            'gstText' => $gstText,
            'gst' => $gst,
            'packagingprice' => $packingfee,
            'shippingprice' => $deliverycost,
            'grandtotal' => $grandtotal,
            'deliverycosttotalitem' => $deliverycosttotalitem,
            'totaldeliverycost' => $totaldeliverycost,
            'totalweight' => $totalweight,
            'subtotal_text' => 'w/o ' . $taxDetails['taxLabelOnly'],
            'subtotal' => $subtotal,
            'discount' => $discount,
            'discount_text' => $discounttext,
            'is_discount_invalid' => $discountInvalid,
            'discount_error_text' => $discounterrortext,
            'fuelcharges' => number_format($fuelcharges, 2),
            'handlingfee' => number_format($handlingfee, 2),
        ];
    }

    /** The grand total as a number (the quote formats it like "1,234.50"). */
    public static function amount(array $quote): float
    {
        return (float) str_replace(',', '', (string) $quote['grandtotal']);
    }
}
