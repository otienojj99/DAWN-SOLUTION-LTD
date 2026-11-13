<?php

namespace Database\Seeders;

use App\Models\Brands;
use App\Models\Category;
use App\Models\Products;
use App\Models\ProductImages;
use App\Models\UseCase;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class LogitechMiceSeeder extends Seeder
{
    /**
     * Margin: 10% for Available/Expected (matching Dell & Lenovo convention).
     * Override with a different rate if you prefer — edit $marginRate below.
     */
    protected float $marginRate = 0.10;

    public function run(): void
    {
        DB::transaction(fn () => $this->seed());
    }

    protected function seed(): void
    {
        $brand    = Brands::where('slug', 'logitech')->firstOrFail();
        $category = Category::where('path', 'accessories/mice/logitech')->firstOrFail();
        $useCases = UseCase::pluck('id', 'slug');

        $created = 0;
        $updated = 0;
        $outOfStock = 0;

        foreach ($this->products() as $row) {
            $priceKes = round($row['price'] * (1 + $this->marginRate), 2);
            $inStock  = $row['status'] !== 'out_of_stock';
            $stock    = $inStock ? $this->stockForStatus($row['status']) : 0;

            $slug = Str::slug($row['name'] . ' ' . $row['sku']);

            $product = Products::updateOrCreate(
                ['sku' => $row['sku']],
                [
                    'name'              => $row['name'],
                    'slug'              => $slug,
                    'type'              => 'simple',
                    'category_id'       => $category->id,
                    'brand_id'          => $brand->id,
                    'short_description' => $this->shortDescription($row),
                    'long_description'  => $this->longDescription($row),
                    'specifications'    => $this->specifications($row),
                    'price'             => $priceKes,
                    'compare_at_price'  => null,
                    'price_usd'         => round($priceKes / config('shop.usd_rate', 130), 2),
                    'base_currency'     => 'KES',
                    'stock_quantity'    => $stock,
                    'initial_stock_quantity' => max(1, $stock),
                    'track_inventory'   => true,
                    'allow_backorder'   => $row['status'] === 'expected',
                    'is_active'         => true,
                    'is_featured'       => false,
                    'published_at'      => now(),
                ]
            );

            $product->wasRecentlyCreated ? $created++ : $updated++;
            if (! $inStock) $outOfStock++;

            if ($product->images()->count() === 0) {
                ProductImages::create([
                    'product_id' => $product->id,
                    'path'       => 'demo/' . $slug . '.jpg',
                    'alt_text'   => $product->name,
                    'is_primary' => true,
                    'sort_order' => 0,
                ]);
            }

            $ucIds = collect($row['use_cases'])
                ->map(fn ($s) => $useCases[$s] ?? null)
                ->filter()
                ->values()
                ->all();
            if (! empty($ucIds)) {
                $product->useCases()->sync($ucIds);
            }
        }

        $this->command->info("Logitech mice seeded: {$created} created, {$updated} updated, {$outOfStock} out of stock.");
    }

    protected function stockForStatus(string $status): int
    {
        return match ($status) {
            'available' => 25,
            'expected'  => 5,
            default     => 15,
        };
    }

    protected function shortDescription(array $row): string
    {
        return sprintf(
            '%s %s | %s | %s',
            $row['model'], $row['type'], $row['connectivity'], $row['color'],
        );
    }

    protected function longDescription(array $row): string
    {
        return sprintf(
            'The %s is a %s mouse from Logitech, designed for reliable everyday use. '
            . 'Connectivity: %s. Colour: %s. Backed by Logitech warranty and genuine stock from Dawn Solutions.',
            $row['model'], strtolower($row['type']), $row['connectivity'], $row['color'],
        );
    }

    protected function specifications(array $row): array
    {
        return [
            ['label' => 'Brand',        'value' => 'Logitech'],
            ['label' => 'Model',        'value' => $row['model']],
            ['label' => 'Type',         'value' => $row['type']],
            ['label' => 'Connectivity', 'value' => $row['connectivity']],
            ['label' => 'Colour',       'value' => $row['color']],
        ];
    }

    protected function products(): array
    {
        return [
            ['sku' => '910-001793', 'name' => 'Logitech M90 Corded Mouse Black',       'model' => 'M90',      'type' => 'Mouse',               'connectivity' => 'Corded USB',      'color' => 'Black',           'status' => 'available', 'price' => 500,   'use_cases' => ['lifestyle', 'business']],
            ['sku' => '910-006652', 'name' => 'Logitech M100 Mouse Black',             'model' => 'M100',     'type' => 'Mouse',               'connectivity' => 'Corded USB',      'color' => 'Black',           'status' => 'available', 'price' => 800,   'use_cases' => ['lifestyle', 'business']],
            ['sku' => '910-004642', 'name' => 'Logitech M170 Wireless Mouse Grey',     'model' => 'M170',     'type' => 'Wireless Mouse',      'connectivity' => '2.4GHz USB',      'color' => 'Grey',            'status' => 'available', 'price' => 1000,  'use_cases' => ['lifestyle', 'business']],
            ['sku' => '910-006866', 'name' => 'Logitech M171 Wireless Mouse Blue Grey','model' => 'M171',     'type' => 'Wireless Mouse',      'connectivity' => '2.4GHz USB',      'color' => 'Blue Grey',       'status' => 'available', 'price' => 1050,  'use_cases' => ['lifestyle', 'business']],
            ['sku' => '910-004424', 'name' => 'Logitech M171 Wireless Mouse Black',    'model' => 'M171',     'type' => 'Wireless Mouse',      'connectivity' => '2.4GHz USB',      'color' => 'Black',           'status' => 'available', 'price' => 1050,  'use_cases' => ['lifestyle', 'business']],
            ['sku' => '910-004640', 'name' => 'Logitech M171 Wireless Mouse Blue',     'model' => 'M171',     'type' => 'Wireless Mouse',      'connectivity' => '2.4GHz USB',      'color' => 'Blue',            'status' => 'available', 'price' => 1050,  'use_cases' => ['lifestyle', 'business']],
            ['sku' => '910-004641', 'name' => 'Logitech M171 Wireless Mouse Red',      'model' => 'M171',     'type' => 'Wireless Mouse',      'connectivity' => '2.4GHz USB',      'color' => 'Red',             'status' => 'available', 'price' => 1100,  'use_cases' => ['lifestyle', 'business']],
            ['sku' => '910-002235', 'name' => 'Logitech M185 Wireless Mouse Swift Grey','model' => 'M185',    'type' => 'Wireless Mouse',      'connectivity' => '2.4GHz USB',      'color' => 'Swift Grey',      'status' => 'available', 'price' => 1100,  'use_cases' => ['lifestyle', 'business']],
            ['sku' => '910-002236', 'name' => 'Logitech M185 Wireless Mouse Blue',     'model' => 'M185',     'type' => 'Wireless Mouse',      'connectivity' => '2.4GHz USB',      'color' => 'Blue',            'status' => 'available', 'price' => 1100,  'use_cases' => ['lifestyle', 'business']],
            ['sku' => '910-002237', 'name' => 'Logitech M185 Wireless Mouse Red',      'model' => 'M185',     'type' => 'Wireless Mouse',      'connectivity' => '2.4GHz USB',      'color' => 'Red',             'status' => 'available', 'price' => 1100,  'use_cases' => ['lifestyle', 'business']],
            ['sku' => '910-002731', 'name' => 'Logitech M187 Wireless Mini Mouse Black','model' => 'M187',    'type' => 'Wireless Mini Mouse', 'connectivity' => '2.4GHz USB',      'color' => 'Black',           'status' => 'available', 'price' => 1500,  'use_cases' => ['lifestyle', 'business']],
            ['sku' => '910-002732', 'name' => 'Logitech M187 Wireless Mini Mouse Red', 'model' => 'M187',     'type' => 'Wireless Mini Mouse', 'connectivity' => '2.4GHz USB',      'color' => 'Red',             'status' => 'expected',  'price' => 1500,  'use_cases' => ['lifestyle', 'business']],
            ['sku' => '910-002733', 'name' => 'Logitech M187 Wireless Mini Mouse Blue','model' => 'M187',     'type' => 'Wireless Mini Mouse', 'connectivity' => '2.4GHz USB',      'color' => 'Blue',            'status' => 'available', 'price' => 1500,  'use_cases' => ['lifestyle', 'business']],
            ['sku' => '910-005905', 'name' => 'Logitech M190 Wireless Mouse Charcoal','model' => 'M190',     'type' => 'Wireless Mouse',      'connectivity' => '2.4GHz USB',      'color' => 'Charcoal',        'status' => 'available', 'price' => 1400,  'use_cases' => ['lifestyle', 'business']],
            ['sku' => '910-005906', 'name' => 'Logitech M190 Wireless Mouse Mid Grey','model' => 'M190',     'type' => 'Wireless Mouse',      'connectivity' => '2.4GHz USB',      'color' => 'Mid Grey',        'status' => 'available', 'price' => 1400,  'use_cases' => ['lifestyle', 'business']],
            ['sku' => '910-005908', 'name' => 'Logitech M190 Wireless Mouse Red',     'model' => 'M190',     'type' => 'Wireless Mouse',      'connectivity' => '2.4GHz USB',      'color' => 'Red',             'status' => 'available', 'price' => 1400,  'use_cases' => ['lifestyle', 'business']],
            ['sku' => '910-007459', 'name' => 'Logitech M196 Bluetooth Mouse Graphite','model' => 'M196',    'type' => 'Bluetooth Mouse',     'connectivity' => 'Bluetooth',       'color' => 'Graphite',        'status' => 'available', 'price' => 1200,  'use_cases' => ['lifestyle', 'business']],
            ['sku' => '910-007460', 'name' => 'Logitech M196 Bluetooth Mouse White',  'model' => 'M196',     'type' => 'Bluetooth Mouse',     'connectivity' => 'Bluetooth',       'color' => 'White',           'status' => 'available', 'price' => 1200,  'use_cases' => ['lifestyle', 'business']],
            ['sku' => '910-007461', 'name' => 'Logitech M196 Bluetooth Mouse Rose',   'model' => 'M196',     'type' => 'Bluetooth Mouse',     'connectivity' => 'Bluetooth',       'color' => 'Rose',            'status' => 'available', 'price' => 1200,  'use_cases' => ['lifestyle', 'business']],
            ['sku' => '910-004878', 'name' => 'Logitech M220 Silent Mouse Charcoal Black','model' => 'M220', 'type' => 'Silent Wireless Mouse','connectivity' => '2.4GHz USB',     'color' => 'Charcoal Black',  'status' => 'available', 'price' => 1700,  'use_cases' => ['lifestyle', 'business']],
            ['sku' => '910-006510', 'name' => 'Logitech M221 Wireless Silent Mouse Charcoal Black','model' => 'M221','type' => 'Silent Wireless Mouse','connectivity' => '2.4GHz USB', 'color' => 'Charcoal Black',  'status' => 'available', 'price' => 2200,  'use_cases' => ['lifestyle', 'business']],
            ['sku' => '910-007015', 'name' => 'Logitech M350S Pebble 2 Bluetooth Mouse Tonal Graphite','model' => 'M350S Pebble 2','type' => 'Bluetooth Mouse','connectivity' => 'Bluetooth', 'color' => 'Tonal Graphite','status' => 'available', 'price' => 2800,  'use_cases' => ['lifestyle', 'business']],
            ['sku' => '910-007013', 'name' => 'Logitech M350S Pebble 2 Bluetooth Mouse Tonal White','model' => 'M350S Pebble 2','type' => 'Bluetooth Mouse','connectivity' => 'Bluetooth', 'color' => 'Tonal White',   'status' => 'available', 'price' => 2800,  'use_cases' => ['lifestyle', 'business']],
            ['sku' => '910-007014', 'name' => 'Logitech M350S Pebble 2 Bluetooth Mouse Tonal Rose','model' => 'M350S Pebble 2','type' => 'Bluetooth Mouse','connectivity' => 'Bluetooth', 'color' => 'Tonal Rose',    'status' => 'available', 'price' => 2800,  'use_cases' => ['lifestyle', 'business']],
            ['sku' => '910-006253', 'name' => 'Logitech M650 Signature Bluetooth Mouse Graphite','model' => 'M650 Signature','type' => 'Bluetooth Mouse','connectivity' => 'Bluetooth', 'color' => 'Graphite',      'status' => 'available', 'price' => 4000,  'use_cases' => ['lifestyle', 'business']],
            ['sku' => '910-006239', 'name' => 'Logitech Signature M650 L Left Wireless Mouse Graphite','model' => 'M650 L Left','type' => 'Wireless Mouse','connectivity' => '2.4GHz USB',    'color' => 'Graphite',      'status' => 'available', 'price' => 4700,  'use_cases' => ['lifestyle', 'business']],
            ['sku' => '910-001949', 'name' => 'Logitech M705 Marathon Wireless Mouse Black','model' => 'M705 Marathon','type' => 'Wireless Mouse', 'connectivity' => '2.4GHz USB',    'color' => 'Black',         'status' => 'available', 'price' => 6400,  'use_cases' => ['lifestyle', 'business']],
            ['sku' => '910-006034', 'name' => 'Logitech M705 Marathon Wireless Mouse Charcoal','model' => 'M705 Marathon','type' => 'Wireless Mouse','connectivity' => '2.4GHz USB',  'color' => 'Charcoal',      'status' => 'available', 'price' => 5700,  'use_cases' => ['lifestyle', 'business']],
            ['sku' => '910-006211', 'name' => 'Logitech MX Anywhere 2S Bluetooth Mouse Graphite','model' => 'MX Anywhere 2S','type' => 'Bluetooth Mouse','connectivity' => 'Bluetooth', 'color' => 'Graphite',      'status' => 'available', 'price' => 11500, 'use_cases' => ['business', 'enterprise']],
            ['sku' => '910-006958', 'name' => 'Logitech MX Anywhere 3S For Business Graphite','model' => 'MX Anywhere 3S','type' => 'Business Bluetooth Mouse','connectivity' => 'Bluetooth','color' => 'Graphite','status' => 'available', 'price' => 10500, 'use_cases' => ['business', 'enterprise']],
            ['sku' => '910-006929', 'name' => 'Logitech MX Anywhere 3S Silent Bluetooth Mouse Graphite','model' => 'MX Anywhere 3S','type' => 'Silent Bluetooth Mouse','connectivity' => 'Bluetooth','color' => 'Graphite','status' => 'available', 'price' => 9500,  'use_cases' => ['lifestyle', 'business']],
            ['sku' => '910-006559', 'name' => 'Logitech Bolt MX Master 3S Bluetooth Mouse Graphite','model' => 'MX Master 3S','type' => 'Bluetooth Mouse','connectivity' => 'Bluetooth',  'color' => 'Graphite',      'status' => 'available', 'price' => 12500, 'use_cases' => ['business', 'enterprise']],
            ['sku' => '910-005179', 'name' => 'Logitech MX Ergo Wireless Trackball Mouse Graphite','model' => 'MX Ergo','type' => 'Wireless Trackball Mouse','connectivity' => '2.4GHz + Bluetooth','color' => 'Graphite','status' => 'available', 'price' => 14500, 'use_cases' => ['business', 'enterprise']],
            ['sku' => '910-006547', 'name' => 'Logitech Wireless Pop Mouse With Customizable Emoji','model' => 'Pop Mouse','type' => 'Wireless Mouse','connectivity' => 'Bluetooth',      'color' => 'Multi',         'status' => 'available', 'price' => 3700,  'use_cases' => ['lifestyle']],
            ['sku' => '910-006473', 'name' => 'Logitech Lift Vertical Ergonomic Mouse Graphite','model' => 'Lift Vertical','type' => 'Ergonomic Wireless Mouse','connectivity' => 'Bluetooth + USB','color' => 'Graphite','status' => 'available', 'price' => 7500,  'use_cases' => ['business', 'enterprise']],
            ['sku' => '910-007562', 'name' => 'Logitech MX Master 4 Ergonomic Wireless Mouse Graphite','model' => 'MX Master 4','type' => 'Ergonomic Wireless Mouse','connectivity' => 'Bluetooth + USB','color' => 'Graphite','status' => 'available', 'price' => 14200, 'use_cases' => ['business', 'enterprise']],
        ];
    }
}