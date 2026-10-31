<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Couponcode extends Model
{
	protected $table = 'coupon_code';

	public function usages(): HasMany
	{
		return $this->hasMany(CouponCodeUsage::class, 'coupon_id', 'id');
	}
}
