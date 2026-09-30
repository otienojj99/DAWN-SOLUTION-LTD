<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProductVariantResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'             => $this->id,
            'product_id'     => $this->product_id,
            'sku'            => $this->sku,
            'name'           => $this->name,
            'price'          => $this->price !== null ? (float) $this->price : null,
            'compare_at_price' => $this->compare_at_price !== null ? (float) $this->compare_at_price : null,
            'price_usd'      => $this->price_usd !== null ? (float) $this->price_usd : null,
            'stock_quantity' => $this->stock_quantity,
            'allow_backorder' => (bool) $this->allow_backorder,
            'weight'         => $this->weight,
            'dimensions'     => $this->dimensions,
            'image_id'       => $this->image_id,
            'is_default'     => (bool) $this->is_default,
            'is_active'      => (bool) $this->is_active,
            'sort_order'     => $this->sort_order,
            'attributes'     => AttributeValueResource::collection(
                $this->whenLoaded('attributeValues')
            ),
        ];
    }
}