<?php

namespace App\Http\Requests\Category;

use App\Rules\UniqueCategorySlug;
use App\Rules\ValidCategoryParent;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Str;

class UpdateCategoryRequest extends FormRequest
{
    public function authorize(): bool
    {
        // Wire to a policy later, e.g.:
        // return $this->user()->can('update', $this->route('category'));
        return true;
    }

    protected function prepareForValidation(): void
    {
        $payload = [];

        if ($this->has('name')) {
            $payload['name'] = trim((string) $this->input('name'));

            // If slug wasn't explicitly provided, regenerate from new name
            if (! $this->has('slug')) {
                $payload['slug'] = Str::slug($payload['name']);
            }
        }

        if ($this->has('slug')) {
            $payload['slug'] = Str::slug((string) $this->input('slug'));
        }

        if ($this->has('parent_id')) {
            $payload['parent_id'] = $this->input('parent_id') ?: null;
        }

        if ($this->has('sort_order')) {
            $payload['sort_order'] = (int) $this->input('sort_order');
        }

        if ($this->has('is_active')) {
            $payload['is_active'] = filter_var($this->input('is_active'), FILTER_VALIDATE_BOOLEAN);
        }

        if (! empty($payload)) {
            $this->merge($payload);
        }
    }

    public function rules(): array
    {
        /** @var \App\Models\Category $category */
        $category = $this->route('category');
        $categoryId = $category?->id;

        return [
            'name' => ['sometimes', 'required', 'string', 'min:2', 'max:150'],

            'slug' => [
                'sometimes', 'required', 'string', 'max:160',
                'regex:/^[a-z0-9]+(?:-[a-z0-9]+)*$/',
                new UniqueCategorySlug(
                    $this->input('parent_id', $category?->parent_id),
                    $categoryId
                ),
            ],

            'parent_id' => [
                'sometimes', 'nullable', 'integer', 'exists:categories,id',
                new ValidCategoryParent($categoryId),
            ],

            'sort_order' => ['sometimes', 'nullable', 'integer', 'min:0', 'max:65535'],

            'is_active'  => ['sometimes', 'boolean'],
        ];
    }

    public function messages(): array
    {
        return [
            'name.required'     => 'Category name is required.',
            'name.min'          => 'Category name must be at least 2 characters.',
            'name.max'          => 'Category name may not exceed 150 characters.',
            'slug.regex'        => 'Slug may only contain lowercase letters, numbers, and hyphens.',
            'parent_id.exists'  => 'The selected parent category does not exist.',
            'sort_order.integer'=> 'Sort order must be a whole number.',
        ];
    }

    public function attributes(): array
    {
        return [
            'parent_id'  => 'parent category',
            'sort_order' => 'sort order',
        ];
    }
}