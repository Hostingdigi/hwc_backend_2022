<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Laravel\Sanctum\HasApiTokens;

/**
 * Storefront / mobile-app customer. Also the user model of the "web" auth guard
 * (config/auth.php); the legacy column names are mapped below.
 *
 * @property-read string $full_name
 */
class Customer extends Authenticatable
{
    use HasApiTokens, HasFactory;

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

    /**
     * "First Last", e.g. $customer->full_name. Skips a missing first or last name.
     */
    protected function fullName(): Attribute
    {
        return Attribute::get(fn (): string => trim(
            trim((string) $this->cust_firstname) . ' ' . trim((string) $this->cust_lastname)
        ));
    }

    // Password resets (App\Services\PasswordResets) key tokens by this address
    public function getEmailForPasswordReset()
    {
        return $this->cust_email;
    }

    public function orders(): HasMany
    {
        return $this->hasMany(OrderMaster::class, 'user_id', 'cust_id');
    }

    public function cartDetails(): HasMany
    {
        return $this->hasMany(CartDetail::class, 'user_id', 'cust_id');
    }

    public function favouriteProducts(): HasMany
    {
        return $this->hasMany(FavouriteProducts::class, 'cust_id', 'cust_id');
    }
}
