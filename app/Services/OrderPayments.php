<?php

namespace App\Services;

use App\Mail\TemplateMail;
use App\Models\CouponCodeUsage;
use App\Models\Customer;
use App\Models\EmailTemplate;
use App\Models\OrderDetails;
use App\Models\OrderMaster;
use App\Models\OrderPayment;
use App\Models\Settings;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;

/**
 * The one place an API v2 order becomes paid, whichever way the confirmation
 * arrives (app confirm call, Stripe webhook, PayPal capture, Atome check).
 * Idempotent: a second confirmation for the same order changes nothing.
 */
class OrderPayments
{
    public function __construct(private CartStore $cart)
    {
    }

    /** @return bool true if this call marked the order paid, false if it already was */
    public function markPaid(OrderMaster $order, string $transactionId): bool
    {
        $changed = DB::transaction(function () use ($order, $transactionId) {
            $updated = OrderMaster::where('order_id', $order->order_id)->where('order_status', 0)
                ->update(['order_status' => 1, 'trans_id' => $transactionId, 'is_fulfilled' => 1]);
            if (!$updated) {
                return false;
            }

            OrderPayment::where('order_id', $order->order_id)->latest('id')->limit(1)
                ->update(['payment_status' => 1, 'trans_id' => $transactionId]);

            if ($order->discount_id > 0 && !CouponCodeUsage::where(['coupon_id' => $order->discount_id, 'order_id' => $order->order_id])->exists()) {
                CouponCodeUsage::insert(['coupon_id' => $order->discount_id, 'customer_id' => $order->user_id, 'order_id' => $order->order_id]);
            }

            return true;
        });

        if ($changed) {
            $this->cart->clear((int) $order->user_id);
            $this->sendConfirmation(OrderMaster::where('order_id', $order->order_id)->first());
        }

        return $changed;
    }

    /** Order confirmation from email template type 2 (same placeholders as the v1 email). */
    private function sendConfirmation(OrderMaster $order): void
    {
        $template = EmailTemplate::where('template_type', 2)->where('status', 1)->first();
        $settings = Settings::where('id', 1)->first();
        if (!$template || !$order->bill_email) {
            return;
        }

        $number = app(OrderServices::class)->manipulateOrderNumber($order->order_id, $order->date_entered);
        $address = fn ($p) => e(trim($order->{$p . '_fname'} . ' ' . $order->{$p . '_lname'})) . '<br>'
            . e($order->{$p . '_ads1'}) . ' ' . e($order->{$p . '_ads2'}) . '<br>'
            . e(trim($order->{$p . '_city'} . ' ' . $order->{$p . '_state'} . ' ' . $order->{$p . '_zip'})) . '<br>'
            . e($order->{$p . '_country'});

        $rows = OrderDetails::where('order_id', $order->order_id)->get()->map(fn ($l) => '<tr><td>' . e($l->prod_name)
            . ($l->prod_option ? ' (' . e($l->prod_option) . ')' : '') . '</td><td style="text-align:center">' . (int) $l->prod_quantity
            . '</td><td style="text-align:right">S$' . number_format($l->prod_unit_price, 2)
            . '</td><td style="text-align:right">S$' . number_format($l->prod_unit_price * $l->prod_quantity, 2) . '</td></tr>')->implode('');
        $items = '<table style="width:100%" cellpadding="5"><tr><th style="text-align:left">Item</th><th>Qty</th><th style="text-align:right">Price</th><th style="text-align:right">Total</th></tr>'
            . $rows . '<tr><td colspan="3" style="text-align:right"><b>Grand Total</b></td><td style="text-align:right"><b>S$' . number_format($order->payable_amount, 2) . '</b></td></tr></table>';

        $replace = [
            '{companyname}' => $settings->company_name ?? config('app.name'),
            '{companyaddress}' => $settings->company_address ?? '',
            '{customername}' => e(trim($order->bill_fname . ' ' . $order->bill_lname)),
            '{logo}' => '<img src="' . url('/front/img/logo.png') . '">',
            '{email}' => $settings->admin_email ?? '',
            '{orderid}' => $number,
            '{paymentmethod}' => e($order->pay_method),
            '{billinginfo}' => $address('bill'),
            '{shippinginfo}' => $address('ship'),
            '{orderdetails}' => $items,
            '{status}' => 'Paid',
            '{statusdetails}' => '',
        ];

        $recipients = array_values(array_filter([$order->bill_email, $settings->admin_email ?? null]));
        Mail::to($recipients)->send(new TemplateMail(strtr($template->subject, $replace), strtr($template->content, $replace)));
    }
}
