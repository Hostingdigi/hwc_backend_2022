<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;

/**
 * Storefront / mobile-app customer. Also the user model of the "web" auth guard
 * (config/auth.php); the legacy column names are mapped below.
 */
class Customer extends Authenticatable
{
    use HasFactory;

    public $table = 'customer_master';

    // Safe to set: no existing code calls Customer::find() or reads $customer->id.
    // Needed so Eloquent saves (e.g. password rehash on login) target cust_id.
    protected $primaryKey = 'cust_id';

    protected $hidden = ['cust_password'];

    public function getAuthIdentifierName()
    {
        return 'cust_id';
    }

    public function getAuthPasswordName()
    {
        return 'cust_password';
    }

    public function getAuthPassword()
    {
        return $this->cust_password;
    }

    // customer_master has no remember_token column
    public function getRememberTokenName()
    {
        return '';
    }

    public function orders(): HasMany
    {
        return $this->hasMany(OrderMaster::class, 'user_id', 'cust_id');
    }

    public function favouriteProducts(): HasMany
    {
        return $this->hasMany(FavouriteProducts::class, 'cust_id', 'cust_id');
    }
}
