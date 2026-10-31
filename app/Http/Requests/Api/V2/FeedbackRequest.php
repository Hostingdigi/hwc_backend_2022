<?php

namespace App\Http\Requests\Api\V2;

/** Send feedback to the store. */
class FeedbackRequest extends ApiRequest
{
    public function rules(): array
    {
        return [
            'name' => 'required|string|max:150',
            'email' => 'required|email|max:150',
            'phone' => 'nullable|string|max:30',
            'message' => 'required|string|max:5000',
        ];
    }
}
