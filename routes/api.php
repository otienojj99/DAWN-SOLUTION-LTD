<?php
use App\Http\Controllers\Api\Admin\CategoryController as AdminCategoryController;
use App\Http\Controllers\Api\Storefront\CategoryController as StorefrontCategoryController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\Admin\ProductController as AdminProductController;
use App\Http\Controllers\Api\Storefront\ProductController as StorefrontProductController;

/*
|--------------------------------------------------------------------------
| ADMIN — dashboard operations
|--------------------------------------------------------------------------
*/
// Route::prefix('admin')
//     ->middleware(['auth:sanctum'])
//     ->group(function () {
//         Route::get('categories/tree', [AdminCategoryController::class, 'tree']);
//         Route::patch('categories/reorder', [AdminCategoryController::class, 'reorder']);
//         Route::patch('categories/{category}/toggle-active', [AdminCategoryController::class, 'toggleActive']);
//         Route::patch('categories/{category}/move', [AdminCategoryController::class, 'move']);
//         Route::apiResource('categories', AdminCategoryController::class);
//     });

Route::prefix('admin')
    ->middleware(['auth:sanctum'])
    ->group(function () {

        // Categories (from earlier)

        Route::get('categories/tree', [AdminCategoryController::class, 'tree']);
        Route::patch('categories/reorder', [AdminCategoryController::class, 'reorder']);
        Route::patch('categories/{category}/toggle-active', [AdminCategoryController::class, 'toggleActive']);
        Route::patch('categories/{category}/move', [AdminCategoryController::class, 'move']);
        Route::apiResource('categories', AdminCategoryController::class);

        // Products — static routes first
        Route::patch('products/{product}/toggle-active', [AdminProductController::class, 'toggleActive']);
        Route::patch('products/{product}/toggle-featured', [AdminProductController::class, 'toggleFeatured']);
        Route::patch('products/{product}/publish', [AdminProductController::class, 'publish']);
        Route::patch('products/{product}/unpublish', [AdminProductController::class, 'unpublish']);
        Route::post('products/{product}/duplicate', [AdminProductController::class, 'duplicate']);
        Route::patch('products/{product}/primary-image', [AdminProductController::class, 'setPrimaryImage']);
        Route::patch('products/{product}/bundle', [AdminProductController::class, 'syncBundle']);
        Route::patch('products/{product}/stock', [AdminProductController::class, 'adjustStock']);

        Route::apiResource('products', AdminProductController::class);
    });




Route::prefix('storefront')->group(function () {
    Route::get('categories', [StorefrontCategoryController::class, 'index']);
    Route::get('categories/tree', [StorefrontCategoryController::class, 'tree']);
    Route::get('categories/{category}', [StorefrontCategoryController::class, 'show']);

    // Products — static routes first
    Route::get('products/discounts', [StorefrontProductController::class, 'onDiscount']);
    Route::get('products/clearance', [StorefrontProductController::class, 'onClearance']);
    Route::get('products/best-deals', [StorefrontProductController::class, 'bestDeals']);
    Route::get('products/new-arrivals', [StorefrontProductController::class, 'newArrivals']);

    Route::get('products', [StorefrontProductController::class, 'index']);
    Route::get('products/{product:slug}', [StorefrontProductController::class, 'show']);
});


