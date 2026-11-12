<?php

use App\Models\Category;
use App\Models\Products;
use Inertia\Testing\AssertableInertia as Assert;

it('provides visible products from the same category as related products', function () {
    $category = Category::create([
        'name' => 'Product test category',
        'slug' => 'product-test-category',
        'path' => 'product-test-category',
        'level' => 1,
    ]);

    $otherCategory = Category::create([
        'name' => 'Other product category',
        'slug' => 'other-product-category',
        'path' => 'other-product-category',
        'level' => 1,
    ]);

    $product = Products::create([
        'name' => 'Current product',
        'slug' => 'current-product',
        'sku' => 'CURRENT-PRODUCT',
        'category_id' => $category->id,
        'price' => 100,
        'published_at' => now(),
    ]);
    $related = Products::create([
        'name' => 'Related product',
        'slug' => 'related-product',
        'sku' => 'RELATED-PRODUCT',
        'category_id' => $category->id,
        'price' => 100,
        'published_at' => now(),
    ]);
    Products::create([
        'name' => 'Unrelated product',
        'slug' => 'unrelated-product',
        'sku' => 'UNRELATED-PRODUCT',
        'category_id' => $otherCategory->id,
        'price' => 100,
        'published_at' => now(),
    ]);

    $response = $this->get(route('shop.products.show', $product->slug));

    $response
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->component('storefront/product/product')
            ->where('product.id', $product->id)
            ->where('product.sku', $product->sku)
            ->has('related', 1)
            ->has('related.0.images')
            ->where('related.0.id', $related->id),
        );

    $this->getJson("/api/storefront/products/{$product->slug}")
        ->assertOk()
        ->assertJsonPath('data.id', $product->id);
});
