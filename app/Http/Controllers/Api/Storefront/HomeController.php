<?php

namespace App\Http\Controllers\Api\Storefront;

use App\Http\Controllers\Controller;
use App\Models\Promotions;
use App\Models\Category;
use App\Models\Products;
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

        return Inertia::render('storefront/home', [
            'shopCategories' => $shopCategories,
            'promoOffers' => $promoOffers,
            'bestSellers' => $bestSellers,
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