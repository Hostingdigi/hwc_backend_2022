<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderPayment extends Model
{
    use HasFactory;
    protected $fillable = ['order_id','shipping_cost','fuelcharge_percentage','fuelcharges','handlingfee','packaging_fee','ship_method',
        'tax_label', 'tax_percentage', 'tax_collected', 'payable_amount', 'discount_amount', 'discount_id', 
        'pay_method', 'payment_status', 'ship_fname', 'ship_lname', 'ship_ads1', 'ship_ads2', 'ship_city', 
        'ship_state', 'ship_zip', 'ship_country', 'ship_email', 'ship_mobile', 'bill_fname', 'bill_lname', 'bill_email', 'bill_mobile', 
        'bill_compname', 'bill_ads1', 'bill_ads2', 'bill_city', 'bill_state', 'bill_zip', 'bill_country', 'trans_id', 'error_reason',
        'delivery_instructions', 'if_items_unavailabel', 'order_from'
    ];
}
