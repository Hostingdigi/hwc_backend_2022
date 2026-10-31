<?php

namespace App\Http\Requests\Api\V2;

/** Update the signed-in customer's details. */
class UpdateProfileRequest extends ApiRequest
{
    public function rules(): array
    {
        return [
            'first_name' => 'sometimes|required|string|max:100',
            'last_name' => 'sometimes|required|string|max:100',
            'phone' => 'sometimes|nullable|string|max:30',
            'company' => 'sometimes|nullable|string|max:150',
            'address1' => 'sometimes|nullable|string|max:255',
            'address2' => 'sometimes|nullable|string|max:255',
            'city' => 'sometimes|nullable|string|max:100',
            'state' => 'sometimes|nullable|string|max:100',
            'postcode' => 'sometimes|nullable|string|max:20',
            'country' => 'sometimes|nullable|string|size:2|exists:country,countrycode',
        ];
    }
}
