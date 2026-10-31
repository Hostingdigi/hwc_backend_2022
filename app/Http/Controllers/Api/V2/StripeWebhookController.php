<?php

namespace App\Http\Controllers\Api\V2;

use App\Http\Controllers\Controller;
use App\Models\OrderMaster;
use App\Services\OrderPayments;
use App\Services\Payments\StripePayments;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Throwable;

/**
 * POST /api/webhooks/stripe. Configure in the Stripe dashboard with the event
 * payment_intent.succeeded and put the signing secret in STRIPE_WEBHOOK_SECRET.
 * Confirms orders even if the app is closed before it can call /confirm.
 */
class StripeWebhookController extends Controller
{
    public function __invoke(Request $request, StripePayments $stripe, OrderPayments $payments)
    {
        if ((string) config('services.stripe.webhook_secret') === '') {
            return response()->json(['message' => 'Webhook not configured.'], 503);
        }

        try {
            $event = $stripe->webhookEvent($request->getContent(), (string) $request->header('Stripe-Signature'));
        } catch (Throwable $e) {
            return response()->json(['message' => 'Invalid signature.'], 400);
        }

        if ($event->type !== 'payment_intent.succeeded') {
            return response()->json(['received' => true]);
        }

        $intent = $event->data->object;
        $order = OrderMaster::where('order_id', (int) ($intent->metadata->order_id ?? 0))->first();

        if (!$order || (int) $intent->amount_received !== (int) round((float) $order->payable_amount * 100)) {
            Log::warning('STRIPE WEBHOOK NOT MATCHED', ['intent' => $intent->id, 'order_id' => $intent->metadata->order_id ?? null]);

            return response()->json(['received' => true]);
        }

        $payments->markPaid($order, $intent->id);

        return response()->json(['received' => true]);
    }
}
