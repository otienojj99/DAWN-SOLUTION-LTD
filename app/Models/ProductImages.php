<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProductImages extends Model
{
    protected $fillable = [
        'product_id', 'variant_id', 'path', 'alt_text',
        'is_primary', 'sort_order',
    ];

    protected $casts = [
        'is_primary' => 'boolean',
        'sort_order' => 'integer',
    ];

    public function product(): BelongsTo
    {
        return $this->belongsTo(Products::class);
    }

    public function variant(): BelongsTo
    {
        return $this->belongsTo(ProductVariants::class, 'variant_id');
    }

    public function scopeOrdered(Builder $q): Builder
    {
        return $q->orderByDesc('is_primary')->orderBy('sort_order');
    }
}
