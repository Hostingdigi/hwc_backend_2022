<?php

namespace App\Http\Requests\Api\V2;

/** Place an order (or quotation) from the cart. */
class PlaceOrderRequest extends ApiRequest
{
    public function rules(): array
    {
        return [
            'shipping' => 'required|array',
            'shipping.first_name' => 'required|string|max:100',
            'shipping.last_name' => 'required|string|max:100',
            'shipping.email' => 'nullable|email|max:150',
            'shipping.phone' => 'required|string|max:30',
            'shipping.company' => 'nullable|string|max:150',
            'shipping.address1' => 'required|string|max:255',
            'shipping.address2' => 'nullable|string|max:255',
            'shipping.city' => 'nullable|string|max:100',
            'shipping.state' => 'nullable|string|max:100',
            'shipping.postcode' => 'required|string|max:20',
            'shipping.country' => 'required|string|size:2|exists:country,countrycode',
            'billing' => 'nullable|array',
            'billing.first_name' => 'required_with:billing|string|max:100',
            'billing.last_name' => 'required_with:billing|string|max:100',
            'billing.email' => 'nullable|email|max:150',
            'billing.phone' => 'required_with:billing|string|max:30',
            'billing.company' => 'nullable|string|max:150',
            'billing.address1' => 'required_with:billing|string|max:255',
            'billing.address2' => 'nullable|string|max:255',
            'billing.city' => 'nullable|string|max:100',
            'billing.state' => 'nullable|string|max:100',
            'billing.postcode' => 'required_with:billing|string|max:20',
            'billing.country' => 'required_with:billing|string|size:2|exists:country,countrycode',
            'shipping_method_id' => 'required|integer',
            'payment_method_id' => 'required|integer',
            'coupon_code' => 'nullable|string|max:50',
            'delivery_note' => 'nullable|string|max:1000',
            'if_items_unavailable' => 'nullable|integer|in:1,2,3',
            'expected_total' => 'nullable|numeric|min:0',
            'type' => 'nullable|in:order,quotation',
            'platform' => 'nullable|in:ios,android',
        ];
    }
}
