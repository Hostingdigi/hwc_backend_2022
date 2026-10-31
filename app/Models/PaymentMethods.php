<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PaymentMethods extends Model
{
    use HasFactory;
    public $table = 'paymentmethods';

    /** payment_methods.Id of the Hoolah row (gateway removed in Phase 4) */
    public const HOOLAH = 4;

    /**
     * Methods that may be offered at checkout. Hoolah is excluded even if its row
     * is still active in the database, so it disappears from the web checkout and
     * the app without a data change. Past orders keep their pay_method text.
     */
    public static function available(): Builder
    {
        return static::query()
            ->where('Id', '!=', self::HOOLAH)
            ->where('payment_name', 'NOT LIKE', '%hoolah%');
    }
}
