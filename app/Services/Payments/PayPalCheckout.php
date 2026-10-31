<?php

namespace App\Services\Payments;

use App\Models\OrderMaster;
use Illuminate\Support\Facades\Http;
use RuntimeException;

/**
 * PayPal Orders v2 (replaces the deprecated checkout.js / v1 payments flow).
 *
 * The server creates the PayPal order for the stored order total, the buyer
 * approves it in PayPal, and the server captures it. Nothing the browser or app
 * sends decides the amount.
 */
class PayPalCheckout
{
    public function baseUrl(): string
    {
        return GatewayCredentials::for(GatewayCredentials::PAYPAL)['live'] ? 'https://api-m.paypal.com' : 'https://api-m.sandbox.paypal.com';
    }

    public function clientId(): string
    {
        return GatewayCredentials::for(GatewayCredentials::PAYPAL)['key'];
    }

    /** @return array{id: string, approve_url: ?string} */
    public function createOrder(OrderMaster $order, string $returnUrl, string $cancelUrl): array
    {
        $response = Http::withToken($this->token())->post($this->baseUrl() . '/v2/checkout/orders', [
            'intent' => 'CAPTURE',
            'purchase_units' => [[
                'reference_id' => (string) $order->order_id,
                'custom_id' => (string) $order->order_id,
                'amount' => ['currency_code' => 'SGD', 'value' => number_format((float) $order->payable_amount, 2, '.', '')],
            ]],
            'application_context' => ['return_url' => $returnUrl, 'cancel_url' => $cancelUrl, 'shipping_preference' => 'NO_SHIPPING'],
        ]);

        if (!$response->successful()) {
            throw new RuntimeException('PayPal order could not be created.');
        }

        $approve = collect($response->json('links') ?? [])->firstWhere('rel', 'approve');

        return ['id' => (string) $response->json('id'), 'approve_url' => $approve['href'] ?? null];
    }

    /**
     * Captures an approved PayPal order and checks it belongs to $order and paid its total.
     *
     * @return string|null the capture id on success
     */
    public function capture(OrderMaster $order, string $paypalOrderId): ?string
    {
        $response = Http::withToken($this->token())->withBody('{}', 'application/json')
            ->post($this->baseUrl() . '/v2/checkout/orders/' . urlencode($paypalOrderId) . '/capture');

        $data = $response->json() ?? [];
        // Already captured earlier (e.g. retried request): read the order instead
        if (!$response->successful()) {
            $data = Http::withToken($this->token())->get($this->baseUrl() . '/v2/checkout/orders/' . urlencode($paypalOrderId))->json() ?? [];
        }

        $unit = $data['purchase_units'][0] ?? [];
        $capture = $unit['payments']['captures'][0] ?? null;

        $belongs = ($unit['custom_id'] ?? $unit['reference_id'] ?? null) === (string) $order->order_id;
        $paid = $capture && ($capture['status'] ?? '') === 'COMPLETED'
            && abs((float) ($capture['amount']['value'] ?? 0) - (float) $order->payable_amount) <= 0.01;

        return $belongs && $paid ? (string) $capture['id'] : null;
    }

    private function token(): string
    {
        $creds = GatewayCredentials::for(GatewayCredentials::PAYPAL);
        $token = Http::asForm()->withBasicAuth($creds['key'], $creds['secret'])
            ->post($this->baseUrl() . '/v1/oauth2/token', ['grant_type' => 'client_credentials'])->json('access_token');

        if (!$token) {
            throw new RuntimeException('PayPal authentication failed.');
        }

        return $token;
    }
}
