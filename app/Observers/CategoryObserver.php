<?php

namespace App\Observers;

use App\Models\Category;
use Illuminate\Support\Facades\Cache;

class CategoryObserver
{
    public function saved(Category $category): void
    {
        $this->flush();
    }

    public function deleted(Category $category): void
    {
        $this->flush();
    }

    protected function flush(): void
    {
        Cache::forget('storefront.category_tree');
    }
}