<?php

namespace App\Services;

use App\Models\Price;
use App\Models\Product;
use App\Models\ProductOptions;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

/**
 * A customer's server-side cart (cart_details, keyed by user_id). The same rows
 * the v1 mobile endpoints use, so a customer moving between app versions keeps
 * their cart. Unit prices are always recomputed from the database.
 */
class CartStore
{
    public function lines(int $customerId): Collection
    {
        $price = new Price();

        return DB::table('cart_details')->where('user_id', $customerId)->orderBy('detail_id')->get()
            ->map(function ($row) use ($price) {
                $product = Product::where('Id', $row->prod_id)->first();
                if (!$product) {
                    return null;
                }
                $option = $row->prod_option_id > 0
                    ? ProductOptions::where('Id', $row->prod_option_id)->where('Prod', $row->prod_id)->first()
                    : null;
                $unit = $price->getDiscountPrice($row->prod_id) + ($option ? $price->getOptionPrice($row->prod_id, $option->Id) : 0);

                return (object) [
                    'id' => (int) $row->detail_id,
                    'product' => $product,
                    'option' => $option,
                    'quantity' => (int) $row->prod_quantity,
                    'unit_price' => round((float) $unit, 2),
                    'line_total' => round((float) $unit * (int) $row->prod_quantity, 2),
                    'in_stock' => (int) ($option ? $option->Quantity : $product->Quantity) > 0,
                ];
            })
            ->filter()
            ->values();
    }

    /** Adds to an existing line for the same product + option, or creates one. */
    public function add(int $customerId, Product $product, ?ProductOptions $option, int $quantity): void
    {
        $optionId = $option ? $option->Id : 0;
        $existing = DB::table('cart_details')->where(['user_id' => $customerId, 'prod_id' => $product->Id, 'prod_option_id' => $optionId])->first();

        if ($existing) {
            DB::table('cart_details')->where('detail_id', $existing->detail_id)
                ->update(['prod_quantity' => $existing->prod_quantity + $quantity, 'updated_at' => now()]);
            return;
        }

        DB::table('cart_details')->insert([
            'user_id' => $customerId, 'user_token' => '', 'row_key' => 0,
            'prod_id' => $product->Id, 'prod_name' => $product->EnName, 'prod_option' => $option ? $option->Title : '',
            'prod_option_id' => $optionId, 'prod_quantity' => $quantity,
            'prod_unit_price' => (new Price())->getDiscountPrice($product->Id),
            'Weight' => $option ? $option->Weight : $product->Weight, 'prod_code' => (string) $product->Code,
            'created_at' => now(), 'updated_at' => now(),
        ]);
    }

    /** @return bool false when the line is not in this customer's cart */
    public function setQuantity(int $customerId, int $lineId, int $quantity): bool
    {
        return DB::table('cart_details')->where(['user_id' => $customerId, 'detail_id' => $lineId])
            ->update(['prod_quantity' => $quantity, 'updated_at' => now()]) > 0
            || DB::table('cart_details')->where(['user_id' => $customerId, 'detail_id' => $lineId])->exists();
    }

    public function remove(int $customerId, int $lineId): bool
    {
        return DB::table('cart_details')->where(['user_id' => $customerId, 'detail_id' => $lineId])->delete() > 0;
    }

    public function clear(int $customerId): void
    {
        DB::table('cart_details')->where('user_id', $customerId)->delete();
    }
}
