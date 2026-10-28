<?php

namespace App\Http\Requests\Brand;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class UpdateBrandRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        $payload = [];

        if ($this->has('name')) {
            $payload['name'] = trim((string) $this->input('name'));

            // Auto-regenerate the slug from the new name — but only if the
            // request did not explicitly send a slug.
            if (! $this->has('slug')) {
                $payload['slug'] = Str::slug($payload['name']);
            }
        }

        if ($this->has('slug')) {
            $payload['slug'] = Str::slug((string) $this->input('slug'));
        }

        if ($this->has('website')) {
            $payload['website'] = $this->input('website')
                ? trim((string) $this->input('website'))
                : null;
        }

        if ($this->has('is_active')) {
            $payload['is_active'] = filter_var($this->input('is_active'), FILTER_VALIDATE_BOOLEAN);
        }

        if ($this->has('sort_order')) {
            $payload['sort_order'] = (int) $this->input('sort_order');
        }

        // Explicit "remove the logo" flag so admins can clear an image
        // without uploading a new one.
        if ($this->has('remove_logo')) {
            $payload['remove_logo'] = filter_var($this->input('remove_logo'), FILTER_VALIDATE_BOOLEAN);
        }

        if (! empty($payload)) {
            $this->merge($payload);
        }
    }

    public function rules(): array
    {
        /** @var \App\Models\Brands $brand */
        $brand = $this->route('brand');
        $brandId = $brand?->id;

        return [
            'name' => ['sometimes', 'required', 'string', 'min:2', 'max:120'],

            'slug' => [
                'sometimes',
                'required',
                'string',
                'max:140',
                'regex:/^[a-z0-9]+(?:-[a-z0-9]+)*$/',
                Rule::unique('brands', 'slug')->ignore($brandId),
            ],

            'logo'        => ['nullable', 'image', 'mimes:png,jpg,jpeg,webp,svg', 'max:2048'],
            'remove_logo' => ['sometimes', 'boolean'],

            'website'     => ['sometimes', 'nullable', 'url', 'max:300'],
            'description' => ['sometimes', 'nullable', 'string', 'max:2000'],
            'sort_order'  => ['sometimes', 'nullable', 'integer', 'min:0', 'max:65535'],
            'is_active'   => ['sometimes', 'boolean'],
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
        ];
    }

    public function attributes(): array
    {
        return [
            'logo'        => 'logo image',
            'remove_logo' => 'remove logo',
            'website'     => 'website URL',
            'sort_order'  => 'sort order',
            'is_active'   => 'active status',
        ];
    }
}