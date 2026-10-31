<?php

namespace App\Services;

use App\Models\OrderMaster;
use App\Models\PaymentMethods;
use Illuminate\Support\Facades\Http;
use Log;
use Throwable;

/**
 * Confirms with the payment gateway that an order was really paid before the
 * "success" callbacks mark it paid. Callbacks used to trust the orderid in the
 * URL, so anyone could mark any order as paid.
 *
 * services.payment_verification.mode:
 *   enforce (default) - unverified orders stay pending (order_status 0) for admin review
 *   log               - old behaviour (mark paid) but every failed check is logged
 */
class PaymentVerifier
{
    // payment_methods.id values used throughout the controllers
    const STRIPE = 3;
    const PAYPAL = 6;
    const ATOME = 7;

    /**
     * @param  OrderMaster|null  $order
     * @param  array  $params  request input (transactionid, paymentId, ...)
     * @return array ['verified' => bool, 'reason' => string, 'transaction_id' => string|null]
     */
    public function verify($order, array $params = [])
    {
        if (!$order) {
            return $this->fail('order not found');
        }

        // Already confirmed server-side (web Stripe charge, mobile token charge, GrabPay complete)
        if ((string) $order->order_status === '1') {
            return $this->pass('already paid', $order->trans_id);
        }

        $method = strtolower((string) $order->pay_method);

        try {
            if (strpos($method, 'paypal') !== false) {
                return $this->verifyPaypal($order, $params);
            }
            if (strpos($method, 'atome') !== false) {
                return $this->verifyAtome($order);
            }
            if (preg_match('/credit|card|stripe|apple|google/', $method)) {
                return $this->verifyStripe($order, $params);
            }
        } catch (Throwable $e) {
            return $this->fail('gateway check error: ' . $e->getMessage());
        }

        // Hoolah and anything else: no server-side check implemented
        return $this->fail('no verification available for pay_method "' . $order->pay_method . '"');
    }

    /**
     * Whether the caller may mark the order paid, given a verify() result.
     */
    public function allowsMarkingPaid(array $result, $orderid = null)
    {
        if ($result['verified']) {
            return true;
        }

        $enforce = config('services.payment_verification.mode', 'enforce') !== 'log';

        Log::warning('PAYMENT VERIFICATION FAILED', [
            'order_id' => $orderid,
            'reason' => $result['reason'],
            'action' => $enforce ? 'left pending' : 'marked paid (log mode)',
        ]);

        return !$enforce;
    }

    protected function verifyStripe($order, array $params)
    {
        $id = $params['transactionid'] ?? $params['payment_intent'] ?? $order->trans_id;
        $id = trim((string) $id);

        $secret = $this->credentials(self::STRIPE)['secret'];
        if ($secret === '') {
            return $this->fail('stripe key missing');
        }
        $stripe = new \Stripe\StripeClient($secret);

        if (strpos($id, 'pi_') === 0) {
            $intent = $stripe->paymentIntents->retrieve($id);
            if ($intent->status !== 'succeeded') {
                return $this->fail('stripe payment intent status ' . $intent->status);
            }
            return $this->checkAmount($order, $intent->amount_received / 100, $id);
        }

        if (strpos($id, 'ch_') === 0) {
            $charge = $stripe->charges->retrieve($id);
            if ($charge->status !== 'succeeded' || !$charge->paid || $charge->refunded) {
                return $this->fail('stripe charge status ' . $charge->status);
            }
            return $this->checkAmount($order, $charge->amount / 100, $id);
        }

        return $this->fail('no stripe charge / payment intent id');
    }

