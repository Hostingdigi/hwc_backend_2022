<?php

namespace App\Services\Payments;

use App\Models\OrderDetails;
use App\Models\OrderMaster;
use Illuminate\Support\Facades\Http;
use RuntimeException;

/**
 * Atome "pay later" payments for API v2 orders (the same calls as the v1
 * createorder Atome branch). The result is confirmed afterwards with
 * GET /payments/{order_id} (PaymentVerifier).
 */
class AtomeCheckout
{
    /** @return string the URL the app opens to pay */
    public function createPayment(OrderMaster $order, string $resultUrl, string $cancelUrl): string
    {
        $creds = GatewayCredentials::for(GatewayCredentials::ATOME);
        $cents = fn ($v) => (int) round((float) str_replace(',', '', (string) $v) * 100);
        $mobile = substr(preg_replace('/\D/', '', (string) $order->bill_mobile), -8);

        $items = OrderDetails::where('order_id', $order->order_id)->get()->map(fn ($l) => [
            'itemId' => (string) $l->prod_id, 'name' => $l->prod_name, 'quantity' => (int) $l->prod_quantity,
            'price' => $cents($l->prod_unit_price), 'originalPrice' => $cents($l->prod_unit_price),
        ])->all();

        $response = Http::withBasicAuth($creds['key'], $creds['secret'])->acceptJson()->post(rtrim($creds['url'], '/') . '/payments', [
            'referenceId' => (string) $order->order_id,
            'merchantReferenceId' => (string) $order->order_id,
            'currency' => 'SGD',
            'amount' => $cents($order->payable_amount),
            'originalAmount' => $cents($order->payable_amount),
            'taxAmount' => $cents($order->tax_collected),
            'shippingAmount' => $cents($order->shipping_cost),
            'callbackUrl' => url('/atomecallback'),
            'paymentResultUrl' => $resultUrl,
            'paymentCancelUrl' => $cancelUrl,
            'customerInfo' => ['mobileNumber' => $mobile, 'fullName' => trim($order->bill_fname . ' ' . $order->bill_lname), 'email' => $order->bill_email],
            'shippingAddress' => ['countryCode' => $order->ship_country, 'lines' => [$order->ship_ads1], 'postCode' => $order->ship_zip],
            'billingAddress' => ['countryCode' => $order->bill_country, 'lines' => [$order->bill_ads1], 'postCode' => $order->bill_zip],
            'items' => $items,
        ]);

        $url = $response->json('appPaymentUrl') ?? $response->json('redirectUrl');
        if (!$response->successful() || !$url) {
            throw new RuntimeException('Atome payment could not be created.');
        }

        return $url;
    }
}
