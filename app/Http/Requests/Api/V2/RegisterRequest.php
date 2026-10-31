<?php

namespace App\Http\Requests\Api\V2;

/** Create a customer account. */
class RegisterRequest extends ApiRequest
{
    public function rules(): array
    {
        return [
            'first_name' => 'required|string|max:100',
            'last_name' => 'required|string|max:100',
            'email' => 'required|email|max:150|unique:customer_master,cust_email',
            'password' => 'required|string|min:8|confirmed',
            'phone' => 'nullable|string|max:30',
            'company' => 'nullable|string|max:150',
            'device_name' => 'required|string|max:100',
            'newsletter' => 'nullable|boolean',
        ];
    }
}
