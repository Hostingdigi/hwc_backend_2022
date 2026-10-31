<?php

namespace App\Http\Controllers\Api\V2;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V2\PayPalCaptureRequest;
use App\Http\Requests\Api\V2\StripeConfirmRequest;
use App\Http\Resources\V2\OrderResource;
use App\Models\OrderMaster;
use App\Services\OrderPayments;
use App\Services\PaymentVerifier;
use App\Services\Payments\AtomeCheckout;
use App\Services\Payments\PayPalCheckout;
use App\Services\Payments\StripePayments;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Throwable;

/**
 * Paying an API v2 order. Every flow starts from the stored order total and
 * ends in OrderPayments::markPaid() only after the gateway confirms the money.
 */
class PaymentController extends Controller
{
    public function __construct(private OrderPayments $payments)
    {
    }

    /** Card / Apple Pay / Google Pay: returns a PaymentIntent client secret for the Stripe SDK. */
    public function stripeIntent(Request $request, int $order, StripePayments $stripe): JsonResponse
    {
        $order = $this->payableOrder($request, $order);

        try {
            $intent = $stripe->createIntent($order);
        } catch (Throwable $e) {
            return $this->gatewayError('Stripe', $e);
        }

        return response()->json(['data' => ['payment_intent_id' => $intent['id'], 'client_secret' => $intent['client_secret']]]);
    }

    /** Called by the app after the Stripe SDK succeeds (the webhook may also confirm it). */
    public function stripeConfirm(StripeConfirmRequest $request, int $order, PaymentVerifier $verifier)
    {
        $order = OrderController::ownOrder($request, $order);
        $result = $verifier->verify($order, ['transactionid' => $request->payment_intent_id]);

        return $this->settle($order, $result);
    }

    /** Creates a PayPal order; the app opens approve_url (or uses the id with the PayPal SDK). */
    public function paypalCreate(Request $request, int $order, PayPalCheckout $paypal): JsonResponse
    {
        $order = $this->payableOrder($request, $order);

        try {
            $created = $paypal->createOrder($order, url('/api/v2/payments/paypal/return'), url('/api/v2/payments/paypal/cancel'));
        } catch (Throwable $e) {
            return $this->gatewayError('PayPal', $e);
        }

        return response()->json(['data' => ['paypal_order_id' => $created['id'], 'approve_url' => $created['approve_url']]]);
    }

    public function paypalCapture(PayPalCaptureRequest $request, int $order, PayPalCheckout $paypal)
    {
        $order = OrderController::ownOrder($request, $order);

        try {
            $captureId = $paypal->capture($order, $request->paypal_order_id);
        } catch (Throwable $e) {
            return $this->gatewayError('PayPal', $e);
        }

        return $this->settle($order, $captureId
            ? ['verified' => true, 'reason' => 'captured', 'transaction_id' => $captureId]
            : ['verified' => false, 'reason' => 'PayPal capture not completed for this order', 'transaction_id' => null]);
    }

    /** Atome: returns the URL the app opens; call atomeConfirm when the customer comes back. */
    public function atomeCreate(Request $request, int $order, AtomeCheckout $atome): JsonResponse
    {
        $order = $this->payableOrder($request, $order);

        try {
            $url = $atome->createPayment($order, url('/api/v2/payments/atome/return?order=' . $order->order_id), url('/api/v2/payments/atome/cancel?order=' . $order->order_id));
        } catch (Throwable $e) {
            return $this->gatewayError('Atome', $e);
        }

        return response()->json(['data' => ['payment_url' => $url]]);
    }

    public function atomeConfirm(Request $request, int $order, PaymentVerifier $verifier)
    {
        $order = OrderController::ownOrder($request, $order);

        return $this->settle($order, $verifier->verify($order));
    }

    /** Landing pages the gateways redirect the browser to; the app watches for these URLs. */
    public function gatewayReturn(): JsonResponse
    {
        return response()->json(['message' => 'You can return to the app.']);
    }

    private function payableOrder(Request $request, int $orderId): OrderMaster
    {
        $order = OrderController::ownOrder($request, $orderId);
        abort_if((int) $order->order_status !== 0, 409, 'This order is already paid.');

        return $order;
    }

    private function settle(OrderMaster $order, array $verification)
    {
        if (!$verification['verified']) {
            Log::warning('API V2 PAYMENT NOT CONFIRMED', ['order_id' => $order->order_id, 'reason' => $verification['reason']]);

            return response()->json(['message' => 'The payment could not be confirmed yet.', 'reason' => 'payment_not_confirmed'], 402);
        }

        $this->payments->markPaid($order, (string) $verification['transaction_id']);

        return new OrderResource(OrderMaster::where('order_id', $order->order_id)->first()->load('details'));
    }

    private function gatewayError(string $gateway, Throwable $e): JsonResponse
    {
        Log::error("API V2 $gateway ERROR", ['message' => $e->getMessage()]);

        return response()->json(['message' => "$gateway is not available right now. Please try another payment method."], 502);
    }
}
