<?php

namespace App\Http\Requests\Api\V2;

/** Email or username plus password; device_name labels the token. */
class LoginRequest extends ApiRequest
{
    public function rules(): array
    {
        return [
            'login' => 'required|string|max:150',
            'password' => 'required|string',
            'device_name' => 'required|string|max:100',
        ];
    }
}
