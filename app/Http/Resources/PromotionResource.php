<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PromotionResource extends JsonResource
{
    protected ?string $context = 'admin';

    public function forAdmin(): self
    {
        $this->context = 'admin';
        return $this;
    }

    public function forStorefront(): self
    {
        $this->context = 'storefront';
        return $this;
    }

    public function toArray(Request $request): array
    {
        return $this->context === 'storefront'
            ? $this->storefrontPayload()
            : $this->adminPayload();
    }

    /* =========================================================
     |  STOREFRONT — what a shopper sees
     ========================================================= */

    protected function storefrontPayload(): array
    {
        return [
            'id'          => $this->id,
            'name'        => $this->name,
            'slug'        => $this->slug,
            'kind'        => $this->kind,            // discount, clearance, flash_sale, ...
            'type'        => $this->type,            // percentage | fixed
            'value'       => (float) $this->value,
            'value_usd'   => $this->value_usd !== null ? (float) $this->value_usd : null,

            'badge_label' => $this->display_badge_label,
            'badge_color' => $this->display_badge_color,

            // Human-friendly description, e.g. "15% off" / "KSh 5,000 off"
            'display_value' => $this->when(
                true,
                fn () => $this->type === 'percentage'
                    ? "{$this->value}% off"
                    : 'KSh ' . number_format((float) $this->value)
            ),

            'starts_at'   => $this->starts_at?->toIso8601String(),
            'ends_at'     => $this->ends_at?->toIso8601String(),
            'is_live'     => $this->isLive(),

            // Only for flash sales — lets the frontend show a countdown
            'seconds_remaining' => $this->when(
                $this->kind === 'flash_sale' && $this->ends_at,
                fn () => max(0, now()->diffInSeconds($this->ends_at, false))
            ),
        ];
    }

    /* =========================================================
     |  ADMIN — everything
     ========================================================= */

    protected function adminPayload(): array
    {
        return [
            'id'          => $this->id,
            'name'        => $this->name,
            'slug'        => $this->slug,
            'description' => $this->description,

            'kind'        => $this->kind,
            'type'        => $this->type,
            'value'       => (float) $this->value,
            'value_usd'   => $this->value_usd !== null ? (float) $this->value_usd : null,

            /* BOGO fields */
            'buy_quantity'          => $this->buy_quantity,
            'get_quantity'          => $this->get_quantity,
            'get_discount_percent'  => $this->get_discount_percent !== null
                ? (float) $this->get_discount_percent
                : null,

            /* Limits */
            'max_uses'              => $this->max_uses,
            'max_uses_per_customer' => $this->max_uses_per_customer,
            'uses_count'            => (int) $this->uses_count,
            'priority'              => (int) $this->priority,
            'min_order_amount'      => $this->min_order_amount !== null
                ? (float) $this->min_order_amount
                : null,

            /* Rules */
            'is_stackable'          => (bool) $this->is_stackable,
            'applies_to_variants'   => (bool) $this->applies_to_variants,
            'scope'                 => $this->scope,

            /* Display */
            'badge_label'        => $this->badge_label,
            'badge_color'        => $this->badge_color,
            'display_badge_label'=> $this->display_badge_label,
            'display_badge_color'=> $this->display_badge_color,
            'show_on_storefront' => (bool) $this->show_on_storefront,

            /* Schedule */
            'starts_at'  => $this->starts_at?->toIso8601String(),
            'ends_at'    => $this->ends_at?->toIso8601String(),
            'is_active'  => (bool) $this->is_active,
            'is_live'    => $this->isLive(),
            'has_reached_max_uses' => $this->hasReachedMaxUses(),

            /* Attached entities (only when eager-loaded) */
            'products_count'   => $this->when(
                isset($this->products_count),
                fn () => (int) $this->products_count
            ),
            'categories_count' => $this->when(
                isset($this->categories_count),
                fn () => (int) $this->categories_count
            ),

            'products'   => ProductResource::collection($this->whenLoaded('products')),
            'categories' => CategoryResource::collection($this->whenLoaded('categories')),

            'created_at' => $this->created_at?->toIso8601String(),
            'updated_at' => $this->updated_at?->toIso8601String(),
            'deleted_at' => $this->deleted_at?->toIso8601String(),
        ];
    }
}