<?php

namespace App\Http\Controllers\Api\Storefront;

use App\Http\Controllers\Controller;
use App\Http\Resources\ProductResource;
use App\Models\Brands;
use App\Models\Products;
use App\Services\Product\Filters\ProductFilters;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class BrandController extends Controller
{
    public function show(string $slug, Request $request): Response
    {
        $brand = Brands::query()
            ->where('slug', $slug)
            ->where('is_active', true)
            ->firstOrFail();

        $filters = ProductFilters::fromRequest($request, ['is_active' => true]);

        $products = Products::query()
            ->visible()
            ->where('brand_id', $brand->id)
            ->with(['primaryImage', 'brand', 'category'])
            ->tap(fn ($q) => $filters->apply($q))
            ->paginate(24)
            ->withQueryString();

        return Inertia::render('storefront/brands/show', [
            'brand' => [
                'id'       => $brand->id,
                'name'     => $brand->name,
                'slug'     => $brand->slug,
                'logoUrl'  => $brand->logo_url,
                'website'  => $brand->website,
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