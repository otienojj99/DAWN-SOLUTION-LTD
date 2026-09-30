<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AttributeValueResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'             => $this->id,
            'attribute_id'   => $this->attribute_id,
            'attribute_name' => $this->whenLoaded('attribute', fn () => $this->attribute->name),
            'attribute_slug' => $this->whenLoaded('attribute', fn () => $this->attribute->slug),
            'value'          => $this->value,
            'slug'           => $this->slug,
            'meta'           => $this->meta,
        ];
    }
}