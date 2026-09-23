<?php

namespace App\Http\Requests\Category;

use App\Rules\UniqueCategorySlug;
use App\Rules\ValidCategoryParent;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Str;

class StoreCategoryRequest extends FormRequest
{
    public function authorize(): bool
    {
        // Wire to a policy later, e.g.:
        // return $this->user()->can('create', Category::class);
        return true;
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'slug'       => $this->filled('slug')
                ? Str::slug($this->input('slug'))
                : Str::slug((string) $this->input('name')),

            'parent_id'  => $this->input('parent_id') ?: null,

            'sort_order' => $this->filled('sort_order')
                ? (int) $this->input('sort_order')
                : 0,

            'is_active'  => $this->has('is_active')
                ? filter_var($this->input('is_active'), FILTER_VALIDATE_BOOLEAN)
                : true,
        ]);
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'min:2', 'max:150'],

            'slug' => [
                'required', 'string', 'max:160',
                'regex:/^[a-z0-9]+(?:-[a-z0-9]+)*$/',
                new UniqueCategorySlug($this->input('parent_id')),
            ],

            'parent_id' => [
                'nullable', 'integer', 'exists:categories,id',
                new ValidCategoryParent(),
            ],

            'sort_order' => ['nullable', 'integer', 'min:0', 'max:65535'],

            'is_active'  => ['nullable', 'boolean'],
        ];
    }

    public function messages(): array
    {
        return [
            'name.required'      => 'Category name is required.',
            'name.min'           => 'Category name must be at least 2 characters.',
            'name.max'           => 'Category name may not exceed 150 characters.',
            'slug.regex'         => 'Slug may only contain lowercase letters, numbers, and hyphens.',
            'slug.unique'        => 'This slug is already taken.',
            'parent_id.exists'   => 'The selected parent category does not exist.',
            'sort_order.integer' => 'Sort order must be a whole number.',
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