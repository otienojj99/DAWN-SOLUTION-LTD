<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductPromotion extends Model
{
     protected $fillable = [
        'bundle_product_id', 'component_product_id', 'variant_id',
        'quantity', 'sort_order', 'component_price_snapshot',
    ];

    protected $casts = [
        'quantity'                 => 'integer',
        'sort_order'               => 'integer',
        'component_price_snapshot' => 'decimal:2',
    ];

    public function bundle(): BelongsTo
    {
        return $this->belongsTo(Product::class, 'bundle_product_id');
    }

    public function component(): BelongsTo
    {
        return $this->belongsTo(Product::class, 'component_product_id');
    }

    public function variant(): BelongsTo
    {
        return $this->belongsTo(ProductVariant::class, 'variant_id');
    }

    public function scopeOrdered(Builder $q): Builder
    {
        return $q->orderBy('sort_order')->orderBy('id');
    }
}
