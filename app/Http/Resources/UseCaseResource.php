<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UseCaseResource extends JsonResource
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
     |  STOREFRONT
     ========================================================= */

    protected function storefrontPayload(): array
    {
        return [
            'id'          => $this->id,
            'name'        => $this->name,
            'slug'        => $this->slug,
            'description' => $this->description,
            'icon'        => $this->icon,
            'color'       => $this->color,
            'products_count' => $this->when(
                isset($this->products_count),
                fn () => (int) $this->products_count
            ),
        ];
    }

    /* =========================================================
     |  ADMIN
     ========================================================= */

    protected function adminPayload(): array
    {
        return [
            'id'             => $this->id,
            'name'           => $this->name,
            'slug'           => $this->slug,
            'description'    => $this->description,
            'icon'           => $this->icon,
            'color'          => $this->color,
            'sort_order'     => (int) $this->sort_order,
            'is_active'      => (bool) $this->is_active,
            'products_count' => $this->when(
                isset($this->products_count),
                fn () => (int) $this->products_count
            ),
            'created_at'     => $this->created_at?->toIso8601String(),
            'updated_at'     => $this->updated_at?->toIso8601String(),
        ];
    }
}