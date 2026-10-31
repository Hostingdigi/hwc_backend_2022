<?php

namespace App\Http\Requests\Api\V2;

/** Price the cart for a destination and shipping method. */
class QuoteRequest extends ApiRequest
{
    public function rules(): array
    {
        return [
            'country' => 'required|string|size:2|exists:country,countrycode',
            'shipping_method_id' => 'required|integer',
            'coupon_code' => 'nullable|string|max:50',
        ];
    }
}
