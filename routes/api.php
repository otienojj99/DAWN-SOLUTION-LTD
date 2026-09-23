<?php

use App\Http\Controllers\Api\Admin\CategoryController as AdminCategoryController;
use App\Http\Controllers\Api\Storefront\CategoryController as StorefrontCategoryController;
use Illuminate\Support\Facades\Route;



<?php

use App\Http\Controllers\Api\Admin\CategoryController as AdminCategoryController;
use App\Http\Controllers\Api\Storefront\CategoryController as StorefrontCategoryController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| ADMIN — dashboard operations
|--------------------------------------------------------------------------
*/
Route::prefix('admin')
    ->middleware(['auth:sanctum'])
    ->group(function () {
        Route::get('categories/tree', [AdminCategoryController::class, 'tree']);
        Route::patch('categories/reorder', [AdminCategoryController::class, 'reorder']);
        Route::patch('categories/{category}/toggle-active', [AdminCategoryController::class, 'toggleActive']);
        Route::patch('categories/{category}/move', [AdminCategoryController::class, 'move']);
        Route::apiResource('categories', AdminCategoryController::class);
    });

Route::prefix('storefront')->group(function () {
    Route::get('categories', [StorefrontCategoryController::class, 'index']);
    Route::get('categories/tree', [StorefrontCategoryController::class, 'tree']);
    Route::get('categories/{category}', [StorefrontCategoryController::class, 'show']);
});