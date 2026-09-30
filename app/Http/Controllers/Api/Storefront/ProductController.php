<?php

namespace App\Http\Controllers\Api\Storefront;

use App\Enums\ApiErrorCode;
use App\Http\Resources\ProductResource;
use App\Models\Product;
use App\Services\Product\Filters\ProductFilters;
use App\Services\Product\ProductService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ProductController extends StorefrontApiController
{
    public function __construct(protected ProductService $service) {}

    /* ---------- LIST ---------- */

    public function index(Request $request): JsonResponse
    {
        // Force storefront visibility — no auth bypass
        $filters = ProductFilters::fromRequest($request, [
            'is_active' => true,
        ]);

        $paginated = $this->service->paginate($filters, (int) $request->input('per_page', 24));

        return $this->respondPaginated(
            $paginated,
            'Products fetched successfully',
            fn (Product $p) => (new ProductResource($p))->forStorefront()->resolve()
        );
    }

    /* ---------- SHOW ---------- */

    public function show(string $slug): JsonResponse
    {
        $product = $this->service->findBySlug($slug, [
            'brands', 'category',
            'images', 'variants.attributeValues.attribute',
            'attributeValues.attribute',
            'useCases', 'promotions',
            'bundleItems.component.primaryImage',
        ]);

        if (! $product->is_active || $product->published_at === null || $product->published_at->isFuture()) {
            $this->fail(ApiErrorCode::PRODUCT_NOT_FOUND, 'Product not found.');
        }

        return $this->respondSuccess(
            (new ProductResource($product))->forStorefront(),
            'Product fetched successfully'
        );
    }

    /* ---------- CONVENIENCE ENDPOINTS ---------- */

    public function onDiscount(Request $request): JsonResponse
    {
        $request->merge(['promotion_kind' => 'discount']);
        return $this->index($request);
    }

    public function onClearance(Request $request): JsonResponse
    {
        $request->merge(['promotion_kind' => 'clearance']);
        return $this->index($request);
    }

    public function bestDeals(Request $request): JsonResponse
    {
        $request->merge(['type' => 'bundle']);
        return $this->index($request);
    }

    public function newArrivals(Request $request): JsonResponse
    {
        $request->merge([
            'sort' => 'published_at',
            'direction' => 'desc',
        ]);

        $filters = ProductFilters::fromRequest($request, ['is_active' => true]);

        // Additional scope: only published in last 30 days
        $paginated = \App\Models\Products::query()
            ->with(['primaryImage', 'brand', 'category'])
            ->visible()
            ->newArrivals(30)
            ->tap(fn ($q) => $filters->apply($q))
            ->paginate((int) $request->input('per_page', 24));

        return $this->respondPaginated(
            $paginated,
            'New arrivals fetched successfully',
            fn (Product $p) => (new ProductResource($p))->forStorefront()->resolve()
        );
    }
}