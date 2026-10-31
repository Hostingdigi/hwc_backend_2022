<?php

namespace App\Services;

use App\Models\Couponcode;
use App\Models\CouponCodeUsage;
use App\Models\Customer;
use App\Models\Product;

/**
 * The coupon rules of POST /api/discountcoupon (CartMobileController), in one
 * place for API v2: active + not expired, every item in the coupon's category /
 * brand, customer-specific coupons only for that customer, customer_type 3 only
 * for group admins, and the usage limit (per customer for type 1, overall for
 * type 2).
 */
class CouponValidator
{
    /**
     * @param  int[]  $productIds
     * @return array{valid: bool, message: string, coupon: ?Couponcode}
     */
    public function check(string $code, int $customerId, array $productIds): array
    {
        $coupon = Couponcode::where('coupon_code', trim($code))->where('status', 1)->first();
        if (!$coupon) {
            return $this->invalid('Invalid coupon code.');
        }

        if (strtotime($coupon->validity) < strtotime(date('Y-m-d'))) {
            return $this->invalid('This coupon has expired.');
        }

        foreach (['category_id' => 'Types', 'brand_id' => 'Brand'] as $couponColumn => $productColumn) {
            if ($coupon->$couponColumn > 0) {
                $matching = Product::whereIn('Id', $productIds)->where($productColumn, $coupon->$couponColumn)->where('ProdStatus', 1)->count();
                if ($matching < count(array_unique($productIds))) {
                    return $this->invalid('This coupon does not apply to every item in your cart.');
                }
            }
        }

        if ($coupon->customer_id > 0 && (int) $coupon->customer_id !== $customerId) {
            return $this->invalid('Invalid coupon code.');
        }

        if ($coupon->customer_id == 0 && $coupon->customer_type == 3
            && (int) Customer::where('cust_id', $customerId)->value('group_admin') !== 1) {
            return $this->invalid('This coupon is only for group administrators.');
        }

        $usage = CouponCodeUsage::where('coupon_id', $coupon->id);
        if ($coupon->customer_type != 2) {
            $usage->where('customer_id', $customerId);
        }
        if ($coupon->nooftimes <= $usage->count()) {
            return $this->invalid('This coupon has reached its usage limit.');
        }

        return ['valid' => true, 'message' => 'Coupon applied.', 'coupon' => $coupon];
    }

    private function invalid(string $message): array
    {
        return ['valid' => false, 'message' => $message, 'coupon' => null];
    }
}
