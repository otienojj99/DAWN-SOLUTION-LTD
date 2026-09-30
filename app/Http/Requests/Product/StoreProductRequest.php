<?php

namespace App\Http\Requests\Product;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class StoreProductRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        $payload = [];

        if ($this->filled('name') && ! $this->filled('slug')) {
            $payload['slug'] = Str::slug($this->input('name'));
        } elseif ($this->filled('slug')) {
            $payload['slug'] = Str::slug($this->input('slug'));
        }

        foreach (['is_active', 'is_featured', 'track_inventory', 'allow_backorder'] as $flag) {
            if ($this->has($flag)) {
                $payload[$flag] = filter_var($this->input($flag), FILTER_VALIDATE_BOOLEAN);
            }
        }

        if ($this->has('price'))         $payload['price'] = (float) $this->input('price');
        if ($this->has('compare_at_price')) $payload['compare_at_price'] = $this->input('compare_at_price') !== null ? (float) $this->input('compare_at_price') : null;
        if ($this->has('cost_price'))    $payload['cost_price'] = $this->input('cost_price') !== null ? (float) $this->input('cost_price') : null;
        if ($this->has('price_usd'))     $payload['price_usd'] = $this->input('price_usd') !== null ? (float) $this->input('price_usd') : null;
        if ($this->has('stock_quantity'))$payload['stock_quantity'] = (int) $this->input('stock_quantity');

        if (! $this->has('published_at') && filter_var($this->input('is_active', true), FILTER_VALIDATE_BOOLEAN)) {
            $payload['published_at'] = now();
        }

        if (! empty($payload)) {
            $this->merge($payload);
        }
    }

    public function rules(): array
    {
        return [
            'name'              => ['required', 'string', 'min:2', 'max:200'],
            'slug'              => ['nullable', 'string', 'max:220', 'regex:/^[a-z0-9]+(?:-[a-z0-9]+)*$/'],
            'sku'               => ['required', 'string', 'max:100'],
            'barcode'           => ['nullable', 'string', 'max:100'],
            'type'              => ['required', Rule::in(['simple', 'variable', 'bundle'])],

            'category_id'       => ['required', 'integer', 'exists:categories,id'],
            'brand_id'          => ['nullable', 'integer', 'exists:brands,id'],

            'short_description' => ['nullable', 'string', 'max:500'],
            'long_description'  => ['nullable', 'string'],
            'specifications'    => ['nullable', 'array'],

            'price'             => ['required', 'numeric', 'min:0'],
            'compare_at_price'  => ['nullable', 'numeric', 'min:0', 'gte:price'],
            'cost_price'        => ['nullable', 'numeric', 'min:0'],
            'price_usd'         => ['nullable', 'numeric', 'min:0'],
            'compare_at_price_usd' => ['nullable', 'numeric', 'min:0'],

            'stock_quantity'    => ['nullable', 'integer', 'min:0'],
            'low_stock_threshold' => ['nullable', 'integer', 'min:0'],
            'track_inventory'   => ['nullable', 'boolean'],
            'allow_backorder'   => ['nullable', 'boolean'],

            'weight'            => ['nullable', 'numeric', 'min:0'],
            'dimensions'        => ['nullable', 'array'],

            'is_active'         => ['nullable', 'boolean'],
            'is_featured'       => ['nullable', 'boolean'],
            'published_at'      => ['nullable', 'date'],

            'meta_title'        => ['nullable', 'string', 'max:200'],
            'meta_description'  => ['nullable', 'string', 'max:300'],

            /* Nested: images */
            'images'                    => ['nullable', 'array'],
            'images.*.path'             => ['required_with:images', 'string', 'max:500'],
            'images.*.alt_text'         => ['nullable', 'string', 'max:200'],
            'images.*.is_primary'       => ['nullable', 'boolean'],
            'images.*.sort_order'       => ['nullable', 'integer', 'min:0'],

            /* Nested: variants */
            'variants'                          => ['nullable', 'array'],
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

            /* Use cases */
            'use_cases'    => ['nullable', 'array'],
            'use_cases.*'  => ['integer', 'exists:use_cases,id'],

            /* Promotions */
            'promotions'    => ['nullable', 'array'],
            'promotions.*'  => ['integer', 'exists:promotions,id'],

            /* Bundle items */
            'bundle_items'                          => ['required_if:type,bundle', 'array'],
            'bundle_items.*.component_product_id'   => ['required_with:bundle_items', 'integer', 'exists:products,id'],
            'bundle_items.*.variant_id'             => ['nullable', 'integer', 'exists:product_variants,id'],
            'bundle_items.*.quantity'               => ['nullable', 'integer', 'min:1'],
            'bundle_items.*.sort_order'             => ['nullable', 'integer', 'min:0'],
        ];
    }
}