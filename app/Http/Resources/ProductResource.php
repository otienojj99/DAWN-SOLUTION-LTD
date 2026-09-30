<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProductResource extends JsonResource
{
    protected ?string $context = 'admin';

    public function forStorefront(): self
    {
        $this->context = 'storefront';
        return $this;
    }

    public function forAdmin(): self
    {
        $this->context = 'admin';
        return $this;
    }

    public function toArray(Request $request): array
    {
        return $this->context === 'storefront'
            ? $this->storefrontPayload()
            : $this->adminPayload();
    }

    /* =========================================================
     |  STOREFRONT
     ========================================================= */

    protected function storefrontPayload(): array
    {
        return [
            'id'                => $this->id,
            'name'              => $this->name,
            'slug'              => $this->slug,
            'type'              => $this->type,
            'short_description' => $this->short_description,
            'long_description'  => $this->when($request = request()->routeIs('*.show'), $this->long_description),

            'category'          => new CategoryResource($this->whenLoaded('category')),
            'brand'             => new BrandResource($this->whenLoaded('brand')),

            'price' => [
                'base_kes'       => (float) $this->price,
                'effective_kes'  => (float) $this->effective_price,
                'compare_at_kes' => $this->compare_at_price !== null ? (float) $this->compare_at_price : null,
                'base_usd'       => $this->price_usd !== null ? (float) $this->price_usd : null,
                'effective_usd'  => $this->effective_price_usd,
                'currency'       => 'KES',
            ],

            'promotions' => PromotionResource::collection($this->whenLoaded('promotions'))
            ->collection
            ->map(fn ($p) => (new PromotionResource($p))->forStorefront()),

            'badge'            => $this->badge,
            'discount_percent' => $this->discount_percent,
            'is_in_stock'      => $this->is_in_stock,
            'in_stock'         => $this->is_in_stock,

            'images' => ProductImageResource::collection($this->whenLoaded('images')),

            'variants' => ProductVariantResource::collection($this->whenLoaded('variants')),

            'attribute_values' => AttributeValueResource::collection(
                $this->whenLoaded('attributeValues')
            ),

            'use_cases' => UseCaseResource::collection($this->whenLoaded('useCases')),
        ];
    }

    /* =========================================================
     |  ADMIN
     ========================================================= */

    protected function adminPayload(): array
    {
        return [
            'id'                => $this->id,
            'name'              => $this->name,
            'slug'              => $this->slug,
            'sku'               => $this->sku,
            'barcode'           => $this->barcode,
            'type'              => $this->type,

            'category_id'       => $this->category_id,
            'brand_id'          => $this->brand_id,
            'category'          => new CategoryResource($this->whenLoaded('category')),
            'brand'             => new BrandResource($this->whenLoaded('brand')),

            'short_description' => $this->short_description,
            'long_description'  => $this->long_description,
            'specifications'    => $this->specifications,

            'price'             => (float) $this->price,
            'compare_at_price'  => $this->compare_at_price !== null ? (float) $this->compare_at_price : null,
            'cost_price'        => $this->cost_price !== null ? (float) $this->cost_price : null,
            'price_usd'         => $this->price_usd !== null ? (float) $this->price_usd : null,
            'compare_at_price_usd' => $this->compare_at_price_usd !== null ? (float) $this->compare_at_price_usd : null,
            'effective_price'   => (float) $this->effective_price,
            'base_currency'     => $this->base_currency,

            'stock_quantity'    => $this->stock_quantity,
            'low_stock_threshold' => $this->low_stock_threshold,
            'track_inventory'   => (bool) $this->track_inventory,
            'allow_backorder'   => (bool) $this->allow_backorder,
            'is_in_stock'       => $this->is_in_stock,

            'weight'            => $this->weight,
            'dimensions'        => $this->dimensions,

            'is_active'         => (bool) $this->is_active,
            'is_featured'       => (bool) $this->is_featured,
            'published_at'      => $this->published_at?->toIso8601String(),

            'meta_title'        => $this->meta_title,
            'meta_description'  => $this->meta_description,

            'badge'             => $this->badge,

            'images'            => ProductImageResource::collection($this->whenLoaded('images')),
            'variants'          => ProductVariantResource::collection($this->whenLoaded('variants')),
            'attribute_values'  => AttributeValueResource::collection($this->whenLoaded('attributeValues')),
            'use_cases'         => UseCaseResource::collection($this->whenLoaded('useCases')),
            // 'promotions'        => PromotionResource::collection($this->whenLoaded('promotions')),
            // 'promotions' => PromotionResource::collection($this->whenLoaded('promotions')),
            // or explicitly:
            'promotions' => $this->whenLoaded('promotions', function () {
                return $this->promotions->map(
                    fn ($p) => (new PromotionResource($p))->forAdmin()->resolve()
                );
            }),

            'bundle_items'      => BundleItemResource::collection($this->whenLoaded('bundleItems')),

            'created_at'        => $this->created_at?->toIso8601String(),
            'updated_at'        => $this->updated_at?->toIso8601String(),
            'deleted_at'        => $this->deleted_at?->toIso8601String(),
        ];
    }
}