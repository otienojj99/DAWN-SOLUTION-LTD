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
use App\Http\Controllers\Api\Storefront\NewsletterController;
use App\Http\Controllers\Api\Storefront\QuoteController;
use App\Http\Controllers\Api\Storefront\ShopController;
use App\Http\Controllers\Api\Storefront\ReviewController as StorefrontReviewController;
use App\Http\Controllers\Api\Storefront\CartController;

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

Route::prefix('cart')->name('cart.')->group(function () {
    Route::get('/', [CartController::class, 'show'])->name('show');
    Route::post('items', [CartController::class, 'add'])->name('items.add');
    Route::patch('items/{itemId}', [CartController::class, 'updateItem'])->name('items.update');
    Route::delete('items/{itemId}', [CartController::class, 'removeItem'])->name('items.remove');
    Route::delete('/', [CartController::class, 'clear'])->name('clear');
});

Route::post('newsletter/subscribe', [NewsletterController::class, 'store'])
    ->name('newsletter.subscribe');

    Route::post('quote-requests', [QuoteController::class, 'store'])
    ->name('quotes.store');
    Route::post('reviews', [StorefrontReviewController::class, 'store'])
        ->name('reviews.store');
// Storefront (Inertia pages — public)
    Route::prefix('shop')->name('shop.')->group(function () {

    // 1. Shop index (must be first — most specific)
    Route::get('/', [ShopController::class, 'index'])->name('index');

    // 2. All SPECIFIC routes BEFORE the catch-all
    Route::get('categories', [StorefrontCategoryController::class, 'index'])->name('categories.index');
    Route::get('categories/tree', [StorefrontCategoryController::class, 'tree'])->name('categories.tree');
    Route::get('categories/{categorySlug}/deals/{promotionSlug}', CategoryDealsController::class)->name('categories.deals');
    Route::get('categories/{category}', [StorefrontCategoryController::class, 'show'])->name('categories.show');

    Route::get('products', [StorefrontProductController::class, 'index'])->name('products.index');
    Route::get('products/discounts', [StorefrontProductController::class, 'onDiscount'])->name('products.discounts');
    Route::get('products/clearance', [StorefrontProductController::class, 'onClearance'])->name('products.clearance');
    Route::get('products/best-deals', [StorefrontProductController::class, 'bestDeals'])->name('products.best-deals');
    Route::get('products/new-arrivals', [StorefrontProductController::class, 'newArrivals'])->name('products.new-arrivals');
    Route::get('products/{product:slug}', [StorefrontProductController::class, 'show'])->name('products.show');

    Route::get('shop-by/{slug}', [StorefrontUseCaseController::class, 'show'])->name('use-cases.show');

    Route::get('deals/{slug}', CollectionController::class)
        ->whereIn('slug', CollectionRegistry::slugs())
        ->name('deals.show');
    Route::get('new-arrivals', CollectionController::class)
        ->defaults('slug', 'new-arrivals')
        ->name('new-arrivals');

    Route::get('brands/{slug}', [StorefrontBrandController::class, 'show'])->name('brands.show');

    // 3. Catch-all LAST — anything not matched above goes to the shop drill-down
    Route::get('{path?}', [ShopController::class, 'browse'])
        ->where('path', '.*')
        ->name('browse');
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