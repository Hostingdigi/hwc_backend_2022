<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OrderDetailsHistory extends Model
{
    protected $table = 'order_details_history';
	protected $fillable = [
		'order_id', 'order_no', 'prod_id', 'prod_name', 'prod_option', 'prod_quantity', 'prod_unit_price', 'Weight', 'prod_code'
	];
}