    protected function verifyPaypal($order, array $params)
    {
        // checkout.js (v1) appends paymentId to the return URL; the app may send transactionid
        $id = trim((string) ($params['paymentId'] ?? $params['transactionid'] ?? ''));
        if ($id === '') {
            return $this->fail('no paypal payment id');
        }

        $creds = $this->credentials(self::PAYPAL);
        $base = $creds['live'] ? 'https://api-m.paypal.com' : 'https://api-m.sandbox.paypal.com';

        $token = data_get(Http::asForm()->withBasicAuth($creds['key'], $creds['secret'])
            ->post($base . '/v1/oauth2/token', ['grant_type' => 'client_credentials'])
            ->json(), 'access_token');
        if (!$token) {
            return $this->fail('paypal auth failed');
        }

        // v1 REST payment (PAY-... / PAYID-...)
        if (stripos($id, 'PAY') === 0) {
            $payment = Http::withToken($token)->get($base . '/v1/payments/payment/' . urlencode($id))->json();
            if (($payment['state'] ?? '') !== 'approved') {
                return $this->fail('paypal payment state ' . ($payment['state'] ?? 'unknown'));
            }
            return $this->checkAmount($order, $payment['transactions'][0]['amount']['total'] ?? null, $id);
        }

        // v2 capture id, then v2 order id
        $capture = Http::withToken($token)->get($base . '/v2/payments/captures/' . urlencode($id));
        if ($capture->successful()) {
            $capture = $capture->json();
            if (data_get($capture, 'status') !== 'COMPLETED') {
                return $this->fail('paypal capture status ' . data_get($capture, 'status'));
            }
            return $this->checkAmount($order, data_get($capture, 'amount.value'), $id);
        }

        $paypalOrder = Http::withToken($token)->get($base . '/v2/checkout/orders/' . urlencode($id));
        if ($paypalOrder->successful() && data_get($paypalOrder->json(), 'status') === 'COMPLETED') {
            return $this->checkAmount($order, data_get($paypalOrder->json(), 'purchase_units.0.amount.value'), $id);
        }

        return $this->fail('paypal id not found or not completed');
    }

    protected function verifyAtome($order)
    {
        $creds = $this->credentials(self::ATOME);
        if ($creds['url'] === '') {
            return $this->fail('atome url missing');
        }

        // Orders are created with referenceId = order_id
        $payment = Http::withBasicAuth($creds['key'], $creds['secret'])
            ->acceptJson()
            ->get(rtrim($creds['url'], '/') . '/payments/' . urlencode($order->order_id))
            ->json();

        if (($payment['status'] ?? '') !== 'PAID') {
            return $this->fail('atome status ' . ($payment['status'] ?? 'unknown'));
        }

        $amount = isset($payment['amount']) ? $payment['amount'] / 100 : null;
        return $this->checkAmount($order, $amount, (string) $order->order_id);
    }

    protected function checkAmount($order, $paid, $transactionId)
    {
        if ($paid === null) {
            return $this->fail('gateway did not return an amount');
        }

        $expected = (float) str_replace(',', '', (string) $order->payable_amount);
        if (abs((float) $paid - $expected) > 0.01) {
            return $this->fail('amount mismatch: paid ' . $paid . ', order ' . $expected);
        }

        // One gateway transaction must not confirm two orders
        $reused = OrderMaster::where('trans_id', $transactionId)->where('order_id', '!=', $order->order_id)->exists();
        if ($reused) {
            return $this->fail('transaction ' . $transactionId . ' already used by another order');
        }

        return $this->pass('verified', $transactionId);
    }

    protected function credentials($paymentMethodId)
    {
        $pm = PaymentMethods::where('id', $paymentMethodId)->first();
        $live = $pm && $pm->payment_mode == 'live';

        return [
            'live' => $live,
            'key' => (string) ($pm ? ($live ? $pm->api_key : $pm->test_api_key) : ''),
            'secret' => (string) ($pm ? ($live ? $pm->api_signature : $pm->test_api_signature) : ''),
            'url' => (string) ($pm ? ($live ? $pm->live_url : $pm->testing_url) : ''),
        ];
    }

    protected function pass($reason, $transactionId = null)
    {
        return ['verified' => true, 'reason' => $reason, 'transaction_id' => $transactionId];
    }

    protected function fail($reason)
    {
        return ['verified' => false, 'reason' => $reason, 'transaction_id' => null];
    }
}
