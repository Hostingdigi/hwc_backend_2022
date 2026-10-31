<?php

namespace App\Services;

use App\Models\Country;
use App\Models\Customer;
use App\Models\OrderMaster;
use App\Models\OrderPayment;
use App\Models\PaymentMethods;
use App\Models\PaymentSettings;
use Illuminate\Support\Facades\DB;

/**
 * Creates an unpaid order from the customer's server-side cart (API v2).
 *
 * Every amount comes from the server: item prices from the catalogue, totals
 * from MobileCheckoutQuote. The app may send the total it displayed
 * (expected_total); if it no longer matches, nothing is created and the caller
 * gets the current quote back.
 */
class OrderPlacement
{
    public function __construct(
        private CartStore $cart,
        private MobileCheckoutQuote $quotes,
        private CouponValidator $coupons,
    ) {
    }

    /**
     * @param  array  $data  validated PlaceOrderRequest data
     * @return array{order: OrderMaster, quote: array}
     *
     * @throws OrderPlacementException
     */
    public function place(Customer $customer, array $data): array
    {
        $lines = $this->cart->lines($customer->cust_id);
        if ($lines->isEmpty()) {
            throw new OrderPlacementException('Your cart is empty.', 'cart_empty');
        }
        if ($lines->contains(fn ($line) => !$line->in_stock)) {
            throw new OrderPlacementException('Some items in your cart are out of stock.', 'out_of_stock');
        }

        $shipping = $data['shipping'];
        $billing = $data['billing'] ?? $shipping;
        $couponCode = $data['coupon_code'] ?? '';

        $coupon = null;
        if ($couponCode !== '') {
            $check = $this->coupons->check($couponCode, $customer->cust_id, $lines->pluck('product.Id')->all());
            if (!$check['valid']) {
                throw new OrderPlacementException($check['message'], 'coupon_invalid');
            }
            $coupon = $check['coupon'];
        }

        $quote = $this->quotes->forCustomer($customer->cust_id, $shipping['country'], $data['shipping_method_id'], $couponCode);
        $total = MobileCheckoutQuote::amount($quote);

        if (isset($data['expected_total']) && abs((float) $data['expected_total'] - $total) > 0.01) {
            throw new OrderPlacementException('Prices have changed. Please review your order.', 'price_changed', $quote);
        }

        $method = PaymentMethods::available()->where('status', 1)->where('Id', $data['payment_method_id'])->first();
        if (!$method) {
            throw new OrderPlacementException('This payment method is not available.', 'payment_method_unavailable');
        }

        $country = Country::where('countrycode', $shipping['country'])->first();
        $fuelPercent = $shipping['country'] !== 'SG' ? (float) PaymentSettings::where('id', 1)->value('fuelcharge_percentage') : 0;
        $num = fn ($v) => (float) str_replace(',', '', (string) $v);

        $order = DB::transaction(function () use ($customer, $data, $shipping, $billing, $quote, $total, $method, $country, $fuelPercent, $num, $coupon, $lines) {
            $order = new OrderMaster;
            $order->forceFill([
                'user_id' => $customer->cust_id,
                'order_type' => ($data['type'] ?? 'order') === 'quotation' ? 2 : 1,
                'order_from' => '1',
                'mobile_platform' => $data['platform'] ?? '',
                'ship_method' => $data['shipping_method_id'],
                'pay_method' => $method->payment_name,
                'shipping_cost' => $num($quote['totaldeliverycost']),
                'fuelcharge_percentage' => $fuelPercent,
                'fuelcharges' => $num($quote['fuelcharges']),
                'handlingfee' => $num($quote['handlingfee']),
                'packaging_fee' => $num($quote['packagingprice']),
                'tax_label' => trim(($country->taxtitle ?? '') . ' (' . ($country->taxpercentage ?? 0) . '%)'),
                'tax_percentage' => $country->taxpercentage ?? 0,
                'tax_collected' => $num($quote['gst']),
                'discount_amount' => $num($quote['discount']),
                'discount_id' => $coupon?->id ?? 0,
                'payable_amount' => number_format($total, 2, '.', ''),
                'order_status' => 0,
                'is_fulfilled' => 0,
                'date_entered' => now(),
                'delivery_instructions' => $data['delivery_note'] ?? '',
                'if_items_unavailabel' => $data['if_items_unavailable'] ?? 1,
                'bill_fname' => $billing['first_name'], 'bill_lname' => $billing['last_name'],
                'bill_email' => $billing['email'] ?? $customer->cust_email, 'bill_mobile' => $billing['phone'],
                'bill_compname' => $billing['company'] ?? '',
                'bill_ads1' => $billing['address1'], 'bill_ads2' => $billing['address2'] ?? '',
                'bill_city' => $billing['city'] ?? '', 'bill_state' => $billing['state'] ?? '',
                'bill_zip' => $billing['postcode'], 'bill_country' => $billing['country'],
                'ship_fname' => $shipping['first_name'], 'ship_lname' => $shipping['last_name'],
                'ship_email' => $shipping['email'] ?? $customer->cust_email, 'ship_mobile' => $shipping['phone'],
                'ship_ads1' => $shipping['address1'], 'ship_ads2' => $shipping['address2'] ?? '',
                'ship_city' => $shipping['city'] ?? '', 'ship_state' => $shipping['state'] ?? '',
                'ship_zip' => $shipping['postcode'], 'ship_country' => $shipping['country'],
            ])->save();

            $orderId = $order->id; // $primaryKey is "id", so the new order_id lands here

            OrderLines::sync($orderId, $lines->map(fn ($line) => [
                'prod_id' => $line->product->Id,
                'prod_name' => $line->product->EnName,
                'prod_quantity' => $line->quantity,
                'prod_unit_price' => $line->unit_price,
                'prod_option' => $line->option->Title ?? '',
                'Weight' => $line->option->Weight ?? $line->product->Weight,
                'prod_code' => (string) $line->product->Code,
            ])->all());

            OrderPayment::create([
                'order_id' => $orderId, 'pay_method' => $method->payment_name, 'payable_amount' => $total,
                'payment_status' => 0, 'order_from' => '1',
            ]);

            return OrderMaster::where('order_id', $orderId)->first();
        });

        return ['order' => $order, 'quote' => $quote];
    }
}
