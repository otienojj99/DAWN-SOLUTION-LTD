<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Attributes extends Model
{
    protected $fillable = [
        'name', 'slug', 'group', 'type',
        'is_filterable', 'is_variant_attribute', 'unit',
        'sort_order', 'is_active',
    ];

    protected $casts = [
        'is_filterable'        => 'boolean',
        'is_variant_attribute' => 'boolean',
        'is_active'            => 'boolean',
        'sort_order'           => 'integer',
    ];

    public function values(): HasMany
    {
        return $this->hasMany(AttributeValue::class)->ordered();
    }

    public function variants(): BelongsToMany
    {
        return $this->belongsToMany(
            ProductVariant::class,
            'product_variant_attribute_value',
            'attribute_value_id',
            'variant_id'
        );
    }

    public function scopeActive(Builder $q): Builder
    {
        return $q->where('is_active', true);
    }

    public function scopeFilterable(Builder $q): Builder
    {
        return $q->where('is_filterable', true);
    }

    public function scopeOrdered(Builder $q): Builder
    {
        return $q->orderBy('sort_order')->orderBy('name');
    }
}
