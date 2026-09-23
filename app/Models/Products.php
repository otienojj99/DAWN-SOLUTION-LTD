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

}
