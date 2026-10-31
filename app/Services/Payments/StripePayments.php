<?php

namespace App\Services\Payments;

use App\Models\OrderMaster;

/**
 * Stripe PaymentIntents for API v2 orders, plus webhook verification.
 * The intent is always created for the stored order total.
 */
class StripePayments
{
    private function client(): \Stripe\StripeClient
    {
        return new \Stripe\StripeClient(GatewayCredentials::for(GatewayCredentials::STRIPE)['secret']);
    }

    /** @return array{id: string, client_secret: string} */
    public function createIntent(OrderMaster $order): array
    {
        $intent = $this->client()->paymentIntents->create([
            'amount' => (int) round((float) $order->payable_amount * 100),
            'currency' => 'sgd',
            'automatic_payment_methods' => ['enabled' => true],
            'description' => 'HardwareCity order ' . $order->order_id,
            'metadata' => ['order_id' => (string) $order->order_id],
        ]);

        return ['id' => $intent->id, 'client_secret' => $intent->client_secret];
    }

    /**
     * Verifies a webhook payload against STRIPE_WEBHOOK_SECRET.
     *
     * @throws \Stripe\Exception\SignatureVerificationException|\UnexpectedValueException
     */
    public function webhookEvent(string $payload, string $signatureHeader): \Stripe\Event
    {
        return \Stripe\Webhook::constructEvent($payload, $signatureHeader, (string) config('services.stripe.webhook_secret'));
    }
}
