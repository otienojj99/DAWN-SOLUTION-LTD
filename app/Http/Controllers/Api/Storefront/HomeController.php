<?php

namespace App\Http\Controllers\Api\Storefront;

use App\Http\Controllers\Controller;
use App\Models\Promotions;
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

        return Inertia::render('storefront/home', [
            'promoOffers' => $promoOffers,
        ]);
    }
}