<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OrderDeliveryInfo extends Model
{
    protected $table = 'order_delivery_info';

    public function deliverydetails()
    {
        return $this->hasMany('App\Models\OrderDeliveryDetails', 'delivery_info_id');
    }
}
