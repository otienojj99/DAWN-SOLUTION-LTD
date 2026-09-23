<?php

namespace App\Http\Controllers\Api\Storefront;

use App\Enums\ApiErrorCode;
use App\Http\Resources\CategoryResource;
use App\Models\Category;
use App\Services\Category\CategoryService;
use App\Services\Category\CategoryTreeService;
use App\Services\Category\Filters\CategoryFilters;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CategoryController extends StorefrontApiController
{
    public function __construct(
        protected CategoryService $service,
        protected CategoryTreeService $treeService
    ) {}

    public function index(Request $request): JsonResponse
    {
        // Force is_active = true at the filter layer — no auth bypass possible
        $filters = CategoryFilters::fromRequest($request, ['is_active' => true]);

        return $this->respondSuccess(
            CategoryResource::collection($this->service->list($filters)),
            'Categories fetched successfully'
        );
    }

    public function tree(): JsonResponse
    {
        return $this->respondSuccess(
            CategoryResource::collection($this->treeService->build(activeOnly: true)),
            'Category tree fetched successfully'
        );
    }

    public function show(Category $category): JsonResponse
    {
        if (! $category->is_active) {
            $this->fail(ApiErrorCode::CATEGORY_NOT_FOUND, 'Category not found.');
        }

        return $this->respondSuccess(
            new CategoryResource(
                $this->service->find($category, ['parent', 'children'])
            ),
            'Category fetched successfully'
        );
    }
}