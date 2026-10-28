<?php

use App\Http\Controllers\Api\Admin\BrandController as AdminBrandController;
use App\Http\Controllers\Api\Storefront\BrandController as StorefrontBrandController;
use App\Http\Controllers\Api\Storefront\CategoryController as StorefrontCategoryController;
use App\Http\Controllers\Api\Storefront\CategoryDealsController;
use App\Http\Controllers\Api\Storefront\CollectionController;
use App\Http\Controllers\Api\Storefront\CollectionRegistry;
use App\Http\Controllers\Api\Storefront\HomeController;
use App\Http\Controllers\Api\Storefront\ProductController as StorefrontProductController;
use App\Http\Controllers\Api\Storefront\UseCaseController as StorefrontUseCaseController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\Teams\TeamInvitationController;
use App\Http\Middleware\EnsureTeamMembership;
use Illuminate\Support\Facades\Route;

// Home
Route::get('/', HomeController::class)->name('home');

// Admin (Inertia pages — auth + verified)
Route::middleware(['auth', 'verified'])
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {
        Route::resource('brands', AdminBrandController::class);
        // More admin web routes here as they're built
    });

// Storefront (Inertia pages — public)
Route::prefix('shop')->name('shop.')->group(function () {
    // Categories
    Route::get('categories', [StorefrontCategoryController::class, 'index'])->name('categories.index');
    Route::get('categories/tree', [StorefrontCategoryController::class, 'tree'])->name('categories.tree');
    Route::get('categories/{categorySlug}/deals/{promotionSlug}', CategoryDealsController::class)->name('categories.deals');
    Route::get('categories/{category}', [StorefrontCategoryController::class, 'show'])->name('categories.show');

    // Products — static routes first
    Route::get('products', [StorefrontProductController::class, 'index'])->name('products.index');
    Route::get('products/discounts', [StorefrontProductController::class, 'onDiscount'])->name('products.discounts');
    Route::get('products/clearance', [StorefrontProductController::class, 'onClearance'])->name('products.clearance');
    Route::get('products/best-deals', [StorefrontProductController::class, 'bestDeals'])->name('products.best-deals');
    Route::get('products/new-arrivals', [StorefrontProductController::class, 'newArrivals'])->name('products.new-arrivals');
    Route::get('products/{product:slug}', [StorefrontProductController::class, 'show'])->name('products.show');

    // Use cases
    Route::get('shop-by/{slug}', [StorefrontUseCaseController::class, 'show'])->name('use-cases.show');

    // Collections
    Route::get('deals/{slug}', CollectionController::class)
        ->whereIn('slug', CollectionRegistry::slugs())
        ->name('deals.show');
    Route::get('new-arrivals', CollectionController::class)
        ->defaults('slug', 'new-arrivals')
        ->name('new-arrivals');

    // Brands
    Route::get('brands/{slug}', [StorefrontBrandController::class, 'show'])->name('brands.show');
});

// Dashboard
Route::prefix('{current_team}')
    ->middleware(['auth', 'verified', EnsureTeamMembership::class])
    ->group(function () {
        Route::get('dashboard', DashboardController::class)->name('dashboard');
    });

// Invitations
Route::middleware(['auth'])->group(function () {
    Route::post('invitations/{invitation}/accept', [TeamInvitationController::class, 'accept'])->name('invitations.accept');
    Route::delete('invitations/{invitation}', [TeamInvitationController::class, 'decline'])->name('invitations.decline');
});

require __DIR__.'/settings.php';