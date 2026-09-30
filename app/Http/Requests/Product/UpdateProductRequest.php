<?php

namespace App\Http\Requests\Product;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Str;

class UpdateProductRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        $payload = [];

        if ($this->has('name') && ! $this->has('slug')) {
            $payload['slug'] = Str::slug($this->input('name'));
        } elseif ($this->has('slug')) {
            $payload['slug'] = Str::slug($this->input('slug'));
        }

        foreach (['is_active', 'is_featured', 'track_inventory', 'allow_backorder'] as $flag) {
            if ($this->has($flag)) {
                $payload[$flag] = filter_var($this->input($flag), FILTER_VALIDATE_BOOLEAN);
            }
        }

        foreach (['price', 'compare_at_price', 'cost_price', 'price_usd', 'compare_at_price_usd', 'weight'] as $num) {
            if ($this->has($num)) {
                $payload[$num] = $this->input($num) !== null ? (float) $this->input($num) : null;
            }
        }

        if ($this->has('stock_quantity')) {
            $payload['stock_quantity'] = (int) $this->input('stock_quantity');
        }

        if (! empty($payload)) {
            $this->merge($payload);
        }
    }

    public function rules(): array
    {
        // Same as Store but with 'sometimes' on top-level fields.
        // Instead of duplicating everything, we could share a trait, but explicit is clearer.
        return [
            'name'              => ['sometimes', 'required', 'string', 'min:2', 'max:200'],
            'slug'              => ['sometimes', 'required', 'string', 'max:220', 'regex:/^[a-z0-9]+(?:-[a-z0-9]+)*$/'],
            'sku'               => ['sometimes', 'required', 'string', 'max:100'],
            'barcode'           => ['sometimes', 'nullable', 'string', 'max:100'],
            'type'              => ['sometimes', 'required', 'in:simple,variable,bundle'],

            'category_id'       => ['sometimes', 'required', 'integer', 'exists:categories,id'],
            'brand_id'          => ['sometimes', 'nullable', 'integer', 'exists:brands,id'],

            'short_description' => ['sometimes', 'nullable', 'string', 'max:500'],
            'long_description'  => ['sometimes', 'nullable', 'string'],
            'specifications'    => ['sometimes', 'nullable', 'array'],

            'price'             => ['sometimes', 'required', 'numeric', 'min:0'],
            'compare_at_price'  => ['sometimes', 'nullable', 'numeric', 'min:0'],
            'cost_price'        => ['sometimes', 'nullable', 'numeric', 'min:0'],
            'price_usd'         => ['sometimes', 'nullable', 'numeric', 'min:0'],
            'compare_at_price_usd' => ['sometimes', 'nullable', 'numeric', 'min:0'],

            'stock_quantity'    => ['sometimes', 'integer', 'min:0'],
            'low_stock_threshold' => ['sometimes', 'integer', 'min:0'],
            'track_inventory'   => ['sometimes', 'boolean'],
            'allow_backorder'   => ['sometimes', 'boolean'],

            'weight'            => ['sometimes', 'nullable', 'numeric', 'min:0'],
            'dimensions'        => ['sometimes', 'nullable', 'array'],

            'is_active'         => ['sometimes', 'boolean'],
            'is_featured'       => ['sometimes', 'boolean'],
            'published_at'      => ['sometimes', 'nullable', 'date'],

            'meta_title'        => ['sometimes', 'nullable', 'string', 'max:200'],
            'meta_description'  => ['sometimes', 'nullable', 'string', 'max:300'],

            'images'                    => ['sometimes', 'array'],
            'images.*.path'             => ['required_with:images', 'string', 'max:500'],
            'images.*.alt_text'         => ['nullable', 'string', 'max:200'],
            'images.*.is_primary'       => ['nullable', 'boolean'],
            'images.*.sort_order'       => ['nullable', 'integer', 'min:0'],

            'variants'                          => ['sometimes', 'array'],
            'variants.*.id'                     => ['nullable', 'integer', 'exists:product_variants,id'],
            'variants.*.sku'                    => ['required_with:variants', 'string', 'max:100'],
            'variants.*.name'                   => ['required_with:variants', 'string', 'max:200'],
            'variants.*.price'                  => ['nullable', 'numeric', 'min:0'],
            'variants.*.compare_at_price'       => ['nullable', 'numeric', 'min:0'],
            'variants.*.price_usd'              => ['nullable', 'numeric', 'min:0'],
            'variants.*.stock_quantity'         => ['nullable', 'integer', 'min:0'],
            'variants.*.is_default'             => ['nullable', 'boolean'],
            'variants.*.is_active'              => ['nullable', 'boolean'],
            'variants.*.sort_order'             => ['nullable', 'integer', 'min:0'],
            'variants.*.attribute_value_ids'    => ['nullable', 'array'],
            'variants.*.attribute_value_ids.*'  => ['integer', 'exists:attribute_values,id'],

            'use_cases'    => ['sometimes', 'array'],
            'use_cases.*'  => ['integer', 'exists:use_cases,id'],

            'promotions'    => ['sometimes', 'array'],
            'promotions.*'  => ['integer', 'exists:promotions,id'],

            'bundle_items'                          => ['sometimes', 'array'],
            'bundle_items.*.component_product_id'   => ['required_with:bundle_items', 'integer', 'exists:products,id'],
            'bundle_items.*.variant_id'             => ['nullable', 'integer', 'exists:product_variants,id'],
            'bundle_items.*.quantity'               => ['nullable', 'integer', 'min:1'],
            'bundle_items.*.sort_order'             => ['nullable', 'integer', 'min:0'],
        ];
    }
}