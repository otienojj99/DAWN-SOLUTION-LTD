<?php

namespace App\Services\Category\Filters;

use App\Models\Category;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;

class CategoryFilters
{
    public function __construct(
        protected array $filters = []
    ) {}

    public static function fromRequest(Request $request, array $overrides = []): self
    {
        return new self(array_merge($request->only([
            'search', 'level', 'parent_id', 'is_active', 'parents_only',
        ]), $overrides));
    }

    public function apply(Builder $query): Builder
    {
        if ($this->has('search')) {
            $query->search((string) $this->filters['search']);
        }

        if ($this->has('level')) {
            $query->level((int) $this->filters['level']);
        }

        if ($this->has('parent_id')) {
            $parentId = $this->filters['parent_id'];
            $query->forParent($parentId === '' || $parentId === null ? null : (int) $parentId);
        } elseif (! empty($this->filters['parents_only'])) {
            $query->parents();
        }

        if ($this->has('is_active')) {
            $query->where('is_active', filter_var($this->filters['is_active'], FILTER_VALIDATE_BOOLEAN));
        }

        return $query;
    }

    protected function has(string $key): bool
    {
        return array_key_exists($key, $this->filters) && $this->filters[$key] !== null;
    }
}