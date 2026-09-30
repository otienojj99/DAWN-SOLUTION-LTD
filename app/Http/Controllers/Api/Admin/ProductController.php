<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Requests\Product\StoreProductRequest;
use App\Http\Requests\Product\UpdateProductRequest;
use App\Http\Resources\ProductResource;
use App\Models\Product;
use App\Services\Product\Filters\ProductFilters;
use App\Services\Product\ProductService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ProductController extends AdminApiController
{
    public function __construct(protected ProductService $service) {}

    /* ---------- LIST ---------- */

    public function index(Request $request): JsonResponse
    {
        $paginated = $this->service->paginate(
            ProductFilters::fromRequest($request),
            (int) $request->input('per_page', 20)
        );

        return $this->respondPaginated(
            $paginated,
            'Products fetched successfully',
            fn (Product $p) => (new ProductResource($p))->forAdmin()->resolve()
        );
    }

    /* ---------- SHOW ---------- */

    public function show(Product $product): JsonResponse
    {
        $product->load([
            'brand', 'category',
            'images', 'variants.attributeValues.attribute',
            'attributeValues.attribute',
            'useCase', 'promotions',
            'bundleItems.component.primaryImage',
        ]);

        return $this->respondSuccess(
            (new ProductResource($product))->forAdmin(),
            'Product fetched successfully'
        );
    }

    /* ---------- STORE ---------- */

    public function store(StoreProductRequest $request): JsonResponse
    {
        $product = $this->service->create($request->validated());

        return $this->respondCreated(
            (new ProductResource($product))->forAdmin(),
            'Product created successfully'
        );
    }

    /* ---------- UPDATE ---------- */

    public function update(UpdateProductRequest $request, Product $product): JsonResponse
    {
        $product = $this->service->update($product, $request->validated());

        return $this->respondUpdated(
            (new ProductResource($product))->forAdmin(),
            'Product updated successfully'
        );
    }

    /* ---------- DELETE ---------- */

    public function destroy(Product $product): JsonResponse
    {
        $name = $product->name;
        $this->service->delete($product);

        return $this->respondNoContent("Product '{$name}' deleted successfully");
    }

    /* ---------- STATE TOGGLES ---------- */

    public function toggleActive(Product $product): JsonResponse
    {
        $product = $this->service->toggleActive($product);

        return $this->respondUpdated(
            (new ProductResource($product))->forAdmin(),
            $product->is_active
                ? "Product '{$product->name}' activated"
                : "Product '{$product->name}' deactivated"
        );
    }

    public function toggleFeatured(Product $product): JsonResponse
    {
        $product = $this->service->toggleFeatured($product);

        return $this->respondUpdated(
            (new ProductResource($product))->forAdmin(),
            $product->is_featured
                ? "Product '{$product->name}' marked as featured"
                : "Product '{$product->name}' unfeatured"
        );
    }

    public function publish(Product $product): JsonResponse
    {
        $product = $this->service->publish($product);

        return $this->respondUpdated(
            (new ProductResource($product))->forAdmin(),
            "Product '{$product->name}' published"
        );
    }

    public function unpublish(Product $product): JsonResponse
    {
        $product = $this->service->unpublish($product);

        return $this->respondUpdated(
            (new ProductResource($product))->forAdmin(),
            "Product '{$product->name}' unpublished"
        );
    }

    /* ---------- DUPLICATE ---------- */

    public function duplicate(Product $product): JsonResponse
    {
        $copy = $this->service->duplicate($product);

        return $this->respondCreated(
            (new ProductResource($copy))->forAdmin(),
            'Product duplicated successfully'
        );
    }

    /* ---------- IMAGES ---------- */

    public function setPrimaryImage(Request $request, Product $product): JsonResponse
    {
        $validated = $request->validate([
            'image_id' => ['required', 'integer', 'exists:product_images,id'],
        ]);

        $this->service->setPrimaryImage($product, $validated['image_id']);

        return $this->respondSuccess(
            (new ProductResource($product->fresh()->load('images')))->forAdmin(),
            'Primary image updated successfully'
        );
    }

    /* ---------- BUNDLE ---------- */

    public function syncBundle(Request $request, Product $product): JsonResponse
    {
        $validated = $request->validate([
            'bundle_items'                          => ['required', 'array', 'min:1'],
            'bundle_items.*.component_product_id'   => ['required', 'integer', 'exists:products,id'],
            'bundle_items.*.variant_id'             => ['nullable', 'integer', 'exists:product_variants,id'],
            'bundle_items.*.quantity'               => ['nullable', 'integer', 'min:1'],
            'bundle_items.*.sort_order'             => ['nullable', 'integer', 'min:0'],
        ]);

        $this->service->syncBundleItems($product, $validated['bundle_items']);

        return $this->respondSuccess(
            (new ProductResource($product->fresh()->load('bundleItems.component')))->forAdmin(),
            'Bundle items updated successfully'
        );
    }

    /* ---------- STOCK ---------- */

    public function adjustStock(Request $request, Product $product): JsonResponse
    {
        $validated = $request->validate([
            'delta' => ['required', 'integer'],
        ]);

        $product = $this->service->adjustStock($product, $validated['delta']);

        return $this->respondUpdated(
            (new ProductResource($product))->forAdmin(),
            'Stock adjusted successfully'
        );
    }
}