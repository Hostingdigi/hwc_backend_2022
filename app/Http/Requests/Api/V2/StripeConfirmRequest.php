<?php

namespace App\Http\Requests\Api\V2;

/** Confirm a Stripe payment for an order. */
class StripeConfirmRequest extends ApiRequest
{
    public function rules(): array
    {
        return [
            'payment_intent_id' => 'required|string|starts_with:pi_',
        ];
    }
}
