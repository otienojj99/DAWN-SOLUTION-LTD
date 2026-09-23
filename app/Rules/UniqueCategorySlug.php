<?php

namespace App\Rules;

use App\Models\Category;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class UniqueCategorySlug implements ValidationRule
{
    public function __construct(
        protected ?int $parentId,
        protected ?int $ignoreCategoryId = null
    ) {}

    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $query = Category::query()
            ->where('slug', $value)
            ->where(function ($q) {
                if ($this->parentId === null) {
                    $q->whereNull('parent_id');
                } else {
                    $q->where('parent_id', $this->parentId);
                }
            });

        if ($this->ignoreCategoryId !== null) {
            $query->whereKeyNot($this->ignoreCategoryId);
        }

        if ($query->exists()) {
            $fail('A category with this slug already exists under the same parent.');
        }
    }
}