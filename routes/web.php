<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\Api\Storefront\CategoryDealsController;
use App\Http\Controllers\Api\Storefront\CollectionController;
use App\Http\Controllers\Api\Storefront\CollectionRegistry;
use App\Http\Controllers\Api\Storefront\HomeController;
use App\Http\Controllers\Teams\TeamInvitationController;
use App\Http\Middleware\EnsureTeamMembership;
use Illuminate\Support\Facades\Route;

Route::get('/', HomeController::class)->name('home');
Route::get('shop/categories/{categorySlug}/deals/{promotionSlug}', CategoryDealsController::class)
    ->name('shop.categories.deals');
Route::get('shop/deals/{slug}', CollectionController::class)
    ->whereIn('slug', CollectionRegistry::slugs())
    ->name('shop.deals.show');

Route::prefix('{current_team}')
    ->middleware(['auth', 'verified', EnsureTeamMembership::class])
    ->group(function () {
        Route::get('dashboard', DashboardController::class)->name('dashboard');
    });

Route::middleware(['auth'])->group(function () {
    Route::post('invitations/{invitation}/accept', [TeamInvitationController::class, 'accept'])->name('invitations.accept');
    Route::delete('invitations/{invitation}', [TeamInvitationController::class, 'decline'])->name('invitations.decline');
});

require __DIR__.'/settings.php';
