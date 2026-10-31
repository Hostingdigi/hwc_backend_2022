<?php

namespace App\Http\Requests\Api\V2;

/** Check a coupon against the signed-in customer's cart. */
class CouponRequest extends ApiRequest
{
    public function rules(): array
    {
        return [
            'coupon_code' => 'required|string|max:50',
        ];
    }
}
