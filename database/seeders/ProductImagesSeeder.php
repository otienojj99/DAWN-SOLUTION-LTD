<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Products;
use App\Models\ProductImages;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ProductImagesSeeder extends Seeder
{
    /**
     * Curated Unsplash photos per category.
     * All products in a category share these 3 photos.
     * Primary = [0], gallery = [1], [2].
     */
    protected array $categoryImages = [
        'laptops' => [
            'https://images.unsplash.com/photo-1496181133206-80ce9b88a853?w=1000&q=80',
            'https://images.unsplash.com/photo-1541807084-5c52b6b3adef?w=1000&q=80',
            'https://images.unsplash.com/photo-1603302576837-37561b2e2302?w=1000&q=80',
        ],
        'desktops' => [
            'https://images.unsplash.com/photo-1587831990711-23ca6441447b?w=1000&q=80',
            'https://images.unsplash.com/photo-1547082299-de196ea013d6?w=1000&q=80',
            'https://images.unsplash.com/photo-1593640408182-31c70c8268f5?w=1000&q=80',
        ],
        'components' => [
            'https://images.unsplash.com/photo-1587202372634-32705e3bf49c?w=1000&q=80',
            'https://images.unsplash.com/photo-1591488320449-011701bb6704?w=1000&q=80',
            'https://images.unsplash.com/photo-1555617981-dac3880eac6e?w=1000&q=80',
        ],
        'servers-networking' => [
            'https://images.unsplash.com/photo-1591405351990-4726e331f141?w=1000&q=80',
            'https://images.unsplash.com/photo-1558494949-ef010cbdcc31?w=1000&q=80',
            'https://images.unsplash.com/photo-1544197150-b99a580bb7a8?w=1000&q=80',
        ],
        'mice' => [
            'https://images.unsplash.com/photo-1527864550417-7fd91fc51a46?w=1000&q=80',
            'https://images.unsplash.com/photo-1615663245857-ac93bb7c39e7?w=1000&q=80',
            'https://images.unsplash.com/photo-1587829741301-dc798b83add3?w=1000&q=80',
        ],
        'keyboards' => [
            'https://images.unsplash.com/photo-1587829741301-dc798b83add3?w=1000&q=80',
            'https://images.unsplash.com/photo-1595044426077-d36d9236d54a?w=1000&q=80',
            'https://images.unsplash.com/photo-1618384887929-16ec33fab9ef?w=1000&q=80',
        ],
        'headsets' => [
            'https://images.unsplash.com/photo-1505740420928-5e560c06d30e?w=1000&q=80',
            'https://images.unsplash.com/photo-1583394838336-acd977736f90?w=1000&q=80',
            'https://images.unsplash.com/photo-1484704849700-f032a568e944?w=1000&q=80',
        ],
        'speakers' => [
            'https://images.unsplash.com/photo-1608043152269-423dbba4e7e1?w=1000&q=80',
            'https://images.unsplash.com/photo-1545454675-3531b543be5d?w=1000&q=80',
            'https://images.unsplash.com/photo-1589003077984-894e133dabab?w=1000&q=80',
        ],
        'video-conferencing' => [
            'https://images.unsplash.com/photo-1600880292203-757bb62b4baf?w=1000&q=80',
            'https://images.unsplash.com/photo-1556761175-5973dc0f32e7?w=1000&q=80',
            'https://images.unsplash.com/photo-1587825140708-dfaf72ae4b04?w=1000&q=80',
        ],
        'gamepads' => [
            'https://images.unsplash.com/photo-1606144042614-b2417e99c4e3?w=1000&q=80',
            'https://images.unsplash.com/photo-1493711662062-fa541adb3fc8?w=1000&q=80',
            'https://images.unsplash.com/photo-1552820728-8b83bb6b773f?w=1000&q=80',
        ],
        'monitors' => [
            'https://images.unsplash.com/photo-1527443224154-c4a3942d3acf?w=1000&q=80',
            'https://images.unsplash.com/photo-1547119957-637f8679db1e?w=1000&q=80',
            'https://images.unsplash.com/photo-1551645120-d70bfe84c826?w=1000&q=80',
        ],
        'printers' => [
            'https://images.unsplash.com/photo-1612815154858-60aa4c59eabd?w=1000&q=80',
            'https://images.unsplash.com/photo-1562408590-e32931084e23?w=1000&q=80',
            'https://images.unsplash.com/photo-1585241645927-c7a8e5840c42?w=1000&q=80',
        ],
        // Fallback for anything uncategorised
        '_default' => [
            'https://images.unsplash.com/photo-1550009158-9ebf69173e03?w=1000&q=80',
            'https://images.unsplash.com/photo-1498049794561-7780e7231661?w=1000&q=80',
            'https://images.unsplash.com/photo-1526738549149-8e07eca6c147?w=1000&q=80',
        ],
    ];

    public function run(): void
    {
        DB::transaction(fn () => $this->seed());
    }

    protected function seed(): void
    {
        $imagesPerProduct = 3;

        // Preload category paths so we don't N+1 per product
        $categoryPathById = Category::pluck('path', 'id')->all();

        $products = Products::query()
            ->with(['images', 'category:id,path'])
            ->get();

        $stats = ['products' => 0, 'created' => 0, 'kept' => 0];

        foreach ($products as $product) {
            $categoryPath = $product->category?->path ?? '';
            $key          = $this->categoryKey($categoryPath);
            $urls         = $this->categoryImages[$key] ?? $this->categoryImages['_default'];

            $existing = $product->images;

            // If the product already has 3 images with our Unsplash URLs, skip
            $alreadySeeded = $existing->count() === $imagesPerProduct
                && $existing->every(fn ($img) => str_starts_with((string) $img->path, 'https://images.unsplash.com/'));

            if ($alreadySeeded) {
                $stats['kept']++;
                continue;
            }

            // Wipe and recreate
            ProductImages::where('product_id', $product->id)->delete();

            foreach ($urls as $index => $url) {
                ProductImages::create([
                    'product_id' => $product->id,
                    'path'       => $url,
                    'alt_text'   => $product->name . ($index > 0 ? ' (view ' . ($index + 1) . ')' : ''),
                    'is_primary' => $index === 0,
                    'sort_order' => $index * 10,
                ]);
                $stats['created']++;
            }

            $stats['products']++;
        }

        $this->command->info(sprintf(
            'Product images seeded: %d products (%d new image rows, %d already in place).',
            $stats['products'],
            $stats['created'],
            $stats['kept'],
        ));
    }

    /**
     * Map a category path to an image-set key.
     *   laptops/lenovo-laptops/thinkpad-t-series  → 'laptops'
     *   accessories/mice/logitech                 → 'mice'
     */
    protected function categoryKey(string $path): string
    {
        if ($path === '') {
            return '_default';
        }

        $segments = explode('/', $path);
        $root     = $segments[0];

        // Root categories directly
        if (in_array($root, ['laptops', 'desktops', 'components', 'servers-networking', 'printers'], true)) {
            return $root;
        }

        // Accessories → look at level 2 (mice, keyboards, headsets, ...)
        if ($root === 'accessories' && isset($segments[1])) {
            return $segments[1];
        }

        return '_default';
    }
}