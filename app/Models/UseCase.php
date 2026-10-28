<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class UseCase extends Model
{
    //
    protected $fillable = [
        'name', 'slug', 'description', 'icon',
        'color', 'sort_order', 'is_active', 'image_path', 'image_alt', 'cta_label',
    ];

    protected $casts = [
        'is_active'  => 'boolean',
        'sort_order' => 'integer',
    ];

    protected $attributes = [
        'is_active'  => true,
        'sort_order' => 0,
    ];

     /* ---------- Relations ---------- */

    public function products(): BelongsToMany
    {
        return $this->belongsToMany(Products::class, 'product_use_case', 'use_case_id', 'product_id');
    }

    /* ---------- Scopes ---------- */

    public function scopeActive(Builder $q): Builder
    {
        return $q->where('is_active', true);
    }

    public function scopeOrdered(Builder $q): Builder
    {
        return $q->orderBy('sort_order')->orderBy('name');
    }


    protected function imageUrl(): Attribute
    {
        return Attribute::get(function () {
            if( ! $this->image_path) return null;

            $diskPath = storage_path('app/public/' . ltrim($this->image_path, '/'));

            return file_exists($diskPath)
            ? asset('storage/' . ltrim($this->image_path, '/'))
            : null;
        });
    }
}
