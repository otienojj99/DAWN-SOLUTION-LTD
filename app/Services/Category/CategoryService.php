<?php

namespace App\Services\Category;

use App\Enums\ApiErrorCode;
use App\Exceptions\ApiException;
use App\Models\Category;
use App\Services\Category\Filters\CategoryFilters;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;

class CategoryService
{
    /* =========================================================
     |  LIST — paginated
     ========================================================= */

    public function paginate(CategoryFilters $filters, int $perPage = 15): LengthAwarePaginator
    {
        return Category::query()
            ->with('parent')
            ->tap(fn ($q) => $filters->apply($q))
            ->ordered()
            ->paginate(min($perPage, 100))
            ->withQueryString();
    }

    /* =========================================================
     |  LIST — all (for storefront nav)
     ========================================================= */

    public function list(CategoryFilters $filters): Collection
    {
        return Category::query()
            ->tap(fn ($q) => $filters->apply($q))
            ->ordered()
            ->get();
    }

    /* =========================================================
     |  SHOW — with configurable relations
     ========================================================= */

    public function find(Category $category, array $with = []): Category
    {
        return $category->load($with ?: ['parent']);
    }

    /* =========================================================
     |  CREATE
     ========================================================= */

    public function create(array $data): Category
    {
        return DB::transaction(function () use ($data) {
            $category = Category::create($data);
            return $category->load('parent');
        });
    }

    /* =========================================================
     |  UPDATE
     ========================================================= */

    public function update(Category $category, array $data): Category
    {
        return DB::transaction(function () use ($category, $data) {
            $category->update($data);
            return $category->fresh()->load('parent');
        });
    }

    /* =========================================================
     |  DELETE — guarded
     ========================================================= */

    public function delete(Category $category): void
    {
        $childCount = $category->children()->count();

        if ($childCount > 0) {
            throw new ApiException(
                ApiErrorCode::CATEGORY_HAS_CHILDREN,
                sprintf(
                    "Cannot delete '%s' — it has %d %s still attached.",
                    $category->name,
                    $childCount,
                    $childCount === 1 ? 'child category' : 'child categories'
                )
            );
        }

        DB::transaction(fn () => $category->delete());
    }

    /* =========================================================
     |  TOGGLE ACTIVE
     ========================================================= */

    public function toggleActive(Category $category): Category
    {
        $category->update(['is_active' => ! $category->is_active]);
        return $category->fresh();
    }

    /* =========================================================
     |  MOVE — reparent with cascade
     ========================================================= */

    public function move(Category $category, ?int $newParentId): Category
    {
        try {
            $category->moveTo($newParentId);
        } catch (\RuntimeException $e) {
            throw new ApiException(ApiErrorCode::CATEGORY_INVALID_MOVE, $e->getMessage());
        }

        return $category->fresh()->load('parent');
    }

    /* =========================================================
     |  REORDER — bulk
     ========================================================= */

    public function reorder(array $items): void
    {
        DB::transaction(function () use ($items) {
            foreach ($items as $item) {
                Category::whereKey($item['id'])->update([
                    'sort_order' => $item['sort_order'],
                ]);
            }
        });
    }
}