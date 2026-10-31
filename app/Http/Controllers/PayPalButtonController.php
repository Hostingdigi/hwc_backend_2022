<?php

namespace App\Http\Controllers;

use App\Models\OrderMaster;
use App\Services\Payments\PayPalCheckout;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Throwable;

/**
 * Server side of the PayPal JS SDK button (PayPal Orders v2) on the web
 * checkout page and the v1 app's PayPal web view. Replaces the deprecated
 * checkout.js flow, where the browser chose the amount.
 *
 * These endpoints need no session: creating a PayPal order only ever asks for
 * the stored total of an unpaid order, and a capture only counts if PayPal
 * reports that exact order and amount as COMPLETED. Marking the order paid
 * still goes through the usual success URL and PaymentVerifier.
 */
class PayPalButtonController extends Controller
{
    public function create(int $order, PayPalCheckout $paypal): JsonResponse
    {
        $order = $this->unpaidOrder($order);

        try {
            $created = $paypal->createOrder($order, url('/'), url('/'));
        } catch (Throwable $e) {
            Log::error('PAYPAL BUTTON CREATE FAILED', ['order_id' => $order->order_id, 'message' => $e->getMessage()]);
            return response()->json(['message' => 'PayPal is not available right now.'], 502);
        }

        return response()->json(['id' => $created['id']]);
    }

    /**
     * @param  string  $return  "web" (storefront /success) or "app" (v1 app /successorder)
     */
    public function capture(Request $request, int $order, string $return, PayPalCheckout $paypal): JsonResponse
    {
        $request->validate(['paypal_order_id' => 'required|string|max:64']);
        $order = $this->unpaidOrder($order);

        try {
            $captureId = $paypal->capture($order, $request->paypal_order_id);
        } catch (Throwable $e) {
            Log::error('PAYPAL BUTTON CAPTURE FAILED', ['order_id' => $order->order_id, 'message' => $e->getMessage()]);
            $captureId = null;
        }

        if (!$captureId) {
            return response()->json(['message' => 'The PayPal payment was not completed.'], 402);
        }

        // The success handlers re-check this capture id with PayPal before marking the order paid
        $path = $return === 'app' ? '/successorder' : '/success';

        return response()->json(['redirect' => url($path) . '?orderid=' . $order->order_id . '&paymentId=' . urlencode($captureId)]);
    }

    private function unpaidOrder(int $orderId): OrderMaster
    {
        $order = OrderMaster::where('order_id', $orderId)->firstOrFail();
        abort_if((int) $order->order_status !== 0, 409, 'This order is already paid.');

        return $order;
    }
}
