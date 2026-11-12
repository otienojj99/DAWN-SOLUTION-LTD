<?php

namespace App\Services\Product\Filters;

use App\Models\Product;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;


class ProductFilters 
{
    public function __construct(protected array $filters = []){}

    public static function fromRequest(Request $request, array $overrides = []): self 
    {
         return new self(array_merge($request->only([
            'search', 'category_id', 'brand_id', 'type',
            'is_active', 'is_featured', 'in_stock', 'on_sale',
            'promotion_kind', 'use_case_id',
            'price_min', 'price_max', 'currency',
            'attribute_filters', 'sort', 'direction',
        ]), $overrides));
    }



    public function apply(Builder $query): Builder 
    {
        $this->applyText($query);
        $this->applyRelations($query);
        $this->applyFlags($query);
        $this->applyPrice($query);
        $this->applyAttributes($query);
        $this->applySort($query);

        return $query;
    }


    protected function applyText(Builder $q): void
    {
        if ($this->filled('search')) {
            $term = (string) $this->filters['search'];
            $q->where(function ($q) use ($term) {
                $q->where('name', 'LIKE', "%{$term}%")
                  ->orWhere('sku', 'LIKE', "%{$term}%")
                  ->orWhere('barcode', 'LIKE', "%{$term}%");
            });
        }
    }

    protected function applyReletionship(Bulder $q){
         if ($this->filled('category_id')) {
            $q->where('category_id', (int) $this->filters['category_id']);
         }

         if ($this->filled('brand_id')) {
            $q->where('brand_id', (int) $this->filters['brand_id']);
         }

         if ($this->filled('type')) {
            $q->where('type', (string) $this->filters['type']);
        }


        if ($this->filled('use_case_id')) {
            $ids = is_array($this->filters['use_case_id'])
                ? $this->filters['use_case_id']
                : [$this->filters['use_case_id']];

            $q->whereHas('useCases', fn ($u) => $u->whereIn('use_cases.id', $ids));
        }

        if ($this->filled('promotion_kind')) {
            $kinds = is_array($this->filters['promotion_kind'])
                ? $this->filters['promotion_kind']
                : [$this->filters['promotion_kind']];

            $q->whereHas('promotions', fn ($p) => $p->live()->whereIn('kind', $kinds));
        }
    }


    protected function applyFlags(Builder q): void
    {
        if($this->has('is_active')){
            $q->where('is_active', filter_var($this->filters['is_active'], FILTER_VALIDATE_BOOLEAN));
        }
        if (! empty($this->filters['is_featured'])) {
            $q->where('is_featured', true);
        }

        if (! empty($this->filters['in_stock'])) {
            $q->inStock();
        }

        if (! empty($this->filters['on_sale'])) {
            $q->whereNotNull('compare_at_price')
              ->whereColumn('compare_at_price', '>', 'price');
        }
    }


    protected function applyPrice(Builder $q): void
    {
        if ($this->filled('price_min')) {
            $q->where('price', '>=', (float) $this->filters['price_min']);
        }

        if ($this->filled('price_max')) {
            $q->where('price', '<=', (float) $this->filters['price_max']);
        }
    }


    /**
     * attribute_filters = [
     *   "ram"       => ["16gb", "32gb"],
     *   "storage"   => ["512gb", "1tb"],
     *   "cpu-model" => ["i7"],
     * ]
     */
    protected function applyAttributes(Builder $q): void
    {
        if (empty($this->filters['attribute_filters'])) {
            return;
        }

        foreach ($this->filters['attribute_filters'] as $attributeSlug => $valueSlugs) {
            $valueSlugs = (array) $valueSlugs;

            if (empty($valueSlugs)) {
                continue;
            }

            $q->where(function ($q) use ($attributeSlug, $valueSlugs) {
                $q->whereHas('variants.attributeValues', function ($v) use ($attributeSlug, $valueSlugs) {
                    $v->whereIn('attribute_values.slug', $valueSlugs)
                      ->whereHas('attribute', fn ($a) => $a->where('slug', $attributeSlug));
                })
                ->orWhereHas('attributeValues', function ($v) use ($attributeSlug, $valueSlugs) {
                    $v->whereIn('attribute_values.slug', $valueSlugs)
                      ->whereHas('attribute', fn ($a) => $a->where('slug', $attributeSlug));
                });
            });
        }
    }


    protected function applySort(Builder $q): void
    {
        $sort      = $this->filters['sort'] ?? 'created_at';
        $direction = strtolower($this->filters['direction'] ?? 'desc') === 'asc' ? 'asc' : 'desc';

        $allowed = [
            'created_at', 'updated_at', 'price',
            'name', 'stock_quantity', 'published_at',
        ];

        if (! in_array($sort, $allowed, true)) {
            $sort = 'created_at';
        }

        $q->orderBy($sort, $direction);
    }

    protected function has(string $key): bool
    {
        return array_key_exists($key, $this->filters) && $this->filters[$key] !== null;
    }

    protected function filled(string $key): bool
    {
        return $this->has($key) && $this->filters[$key] !== '';
    }
}

