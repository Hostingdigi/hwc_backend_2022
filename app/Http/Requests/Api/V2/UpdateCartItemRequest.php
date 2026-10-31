<?php

namespace App\Http\Requests\Api\V2;

/** Change a cart line's quantity. */
class UpdateCartItemRequest extends ApiRequest
{
    public function rules(): array
    {
        return [
            'quantity' => 'required|integer|min:1|max:999',
        ];
    }
}
