<?php

namespace App\Http\Resources\V2;

use App\Support\Media;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/** @mixin \App\Models\Category */
class CategoryResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => (int) $this->TypeId,
            'name' => (string) $this->EnName,
            'slug' => (string) $this->UniqueKey,
            'parent_id' => (int) $this->ParentLevel ?: null,
            'image' => Media::upload('category', $this->MobileImage ?: $this->Image),
            'children' => CategoryResource::collection($this->whenLoaded('children')),
        ];
    }
}
