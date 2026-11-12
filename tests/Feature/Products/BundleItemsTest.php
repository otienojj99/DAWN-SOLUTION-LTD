<?php

use App\Models\BundleItems;
use App\Models\Category;
use App\Models\Products;

it('loads bundle components in configured order', function () {
    $category = Category::create([
        'name' => 'Bundle test category',
        'slug' => 'bundle-test-category',
        'path' => 'bundle-test-category',
        'level' => 1,
    ]);

    $bundle = Products::create([
        'name' => 'Test bundle',
        'slug' => 'test-bundle',
        'sku' => 'TEST-BUNDLE',
        'type' => 'bundle',
        'category_id' => $category->id,
        'price' => 100,
    ]);
    $firstComponent = Products::create([
        'name' => 'First component',
        'slug' => 'first-component',
        'sku' => 'FIRST-COMPONENT',
        'category_id' => $category->id,
        'price' => 25,
    ]);
    $secondComponent = Products::create([
        'name' => 'Second component',
        'slug' => 'second-component',
        'sku' => 'SECOND-COMPONENT',
        'category_id' => $category->id,
        'price' => 50,
    ]);

    BundleItems::create([
        'bundle_product_id' => $bundle->id,
        'component_product_id' => $secondComponent->id,
        'quantity' => 1,
        'sort_order' => 20,
    ]);
    BundleItems::create([
        'bundle_product_id' => $bundle->id,
        'component_product_id' => $firstComponent->id,
        'quantity' => 1,
        'sort_order' => 10,
    ]);

    $loadedBundle = Products::with('bundleItems.component')->findOrFail($bundle->id);

    expect($loadedBundle->bundleItems)->toHaveCount(2)
        ->and($loadedBundle->bundleItems->first()->component->is($firstComponent))->toBeTrue();
});
