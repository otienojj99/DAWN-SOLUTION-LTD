<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProductImageResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'         => $this->id,
            'path'       => $this->path,
            'url'        => $this->path ? asset('storage/' . ltrim($this->path, '/')) : null,
            'alt_text'   => $this->alt_text,
            'is_primary' => (bool) $this->is_primary,
            'sort_order' => $this->sort_order,
            'variant_id' => $this->variant_id,
        ];
    }
}