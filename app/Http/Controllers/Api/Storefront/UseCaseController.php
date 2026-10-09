<?php

namespace App\Http\Controllers\Api\Storefront;

use App\Http\Controllers\Controller;
use App\Http\Resources\ProductResource;
use App\Models\Product;
use App\Models\UseCase;
use App\Services\Product\Filters\ProductFilters;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class UseCaseController extends Controller
{
    public function show(string $slug, Request $request): Response
    {
        $useCase = UseCase::query()
            ->where('slug', $slug)
            ->where('is_active', true)
            ->firstOrFail();

        $filters = ProductFilters::fromRequest($request, [
            'is_active' => true,
        ]);

        $products = Product::query()
            ->visible()
            ->whereHas('useCases', fn ($q) => $q->where('use_cases.id', $useCase->id))
            ->with(['primaryImage', 'brand', 'category'])
            ->tap(fn ($q) => $filters->apply($q))
            ->paginate(24)
            ->withQueryString();

        return Inertia::render('storefront/use-cases/show', [
            'useCase' => [
                'id'    => $useCase->id,
                'name'  => $useCase->name,
                'slug'  => $useCase->slug,
                'icon'  => $useCase->icon,
                'color' => $useCase->color,
            ],
            'products' => ProductResource::collection($products)->resolve(),
            'pagination' => [
                'current_page' => $products->currentPage(),
                'per_page'     => $products->perPage(),
                'total'        => $products->total(),
                'last_page'    => $products->lastPage(),
            ],
        ]);
    }
}