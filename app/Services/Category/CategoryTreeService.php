<?php

namespace App\Services\Category;

use App\Models\Category;
use Illuminate\Database\Eloquent\Collection;

class CategoryTreeService
{
    public function build(bool $activeOnly = true): Collection
    {
        return Category::tree(parentId: null, activeOnly: $activeOnly);
    }
}