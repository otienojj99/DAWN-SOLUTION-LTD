<?php

namespace App\Http\Middleware;

use App\Models\UseCase;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Inertia\Middleware;

class HandleInertiaRequests extends Middleware
{
    /**
     * The root template that's loaded on the first page visit.
     *
     * @see https://inertiajs.com/server-side-setup#root-template
     *
     * @var string
     */
    protected $rootView = 'app';

    /**
     * Determines the current asset version.
     *
     * @see https://inertiajs.com/asset-versioning
     */
    public function version(Request $request): ?string
    {
        return parent::version($request);
    }

    /**
     * Define the props that are shared by default.
     *
     * @see https://inertiajs.com/shared-data
     *
     * @return array<string, mixed>
     */
  public function share(Request $request): array
{
    return array_merge(parent::share($request), [

        'auth' => [
            'user' => $request->user() ? [
                'id'    => $request->user()->id,
                'name'  => $request->user()->name,
                'email' => $request->user()->email,
            ] : null,
        ],

        'useCases' => fn () => Cache::remember(
            'storefront.use_cases',
            now()->addHour(),
            fn () => \App\Models\UseCase::query()
                ->active()
                ->ordered()
                ->get(['id', 'name', 'slug', 'icon', 'color'])
                ->map(fn ($u) => [
                    'id'    => $u->id,
                    'name'  => $u->name,
                    'slug'  => $u->slug,
                    'icon'  => $u->icon,
                    'color' => $u->color,
                    'href'  => '/shop/shop-by/' . $u->slug,
                ])
                ->values()
                ->all()
        ),

        'categoryTree' => fn () => Cache::remember(
            'storefront.category_tree',
            now()->addHour(),
            fn () => \App\Models\Category::query()
                ->active()
                ->parents()
                ->ordered()
                ->with(['children' => fn ($q) => $q->active()->ordered()])
                ->get(['id', 'name', 'slug', 'path', 'level'])
                ->map(fn ($parent) => [
                    'id'       => $parent->id,
                    'name'     => $parent->name,
                    'slug'     => $parent->slug,
                    'href'     => '/shop/categories/' . $parent->slug,
                    'children' => $parent->children->map(fn ($child) => [
                        'id'   => $child->id,
                        'name' => $child->name,
                        'slug' => $child->slug,
                        'href' => '/shop/categories/' . $child->slug,
                    ])->values()->all(),
                ])
                ->values()
                ->all()
        ),

        'flash' => [
            'success' => fn () => $request->session()->get('success'),
            'error'   => fn () => $request->session()->get('error'),
        ],
    ]);
}
}
