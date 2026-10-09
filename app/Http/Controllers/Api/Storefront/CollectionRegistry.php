<?php

namespace App\Http\Controllers\Api\Storefront;

use App\Models\Product;
use Illuminate\Database\Eloquent\Builder;

class CollectionRegistry
{
    /**
     * slug => ['title' => string, 'apply' => Closure(Builder): void]
     */
    public static function all(): array
    {
        return [
            'clearance' => [
                'title' => 'Clearance Sale',
                'apply' => fn (Builder $q) => $q->whereHas(
                    'promotions',
                    fn ($p) => $p->live()->ofKind('clearance')
                ),
            ],

            'discounts' => [
                'title' => 'Discounts',
                'apply' => fn (Builder $q) => $q->whereHas(
                    'promotions',
                    fn ($p) => $p->live()->whereIn('kind', [
                        'discount', 'flash_sale', 'seasonal',
                    ])
                ),
            ],

            'new-arrivals' => [
                'title' => 'New Arrivals',
                'apply' => fn (Builder $q) => $q->newArrivals(30),
            ],

            'best' => [
                'title' => 'Best Deals',
                'apply' => fn (Builder $q) => $q->where('type', 'bundle'),
            ],
        ];
    }

    public static function resolve(string $slug): ?array
    {
        return static::all()[$slug] ?? null;
    }

    public static function slugs(): array
    {
        return array_keys(static::all());
    }
}