<?php

use App\Models\Products;

it('returns the percentage of initial stock that has been claimed', function () {
    $product = new Products([
        'initial_stock_quantity' => 80,
        'stock_quantity' => 20,
    ]);

    expect($product->claimed_percent)->toBe(75);
});

it('returns zero when initial stock is not positive', function () {
    $product = new Products([
        'initial_stock_quantity' => 0,
        'stock_quantity' => 20,
    ]);

    expect($product->claimed_percent)->toBe(0);
});

it('keeps the claimed percentage between zero and one hundred', function () {
    $oversoldProduct = new Products([
        'initial_stock_quantity' => 20,
        'stock_quantity' => 25,
    ]);
    $fullyClaimedProduct = new Products([
        'initial_stock_quantity' => 20,
        'stock_quantity' => -5,
    ]);

    expect($oversoldProduct->claimed_percent)->toBe(0);
    expect($fullyClaimedProduct->claimed_percent)->toBe(100);
});
