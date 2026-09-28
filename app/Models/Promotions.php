<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Promotions extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'name', 'slug', 'description', 'kind', 'type',
        'value', 'value_usd',
        'buy_quantity', 'get_quantity', 'get_discount_percent',
        'max_uses', 'max_uses_per_customer', 'uses_count', 'priority',
        'is_stackable', 'applies_to_variants', 'min_order_amount',
        'scope',
        'badge_label', 'badge_color', 'show_on_storefront',
        'starts_at', 'ends_at', 'is_active',
    ];

    protected $casts = [
        'value'                 => 'decimal:2',
        'value_usd'             => 'decimal:2',
        'get_discount_percent'  => 'decimal:2',
        'min_order_amount'      => 'decimal:2',
        'buy_quantity'          => 'integer',
        'get_quantity'          => 'integer',
        'max_uses'              => 'integer',
        'max_uses_per_customer' => 'integer',
        'uses_count'            => 'integer',
        'priority'              => 'integer',
        'is_stackable'          => 'boolean',
        'applies_to_variants'   => 'boolean',
        'show_on_storefront'    => 'boolean',
        'is_active'             => 'boolean',
        'starts_at'             => 'datetime',
        'ends_at'               => 'datetime',
    ];

    protected $attributes = [
        'kind'                => 'discount',
        'type'                => 'percentage',
        'priority'            => 100,
        'is_stackable'        => false,
        'applies_to_variants' => true,
        'show_on_storefront'  => true,
        'is_active'           => true,
        'scope'               => 'products',
    ];

    /* ---------- Constants ---------- */

    public const KIND_DISCOUNT    = 'discount';
    public const KIND_CLEARANCE   = 'clearance';
    public const KIND_FLASH_SALE  = 'flash_sale';
    public const KIND_SEASONAL    = 'seasonal';
    public const KIND_BUNDLE      = 'bundle';
    public const KIND_BOGO        = 'bogo';

    /* ---------- Relations ---------- */

    public function products(): BelongsToMany 
    {
        return $this->belongsToMany(Product::class, 'product_promotion')
                    ->withPivot([
                        'override_value', 'override_value_usd',
                        'max_uses_per_product', 'uses_count',
                    ])
                    ->withTimestamps();
    }


    public function categories() : BelongsToMany
    {
         return $this->belongsToMany(Category::class, 'category_promotion')
                    ->withPivot(['override_value', 'override_value_usd', 'include_descendants'])
                    ->withTimestamps();
    }

     /* ---------- Scopes ---------- */
    public function scopeActive(Builder $q) : Builder
    {
        return $q-where('is_active', true);
    }


    public function scopeLive(Builder $q) :  Builder
    {
        $now = now();

        return $q->where('is_active', true)
                 ->where(function ($q) use ($now) {
                     $q->whereNull('starts_at')->orWhere('starts_at', '<=', $now);
                 })
                 ->where(function ($q) use ($now) {
                     $q->whereNull('ends_at')->orWhere('ends_at', '>=', $now);
                 });
    }


    public function scopeOfKind(Builder $q, string $kind): Builder
    {
        return $q->where('kind', $kind);
    }

    public function scopeOnStorefront(Builder $q): Builder
    {
        return $q->where('show_on_storefront', true);
    }

    public function scopeOrderedByPriority(Builder $q): Builder
    {
        return $q->orderBy('priority')->orderByDesc('created_at');
    }


     /* ---------- Helpers ---------- */

     public function isLive() : bool
     {
        if(! $this->is_active){
            return false;
            
        }

          $now = now();

        if ($this->starts_at && $this->starts_at->gt($now)) {
            return false;
        }

        if ($this->ends_at && $this->ends_at->lt($now)) {
            return false;
        }

        return true;
     }

      public function hasReachedMaxUses(): bool
    {
        return $this->max_uses !== null && $this->uses_count >= $this->max_uses;
    }

    /**
     * Effective value for a specific product, honoring pivot override.
     */
    public function effectiveValueFor(Product $product): float
    {
        $pivot = $product->promotions->firstWhere('id', $this->id)?->pivot;

        return (float) ($pivot?->override_value ?? $this->value);
    }

    /**
     * Compute the discounted price for a given base price.
     */
    public function applyTo(float $basePrice, ?float $overrideValue = null): float
    {
        $value = $overrideValue ?? (float) $this->value;

        $newPrice = match ($this->type) {
            'percentage' => $basePrice * (1 - ($value / 100)),
            'fixed'      => max(0, $basePrice - $value),
            default      => $basePrice,
        };

        return round(max(0, $newPrice), 2);
    }

    /**
     * Human label for the badge (falls back to kind-based defaults).
     */
    public function getDisplayBadgeLabelAttribute(): string
    {
        if ($this->badge_label) {
            return $this->badge_label;
        }

        return match ($this->kind) {
            'clearance'  => 'Clearance Sale',
            'flash_sale' => 'Flash Sale',
            'seasonal'   => 'Seasonal Offer',
            'bundle'     => 'Best Deal',
            'bogo'       => 'Buy More Save More',
            default      => 'On Discount',
        };
    }

    public function getDisplayBadgeColorAttribute(): string
    {
        if ($this->badge_color) {
            return $this->badge_color;
        }

        return match ($this->kind) {
            'clearance'  => 'red',
            'flash_sale' => 'orange',
            'seasonal'   => 'purple',
            'bundle'     => 'green',
            'bogo'       => 'pink',
            default      => 'amber',
        };
    }
}
