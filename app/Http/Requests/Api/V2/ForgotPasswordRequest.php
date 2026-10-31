<?php

namespace App\Http\Requests\Api\V2;

/** Request a password reset link by email. */
class ForgotPasswordRequest extends ApiRequest
{
    public function rules(): array
    {
        return [
            'email' => 'required|email',
        ];
    }
}
