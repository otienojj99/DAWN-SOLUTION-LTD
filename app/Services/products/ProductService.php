<?php

namespace App\Services\Product;

use App\Enums\ApiErrorCode;
use App\Exceptions\ApiException;
use App\Models\BundleItems;
use App\Models\Products;
use App\Models\ProductImages;
use App\Models\ProductVariants;
use App\Models\Promotions;
use App\Models\UseCase;
use App\Services\Product\Filters\ProductFilters;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;



class ProductServices {
     /* =========================================================
     |  LIST / FIND
     ========================================================= */


     public function paginate(ProductFilters $filters, int $perPage = 20): LengthAwarePaginator
    {
        return Products::query()
            ->with(['primaryImage', 'brand', 'category'])
            ->tap(fn ($q) => $filters->apply($q))
            ->paginate(min($perPage, 100))
            ->withQueryString();
    }


    public function list(ProductFilters $filters): Collection
    {
        return Product::query()
            ->with(['primaryImage', 'brand'])
            ->tap(fn ($q) => $filters->apply($q))
            ->get();
    }

    public function findBySlug(string $slug, array $with = []): Products
    {
        $product = Products::query()
            ->with($with ?: ['primaryImage', 'brand', 'category'])
            ->where('slug', $slug)
            ->first();

        if (! $product) {
            throw new ApiException(ApiErrorCode::PRODUCT_NOT_FOUND);
        }

        return $product;
    }


    public function findBySku(string $sku): Product
    {
        $product = Products::where('sku', $sku)->first();

        if (! $product) {
            throw new ApiException(ApiErrorCode::PRODUCT_NOT_FOUND);
        }

        return $product;
    }

    /* =========================================================
     |  CREATE
     ========================================================= */

    public function create(array $data): Products
    {
        return DB::transaction(function () use ($data) {
            $this->assertSlugUnique($data['slug'] ?? null);
            $this->assertSkuUnique($data['sku'] ?? null);

            $product = Products::create(Arr::except($data, [
                'images', 'variants', 'use_cases', 'promotions', 'bundle_items',
            ]));

            if (! empty($data['images'])) {
                $this->syncImages($product, $data['images']);
            }

            if (! empty($data['variants'])) {
                $this->syncVariants($product, $data['variants']);
            }

            if (! empty($data['use_cases'])) {
                $product->useCases()->sync($data['use_cases']);
            }

            if (! empty($data['promotions'])) {
                $product->promotions()->sync($data['promotions']);
            }

            if (($product->type === 'bundle') && ! empty($data['bundle_items'])) {
                $this->syncBundleItems($product, $data['bundle_items']);
            }

            return $product->fresh()->load($this->defaultEager());
        });
    }

     /* =========================================================
     |  UPDATE
     ========================================================= */

      public function update(Products $product, array $data): Product
    {
        return DB::transaction(function () use ($product, $data) {
            // Guard: type change
            if (isset($data['type']) && $data['type'] !== $product->type) {
                if ($product->variants()->exists() && $data['type'] === 'simple') {
                    throw new ApiException(
                        ApiErrorCode::PRODUCT_TYPE_CHANGE_FORBIDDEN,
                        'Cannot change a variable product to simple while it still has variants.'
                    );
                }
                if ($product->includedInBundles()->exists() && $data['type'] !== 'bundle') {
                    throw new ApiException(
                        ApiErrorCode::PRODUCT_TYPE_CHANGE_FORBIDDEN,
                        'Cannot change type — this product is included in one or more bundles.'
                    );
                }
            }

            if (isset($data['slug'])) {
                $this->assertSlugUnique($data['slug'], $product->id);
            }
            if (isset($data['sku'])) {
                $this->assertSkuUnique($data['sku'], $product->id);
            }

            $product->update(Arr::except($data, [
                'images', 'variants', 'use_cases', 'promotions', 'bundle_items',
            ]));

            if (array_key_exists('images', $data)) {
                $this->syncImages($product, $data['images'] ?? []);
            }

            if (array_key_exists('variants', $data)) {
                $this->syncVariants($product, $data['variants'] ?? []);
            }

            if (array_key_exists('use_cases', $data)) {
                $product->useCases()->sync($data['use_cases'] ?? []);
            }

            if (array_key_exists('promotions', $data)) {
                $product->promotions()->sync($data['promotions'] ?? []);
            }

            if ($product->type === 'bundle' && array_key_exists('bundle_items', $data)) {
                $this->syncBundleItems($product, $data['bundle_items'] ?? []);
            }

            return $product->fresh()->load($this->defaultEager());
        });
    }

