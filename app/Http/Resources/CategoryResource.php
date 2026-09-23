<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;


class CategoryResource extends JsonResource{
     /**
     * Optional mode set by the controller:
     * - 'list'   → compact (for index / nav / dropdowns)
     * - 'detail' → full (for show / edit)
     * - null     → default (medium)
     */
    protected ?string $mode = null;

     public function mode(string $mode): self
    {
        $this->mode = $mode;
        return $this;
    }

     /**
 * Admin shape — includes audit fields, parent, children.
 */

    public static function forAdmin(Category $category): self {
        return ( new self($category))->withContext('admin');
    }

    public static function forStorefront(Category $category): self{
       return (new self($category))->withContext('storefront');
    }

    protected ?string $context = null;

    public function withContext(string $context): self
    {
        $this->context = $context;
        return $this;
    }

    public function toArray(Request $request): array
    {
        return $this->context === 'storefront'
            ? $this->storefrontPayload()
            : $this->adminPayload();
    }

   
    /* =========================================================
     |  PAYLOAD VARIANTS
     ========================================================= */

        protected function storefrontPayload(): array
    {
        return [
            'id'            => $this->id,
            'name'          => $this->name,
            'slug'          => $this->slug,
            'path'          => $this->path,
            'level'         => $this->level,
            'breadcrumb'    => $this->when($this->level > 1, fn () => $this->breadcrumb),
            'children'      => self::collection($this->whenLoaded('children')),
        ];
    }

    protected function adminPayload(): array
{
    return [
        'id'            => $this->id,
        'parent_id'     => $this->parent_id,
        'name'          => $this->name,
        'slug'          => $this->slug,
        'path'          => $this->path,
        'path_segments' => $this->path_segments,
        'level'         => $this->level,
        'sort_order'    => $this->sort_order,
        'is_active'     => (bool) $this->is_active,
        'is_root'       => $this->is_root,
        'is_leaf'       => $this->is_leaf,
        'has_children'  => $this->hasChildren(),
        'breadcrumb'    => $this->when($this->level > 1, fn () => $this->breadcrumb),
        'parent'        => new self($this->whenLoaded('parent')),
        'children'      => self::collection($this->whenLoaded('children')),
        'created_at'    => $this->created_at?->toIso8601String(),
        'updated_at'    => $this->updated_at?->toIso8601String(),
    ];

    protected function detailPayload(): array
    {
        return array_merge($this->defaultPayload(), [
            'parent' => new self($this->whenLoaded('parent')),

            'children' => self::collection(
                $this->whenLoaded('children')
            ),

            // Recursive eager-loaded tree (only if loaded)
            'children_recursive' => self::collection(
                $this->whenLoaded('childrenRecursive')
            ),

            // Optional if you ever attach products
            'products_count' => $this->when(
                isset($this->products_count),
                fn () => $this->products_count
            ),
        ]);
    }

    /* =========================================================
     |  META
     ========================================================= */

    public function with(Request $request): array
    {
        return [];
    }
}