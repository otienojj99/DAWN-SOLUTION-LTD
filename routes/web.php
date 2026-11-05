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
use Illuminate\Http\Request;  
use Inertia\Inertia;
use App\Http\Controllers\Api\Storefront\NewsletterController;
use App\Http\Controllers\Api\Storefront\QuoteController;
use App\Http\Controllers\Api\Storefront\ShopController;
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

Route::post('newsletter/subscribe', [NewsletterController::class, 'store'])
    ->name('newsletter.subscribe');

    Route::post('quote-requests', [QuoteController::class, 'store'])
    ->name('quotes.store');

// Storefront (Inertia pages — public)
Route::prefix('shop')->name('shop.')->group(function () {
    Route::get('/', [ShopController::class, 'index'])->name('index');

    // Categories
    Route::get('categories', [StorefrontCategoryController::class, 'index'])->name('categories.index');
    Route::get('categories/tree', [StorefrontCategoryController::class, 'tree'])->name('categories.tree');
    Route::get('categories/{categorySlug}/deals/{promotionSlug}', CategoryDealsController::class)->name('categories.deals');
    Route::get('categories/{category}', [StorefrontCategoryController::class, 'show'])->name('categories.show');

    // Products — static routes first
    Route::get('{path?}', [ShopController::class, 'browse'])
        ->where('path', '.*')
        ->name('browse');
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


    

    Route::get('{path?}', function (Request $request) {
        $path = $request->route('path') ?? '';

        return Inertia::render('storefront/shop', [
            'initialPath' => $path === '' ? [] : explode('/', $path),
            'initialSearch' => $request->getQueryString() ?? '',
        ]);
    })->where('path', '.*')->name('browse');

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