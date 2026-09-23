<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;


class ProductVariants extends Model
{
     protected $fillable = [
        'product_id', 'sku', 'name',
        'price', 'compare_at_price', 'price_usd', 'compare_at_price_usd',
        'stock_quantity', 'allow_backorder',
        'weight', 'dimensions',
        'image_id', 'is_default', 'is_active', 'sort_order',
    ];

    protected $casts = [
        'dimensions'         => 'array',
        'price'              => 'decimal:2',
        'compare_at_price'   => 'decimal:2',
        'price_usd'          => 'decimal:2',
        'compare_at_price_usd' => 'decimal:2',
        'weight'             => 'decimal:2',
        'stock_quantity'     => 'integer',
        'allow_backorder'    => 'boolean',
        'is_default'         => 'boolean',
        'is_active'          => 'boolean',
        'sort_order'         => 'integer',
    ];

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function image(): BelongsTo
    {
        return $this->belongsTo(ProductImage::class, 'image_id');
    }

    public function attributeValues(): BelongsToMany
    {
        return $this->belongsToMany(
            AttributeValues::class,
            'product_variant_attribute_value',
            'variant_id',
            'attribute_value_id'
        )->with('attribute');
    }

    public function scopeActive(Builder $q): Builder
    {
        return $q->where('is_active', true);
    }

    public function scopeOrdered(Builder $q): Builder
    {
        return $q->orderBy('sort_order')->orderBy('id');
    }

    public function priceIn(string $currency = 'KES'): float
    {
        $base = $this->price ?? $this->product->price;

        if (strtoupper($currency) === 'USD') {
            if ($this->price_usd !== null) {
                return (float) $this->price_usd;
            }
            return round(((float) $base) / config('shop.usd_rate', 130), 2);
        }

        return (float) $base;
    }
}
