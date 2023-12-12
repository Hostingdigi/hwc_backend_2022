<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OrderUpdateHistory extends Model
{
    protected $table = 'order_update_history';
	protected $fillable = [
		'order_id', 'order_no', 'order_data', 'order_items'
	]; 
}
