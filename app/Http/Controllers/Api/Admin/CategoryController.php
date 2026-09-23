<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Requests\Category\ReorderCategoryRequest;
use App\Http\Requests\Category\StoreCategoryRequest;
use App\Http\Requests\Category\UpdateCategoryRequest;
use App\Http\Resources\CategoryResource;
use App\Models\Category;
use App\Services\Category\CategoryService;
use App\Services\Category\CategoryTreeService;
use App\Services\Category\Filters\CategoryFilters;
use App\Rules\ValidCategoryParent;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CategoryController extends AdminApiController
{
    public function __construct(
        protected CategoryService $service,
        protected CategoryTreeService $treeService
    ) {}

    public function index(Request $request): JsonResponse
    {
        $paginated = $this->service->paginate(
            CategoryFilters::fromRequest($request),
            (int) $request->input('per_page', 15)
        );

        return $this->respondPaginated(
            $paginated,
            'Categories fetched successfully',
            fn (Category $c) => (new CategoryResource($c))->resolve()
        );
    }

    public function tree(): JsonResponse
    {
        return $this->respondSuccess(
            CategoryResource::collection($this->treeService->build(activeOnly: false)),
            'Category tree fetched successfully'
        );
    }

    public function show(Category $category): JsonResponse
    {
        return $this->respondSuccess(
            new CategoryResource($this->service->find($category, ['parent', 'children'])),
            'Category fetched successfully'
        );
    }

    public function store(StoreCategoryRequest $request): JsonResponse
    {
        $category = $this->service->create($request->validated());

        return $this->respondCreated(
            new CategoryResource($category),
            'Category created successfully'
        );
    }

    public function update(UpdateCategoryRequest $request, Category $category): JsonResponse
    {
        $category = $this->service->update($category, $request->validated());

        return $this->respondUpdated(
            new CategoryResource($category),
            'Category updated successfully'
        );
    }

    public function destroy(Category $category): JsonResponse
    {
        $name = $category->name;
        $this->service->delete($category);

        return $this->respondNoContent("Category '{$name}' deleted successfully");
    }

    public function toggleActive(Category $category): JsonResponse
    {
        $category = $this->service->toggleActive($category);

        return $this->respondUpdated(
            new CategoryResource($category),
            $category->is_active
                ? "Category '{$category->name}' activated"
                : "Category '{$category->name}' deactivated"
        );
    }

    public function move(Request $request, Category $category): JsonResponse
    {
        $validated = $request->validate([
            'parent_id' => ['nullable', 'integer', 'exists:categories,id', new ValidCategoryParent($category->id)],
        ]);

        $category = $this->service->move($category, $validated['parent_id'] ?? null);

        return $this->respondUpdated(
            new CategoryResource($category),
            'Category moved successfully'
        );
    }

    public function reorder(ReorderCategoryRequest $request): JsonResponse
    {
        $this->service->reorder($request->validated()['items']);

        return $this->respondSuccess(null, 'Categories reordered successfully');
    }
}