<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Concerns\HandlesImageUploads;
use App\Http\Controllers\Controller;
use App\Http\Requests\Brand\StoreBrandRequest;
use App\Http\Requests\Brand\UpdateBrandRequest;
use App\Models\Brands;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class BrandController extends Controller
{
    use HandlesImageUploads;

    public function index(Request $request): Response
    {
        $brands = Brands::query()
            ->when($request->filled('search'), function ($q) use ($request) {
                $q->where('name', 'like', '%' . $request->input('search') . '%');
            })
            ->when($request->has('is_active'), function ($q) use ($request) {
                $q->where('is_active', filter_var($request->input('is_active'), FILTER_VALIDATE_BOOLEAN));
            })
            ->withCount('products')
            ->orderBy('sort_order')
            ->orderBy('name')
            ->paginate(20)
            ->withQueryString();

        return Inertia::render('admin/brands/index', [
            'brands' => $brands,
            'filters' => $request->only(['search', 'is_active']),
        ]);
    }

    public function create(): Response
    {
        return Inertia::render('admin/brands/create');
    }

    public function store(StoreBrandRequest $request): RedirectResponse
    {
        $data = $request->validated();

        if ($request->hasFile('logo')) {
            $data['logo_path'] = $this->uploadImage($request->file('logo'), 'brands');
        }

        unset($data['logo'], $data['remove_logo']);

        $brand = Brands::create($data);

        return redirect()
            ->route('admin.brands.index')
            ->with('success', "Brand '{$brand->name}' created.");
    }

    public function edit(Brands $brand): Response
    {
        return Inertia::render('admin/brands/edit', [
            'brand' => $brand,
        ]);
    }

    public function update(UpdateBrandRequest $request, Brands $brand): RedirectResponse
    {
        $data = $request->validated();

        if ($request->hasFile('logo')) {
            $data['logo_path'] = $this->replaceImage(
                $request->file('logo'),
                $brand->logo_path,
                'brands'
            );
        } elseif (! empty($data['remove_logo'])) {
            $this->deleteImage($brand->logo_path);
            $data['logo_path'] = null;
        }

        unset($data['logo'], $data['remove_logo']);

        $brand->update($data);

        return redirect()
            ->route('admin.brands.index')
            ->with('success', "Brand '{$brand->name}' updated.");
    }

    public function destroy(Brands $brand): RedirectResponse
    {
        if (! $brand->canBeDeleted()) {
            return back()->with('error', "Cannot delete '{$brand->name}' — it has products attached.");
        }

        $this->deleteImage($brand->logo_path);
        $brand->delete();

        return redirect()
            ->route('admin.brands.index')
            ->with('success', "Brand '{$brand->name}' deleted.");
    }
}