<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class BundleItemResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'                        => $this->id,
            'component_product_id'      => $this->component_product_id,
            'variant_id'                => $this->variant_id,
            'quantity'                  => $this->quantity,
            'sort_order'                => $this->sort_order,
            'component_price_snapshot'  => $this->component_price_snapshot !== null
                ? (float) $this->component_price_snapshot
                : null,
            'component' => new ProductResource(
                $this->whenLoaded('component')
            ),
        ];
    }
}