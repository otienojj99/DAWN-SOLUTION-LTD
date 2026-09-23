<?php

namespace App\Rules;

use App\Models\Category;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class ValidCategoryParent implements ValidationRule
{
    public function __construct(
        protected ?int $ignoreCategoryId = null,
        protected int $maxLevel = Category::MAX_LEVEL
    ) {}

    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        // null = parent category, allowed
        if ($value === null) {
            return;
        }

        $parent = Category::find($value);

        if (! $parent) {
            $fail('The selected parent category does not exist.');
            return;
        }

        // Parent must be below max depth so child fits
        if ($parent->level >= $this->maxLevel) {
            $fail("Cannot nest under '{$parent->name}' — maximum depth of {$this->maxLevel} levels reached.");
            return;
        }

        // On update, category can't be its own parent
        if ($this->ignoreCategoryId !== null && $parent->id === $this->ignoreCategoryId) {
            $fail('A category cannot be its own parent.');
            return;
        }

        // On update, category can't move under its own descendant
        if ($this->ignoreCategoryId !== null) {
            $current = Category::find($this->ignoreCategoryId);

            if ($current && $parent->isDescendantOf($current->id)) {
                $fail('Cannot move a category under one of its own descendants.');
            }
        }
    }
}