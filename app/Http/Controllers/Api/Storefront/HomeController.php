<?php

namespace App\Http\Controllers\Api\Storefront;

use App\Http\Controllers\Controller;
use App\Models\Promotions;
use App\Models\Category;
use App\Models\Products;
use App\Models\Brands;
use App\Models\UseCase;
use Inertia\Inertia;
use Inertia\Response;

class HomeController extends Controller
{
    public function __invoke(): Response
    {
        $promoOffers = Promotions::query()
            ->onHomepage()
            ->with('homepageCategory:id,name,slug')
            ->get()
            ->map(fn (Promotions $promotion): array => [
                'id'       => (string) $promotion->id,
                'size'     => $promotion->homepage_size === 'featured' ? 'featured' : 'standard',
                'tag'      => $promotion->resolved_hero_tag,
                'tagStyle' => in_array($promotion->display_badge_color, ['red', 'orange', 'amber'], true)
                    ? 'accent'
                    : 'glass',
                'title'    => $promotion->resolved_hero_title ?? $promotion->name,
                'subtitle' => $promotion->resolved_hero_subtitle,
                'ctaLabel' => $promotion->resolved_hero_cta_label,
                'href'     => $promotion->homepage_href,
                'image'    => $promotion->hero_image_url,
                'imageAlt' => $promotion->hero_image_alt,
            ])
            ->values();


        $shopCategories = Category::query()
            ->active()
            ->parents()
            ->ordered()
            ->get(['id', 'name', 'slug', 'image_path', 'image_alt'])
            ->map(fn (Category $cat) => [
                'id'           => (string) $cat->id,
                'label'        => $cat->name,
                'href'         => '/shop/categories/' . $cat->slug,
                'image'        => $cat->image_url,
                'imageAlt'     => $cat->image_alt,
                'productCount' => $cat->productsCountInSubtree(),
                'badge'        => null,
            ])
            ->values()
            ->all();

        $bestSellers = Products::query()
            ->visible()
            ->bestSellers()
            ->with(['brand:id,name', 'category:id,name', 'primaryImage'])
            ->orderByDesc('is_featured')   // featured ones bubble up
            ->orderByDesc('updated_at')
            ->limit(12)
            ->get()
            ->map(fn (Products $p) => $this->toCardProduct($p))
            ->values()
            ->all();

        $brands = Brands::query()
        ->active()
        ->orderBy('sort_order')
        ->orderBy('name')
        ->get(['id', 'name', 'slug', 'logo_path'])
        ->map(fn (Brands $b) => [
            'id'    => (string) $b->id,
            'name'  => $b->name,
            'logo'  => $b->logo_url,
            'href'  => '/shop/brands/' . $b->slug,
        ])
        ->values()
        ->all();

    $useCases = UseCase::query()
        ->active()
        ->ordered()
        ->withCount('products')
        ->get(['id', 'name', 'slug', 'description', 'icon', 'color', 'image_path', 'image_alt', 'cta_label'])
        ->map(fn (UseCase $u) => [
            'id'             => $u->id,
            'title'          => $u->name,
            'description'    => $u->description,
            'ctaLabel'       => $u->cta_label ?? 'Shop Now',
            'href'           => '/shop/shop-by/' . $u->slug,
            'image'          => $u->image_url,
            'imageAlt'       => $u->image_alt,
            'icon'           => $u->icon,           // string key, resolved on frontend
            'color'          => $u->color,
            'itemCount'      => (int) $u->products_count,
        ])
    ->values()
    ->all();

    $clearancePromo = Promotions::query()
          -> live()
          ->ofKind('clearance')
          ->orderByDesc('ends_at')
          ->first();

    $deals = $clearancePromo
    ? Products::query()
         ->visible()
         ->onClearance()
         ->with(['brand:id,name', 'primaryImage'])
         ->orderByRaw('(compare_at_price - price) / compare_at_price DESC')
         ->limit(4)
         ->get()

         ->map(fn (Products $p) => [
             'id'              => (string) $p->id,
            'brand'           => $p->brand?->name ?? 'Unknown',
            'name'            => $p->name,
            'image'           => $p->primaryImage->first()?->url,
            'discountPercent' => $p->discount_percent ?? 0,
            'priceNow'        => (float) $p->effective_price,
            'priceOld'        => $p->compare_at_price !== null ? (float) $p->compare_at_price : 0,
            'claimedPercent'  => $p->claimed_percent,
            'quantityLeft'    => (int) $p->stock_quantity,
         ]) 

        ->values()
        ->all() :  [];

        $dealsMeta = [
            'badgeText'               => $clearancePromo?->badge_label ?? 'Limited Time',
            'title'                   => 'Clearance Deals — While Stock Lasts',
            'subtitle'                => "Prices this low won't be around long. Grab them before the timer runs out.",
            'countdownDurationSeconds' => $clearancePromo?->ends_at
                ? max(0, now()->diffInSeconds($clearancePromo->ends_at, false))
                : 0,
            'viewAllHref'             => '/shop/products/clearance',
        ];

        return Inertia::render('storefront/home', [
            'shopCategories' => $shopCategories,
            'promoOffers' => $promoOffers,
            'bestSellers' => $bestSellers,
            'brands' => $brands,
            'useCases' => $useCases,
            'deals'     => $deals,
            'dealsMeta' => $dealsMeta,
        ]);
    }



    private function toCardProduct(Products $p): array
    {
        $image = $p->primaryImage->first()?->url
            ?? $p->images->first()?->url
            ?? null;

        // Show the effective (post-discount) price as the current price,
    // and compare_at_price as the strikethrough "was" price.
        $currentPrice = (float) ($p->effective_price ?? $p->price);
        $oldPrice = $p->compare_at_price !== null
            ? (float) $p->compare_at_price
            : null;

        return [
            'id'          => (string) $p->id,
            'slug'        => $p->slug,
            'name'        => $p->name,
            'brand'       => $p->brand?->name ?? 'Unknown',
            'category'    => $p->category?->name ?? 'Uncategorized',
            'image'       => $image,
            'price'       => $currentPrice,
            'oldPrice'    => $oldPrice,
            'currency'    => 'KES',
            'inStock'     => $p->is_in_stock,
            'stockCount'  => $p->stock_quantity,
            'bestSeller'  => true,
            'badge'       => $p->badge,       // { label, color } or null
            'specs'       => [],              // hook this to attributes later if you want
            'rating'      => null,            // no reviews table yet — null = hide row
            'reviewCount' => null,
        ];
    }
}