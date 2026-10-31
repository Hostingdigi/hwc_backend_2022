<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OrderDetails extends Model
{
	protected $table = 'order_details';

	public function order(): BelongsTo
	{
		return $this->belongsTo(OrderMaster::class, 'order_id', 'order_id');
	}

	public function product(): BelongsTo
	{
		return $this->belongsTo(Product::class, 'prod_id', 'Id');
	}
}
