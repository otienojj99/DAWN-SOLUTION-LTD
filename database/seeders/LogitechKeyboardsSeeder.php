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

class LogitechKeyboardsSeeder extends Seeder
{
    protected float $marginRate = 0.10;

    public function run(): void
    {
        DB::transaction(fn () => $this->seed());
    }

    protected function seed(): void
    {
        $brand    = Brands::where('slug', 'logitech')->firstOrFail();
        $category = Category::where('path', 'accessories/keyboards/logitech')->firstOrFail();
        $useCases = UseCase::pluck('id', 'slug');

        $created = 0;
        $updated = 0;

        foreach ($this->products() as $row) {
            $priceKes = round($row['price'] * (1 + $this->marginRate), 2);
            $stock    = 20;

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
                    'initial_stock_quantity' => $stock,
                    'track_inventory'   => true,
                    'allow_backorder'   => false,
                    'is_active'         => true,
                    'is_featured'       => false,
                    'published_at'      => now(),
                ]
            );

            $product->wasRecentlyCreated ? $created++ : $updated++;

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

        $this->command->info("Logitech keyboards seeded: {$created} created, {$updated} updated.");
    }

    protected function shortDescription(array $row): string
    {
        return sprintf('%s | %s | %s', $row['model'], $row['type'], $row['connectivity']);
    }

    protected function longDescription(array $row): string
    {
        return sprintf(
            'The %s is a %s from Logitech. Connectivity: %s. Colour: %s. '
            . 'Genuine Logitech stock backed by manufacturer warranty.',
            $row['model'],
            strtolower($row['type']),
            $row['connectivity'],
            $row['colour'],
        );
    }

    protected function specifications(array $row): array
    {
        return [
            ['label' => 'Brand',        'value' => 'Logitech'],
            ['label' => 'Model',        'value' => $row['model']],
            ['label' => 'Type',         'value' => $row['type']],
            ['label' => 'Connectivity', 'value' => $row['connectivity']],
            ['label' => 'Colour',       'value' => $row['colour']],
        ];
    }

    protected function products(): array
    {
        return [
            ['sku' => '920-002508', 'name' => 'Logitech K120 Corded Keyboard', 'model' => 'K120', 'type' => 'Keyboard', 'connectivity' => 'Corded USB', 'colour' => 'Black', 'price' => 1100, 'use_cases' => ['business', 'education']],
            ['sku' => '920-002562', 'name' => 'Logitech MK120 Combo Wired Keyboard & Mouse', 'model' => 'MK120', 'type' => 'Combo', 'connectivity' => 'Corded USB', 'colour' => 'Black', 'price' => 1650, 'use_cases' => ['business', 'education']],
            ['sku' => '920-003161', 'name' => 'Logitech MK220 Wireless Keyboard & Mouse Combo', 'model' => 'MK220', 'type' => 'Combo', 'connectivity' => '2.4GHz Wireless', 'colour' => 'Black', 'price' => 2300, 'use_cases' => ['business', 'lifestyle']],
            ['sku' => '920-013519', 'name' => 'Logitech MK250 Bluetooth Wireless Combo', 'model' => 'MK250', 'type' => 'Combo', 'connectivity' => 'Bluetooth', 'colour' => 'Black', 'price' => 2750, 'use_cases' => ['business', 'lifestyle']],
            ['sku' => '920-010526', 'name' => 'Logitech MX Keys Mini for Mac Bluetooth Keyboard Pale Grey', 'model' => 'MX Keys Mini for Mac', 'type' => 'Premium Keyboard', 'connectivity' => 'Bluetooth', 'colour' => 'Pale Grey', 'price' => 13500, 'use_cases' => ['business', 'enterprise']],
            ['sku' => '920-010780', 'name' => 'Logitech MX Mechanical Mini Wireless Keyboard Graphite', 'model' => 'MX Mechanical Mini', 'type' => 'Mechanical Keyboard', 'connectivity' => 'Bluetooth + USB', 'colour' => 'Graphite', 'price' => 20500, 'use_cases' => ['business', 'enterprise']],
            ['sku' => '920-010251', 'name' => 'Logitech MX Keys Business Wireless Keyboard', 'model' => 'MX Keys Business', 'type' => 'Premium Keyboard', 'connectivity' => 'Bluetooth + USB', 'colour' => 'Graphite', 'price' => 16000, 'use_cases' => ['business', 'enterprise']],
            ['sku' => '920-011587', 'name' => 'Logitech MX Keys S Advanced Wireless Illuminated Keyboard', 'model' => 'MX Keys S Advanced', 'type' => 'Premium Keyboard', 'connectivity' => 'Bluetooth + USB', 'colour' => 'Graphite', 'price' => 16000, 'use_cases' => ['business', 'enterprise']],
            ['sku' => '920-004509', 'name' => 'Logitech MK270 Combo Wireless Keyboard', 'model' => 'MK270', 'type' => 'Combo', 'connectivity' => '2.4GHz Wireless', 'colour' => 'Black', 'price' => 2600, 'use_cases' => ['business', 'lifestyle']],
            ['sku' => '920-008504', 'name' => 'Logitech Craft Bluetooth Keyboard Graphite', 'model' => 'Craft', 'type' => 'Premium Keyboard', 'connectivity' => 'Bluetooth', 'colour' => 'Graphite', 'price' => 23500, 'use_cases' => ['business', 'enterprise']],
            ['sku' => '920-011852', 'name' => 'Logitech K380S Multi-Device Bluetooth Keyboard Tonal White', 'model' => 'K380S', 'type' => 'Bluetooth Keyboard', 'connectivity' => 'Bluetooth', 'colour' => 'Tonal White', 'price' => 5200, 'use_cases' => ['business', 'lifestyle']],
            ['sku' => '920-011853', 'name' => 'Logitech K380S Multi-Device Bluetooth Keyboard Tonal Rose', 'model' => 'K380S', 'type' => 'Bluetooth Keyboard', 'connectivity' => 'Bluetooth', 'colour' => 'Tonal Rose', 'price' => 5200, 'use_cases' => ['business', 'lifestyle']],
            ['sku' => '920-011851', 'name' => 'Logitech K380S Multi-Device Bluetooth Keyboard Tonal Graphite', 'model' => 'K380S', 'type' => 'Bluetooth Keyboard', 'connectivity' => 'Bluetooth', 'colour' => 'Tonal Graphite', 'price' => 5500, 'use_cases' => ['business', 'lifestyle']],
            ['sku' => '920-007145', 'name' => 'Logitech K400 Plus Wireless Touch Keyboard Dark', 'model' => 'K400 Plus', 'type' => 'Wireless Touch Keyboard', 'connectivity' => '2.4GHz Wireless', 'colour' => 'Dark', 'price' => 3900, 'use_cases' => ['lifestyle']],
            ['sku' => '920-010735', 'name' => 'Logitech POP Keys Bluetooth Mechanical Keyboard Blast Yellow', 'model' => 'POP Keys', 'type' => 'Mechanical Keyboard', 'connectivity' => 'Bluetooth', 'colour' => 'Blast Yellow', 'price' => 14000, 'use_cases' => ['lifestyle']],
            ['sku' => '920-007931', 'name' => 'Logitech MK235 Combo Wireless Keyboard & Mouse', 'model' => 'MK235', 'type' => 'Combo', 'connectivity' => '2.4GHz Wireless', 'colour' => 'Black', 'price' => 2700, 'use_cases' => ['business', 'lifestyle']],
            ['sku' => '920-003989', 'name' => 'Logitech MK330 Wireless Keyboard & Mouse', 'model' => 'MK330', 'type' => 'Combo', 'connectivity' => '2.4GHz Wireless', 'colour' => 'Black', 'price' => 3700, 'use_cases' => ['business', 'lifestyle']],
            ['sku' => '920-006489', 'name' => 'Logitech MK345 Wireless Keyboard & Mouse Combo', 'model' => 'MK345', 'type' => 'Combo', 'connectivity' => '2.4GHz Wireless', 'colour' => 'Black', 'price' => 4500, 'use_cases' => ['business', 'lifestyle']],
            ['sku' => '920-008226', 'name' => 'Logitech MK850 Performance Wireless Keyboard and Mouse', 'model' => 'MK850 Performance', 'type' => 'Combo', 'connectivity' => '2.4GHz + Bluetooth', 'colour' => 'Black', 'price' => 14000, 'use_cases' => ['business', 'enterprise']],
            ['sku' => '920-011614', 'name' => 'Logitech MX Keys S Wireless Combo Keyboard Graphite', 'model' => 'MX Keys S Combo', 'type' => 'Premium Combo', 'connectivity' => 'Bluetooth + USB', 'colour' => 'Graphite', 'price' => 26000, 'use_cases' => ['business', 'enterprise']],
            ['sku' => '920-012240', 'name' => 'Logitech Pebble 2 Combo Wireless Keyboard and Mouse Tonal White', 'model' => 'Pebble 2 Combo', 'type' => 'Combo', 'connectivity' => 'Bluetooth + USB', 'colour' => 'Tonal White', 'price' => 6600, 'use_cases' => ['business', 'lifestyle']],
            ['sku' => '920-009204', 'name' => 'Logitech Slim MK470 Wireless Combo Keyboard and Mouse Graphite', 'model' => 'Slim MK470', 'type' => 'Slim Combo', 'connectivity' => '2.4GHz Wireless', 'colour' => 'Graphite', 'price' => 7000, 'use_cases' => ['business', 'lifestyle']],
            ['sku' => '920-009800', 'name' => 'Logitech Silent MK295 Wireless Combo Keyboard and Mouse', 'model' => 'Silent MK295', 'type' => 'Silent Combo', 'connectivity' => '2.4GHz Wireless', 'colour' => 'Graphite', 'price' => 3300, 'use_cases' => ['business', 'lifestyle']],
            ['sku' => '920-008685', 'name' => 'Logitech Advanced MK540 Wireless Combo Keyboard and Mouse', 'model' => 'Advanced MK540', 'type' => 'Combo', 'connectivity' => '2.4GHz Wireless', 'colour' => 'Black', 'price' => 5500, 'use_cases' => ['business', 'lifestyle']],
            ['sku' => '920-011004', 'name' => 'Logitech Advanced MK650 Wireless Combo Keyboard and Mouse for Business', 'model' => 'Advanced MK650', 'type' => 'Business Combo', 'connectivity' => '2.4GHz + Bluetooth', 'colour' => 'Graphite', 'price' => 12000, 'use_cases' => ['business', 'enterprise']],
        ];
    }
}