<?php

namespace App\Http\Controllers\Api\Storefront;

use App\Http\Controllers\Controller;
use App\Http\Resources\ProductResource;
use App\Models\Product;
use App\Support\Storefront\CollectionRegistry;
use Inertia\Inertia;
use Inertia\Response;

class CollectionController extends Controller
{
    public function __invoke(string $slug): Response
    {
        $collection = CollectionRegistry::resolve($slug);
        abort_if(! $collection, 404);

        $products = Product::query()
            ->visible()
            ->with(['primaryImage', 'brand', 'category'])
            ->tap(fn ($q) => $collection['apply']($q))
            ->paginate(24);

        return Inertia::render('storefront/collections/show', [
            'collection' => [
                'slug'  => $slug,
                'title' => $collection['title'],
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