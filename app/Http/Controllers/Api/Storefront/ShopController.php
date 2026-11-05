<?php

namespace App\Http\Controllers\Api\Storefront;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Products;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class ShopController extends Controller
{
    public function index(Request $request): Response
    {
        return $this->render($request, []);
    }

    public function browse(Request $request): Response
    {
        $path = $request->route('path') ?? '';

        return $this->render($request, $path === '' ? [] : explode('/', $path));
    }

    protected function render(Request $request, array $initialPath): Response
    {
        // All visible products — client filters/paginates for now.
        // When the catalogue grows past ~500, switch to server-side filtering.
        $products = Products::query()
            ->visible()
            ->with(['brand:id,name,slug', 'category:id,name,slug,path', 'primaryImage', 'promotions'])
            ->get();

        // Category tree — only active, sorted
        $tree = Category::query()
            ->active()
            ->parents()
            ->ordered()
            ->with(['children' => fn ($q) => $q->active()->ordered()->with([
                'children' => fn ($q2) => $q2->active()->ordered(),
            ])])
            ->get();

        return Inertia::render('storefront/shop', [
            'products'      => $products->map(fn (Products $p) => $this->mapProduct($p))->values()->all(),
            'tree'          => $this->mapTree($tree),
            'initialPath'   => $initialPath,
            'initialSearch' => $request->getQueryString() ?? '',
        ]);
    }

    /**
     * Convert a Product model into the ShopProduct shape the frontend expects.
     * Pulls filterable fields out of the `specifications` JSON.
     */
    protected function mapProduct(Products $p): array
    {
        $spec = collect($p->specifications ?? [])
           ->mapWithKeys(fn ($s) => [$s['label'] ?? '' => $s['value'] ?? ''])
            ->all();



        $attributes = [];
         if (! empty($spec['Processor'])) {
        $attributes['processor'] = trim($spec['Processor']);
        }
        if (! empty($spec['Memory'])) {
            $attributes['ram'] = preg_replace('/\s*RAM$/i', '', trim($spec['Memory']));
        }
        if (! empty($spec['Storage'])) {
            $attributes['storage'] = trim($spec['Storage']);   // keep "512GB SSD"
        }


        $rootSlug = $p->category?->path 
                 ? explode('/',$p->category->path)[0] : '';

     $rootName = match ($rootSlug) {
        'laptops'            => 'Laptops',
        'desktops'           => 'Desktops',
        'components'         => 'Components',
        'servers-networking' => 'Servers & Networking',
        'accessories'        => 'Accessories',
        'printers'           => 'Printers',
        default              => $p->category?->name ?? 'Other',
     };



        $rawMemory   = $spec['Memory'] ?? '';
        $rawStorage  = $spec['Storage'] ?? '';
        $rawDisplay  = $spec['Display'] ?? '';

        // "8GB RAM" → "8GB"
        $ram = preg_replace('/\s*RAM$/i', '', trim($rawMemory));

        // "512GB SSD" → "512GB"
        $storage = preg_replace('/\s*(SSD|HDD|NVMe).*$/i', '', trim($rawStorage));

        // "15.6\" FHD" → "15.6"
        $screenSize = preg_match('/[\d.]+/', $rawDisplay, $m) ? $m[0] : '';

         $effective = (float) $p->effective_price;
         $base = (float) $p->price;
         $hasDiscount = $effective < $base;

         $categoryPath = $p->category
        ? explode('/', $p->category->path)
        : [];

        $data =  [
              'id'           => (string) $p->id,
        'slug'         => $p->slug,
        'name'         => $p->name,
        'brand'        => $p->brand?->name ?? 'Unknown',
        'category'     => $rootName,
        'categoryPath' => $categoryPath,
        'image'        => $p->primaryImage->first()?->url,
        'price'        => $effective,
        'oldPrice'     => $hasDiscount ? $base : null,
        'currency'     => 'KES',
        'specs'        => $p->specifications ?? [],
        'rating'       => null,
        'reviewCount'  => null,
        'inStock'      => $p->is_in_stock,
        'stockCount'   => $p->stock_quantity,
        'bestSeller'   => (bool) $p->is_best_seller,
        'attributes'   => $attributes,
        'addedAt'      => ($p->published_at ?? $p->created_at)->toIso8601String(),
        'sold'         => $p->is_best_seller ? 100 : 0,   // proxy until orders exist
        ];

        if ($badge = $p->badge) {
        $data['badge'] = $badge;
    }

      return $data;
    }

    /**
     * Convert the Category tree into CategoryNode[] as expected by the sidebar.
     */
    protected function shortName(string $name): string
    {
        return preg_replace('/\s+(?:Laptops|Desktops)$/i', '', $name) ?? $name;
    }

    protected function mapTree($nodes): array
{
    return $nodes->map(fn (Category $c) => [
        'slug'     => $c->slug,
        'label'    => $c->name,
        'short'    => $this->shortName($c->name),
        'heading'  => $c->name,
        'children' => $c->children->isNotEmpty()
            ? $this->mapTree($c->children)
            : [],
    ])->values()->all();
}
}