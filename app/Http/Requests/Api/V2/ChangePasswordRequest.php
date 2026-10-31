<?php

namespace App\Http\Requests\Api\V2;

/** Change password; other devices are signed out. */
class ChangePasswordRequest extends ApiRequest
{
    public function rules(): array
    {
        return [
            'current_password' => 'required|string',
            'password' => 'required|string|min:8|confirmed|different:current_password',
        ];
    }
}
