<?php

namespace App\Http\Requests\Api\V2;

/** Rate a product. */
class ReviewRequest extends ApiRequest
{
    public function rules(): array
    {
        return [
            'rating' => 'required|integer|min:1|max:5',
            'comment' => 'nullable|string|max:2000',
        ];
    }
}
