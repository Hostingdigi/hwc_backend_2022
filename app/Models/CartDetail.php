<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\DB;

/**
 * One line of a server-side cart (cart_details).
 *
 * Guest lines are keyed by user_token (the session's CSRF token); once the
 * customer signs in, user_id is set to customer_master.cust_id.
 *
 * @property int $detail_id
 * @property int|null $user_id
 * @property string|null $user_token
 * @property int $row_key
 * @property int|null $prod_id
 * @property string|null $prod_name
 * @property string|null $prod_option
 * @property int|null $prod_option_id
 * @property int|null $prod_quantity
 * @property string $prod_unit_price
 * @property string $Weight
 * @property string|null $prod_code
 *
 * @method static Builder<static> forCustomer(int $customerId)
 * @method static Builder<static> forGuestToken(string $token)
 */
class CartDetail extends Model
{
    protected $table = 'cart_details';

    protected $primaryKey = 'detail_id';

    protected $fillable = [
        'user_id',
        'user_token',
        'row_key',
        'prod_id',
        'prod_name',
        'prod_option',
        'prod_option_id',
        'prod_quantity',
        'prod_unit_price',
        'Weight',
        'prod_code',
    ];

    protected function casts(): array
    {
        return [
            'user_id' => 'integer',
            'row_key' => 'integer',
            'prod_id' => 'integer',
            'prod_option_id' => 'integer',
            'prod_quantity' => 'integer',
            'prod_unit_price' => 'decimal:2',
            'Weight' => 'decimal:3',
        ];
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class, 'user_id', 'cust_id');
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class, 'prod_id', 'Id');
    }

    public function option(): BelongsTo
    {
        return $this->belongsTo(ProductOptions::class, 'prod_option_id', 'Id');
    }

    /** Lines belonging to a signed-in customer. */
    public function scopeForCustomer(Builder $query, int $customerId): void
    {
        $query->where('user_id', $customerId);
    }

    /** Lines added by a guest, keyed by the session's CSRF token. */
    public function scopeForGuestToken(Builder $query, string $token): void
    {
        $query->where('user_token', $token);
    }

    /**
     * Move a guest's cart lines to the customer, keeping one line per product + option.
     *
     * Guest lines and the customer's existing lines are grouped by prod_id + prod_option_id.
     * The oldest line in each group is kept with the summed quantity; the rest are deleted.
     * Only unclaimed guest lines (no user_id) are taken, never another customer's.
     */
    public static function mergeGuestCart(int $customerId, string $guestToken): void
    {
        DB::transaction(function () use ($customerId, $guestToken) {
            $lines = static::query()
                ->where('user_id', $customerId)
                ->orWhere(fn (Builder $q) => $q
                    ->where('user_token', $guestToken)
                    ->where(fn (Builder $q) => $q->whereNull('user_id')->orWhere('user_id', 0)))
                ->orderBy('detail_id')
                ->lockForUpdate()
                ->get();

            $groups = $lines->groupBy(fn (self $line) => $line->prod_id . ':' . (int) $line->prod_option_id);

            foreach ($groups as $group) {
                /** @var self $keep */
                $keep = $group->first();
                $keep->user_id = $customerId;
                $keep->prod_quantity = (int) $group->sum('prod_quantity');
                $keep->save(); // no query when nothing changed

                if ($group->count() > 1) {
                    static::whereKey($group->slice(1)->modelKeys())->delete();
                }
            }
        });
    }
}
