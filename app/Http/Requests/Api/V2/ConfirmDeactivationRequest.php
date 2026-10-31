<?php

namespace App\Http\Requests\Api\V2;

/** Confirm account deletion with the emailed code. */
class ConfirmDeactivationRequest extends ApiRequest
{
    public function rules(): array
    {
        return [
            'otp' => 'required|string|max:10',
        ];
    }
}
