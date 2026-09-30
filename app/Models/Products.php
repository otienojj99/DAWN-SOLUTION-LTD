<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Products extends Model
{
    use SoftDeletes;

     protected $fillable = [
        'name', 'slug', 'sku', 'barcode', 'type',
        'category_id', 'brand_id',
        'short_description', 'long_description', 'specifications',
        'price', 'compare_at_price', 'cost_price',
        'price_usd', 'compare_at_price_usd', 'base_currency',
        'stock_quantity', 'low_stock_threshold',
        'track_inventory', 'allow_backorder',
        'weight', 'dimensions',
        'is_active', 'is_featured', 'published_at',
        'meta_title', 'meta_description',
    ];

    protected $casts = [
        'specifications'    => 'array',
        'dimensions'        => 'array',
        'price'             => 'decimal:2',
        'compare_at_price'  => 'decimal:2',
        'cost_price'        => 'decimal:2',
        'price_usd'         => 'decimal:2',
        'compare_at_price_usd' => 'decimal:2',
        'weight'            => 'decimal:2',
        'stock_quantity'    => 'integer',
        'low_stock_threshold' => 'integer',
        'track_inventory'   => 'boolean',
        'allow_backorder'   => 'boolean',
        'is_active'         => 'boolean',
        'is_featured'       => 'boolean',
        'published_at'      => 'datetime',
    ];

    protected $attributes = [
        'type'            => 'simple',
        'base_currency'   => 'KES',
        'is_active'       => true,
        'is_featured'     => false,
        'track_inventory' => true,
        'allow_backorder' => false,
        'stock_quantity'  => 0,
    ];

        protected $appends = [
        'is_in_stock',
        'is_on_sale',
        'effective_price',
        'active_promotion_id',
        'badge',
        'discount_percent',
    ];

    public function getActivePromotionIdAttribute(): ?int
    {
        return $this->active_promotion?->id;
    }

     /* ---------- Relations ---------- */

     public function category(): BelongsTo{
        return $this->belongsTo(Category::class);
     }

     public function brand(): BelongsTo
    {
        return $this->belongsTo(Brands::class);
    }

    public function images(): HasMany
    {
        return $this->hasMany(ProductImages::class)->ordered();
    }

    public function primaryImage(): HasMany
    {
        return $this->hasMany(ProductImages::class)->where('is_primary', true);
    }

    public function variants(): HasMany
    {
        return $this->hasMany(ProductVariants::class)->ordered();
    }

    public function defaultVariant(): HasMany
    {
        return $this->hasMany(ProductVariants::class)->where('is_default', true);
    }

    public function attributeValues(): BelongsToMany
    {
        return $this->belongsToMany(
            AttributeValues::class,
            'product_attribute_value',
            'product_id',
            'attribute_value_id'
        );
    }

    public function useCases(): BelongsToMany
    {
        return $this->belongsToMany(UseCase::class, 'product_use_case', 'product_id', 'use_case_id');
    }

    /* ---------- Phase 2 relations ---------- */

    public function promotions(): BelongsToMany
    {
        return $this->belongsToMany(Promotions::class, 'product_promotions', 'product_id', 'promotion_id')
                    ->withPivot([
                        'override_value', 'override_value_usd',
                        'max_uses_per_product', 'uses_count',
                    ])
                    ->withTimestamps();
    }

    /**
     * Promotions inherited from this product's category (and optionally ancestors).
     */
    public function categoryPromotions(): BelongsToMany
    {
        return $this->belongsToMany(
            Promotions::class,
            'category_promotion',
            null, // resolved manually — not a direct FK to products
            null
        );
    }

    /**
     * Bundle components (only meaningful when type = 'bundle').
     */
    public function bundleItems(): HasMany
    {
        return $this->hasMany(BundleItems::class, 'bundle_product_id')->ordered();
    }

    /**
     * Products that include this product as a component in their bundle.
     */
    public function includedInBundles(): HasMany
    {
        return $this->hasMany(BundleItems::class, 'component_product_id');
    }

     /* ---------- Scopes ---------- */

     public function scopeActive(Builder $q): Builder
    {
        return $q->where('is_active', true);
    }

    public function scopePublished(Builder $q): Builder
    {
        return $q->whereNotNull('published_at')->where('published_at', '<=', now());
    }

    public function scopeVisible(Builder $q): Builder
    {
        return $q->active()->published();
    }

    public function scopeFeatured(Builder $q): Builder
    {
        return $q->where('is_featured', true);
    }
        public function scopeOnDiscount(Builder $q): Builder
    {
        return $q->whereHas('promotions', fn ($p) => $p->live()->ofKind('discount'));
    }

    public function scopeOnClearance(Builder $q): Builder
    {
        return $q->whereHas('promotions', fn ($p) => $p->live()->ofKind('clearance'));
    }

    public function scopeWithLivePromotions(Builder $q): Builder
    {
        return $q->whereHas('promotions', fn ($p) => $p->live());
    }

    public function scopeBundles(Builder $q): Builder
    {
        return $q->where('type', 'bundle');
    }

    public function scopeInStock(Builder $q): Builder
    {
        return $q->where(function ($q) {
            $q->where('stock_quantity', '>', 0)
              ->orWhere('allow_backorder', true);
        });
    }

    public function scopeNewArrivals(Builder $q, int $days = 30): Builder
    {
        return $q->where('published_at', '>=', now()->subDays($days))
                 ->orderByDesc('published_at');
    }

    public function scopeType(Builder $q, string $type): Builder
    {
        return $q->where('type', $type);
    }


    /* ---------- Currency helpers ---------- */

    public function priceIn(string $currency = 'KES'): float
    {
        if (strtoupper($currency) === 'USD') {
            return $this->price_usd !== null
                ? (float) $this->price_usd
                : round(((float) $this->price) / config('shop.usd_rate', 130), 2);
        }

        return (float) $this->price;
    }

    public function compareAtPriceIn(string $currency = 'KES'): ?float
    {
        if(strtoupper($currency) === 'USD') {
            if ($this->compare_at_price_usd !== null) {
                return (float) $this->compare_at_price_usd;
            }
            return $this->compare_at_price !== null
                ? round(((float) $this->compare_at_price) / config('shop.usd_rate', 130), 2)
                : null;
        }

        return $this->compare_at_price !== null ? (float) $this->compare_at_price : null;
    }


    /* ---------- Computed ---------- */

    public function getIsInStockAttribute(): bool
    {
        return $this->stock_quantity > 0 || $this->allow_backorder;
    }

    public function getIsOnSaleAttribute(): bool
    {
        return $this->compare_at_price !== null
            && (float) $this->compare_at_price > (float) $this->price;
    }

        /**
     * The single highest-priority live promotion currently applying to this product,
     * accounting for both direct and category-based promotions.
     */
    public function getActivePromotionAttribute(): ?Promotion
    {
        if ($this->relationLoaded('promotions')) {
            $direct = $this->promotions->where('is_active', true);
        } else {
            $direct = $this->promotions()->live()->get();
        }

        return $direct->sortBy('priority')->first();
    }

    /**
     * Effective price after applying the current promotion (if any).
     */
    public function getEffectivePriceAttribute(): float
    {
        $base = (float) $this->price;

        if (! $promo = $this->active_promotion) {
            return $base;
        }

        return $promo->applyTo($base, $promo->effectiveValueFor($this));
    }

    public function getEffectivePriceUsdAttribute(): ?float
    {
        $promo = $this->active_promotion;

        if (! $promo) {
            return $this->price_usd !== null
                ? (float) $this->price_usd
                : null;
        }

        $baseUsd = (float) ($this->price_usd ?? round((float) $this->price / config('shop.usd_rate', 130), 2));

        return $promo->applyTo($baseUsd);
    }

    /**
     * Badge for storefront cards — priority: out-of-stock > bundle > clearance > discount > new.
     */
    public function getBadgeAttribute(): ?array
    {
        if (! $this->is_in_stock) {
            return ['label' => 'Out of Stock', 'color' => 'gray'];
        }

        if ($this->type === 'bundle') {
            return ['label' => 'Best Deal', 'color' => 'green'];
        }

        if ($promo = $this->active_promotion) {
            return [
                'label' => $promo->display_badge_label,
                'color' => $promo->display_badge_color,
            ];
        }

        if ($this->published_at && $this->published_at->gt(now()->subDays(30))) {
            return ['label' => 'New', 'color' => 'blue'];
        }

        return null;
    }

    /**
     * Discount percentage vs. compare_at_price, for card display.
     */
    public function getDiscountPercentAttribute(): ?int
    {
        $compare = (float) ($this->compare_at_price ?? 0);
        $effective = $this->effective_price;

        if ($compare <= 0 || $effective >= $compare) {
            return null;
        }

        return (int) round((($compare - $effective) / $compare) * 100);
    }

}
