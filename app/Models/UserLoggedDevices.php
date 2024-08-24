<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserLoggedDevices extends Model
{
    use HasFactory;
    protected $table = "user_logged_devices";
    protected $fillable = ["user_id", "session_id", "is_log_off"];
}
