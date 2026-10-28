<?php

namespace App\Http\Requests\Brand;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class StoreBrandRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        $payload = [];

        if ($this->filled('name')) {
            $payload['name'] = trim((string) $this->input('name'));

            if (! $this->filled('slug')) {
                $payload['slug'] = Str::slug($payload['name']);
            }
        }

        if ($this->filled('slug')) {
            $payload['slug'] = Str::slug((string) $this->input('slug'));
        }

        if ($this->filled('website')) {
            $payload['website'] = trim((string) $this->input('website'));
        }

        if ($this->has('is_active')) {
            $payload['is_active'] = filter_var($this->input('is_active'), FILTER_VALIDATE_BOOLEAN);
        }

        if ($this->has('sort_order')) {
            $payload['sort_order'] = (int) $this->input('sort_order');
        }

        if (! empty($payload)) {
            $this->merge($payload);
        }
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'min:2', 'max:120'],

            'slug' => [
                'nullable',
                'string',
                'max:140',
                'regex:/^[a-z0-9]+(?:-[a-z0-9]+)*$/',
                Rule::unique('brands', 'slug'),
            ],

            // Logo is an uploaded file — the controller stores it and writes
            // the resulting path into `logo_path`.
            'logo' => ['nullable', 'image', 'mimes:png,jpg,jpeg,webp,svg', 'max:2048'],

            'website'     => ['nullable', 'url', 'max:300'],
            'description' => ['nullable', 'string', 'max:2000'],
            'sort_order'  => ['nullable', 'integer', 'min:0', 'max:65535'],
            'is_active'   => ['nullable', 'boolean'],
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'Brand name is required.',
            'name.min'      => 'Brand name must be at least 2 characters.',
            'name.max'      => 'Brand name may not exceed 120 characters.',
            'slug.regex'    => 'Slug may only contain lowercase letters, numbers, and hyphens.',
            'slug.unique'   => 'This slug is already in use by another brand.',
            'logo.image'    => 'The logo must be an image file.',
            'logo.mimes'    => 'Logo must be a PNG, JPG, WebP, or SVG file.',
            'logo.max'      => 'Logo may not exceed 2MB.',
            'website.url'   => 'Website must be a valid URL (e.g., https://example.com).',
            'website.max'   => 'Website may not exceed 300 characters.',
        ];
    }

    public function attributes(): array
    {
        return [
            'logo'       => 'logo image',
            'website'    => 'website URL',
            'sort_order' => 'sort order',
            'is_active'  => 'active status',
        ];
    }
}