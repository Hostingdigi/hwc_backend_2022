<?php

namespace App\Http\Resources\V2;

use App\Models\Price;
use App\Support\Media;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * A product in a list. Prices include GST and come from the same Price model
 * the store uses (customer-group and promotion discounts applied).
 *
 * @mixin \App\Models\Product
 */
class ProductResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $price = new Price();
        $current = round((float) $price->getDiscountPrice($this->Id), 2);
        $regular = round((float) $price->getGroupPrice($this->Id), 2);

        return [
            'id' => (int) $this->Id,
            'name' => (string) $this->EnName,
            'slug' => (string) $this->UniqueKey,
            'code' => (string) $this->Code,
            'price' => $current,
            'regular_price' => $regular > $current ? $regular : null,
            'currency' => 'SGD',
            'in_stock' => (int) $this->Quantity > 0,
            'is_promotion' => (int) $this->IsPromotion === 1,
            'is_new' => (int) $this->IsNewarrival === 1,
            'image' => Media::productImage($this->resource),
            'category_id' => (int) $this->Types ?: null,
            'brand_id' => (int) $this->Brand ?: null,
        ];
    }
}
