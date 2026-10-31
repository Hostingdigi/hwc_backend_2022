<?php

namespace App\Services;

use App\Models\OrderDetails;
use App\Models\Product;
use Illuminate\Support\Facades\DB;

/**
 * Writes an order's lines (order_details) and keeps product stock in step.
 *
 * Checkout reuses a customer's unfulfilled order when they retry. It used to
 * insert every line again (the order listed each item twice) and take the stock
 * off a second time. sync() replaces the lines and moves stock only by the
 * difference from what the order already held.
 */
class OrderLines
{
    /**
     * @param  array  $lines  each: prod_id, prod_name, prod_quantity, prod_unit_price, prod_option, Weight, prod_code
     */
    public static function sync($orderId, array $lines): void
    {
        DB::transaction(function () use ($orderId, $lines) {
            $previous = OrderDetails::where('order_id', $orderId)->get()
                ->groupBy('prod_id')->map(fn ($rows) => (int) $rows->sum('prod_quantity'));

            $wanted = collect($lines)->groupBy('prod_id')->map(fn ($rows) => (int) $rows->sum('prod_quantity'));

            foreach ($previous->keys()->merge($wanted->keys())->unique() as $productId) {
                self::adjustStock($productId, $wanted->get($productId, 0) - $previous->get($productId, 0));
            }

            OrderDetails::where('order_id', $orderId)->delete();
            foreach ($lines as $line) {
                $detail = new OrderDetails;
                foreach ($line as $column => $value) {
                    $detail[$column] = $value;
                }
                $detail['order_id'] = $orderId;
                $detail->save();
            }
        });
    }

    /**
     * Positive $taken lowers stock with the existing rule (stock goes to 0 when
     * the order takes all or more than what is left); negative puts it back.
     */
    private static function adjustStock($productId, int $taken): void
    {
        if ($taken === 0) {
            return;
        }

        $stock = (int) Product::where('Id', $productId)->value('Quantity');
        $new = $taken > 0 ? ($stock > $taken ? $stock - $taken : 0) : $stock - $taken;

        Product::where('Id', $productId)->update(['Quantity' => $new]);
    }
}
