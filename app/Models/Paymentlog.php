<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Paymentlog extends Model
{
    use HasFactory;
    protected $table = 'paymentlog';
    protected $fillable = ['pay_method','sent_values','received_values','order_id','status'];
}
