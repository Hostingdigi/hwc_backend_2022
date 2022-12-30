<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AccountDeactiveRequest extends Model
{
    protected $fillable = ['user_id', 'date_time', 'otp_number', 'status', 'description'];
    public $table = 'account_deactivate_history';
}
