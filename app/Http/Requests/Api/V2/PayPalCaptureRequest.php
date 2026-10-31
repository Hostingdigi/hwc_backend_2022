<?php

namespace App\Http\Requests\Api\V2;

/** Capture an approved PayPal order. */
class PayPalCaptureRequest extends ApiRequest
{
    public function rules(): array
    {
        return [
            'paypal_order_id' => 'required|string|max:64',
        ];
    }
}
