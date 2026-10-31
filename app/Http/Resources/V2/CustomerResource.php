<?php

namespace App\Http\Resources\V2;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/** @mixin \App\Models\Customer */
class CustomerResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => (int) $this->cust_id,
            'first_name' => (string) $this->cust_firstname,
            'last_name' => (string) $this->cust_lastname,
            'email' => (string) $this->cust_email,
            'username' => (string) $this->cust_username,
            'phone' => (string) $this->cust_phone,
            'company' => (string) $this->cust_company,
            'address' => [
                'address1' => (string) $this->cust_address1,
                'address2' => (string) $this->cust_address2,
                'city' => (string) $this->cust_city,
                'state' => (string) $this->cust_state,
                'postcode' => (string) $this->cust_zip,
                'country_id' => (int) $this->cust_country ?: null,
            ],
            'is_group_admin' => (int) $this->group_admin === 1,
        ];
    }
}