    /* =========================================================
     |  DELETE
     ========================================================= */

    public function delete(Products $product): void
    {
        if ($product->includedInBundles()->exists()) {
            throw new ApiException(
                ApiErrorCode::PRODUCT_HAS_ORDERS, // reuse; refine later when orders exist
                "Cannot delete '{$product->name}' — it is part of one or more bundles."
            );
        }

        if ($product->type === 'bundle' && $product->bundleItems()->exists()) {
            // Bundle delete: remove its items first, then soft delete the bundle product
            DB::transaction(function () use ($product) {
                $product->bundleItems()->delete();
                $product->delete();
            });
            return;
        }

        DB::transaction(fn () => $product->delete());
    }

     /* =========================================================
     |  TOGGLES / STATE
     ========================================================= */

    public function toggleActive(Products $product): Products
    {
        $product->update(['is_active' => ! $product->is_active]);
        return $product->fresh();
    }

    public function toggleFeatured(Products $product): Products
    {
        $product->update(['is_featured' => ! $product->is_featured]);
        return $product->fresh();
    }

    public function publish(Products $product): Products
    {
        $product->update([
            'is_active'    => true,
            'published_at' => $product->published_at ?? now(),
        ]);
        return $product->fresh();
    }

    public function unpublish(Products $product): Products
    {
        $product->update(['published_at' => null]);
        return $product->fresh();
    }


    /* =========================================================
     |  DUPLICATE
     ========================================================= */

    public function duplicate(Products $product): Products
    {
        return DB::transaction(function () use ($product) {
            $copy = $product->replicate(['sku', 'slug', 'published_at']);
            $copy->name         = $product->name . ' (Copy)';
            $copy->slug         = $this->uniqueSlug($product->slug);
            $copy->sku          = $this->uniqueSku($product->sku);
            $copy->is_active    = false;
            $copy->published_at = null;
            $copy->save();

            // Replicate images (paths, not files)
            foreach ($product->images as $img) {
                ProductImage::create([
                    'product_id' => $copy->id,
                    'path'       => $img->path,
                    'alt_text'   => $img->alt_text,
                    'is_primary' => $img->is_primary,
                    'sort_order' => $img->sort_order,
                ]);
            }

            // Replicate variants with new SKUs
            foreach ($product->variants as $variant) {
                $newVariant = $variant->replicate(['sku']);
                $newVariant->product_id = $copy->id;
                $newVariant->sku        = $this->uniqueVariantSku($variant->sku);
                $newVariant->save();

                $newVariant->attributeValues()->sync(
                    $variant->attributeValues->pluck('id')->all()
                );
            }

            $copy->useCases()->sync($product->useCases->pluck('id')->all());

            return $copy->fresh()->load($this->defaultEager());
        });
    }


      /* =========================================================
     |  IMAGES
     ========================================================= */

    public function syncImages(Products $product, array $images): void
    {
        DB::transaction(function () use ($product, $images) {
            // Normalize: ensure exactly one primary (first one wins if multiple)
            $primarySet = false;
            $normalized = [];
            foreach ($images as $i => $img) {
                $isPrimary = ! $primarySet && ! empty($img['is_primary']);
                if ($isPrimary) {
                    $primarySet = true;
                }
                $normalized[] = [
                    'path'       => $img['path'],
                    'alt_text'   => $img['alt_text'] ?? null,
                    'is_primary' => $isPrimary,
                    'sort_order' => $img['sort_order'] ?? $i,
                ];
            }

            // If none flagged primary and there is at least one, first becomes primary
            if (! $primarySet && ! empty($normalized)) {
                $normalized[0]['is_primary'] = true;
            }

            // Simple approach: replace all images
            $product->images()->delete();

            foreach ($normalized as $row) {
                $product->images()->create($row);
            }
        });
    }


