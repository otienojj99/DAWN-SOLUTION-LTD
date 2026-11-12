<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class ShopBanner extends Model {
     protected $fillable = [
        'slug', 'kind', 'tag', 'icon', 'title', 'description', 'highlight',
        'cta_label', 'href', 'image_path', 'image_alt', 'gradient_key',
        'sort_order', 'is_active', 'starts_at', 'ends_at',
    ];

    protected $casts = [
        'is_active'  => 'boolean',
        'sort_order' => 'integer',
        'starts_at'  => 'datetime',
        'ends_at'    => 'datetime',
        
    ];

    public function scopeActive(Builder $q): Builder
    {
        $now = now();

        return $q->where('is_active', true)
                 ->where(fn ($x) => $x->whereNull('starts_at')->orWhere('starts_at', '<=', $now))
                 ->where(fn ($x) => $x->whereNull('ends_at')->orWhere('ends_at', '>=', $now));
    }

    public function scopeOrdered(Builder $q): Builder
    {
        return $q->orderBy('sort_order')->orderBy('id');
    }

    public function getImageUrlAttribute(): ?string
    {
        if (! $this->image_path) {
            return null;
        }

        $diskPath = storage_path('app/public/' . ltrim($this->image_path, '/'));

        return file_exists($diskPath)
            ? asset('storage/' . ltrim($this->image_path, '/'))
            : null;
    }
}