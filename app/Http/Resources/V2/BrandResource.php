<?php

namespace App\Http\Resources\V2;

use App\Support\Media;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/** @mixin \App\Models\Brand */
class BrandResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => (int) $this->BrandId,
            'name' => (string) $this->EnName,
            'slug' => (string) $this->UniqueKey,
            'image' => Media::upload('brands', $this->MobileImage ?: $this->Image),
            'is_popular' => (int) $this->PopularBrand === 1,
        ];
    }
}