     public function setPrimaryImage(Products $product, int $imageId): void
    {
        DB::transaction(function () use ($product, $imageId) {
            $image = $product->images()->find($imageId);

            if (! $image) {
                throw new ApiException(ApiErrorCode::IMAGE_PRIMARY_REQUIRED, 'Image not found.');
            }

            $product->images()->update(['is_primary' => false]);
            $image->update(['is_primary' => true]);
        });
    }

    /* =========================================================
     |  VARIANTS
     ========================================================= */

    public function syncVariants(Products $product, array $variants): void
    {
        DB::transaction(function () use ($product, $variants) {
            $existingIds = $product->variants()->pluck('id')->all();
            $keptIds     = [];

            $defaultSet = false;

            foreach ($variants as $i => $row) {
                $variantId = $row['id'] ?? null;
                $attributes = $row['attribute_value_ids'] ?? [];
                unset($row['attribute_value_ids']);

                $isDefault = ! $defaultSet && ! empty($row['is_default']);
                if ($isDefault) {
                    $defaultSet = true;
                }

                if ($variantId && in_array($variantId, $existingIds, true)) {
                    $variant = ProductVariant::findOrFail($variantId);
                    $this->assertVariantSkuUnique($row['sku'], $variant->id);
                    $variant->update($row + ['is_default' => $isDefault]);
                } else {
                    $this->assertVariantSkuUnique($row['sku']);
                    $variant = $product->variants()->create(
                        $row + [
                            'is_default' => $isDefault,
                            'sort_order' => $row['sort_order'] ?? $i,
                        ]
                    );
                }

                $variant->attributeValues()->sync($attributes);
                $keptIds[] = $variant->id;
            }

            // Remove variants that were dropped (and weren't in the payload)
            $toDelete = array_diff($existingIds, $keptIds);
            if (! empty($toDelete)) {
                ProductVariant::whereIn('id', $toDelete)->delete();
            }

            // Ensure at least one default
            if (! $defaultSet && $product->variants()->exists()) {
                $product->variants()->update(['is_default' => false]);
                $product->variants()->orderBy('sort_order')->first()->update(['is_default' => true]);
            }
        });
    }

    /* =========================================================
     |  USE CASES
     ========================================================= */

    public function syncUseCases(Products $product, array $useCaseIds): void
    {
        $valid = UseCase::whereIn('id', $useCaseIds)->pluck('id')->all();

        if (count($valid) !== count(array_unique($useCaseIds))) {
            throw new ApiException(
                ApiErrorCode::USE_CASE_NOT_FOUND,
                'One or more use cases do not exist.'
            );
        }

        $product->useCases()->sync($valid);
    }

    /* =========================================================
     |  PROMOTIONS
     ========================================================= */

    public function syncPromotions(Products $product, array $promotionIds): void
    {
        $valid = Promotion::whereIn('id', $promotionIds)->pluck('id')->all();

        if (count($valid) !== count(array_unique($promotionIds))) {
            throw new ApiException(
                ApiErrorCode::PROMOTION_NOT_FOUND,
                'One or more promotions do not exist.'
            );
        }

        $product->promotions()->sync($valid);
    }


    /* =========================================================
     |  BUNDLES
     ========================================================= */

