<?php

namespace App\Http\Resources\V2;

use App\Models\Price;
use App\Models\ProductGallery;
use App\Models\ProductOptions;
use App\Models\ProductReviews;
use App\Support\Media;
use Illuminate\Http\Request;

/** A single product with options, gallery and reviews. */
class ProductDetailResource extends ProductResource
{
    public function toArray(Request $request): array
    {
        $price = new Price();

        $options = ProductOptions::where('Prod', $this->Id)->where('Status', 1)->get()->map(fn ($o) => [
            'id' => (int) $o->Id,
            'title' => (string) $o->Title,
            'price' => round((float) $price->getDiscountPrice($this->Id) + (float) $price->getOptionPrice($this->Id, $o->Id), 2),
            'in_stock' => (int) $o->Quantity > 0,
        ]);

        $reviews = ProductReviews::where('ProdId', $this->Id)->where('status', 1)->latest('Id')->get();

        return parent::toArray($request) + [
            'short_description' => (string) $this->EnShortDesc,
            'description' => (string) $this->EnLongDesc,
            'specifications' => (string) $this->Specs,
            'size' => (string) $this->Size,
            'color' => (string) $this->Color,
            'weight' => (float) $this->Weight,
            'dimension' => (string) $this->Dimension,
            'video' => $this->Video ?: null,
            'data_sheets' => [
                'tds' => Media::upload('product', $this->Tds),
                'sds' => Media::upload('product', $this->Sds),
            ],
            'max_quantity_per_day' => (int) $this->cust_qty_per_day ?: null,
            'options' => $options,
            'gallery' => ProductGallery::where('ProdId', $this->Id)->where('Status', 1)->orderBy('DisplayOrder')->get()->map(fn ($g) => [
                'image' => Media::upload('product', $g->Image),
                'large_image' => Media::upload('product', $g->LargeImage ?: $g->Image),
            ]),
            'rating' => [
                'average' => $reviews->isEmpty() ? null : round($reviews->avg('rating'), 1),
                'count' => $reviews->count(),
            ],
            'reviews' => $reviews->take(20)->map(fn ($r) => [
                'rating' => (int) $r->rating,
                'comment' => (string) $r->comments,
                'date' => optional($r->created_at)->toDateString(),
            ])->values(),
        ];
    }
}
