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

class LogitechAudioSeeder extends Seeder
{
    protected float $marginRate = 0.10;

    protected array $categoryPaths = [
        'headsets' => 'accessories/headsets/logitech',
        'speakers' => 'accessories/speakers/logitech',
    ];

    public function run(): void
    {
        DB::transaction(fn () => $this->seed());
    }

    protected function seed(): void
    {
        $brand = Brands::where('slug', 'logitech')->firstOrFail();

        $categories = collect($this->categoryPaths)
            ->map(fn ($path) => Category::where('path', $path)->firstOrFail());

        $useCases = UseCase::pluck('id', 'slug');

        $created = 0;
        $updated = 0;
        $byCategory = ['headsets' => 0, 'speakers' => 0];

        foreach ($this->products() as $row) {
            $category = $categories[$row['category']] ?? null;
            if (! $category) {
                $this->command->warn("Unknown category key {$row['category']} — skipping {$row['sku']}");
                continue;
            }

            $priceKes = round($row['price'] * (1 + $this->marginRate), 2);
            $stock    = 15;

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
            $byCategory[$row['category']]++;

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

        $this->command->info(sprintf(
            'Logitech audio seeded: %d created, %d updated (%d headsets, %d speakers).',
            $created,
            $updated,
            $byCategory['headsets'],
            $byCategory['speakers'],
        ));
    }

    protected function shortDescription(array $row): string
    {
        return sprintf('%s | %s | %s', $row['model'], $row['type'], $row['connectivity']);
    }

    protected function longDescription(array $row): string
    {
        return sprintf(
            'The %s is a %s from Logitech. Connectivity: %s. Colour: %s. '
            . 'Genuine stock from Dawn Solutions, backed by manufacturer warranty.',
            $row['model'],
            strtolower($row['type']),
            $row['connectivity'],
            $row['colour'] ?? 'Black',
        );
    }

    protected function specifications(array $row): array
    {
        $specs = [
            ['label' => 'Brand',        'value' => $row['brand'] ?? 'Logitech'],
            ['label' => 'Model',        'value' => $row['model']],
            ['label' => 'Type',         'value' => $row['type']],
            ['label' => 'Connectivity', 'value' => $row['connectivity']],
        ];

        if (! empty($row['colour'])) {
            $specs[] = ['label' => 'Colour', 'value' => $row['colour']];
        }

        return $specs;
    }

    protected function products(): array
    {
        return [
            // ============ HEADSETS ============
            ['sku' => '981-000271', 'name' => 'Logitech H110 Stereo Headset', 'category' => 'headsets', 'model' => 'H110', 'type' => 'Stereo Headset', 'connectivity' => 'Wired 3.5mm', 'colour' => 'Black', 'price' => 1050, 'use_cases' => ['lifestyle', 'education']],
            ['sku' => '981-000593', 'name' => 'Logitech H111 Stereo Headset', 'category' => 'headsets', 'model' => 'H111', 'type' => 'Stereo Headset', 'connectivity' => 'Wired 3.5mm', 'colour' => 'Black', 'price' => 1080, 'use_cases' => ['lifestyle', 'education']],
            ['sku' => '981-000350', 'name' => 'Logitech H150 Corded Stereo Headset', 'category' => 'headsets', 'model' => 'H150', 'type' => 'Stereo Headset', 'connectivity' => 'Wired 3.5mm', 'colour' => 'Black', 'price' => 1700, 'use_cases' => ['lifestyle', 'education']],
            ['sku' => '981-000589', 'name' => 'Logitech H151 Corded Stereo Headset', 'category' => 'headsets', 'model' => 'H151', 'type' => 'Stereo Headset', 'connectivity' => 'Wired 3.5mm', 'colour' => 'Black', 'price' => 1700, 'use_cases' => ['lifestyle', 'education']],
            ['sku' => '981-000475', 'name' => 'Logitech H340 USB Headset', 'category' => 'headsets', 'model' => 'H340', 'type' => 'USB Headset', 'connectivity' => 'Wired USB', 'colour' => 'Black', 'price' => 2300, 'use_cases' => ['business', 'education']],
            ['sku' => '981-000100', 'name' => 'Logitech 960 Office USB Headset PC/Mac', 'category' => 'headsets', 'model' => '960 Office', 'type' => 'USB Headset', 'connectivity' => 'Wired USB', 'colour' => 'Black', 'price' => 2900, 'use_cases' => ['business', 'education']],
            ['sku' => '981-000406', 'name' => 'Logitech H390 Corded Headset', 'category' => 'headsets', 'model' => 'H390', 'type' => 'USB Headset', 'connectivity' => 'Wired USB', 'colour' => 'Black', 'price' => 3300, 'use_cases' => ['business', 'education']],
            ['sku' => '981-000480', 'name' => 'Logitech H540 USB Headset', 'category' => 'headsets', 'model' => 'H540', 'type' => 'USB Headset', 'connectivity' => 'Wired USB', 'colour' => 'Black', 'price' => 4200, 'use_cases' => ['business', 'education']],
            ['sku' => '981-000575', 'name' => 'Logitech H570e USB Headset UC Version', 'category' => 'headsets', 'model' => 'H570e UC', 'type' => 'Business Headset', 'connectivity' => 'Wired USB', 'colour' => 'Black', 'price' => 6500, 'use_cases' => ['business', 'enterprise']],
            ['sku' => '981-001431', 'name' => 'Logitech H570e USB-C Headset Teams Version', 'category' => 'headsets', 'model' => 'H570e USB-C', 'type' => 'Business Headset', 'connectivity' => 'Wired USB-C', 'colour' => 'Black', 'price' => 6500, 'use_cases' => ['business', 'enterprise']],
            ['sku' => '981-000519', 'name' => 'Logitech H650e Business Headset', 'category' => 'headsets', 'model' => 'H650e', 'type' => 'Business Headset', 'connectivity' => 'Wired USB', 'colour' => 'Black', 'price' => 8500, 'use_cases' => ['business', 'enterprise']],
            ['sku' => '981-001407', 'name' => 'Logitech Zone 300 Bluetooth Wireless Headset Graphite', 'category' => 'headsets', 'model' => 'Zone 300', 'type' => 'Bluetooth Headset', 'connectivity' => 'Bluetooth', 'colour' => 'Graphite', 'price' => 9500, 'use_cases' => ['business', 'lifestyle']],
            ['sku' => '981-001412', 'name' => 'Logitech Zone 300 Bluetooth Wireless Headset Rose', 'category' => 'headsets', 'model' => 'Zone 300', 'type' => 'Bluetooth Headset', 'connectivity' => 'Bluetooth', 'colour' => 'Rose', 'price' => 9500, 'use_cases' => ['business', 'lifestyle']],
            ['sku' => '981-001417', 'name' => 'Logitech Zone 300 Bluetooth Wireless Headset Off White', 'category' => 'headsets', 'model' => 'Zone 300', 'type' => 'Bluetooth Headset', 'connectivity' => 'Bluetooth', 'colour' => 'Off White', 'price' => 9500, 'use_cases' => ['business', 'lifestyle']],
            ['sku' => '981-001213', 'name' => 'Logitech Zone Vibe 100 Bluetooth Headset Graphite', 'category' => 'headsets', 'model' => 'Zone Vibe 100', 'type' => 'Bluetooth Headset', 'connectivity' => 'Bluetooth', 'colour' => 'Graphite', 'price' => 11500, 'use_cases' => ['business', 'lifestyle']],
            ['sku' => '981-001224', 'name' => 'Logitech Zone Vibe 100 Bluetooth Headset Rose', 'category' => 'headsets', 'model' => 'Zone Vibe 100', 'type' => 'Bluetooth Headset', 'connectivity' => 'Bluetooth', 'colour' => 'Rose', 'price' => 11500, 'use_cases' => ['business', 'lifestyle']],
            ['sku' => '981-001458', 'name' => 'Logitech Zone 305 Business Headset', 'category' => 'headsets', 'model' => 'Zone 305', 'type' => 'Business Bluetooth Headset', 'connectivity' => 'Bluetooth', 'colour' => 'Black', 'price' => 11500, 'use_cases' => ['business', 'enterprise']],
            ['sku' => '981-000875', 'name' => 'Logitech Zone Wired Headset with Noise Canceling Mic', 'category' => 'headsets', 'model' => 'Zone Wired', 'type' => 'Business USB Headset', 'connectivity' => 'Wired USB', 'colour' => 'Black', 'price' => 13500, 'use_cases' => ['business', 'enterprise']],
            ['sku' => '981-001157', 'name' => 'Logitech Zone Vibe Wireless Headphones Gray', 'category' => 'headsets', 'model' => 'Zone Vibe', 'type' => 'Bluetooth Headset', 'connectivity' => 'Bluetooth', 'colour' => 'Gray', 'price' => 14500, 'use_cases' => ['business', 'lifestyle']],
            ['sku' => '981-001199', 'name' => 'Logitech Zone Vibe Wireless Headphones for Business UC Version', 'category' => 'headsets', 'model' => 'Zone Vibe UC', 'type' => 'Business Bluetooth Headset', 'connectivity' => 'Bluetooth', 'colour' => 'Graphite', 'price' => 14500, 'use_cases' => ['business', 'enterprise']],
            ['sku' => '981-000854', 'name' => 'Logitech Zone Wireless MSFT Teams', 'category' => 'headsets', 'model' => 'Zone Wireless', 'type' => 'Business Bluetooth Headset', 'connectivity' => 'Bluetooth', 'colour' => 'Black', 'price' => 24000, 'use_cases' => ['business', 'enterprise']],

            // ============ SPEAKERS ============
            ['sku' => '980-000029', 'name' => 'Logitech S150 Computer Desk Speaker', 'category' => 'speakers', 'model' => 'S150', 'type' => 'Computer Speaker', 'connectivity' => 'Wired USB', 'colour' => 'Black', 'price' => 2200, 'use_cases' => ['lifestyle', 'education']],
            ['sku' => '980-000814', 'name' => 'Logitech Z150 Multimedia 2.0 Channel Speakers', 'category' => 'speakers', 'model' => 'Z150', 'type' => 'Multimedia Speaker', 'connectivity' => 'Wired 3.5mm', 'colour' => 'Black', 'price' => 3600, 'use_cases' => ['lifestyle']],
            ['sku' => '980-001295', 'name' => 'Logitech Z207 Bluetooth Stereo Speakers Black English', 'category' => 'speakers', 'model' => 'Z207', 'type' => 'Bluetooth Stereo Speaker', 'connectivity' => 'Bluetooth', 'colour' => 'Black', 'price' => 7800, 'use_cases' => ['lifestyle', 'business']],
            ['sku' => '980-001296', 'name' => 'Logitech Z207 Bluetooth Stereo Speakers Black UK English', 'category' => 'speakers', 'model' => 'Z207', 'type' => 'Bluetooth Stereo Speaker', 'connectivity' => 'Bluetooth', 'colour' => 'Black', 'price' => 7800, 'use_cases' => ['lifestyle', 'business']],
            ['sku' => '984-001360', 'name' => 'Ultimate Ears Boom 3 Wireless Bluetooth Speaker Night Black', 'category' => 'speakers', 'brand' => 'Ultimate Ears', 'model' => 'UE Boom 3', 'type' => 'Portable Bluetooth Speaker', 'connectivity' => 'Bluetooth', 'colour' => 'Night Black', 'price' => 14000, 'use_cases' => ['lifestyle']],
            ['sku' => '984-001363', 'name' => 'Ultimate Ears Boom 3 Wireless Bluetooth Speaker Purple', 'category' => 'speakers', 'brand' => 'Ultimate Ears', 'model' => 'UE Boom 3', 'type' => 'Portable Bluetooth Speaker', 'connectivity' => 'Bluetooth', 'colour' => 'Purple', 'price' => 14000, 'use_cases' => ['lifestyle']],
            ['sku' => '984-001364', 'name' => 'Ultimate Ears Boom 3 Wireless Bluetooth Speaker Sunset Red', 'category' => 'speakers', 'brand' => 'Ultimate Ears', 'model' => 'UE Boom 3', 'type' => 'Portable Bluetooth Speaker', 'connectivity' => 'Bluetooth', 'colour' => 'Sunset Red', 'price' => 14000, 'use_cases' => ['lifestyle']],
            ['sku' => '984-001402', 'name' => 'Ultimate Ears Megaboom 3 Wireless Bluetooth Speaker Night', 'category' => 'speakers', 'brand' => 'Ultimate Ears', 'model' => 'UE Megaboom 3', 'type' => 'Portable Bluetooth Speaker', 'connectivity' => 'Bluetooth', 'colour' => 'Night', 'price' => 18500, 'use_cases' => ['lifestyle']],
            ['sku' => '984-001404', 'name' => 'Ultimate Ears Megaboom 3 Wireless Bluetooth Speaker Lagoon', 'category' => 'speakers', 'brand' => 'Ultimate Ears', 'model' => 'UE Megaboom 3', 'type' => 'Portable Bluetooth Speaker', 'connectivity' => 'Bluetooth', 'colour' => 'Lagoon', 'price' => 18500, 'use_cases' => ['lifestyle']],
            ['sku' => '984-001406', 'name' => 'Ultimate Ears Megaboom 3 Wireless Bluetooth Speaker Sunset', 'category' => 'speakers', 'brand' => 'Ultimate Ears', 'model' => 'UE Megaboom 3', 'type' => 'Portable Bluetooth Speaker', 'connectivity' => 'Bluetooth', 'colour' => 'Sunset', 'price' => 18500, 'use_cases' => ['lifestyle']],
        ];
    }
}