    public function syncBundleItems(Products $product, array $items): void
    {
        if ($product->type !== 'bundle') {
            throw new ApiException(
                ApiErrorCode::BUNDLE_REQUIRES_COMPONENTS,
                'Bundle items can only be set on products of type "bundle".'
            );
        }

        if (empty($items)) {
            throw new ApiException(
                ApiErrorCode::BUNDLE_REQUIRES_COMPONENTS,
                'A bundle must contain at least one component.'
            );
        }

        DB::transaction(function () use ($product, $items) {
            $product->bundleItems()->delete();

            $seen = [];

            foreach ($items as $i => $item) {
                $componentId = (int) $item['component_product_id'];
                $variantId   = $item['variant_id'] ?? null;

                if ($componentId === $product->id) {
                    throw new ApiException(
                        ApiErrorCode::BUNDLE_CANNOT_CONTAIN_ITSELF,
                        'A bundle cannot contain itself.'
                    );
                }

                $component = Product::withTrashed()->find($componentId);
                if (! $component) {
                    throw new ApiException(
                        ApiErrorCode::PRODUCT_NOT_FOUND,
                        "Component product #{$componentId} not found."
                    );
                }

                if ($component->type === 'bundle') {
                    throw new ApiException(
                        ApiErrorCode::BUNDLE_CANNOT_CONTAIN_BUNDLE,
                        'A bundle cannot contain another bundle.'
                    );
                }

                $key = $componentId . ':' . ($variantId ?? 0);
                if (isset($seen[$key])) {
                    continue; // skip dup
                }
                $seen[$key] = true;

                BundleItem::create([
                    'bundle_product_id'        => $product->id,
                    'component_product_id'     => $componentId,
                    'variant_id'               => $variantId,
                    'quantity'                 => max(1, (int) ($item['quantity'] ?? 1)),
                    'sort_order'               => $item['sort_order'] ?? ($i * 10),
                    'component_price_snapshot' => $component->price,
                ]);
            }
        });
    }

    /* =========================================================
     |  STOCK
     ========================================================= */

    public function adjustStock(Products $product, int $delta): Products
    {
        return DB::transaction(function () use ($product, $delta) {
            $new = $product->stock_quantity + $delta;

            if ($new < 0 && ! $product->allow_backorder) {
                throw new ApiException(
                    ApiErrorCode::PRODUCT_OUT_OF_STOCK,
                    "Insufficient stock for '{$product->name}'."
                );
            }

            $product->update(['stock_quantity' => max(0, $new)]);

            return $product->fresh();
        });
    }


        /* =========================================================
        |  INTERNAL
        ========================================================= */

        protected function defaultEager(): array
        {
            return [
                'brand', 'category',
                'images', 'variants.attributeValues.attribute',
                'useCases', 'promotions',
                'bundleItems.component.primaryImage',
            ];
        }

     protected function assertSlugUnique(?string $slug, ?int $ignoreId = null): void
    {
        if (! $slug) {
            return;
        }

        $query = Products::withTrashed()->where('slug', $slug);
        if ($ignoreId) {
            $query->whereKeyNot($ignoreId);
        }

        if ($query->exists()) {
            throw new ApiException(
                ApiErrorCode::PRODUCT_SLUG_DUPLICATE,
                "Slug '{$slug}' is already in use."
            );
        }
    }

    protected function assertSkuUnique(?string $sku, ?int $ignoreId = null): void
    {
        if (! $sku) {
            return;
        }

        $query = Products::withTrashed()->where('sku', $sku);
        if ($ignoreId) {
            $query->whereKeyNot($ignoreId);
        }

        if ($query->exists()) {
            throw new ApiException(
                ApiErrorCode::PRODUCT_SKU_DUPLICATE,
                "SKU '{$sku}' is already in use."
            );
        }
    }

    protected function uniqueSlug(string $base): string
    {
        $slug = $base . '-copy';
        $i = 1;
        while (Products::withTrashed()->where('slug', $slug)->exists()) {
            $slug = $base . '-copy-' . (++$i);
        }
        return $slug;
    }

    protected function uniqueSku(string $base): string
    {
        $sku = $base . '-COPY';
        $i = 1;
        while (Product::withTrashed()->where('sku', $sku)->exists()) {
            $sku = $base . '-COPY-' . (++$i);
        }
        return $sku;
    }


     protected function uniqueVariantSku(string $base): string
    {
        $sku = $base . '-COPY';
        $i = 1;
        while (ProductVariant::where('sku', $sku)->exists()) {
            $sku = $base . '-COPY-' . (++$i);
        }
        return $sku;
    }



}

 

