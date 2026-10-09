<?php

namespace App\Http\Controllers\Api\Storefront;

use App\Http\Controllers\Controller;
use App\Http\Resources\ProductResource;
use App\Models\Category;
use App\Models\Product;
use App\Models\Promotions;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;
use Inertia\Response;

class CategoryDealsController extends Controller {
    public function __invoke(string $categorySlug, string $promotionSlug): Response {
         // 1. Resolve category
        $category = Category::query()
            ->where('slug', $categorySlug)
            ->where('is_active', true)
            ->with(['parent'])
            ->firstOrFail();

            // 2. Resolve promotion — must be homepage-visible
        $promotion = Promotions::query()
            ->where('slug', $promotionSlug)
            ->where('is_active', true)
            ->where('show_on_homepage', true)
            ->with(['homepageCategory:id,name,slug'])
            ->firstOrFail();

        if ($promotion->homepage_category_id !== $category->id) {
            abort(404);
        }

        $directProductIds = DB::table('product_promotions')
            ->where('promotion_id', $promotion->id)
            ->pluck('product_id')
            ->all();

        $affectedCategoryIds = collect();

        $categoryPivots = DB::table('category_promotions')
            ->where('promotion_id', $promotion->id)
            ->get(['category_id', 'include_descendants']);

        foreach ($categoryPivots as $pivot){
             $cat = Category::find($pivot->category_id);
            if (! $cat) {
                continue;
            }

            if ($pivot->include_descendants) {
                $affectedCategoryIds = $affectedCategoryIds->merge(
                    $cat->descendants()->pluck('id')
                );
            }

            $affectedCategoryIds->push($cat->id);
        }

        $affectedCategoryIds = $affectedCategoryIds->unique()->values()->all();

        // 5. Category subtree
        $descendantIds = $category->descendants()
            ->pluck('id')
            ->push($category->id);

        $hasFilters = ! empty($directProductIds) || ! empty($affectedCategoryIds);


        // 6. Products in the subtree that are affected by the promo
        $query = Product::query()
            ->visible()
            ->whereIn('category_id', $descendantIds)
            // ->where(function ($q) use ($directProductIds, $affectedCategoryIds) {
            //     $q->whereIn('id', $directProductIds)
            //       ->orWhereIn('category_id', $affectedCategoryIds);
            // })
            ->with(['primaryImage', 'brand', 'category']);

        if ($hasFilters) {
            $query->where(function ($q) use ($directProductIds, $affectedCategoryIds) {
                $q->whereIn('id', $directProductIds);
                if (! empty($affectedCategoryIds)) {
                    $q->orWhereIn('category_id', $affectedCategoryIds);
                }
            });
        }

        $products = $query->paginate(24)->withQueryString();

        return Inertia::render('storefront/categories/deals', [
            'category' => [
                'id'         => $category->id,
                'name'       => $category->name,
                'slug'       => $category->slug,
                'breadcrumb' => $category->breadcrumb,
            ],
            'promotion' => [
                'id'           => $promotion->id,
                'name'         => $promotion->name,
                'slug'         => $promotion->slug,
                'kind'         => $promotion->kind,
                'tag'          => $promotion->resolved_hero_tag,
                'title'        => $promotion->resolved_hero_title,
                'subtitle'     => $promotion->resolved_hero_subtitle,
                'ctaLabel'     => $promotion->resolved_hero_cta_label,
                'heroImage'    => $promotion->hero_image_url,
                'heroImageAlt' => $promotion->hero_image_alt,
            ],
            'products'   => ProductResource::collection($products)->resolve(),
            'pagination' => [
                'current_page' => $products->currentPage(),
                'per_page'     => $products->perPage(),
                'total'        => $products->total(),
                'last_page'    => $products->lastPage(),
            ],
        ]);
            
    }
}