<?php

namespace App\Http\Requests\Api\V2;

/** Add a product (optionally an option) to the cart. */
class CartItemRequest extends ApiRequest
{
    public function rules(): array
    {
        return [
            'product_id' => 'required|integer|exists:products,Id',
            'option_id' => 'nullable|integer',
            'quantity' => 'required|integer|min:1|max:999',
        ];
    }
}
