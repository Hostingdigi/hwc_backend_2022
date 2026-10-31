<?php

namespace App\Http\Resources\V2;

use App\Services\OrderServices;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/** @mixin \App\Models\OrderMaster */
class OrderResource extends JsonResource
{
    public const STATUSES = [
        0 => 'Payment Pending',
        1 => 'Paid, Shipping Pending',
        2 => 'Shipped',
        3 => 'Shipped',
        4 => 'Delivered',
        5 => 'On The Way To You',
        6 => 'Partially Delivered',
        7 => 'Partially Refunded',
        8 => 'Fully Refunded',
        9 => 'Ready For Collection',
    ];

    public function toArray(Request $request): array
    {
        $money = fn ($v) => round((float) str_replace(',', '', (string) $v), 2);
        $address = fn ($p) => [
            'first_name' => (string) $this->{$p . '_fname'}, 'last_name' => (string) $this->{$p . '_lname'},
            'email' => (string) $this->{$p . '_email'}, 'phone' => (string) $this->{$p . '_mobile'},
            'address1' => (string) $this->{$p . '_ads1'}, 'address2' => (string) $this->{$p . '_ads2'},
            'city' => (string) $this->{$p . '_city'}, 'state' => (string) $this->{$p . '_state'},
            'postcode' => (string) $this->{$p . '_zip'}, 'country' => (string) $this->{$p . '_country'},
        ];

        return [
            'id' => (int) $this->order_id,
            'number' => app(OrderServices::class)->manipulateOrderNumber($this->order_id, $this->date_entered),
            'type' => (int) $this->order_type === 2 ? 'quotation' : 'order',
            'status' => (int) $this->order_status,
            'status_label' => self::STATUSES[(int) $this->order_status] ?? 'Unknown',
            'is_paid' => (int) $this->order_status >= 1,
            'payment_method' => (string) $this->pay_method,
            'placed_at' => $this->date_entered ? date(DATE_ATOM, strtotime($this->date_entered)) : null,
            'totals' => [
                'shipping' => $money($this->shipping_cost),
                'fuel_charges' => $money($this->fuelcharges),
                'handling_fee' => $money($this->handlingfee),
                'packaging_fee' => $money($this->packaging_fee),
                'tax' => $money($this->tax_collected),
                'tax_label' => (string) $this->tax_label,
                'discount' => $money($this->discount_amount),
                'total' => $money($this->payable_amount),
                'currency' => 'SGD',
            ],
            'shipping_address' => $address('ship'),
            'billing_address' => $address('bill'),
            'tracking_number' => $this->ship_tracking_number ?: null,
            'lines' => $this->whenLoaded('details', fn () => $this->details->map(fn ($l) => [
                'product_id' => (int) $l->prod_id,
                'name' => (string) $l->prod_name,
                'option' => $l->prod_option ?: null,
                'code' => (string) $l->prod_code,
                'quantity' => (int) $l->prod_quantity,
                'unit_price' => $money($l->prod_unit_price),
                'total' => $money($l->prod_unit_price * $l->prod_quantity),
            ])->values()),
        ];
    }
}
