<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
     /* =========================================================
     |  MASS ASSIGNMENT
     ========================================================= */

     protected $fillable = [
        'parent_id',
        'name',
        'slug',
        'path',
        'level',
        'sort_order',
        'is_active',
     ];

     /* =========================================================
     |  CASTING
     ========================================================= */


     protected $casts = [
        'parent_id'  => 'integer',
        'level'      => 'integer',
        'sort_order' => 'integer',
        'is_active'  => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
     ];

     /* =========================================================
     |  DEFAULTS
     ========================================================= */

     protected $attributes = [
        'level'      => 1,
        'sort_order' => 0,
        'is_active'  => true,
     ];

     /* =========================================================
     |  ROUTE BINDING
     |  /categories/laptops/lenovo-laptops
     ========================================================= */

     public function getRouteKeyName(): string
     {
        return 'slug';
     }

     /* =========================================================
     |  CONSTANTS
     ========================================================= */

    public const LEVEL_PARENT      = 1;
    public const LEVEL_CHILD       = 2;
    public const LEVEL_SUBCATEGORY = 3;
    public const MAX_LEVEL         = 3;
    public const PATH_SEPARATOR    = '/';

    /* =========================================================
     |  MODEL EVENTS — auto-maintain slug, path, level
     ========================================================= */

    protected static function booted(): void
    {
        static::saving(function (self $category){
             // Auto-slug from name if slug empty
             if(empty($category->slug) && !empty($category->name)){
                $category->slug = \Str::slug($category->name);
             }

               // Enforce max depth

               if($category->parent_id){
                $parent_level = static::query()->wnereKey($category->parent_id)->value('level');

                if($parent_level !== null && $parent_level>= self::MAX_LEVEL){
                   throw new \RuntimeException(
                        'Cannot create a category deeper than level ' . self::MAX_LEVEL . '.'
                    );
                }

                $category->level = (int) $parentLevel + 1;
               }else{
                  $category->level = self::LEVEL_PARENT;
               }

                // Build path

                $parentPath = $category->parent_id  ? static::query()->whereKey($category->parent_id)->value('path') : null;

                $category->path = $parentPath ? $parentPath . self::PATH_SEPERATOR . $category->slug
                                  : $category->slug;
        });

         static::saved(function (self $category){
             // If slug or path changed, cascade to children so their paths stay valid

             if($category->wasChanged(['slug', 'path'])){
               $category->cascadePathToDescendants();
             }
         });

    }

      /* =========================================================
     |  RELATIONS
     ========================================================= */


     /**
     * Direct parent category.
     */

   public function parent(): BelongsTo
    {
        return $this->belongsTo(self::class, 'parent_id');
    }

    
    /**
     * Direct children (ordered).
     */
    public function children(): HasMany
    {
        return $this->hasMany(self::class, 'parent_id')
                    ->orderBy('sort_order')
                    ->orderBy('name');
    }

    /**
     * Recursive children (children + their children).
     */
    public function childrenRecursive(): HasMany
    {
        return $this->children()->with('childrenRecursive');
    }

     /**
     * Descendants fetched via path prefix (fast, index-friendly).
     * Returns a query builder, not a relation, since it uses LIKE.
     */
    public function descendants(): Builder
    {
        return static::query()
            ->where('path', '!=', $this->path)
            ->where('path', 'LIKE', $this->path . self::PATH_SEPARATOR . '%');
    }

      /**
     * Ancestors (all parent levels above this node).
     * Derived from path so no recursion needed.
     */
    public function ancestors(): Builder
    {
        $segments = explode(self::PATH_SEPARATOR, $this->path);
        array_pop($segments); // remove self

        $paths = [];
        $current = '';
        foreach ($segments as $segment) {
            $current = $current === '' ? $segment : $current . self::PATH_SEPARATOR . $segment;
            $paths[] = $current;
        }

        return static::query()
            ->whereIn('path', $paths)
            ->orderBy('level');
    }

    public function promotions(): BelongsToMany
    {
         return $this->belongsToMany(Promotion::class, 'category_promotion')
                ->withPivot(['override_value', 'override_value_usd', 'include_descendants'])
                ->withTimestamps();
    }


    /**
     * Siblings (same parent, excluding self).
     */
    public function siblings(): Builder
    {
        return static::query()
            ->where('parent_id', $this->parent_id)
            ->whereKeyNot($this->getKey())
            ->orderBy('sort_order')
            ->orderBy('name');
    }

    /* =========================================================
     |  QUERY SCOPES
     ========================================================= */

     public function scopeActive(Builder $query): Builder{
      return $query->where('is_active', true);
     }


     public function scopeInactive(Builder $query): Builder
    {
        return $query->where('is_active', false);
    }

    public function scopeParents(Builder $query): Builder
    {
        return $query->whereNull('parent_id');
    }

    public function scopeLevel(Builder $query, int $level): Builder
    {
        return $query->where('level', $level);
    }

    public function scopeForParent(Builder $query, ?int $parentId): Builder
    {
        return $parentId === null
            ? $query->whereNull('parent_id')
            : $query->where('parent_id', $parentId);
    }

     public function scopeOrdered(Builder $query): Builder
    {
        return $query->orderBy('sort_order')->orderBy('name');
    }


    public function scopeSearch(Builder $query, string $term): Builder
    {
        return $query->where('name', 'LIKE', "%{$term}%");
    }

    /**
     * All categories under a given path prefix (subtree of slug).
     */
    public function scopeUnderPath(Builder $query, string $path): Builder
    {
        return $query
            ->where('path', $path)
            ->orWhere('path', 'LIKE', $path . self::PATH_SEPARATOR . '%');
    }

     /* =========================================================
     |  ACCESSORS
     ========================================================= */


     public function getIsRootAttribute(): bool
    {
        return $this->parent_id === null;
    }


    public function getIsLeafAttribute(): bool
    {
        return $this->level === self::MAX_LEVEL;
    }

    public function getIsActiveAttribute($value): bool
    {
        return (bool) $value;
    }

    /**
     * Human breadcrumb: Laptops › Lenovo Laptops › Lenovo IdeaPads
     */
    public function getBreadcrumbAttribute(): string
    {
        return $this->ancestors()
            ->pluck('name')
            ->push($this->name)
            ->implode(' › ');
    }

    /**
     * Array of ancestor categories ending with self.
     */
    public function getBreadcrumbArrayAttribute(): Collection
    {
        return $this->ancestors()->get()->push($this);
    }


    /**
     * Path segments as an array: ['laptops', 'lenovo-laptops', 'lenovo-ideapads']
     */
    public function getPathSegmentsAttribute(): array
    {
        return explode(self::PATH_SEPARATOR, $this->path);
    }

    /* =========================================================
     |  HELPERS — tree manipulation
     ========================================================= */

    public function hasChildren(): bool
    {
        return $this->children()->exists();
    }

    public function isDescendantOf(int $categoryId): bool
    {
        return $this->ancestors()->whereKey($categoryId)->exists();
    }

    public function isAncestorOf(int $categoryId): bool
    {
        return $this->descendants()->whereKey($categoryId)->exists();
    }

    public function canBeDeleted(): bool
    {
        return ! $this->hasChildren();
    }


    /**
     * Move this category under a new parent (updates path + level for whole subtree).
     */
    public function moveTo(?int $newParentId): self
    {
        if ($newParentId === $this->getKey()) {
            throw new \RuntimeException('A category cannot be its own parent.');
        }

        if ($newParentId !== null && $this->isDescendantOf($newParentId)) {
            throw new \RuntimeException('Cannot move a category under one of its own descendants.');
        }

        $oldPath = $this->path;

        $this->parent_id = $newParentId;
        $this->save(); // booted() recalculates level + path

        // Cascade new path to descendants
        static::query()
            ->where('path', 'LIKE', $oldPath . self::PATH_SEPARATOR . '%')
            ->update([
                'path' => \DB::raw(
                    "REPLACE(path, '{$oldPath}/', '{$this->path}/')"
                ),
            ]);

        return $this;
    }

     /**
     * Internal: called from saved() to cascade path changes to descendants.
     */
    protected function cascadePathToDescendants(): void
    {
        $old = $this->getOriginal('path');
        $new = $this->path;

        if (! $old || $old === $new) {
            return;
        }

        static::query()
            ->where('path', 'LIKE', $old . self::PATH_SEPARATOR . '%')
            ->update([
                'path' => \DB::raw(
                    "REPLACE(path, '{$old}/', '{$new}/')"
                ),
            ]);
    }

    /* =========================================================
     |  TREE BUILDER (optional, for API responses)
     ========================================================= */

    /**
     * Return the tree as a nested array/collection.
     */
    public static function tree(?int $parentId = null, bool $activeOnly = true): Collection
    {
        $query = static::query()->forParent($parentId)->ordered();

        if ($activeOnly) {
            $query->active();
        }

        return $query->get()->each(function (self $node) use ($activeOnly) {
            $node->setRelation('children', static::tree($node->id, $activeOnly));
        });
    }

}
