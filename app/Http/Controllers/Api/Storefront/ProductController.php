<?php

namespace App\Http\Controllers\Api\Storefront;

use App\Enums\ApiErrorCode;
use App\Http\Resources\ProductResource;
use App\Models\Product;
use App\Models\Products;
use App\Services\Product\Filters\ProductFilters;
use App\Services\Product\ProductService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response as InertiaResponse;


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

    public function show(string $slug): InertiaResponse
    {
        $product = $this->findVisibleProduct($slug);

        $related = Products::query()
            ->visible()
            ->where('category_id', $product->category_id)
            ->where('id', '!=', $product->id)
            ->with(['brand', 'category', 'images', 'promotions'])
            ->limit(4)
            ->get();

       return Inertia::render('storefront/product/product', [
        'product' => (new ProductResource($product))->forStorefront()->resolve(),
        'related' => ProductResource::collection($related)
            ->map(fn ($r) => $r->forStorefront()->resolve())
            ->all(),
       ]);
    }

    public function showApi(string $slug): JsonResponse
    {
        return $this->respondSuccess(
            (new ProductResource($this->findVisibleProduct($slug)))->forStorefront(),
            'Product fetched successfully'
        );
    }

    protected function findVisibleProduct(string $slug): Products
    {
        $product = $this->service->findBySlug($slug, [
            'brand', 'category',
            'images', 'variants.attributeValues.attribute',
            'attributeValues.attribute',
            'useCases', 'promotions',
            'bundleItems.component.primaryImage',
            'approvedReviews' => fn ($q) => $q->latest(),
        ]);

        if (! $product->is_active || $product->published_at === null || $product->published_at->isFuture()) {
            $this->fail(ApiErrorCode::PRODUCT_NOT_FOUND, 'Product not found.');
        }

        return $product;
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