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

class LogitechGamingSeeder extends Seeder
{
    protected float $marginRate = 0.10;

    /**
     * Each product routes to its natural category:
     *   mice      → accessories/mice/logitech
     *   keyboards → accessories/keyboards/logitech
     *   headsets  → accessories/headsets/logitech
     *   gamepads  → accessories/gamepads/logitech
     */
    protected array $categoryPaths = [
        'mice'      => 'accessories/mice/logitech',
        'keyboards' => 'accessories/keyboards/logitech',
        'headsets'  => 'accessories/headsets/logitech',
        'gamepads'  => 'accessories/gamepads/logitech',
    ];

    public function run(): void
    {
        DB::transaction(fn () => $this->seed());
    }

    protected function seed(): void
    {
        $brand = Brands::where('slug', 'logitech')->firstOrFail();

        // Load all 4 categories once
        $categories = collect($this->categoryPaths)
            ->map(fn ($path) => Category::where('path', $path)->firstOrFail());

        $useCases = UseCase::pluck('id', 'slug');
        $gamingUseCase = $useCases['gaming'] ?? null;

        $created = 0;
        $updated = 0;
        $byCategory = ['mice' => 0, 'keyboards' => 0, 'headsets' => 0, 'gamepads' => 0];

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

            // Gaming + lifestyle tag on every product
            $tagIds = array_filter([$gamingUseCase, $useCases['lifestyle'] ?? null]);
            if (! empty($tagIds)) {
                $product->useCases()->sync($tagIds);
            }
        }

        $this->command->info(sprintf(
            'Logitech gaming seeded: %d created, %d updated (%d mice, %d keyboards, %d headsets, %d gamepads).',
            $created,
            $updated,
            $byCategory['mice'],
            $byCategory['keyboards'],
            $byCategory['headsets'],
            $byCategory['gamepads'],
        ));
    }

    protected function shortDescription(array $row): string
    {
        return sprintf('%s | %s | %s', $row['model'], $row['type'], $row['connectivity']);
    }

    protected function longDescription(array $row): string
    {
        return sprintf(
            'The %s is a %s from Logitech\'s G-series gaming lineup. '
            . 'Connectivity: %s. Colour: %s. Built for competitive play, backed by Logitech warranty.',
            $row['model'],
            strtolower($row['type']),
            $row['connectivity'],
            $row['colour'],
        );
    }

    protected function specifications(array $row): array
    {
        return [
            ['label' => 'Brand',        'value' => 'Logitech G'],
            ['label' => 'Model',        'value' => $row['model']],
            ['label' => 'Type',         'value' => $row['type']],
            ['label' => 'Connectivity', 'value' => $row['connectivity']],
            ['label' => 'Colour',       'value' => $row['colour']],
        ];
    }

    protected function products(): array
    {
        return [
            // ============ HEADSETS (981-*) ============
            ['sku' => '981-001050', 'name' => 'Logitech G435 Lightspeed Wireless Gaming Headset Black', 'category' => 'headsets', 'model' => 'G435', 'type' => 'Gaming Headset', 'connectivity' => 'Lightspeed Wireless', 'colour' => 'Black', 'price' => 8700],
            ['sku' => '981-001062', 'name' => 'Logitech G435 Lightspeed Wireless Gaming Headset Blue', 'category' => 'headsets', 'model' => 'G435', 'type' => 'Gaming Headset', 'connectivity' => 'Lightspeed Wireless', 'colour' => 'Blue', 'price' => 8700],
            ['sku' => '981-001074', 'name' => 'Logitech G435 Lightspeed Wireless Gaming Headset White', 'category' => 'headsets', 'model' => 'G435', 'type' => 'Gaming Headset', 'connectivity' => 'Lightspeed Wireless', 'colour' => 'White', 'price' => 8700],
            ['sku' => '981-000770', 'name' => 'Logitech G432 Wired Gaming Headset Black/Blue', 'category' => 'headsets', 'model' => 'G432', 'type' => 'Gaming Headset', 'connectivity' => 'Wired USB', 'colour' => 'Black/Blue', 'price' => 7900],
            ['sku' => '981-000750', 'name' => 'Logitech G635 LightSync Wired Gaming Headset Black', 'category' => 'headsets', 'model' => 'G635', 'type' => 'Gaming Headset', 'connectivity' => 'Wired USB', 'colour' => 'Black', 'price' => 18500],
            ['sku' => '981-000668', 'name' => 'Logitech G433 Wired Gaming Headset Triple Black', 'category' => 'headsets', 'model' => 'G433', 'type' => 'Gaming Headset', 'connectivity' => 'Wired USB', 'colour' => 'Triple Black', 'price' => 15900],
            ['sku' => '981-000978', 'name' => 'Logitech G335 Wired Gaming Headset Black', 'category' => 'headsets', 'model' => 'G335', 'type' => 'Gaming Headset', 'connectivity' => 'Wired 3.5mm', 'colour' => 'Black', 'price' => 7000],
            ['sku' => '981-001018', 'name' => 'Logitech G335 Wired Gaming Headset White', 'category' => 'headsets', 'model' => 'G335', 'type' => 'Gaming Headset', 'connectivity' => 'Wired 3.5mm', 'colour' => 'White', 'price' => 7000],
            ['sku' => '981-001024', 'name' => 'Logitech G335 Wired Gaming Headset Mint', 'category' => 'headsets', 'model' => 'G335', 'type' => 'Gaming Headset', 'connectivity' => 'Wired 3.5mm', 'colour' => 'Mint', 'price' => 7000],
            ['sku' => '981-001563', 'name' => 'Logitech G321 Lightspeed Wireless Bluetooth Gaming Headset', 'category' => 'headsets', 'model' => 'G321', 'type' => 'Gaming Headset', 'connectivity' => 'Lightspeed + Bluetooth', 'colour' => 'Black', 'price' => 8000],
            ['sku' => '981-000890', 'name' => 'Logitech G733 LightSpeed Wireless RGB Gaming Headset Lilac', 'category' => 'headsets', 'model' => 'G733', 'type' => 'Gaming Headset', 'connectivity' => 'Lightspeed Wireless', 'colour' => 'Lilac', 'price' => 16900],
            ['sku' => '981-000864', 'name' => 'Logitech G733 LightSpeed Wireless RGB Gaming Headset Black', 'category' => 'headsets', 'model' => 'G733', 'type' => 'Gaming Headset', 'connectivity' => 'Lightspeed Wireless', 'colour' => 'Black', 'price' => 16900],
            ['sku' => '981-000943', 'name' => 'Logitech G733 LightSpeed Wireless RGB Gaming Headset Blue', 'category' => 'headsets', 'model' => 'G733', 'type' => 'Gaming Headset', 'connectivity' => 'Lightspeed Wireless', 'colour' => 'Blue', 'price' => 16900],
            ['sku' => '981-000883', 'name' => 'Logitech G733 LightSpeed Wireless RGB Gaming Headset White', 'category' => 'headsets', 'model' => 'G733', 'type' => 'Gaming Headset', 'connectivity' => 'Lightspeed Wireless', 'colour' => 'White', 'price' => 16900],
            ['sku' => '981-000924', 'name' => 'Logitech G+A136+B133 Gaming Audio Bundle', 'category' => 'headsets', 'model' => 'G+A136+B133', 'type' => 'Gaming Bundle', 'connectivity' => 'Wired', 'colour' => 'Black', 'price' => 5200],

            // ============ KEYBOARDS (920-*) ============
            ['sku' => '920-008310', 'name' => 'Logitech G413 Corded Mechanical Gaming Keyboard Carbon', 'category' => 'keyboards', 'model' => 'G413', 'type' => 'Mechanical Keyboard', 'connectivity' => 'Wired USB', 'colour' => 'Carbon', 'price' => 9300],
            ['sku' => '920-010437', 'name' => 'Logitech G413 SE Corded Mechanical Gaming Keyboard', 'category' => 'keyboards', 'model' => 'G413 SE', 'type' => 'Mechanical Keyboard', 'connectivity' => 'Wired USB', 'colour' => 'Black', 'price' => 8800],
            ['sku' => '920-010446', 'name' => 'Logitech G413 TKL SE Corded Mechanical Gaming Keyboard', 'category' => 'keyboards', 'model' => 'G413 TKL SE', 'type' => 'TKL Mechanical Keyboard', 'connectivity' => 'Wired USB', 'colour' => 'Black', 'price' => 7800],
            ['sku' => '920-008946', 'name' => 'Logitech G512 Corded RGB Mechanical Gaming Keyboard', 'category' => 'keyboards', 'model' => 'G512', 'type' => 'RGB Mechanical Keyboard', 'connectivity' => 'Wired USB', 'colour' => 'Carbon', 'price' => 11900],
            ['sku' => '920-008093', 'name' => 'Logitech G213 Prodigy Gaming Keyboard', 'category' => 'keyboards', 'model' => 'G213 Prodigy', 'type' => 'Membrane Gaming Keyboard', 'connectivity' => 'Wired USB', 'colour' => 'Black', 'price' => 6000],
            ['sku' => '920-012872', 'name' => 'Logitech G515 Gaming Keyboard Black', 'category' => 'keyboards', 'model' => 'G515', 'type' => 'Gaming Keyboard', 'connectivity' => 'Wired USB', 'colour' => 'Black', 'price' => 11600],
            ['sku' => '920-009537', 'name' => 'Logitech G915 TKL Tenkeyless Lightspeed Wireless Gaming Keyboard Clicky', 'category' => 'keyboards', 'model' => 'G915 TKL', 'type' => 'Wireless Mechanical Keyboard', 'connectivity' => 'Lightspeed Wireless', 'colour' => 'Carbon', 'price' => 31400],
            ['sku' => '920-009664', 'name' => 'Logitech G915 TKL Tenkeyless Lightspeed Wireless Gaming Keyboard Tactile', 'category' => 'keyboards', 'model' => 'G915 TKL', 'type' => 'Wireless Mechanical Keyboard', 'connectivity' => 'Lightspeed Wireless', 'colour' => 'Carbon', 'price' => 24500],
            ['sku' => '920-009111', 'name' => 'Logitech G915 Lightspeed RGB Mechanical Gaming Keyboard Carbon Clicky', 'category' => 'keyboards', 'model' => 'G915', 'type' => 'Wireless Mechanical Keyboard', 'connectivity' => 'Lightspeed Wireless', 'colour' => 'Carbon', 'price' => 28500],
            ['sku' => '920-012721', 'name' => 'Logitech G915 X Mechanical TKL Tenkeyless Lightspeed Wireless Gaming Keyboard Tactile', 'category' => 'keyboards', 'model' => 'G915 X', 'type' => 'Wireless Mechanical Keyboard', 'connectivity' => 'Lightspeed Wireless', 'colour' => 'Carbon', 'price' => 35000],

            // ============ MICE (910-*) ============
            ['sku' => '910-005730', 'name' => 'Logitech G502 SE Corded Gaming Mouse Hero Black/White', 'category' => 'mice', 'model' => 'G502 SE', 'type' => 'Gaming Mouse', 'connectivity' => 'Wired USB', 'colour' => 'Black/White', 'price' => 9000],
            ['sku' => '910-005292', 'name' => 'Logitech G305 Lightspeed Wireless Gaming Mouse White', 'category' => 'mice', 'model' => 'G305', 'type' => 'Wireless Gaming Mouse', 'connectivity' => 'Lightspeed Wireless', 'colour' => 'White', 'price' => 4200],
            ['sku' => '910-005283', 'name' => 'Logitech G305 Lightspeed Wireless Gaming Mouse Black', 'category' => 'mice', 'model' => 'G305', 'type' => 'Wireless Gaming Mouse', 'connectivity' => 'Lightspeed Wireless', 'colour' => 'Black', 'price' => 4200],
            ['sku' => '910-006014', 'name' => 'Logitech G305 Lightspeed Wireless Gaming Mouse Blue', 'category' => 'mice', 'model' => 'G305', 'type' => 'Wireless Gaming Mouse', 'connectivity' => 'Lightspeed Wireless', 'colour' => 'Blue', 'price' => 5900],
            ['sku' => '910-005823', 'name' => 'Logitech G102 LightSync RGB Gaming Mouse Black', 'category' => 'mice', 'model' => 'G102', 'type' => 'Gaming Mouse', 'connectivity' => 'Wired USB', 'colour' => 'Black', 'price' => 2500],
            ['sku' => '910-005801', 'name' => 'Logitech G102 LightSync RGB Gaming Mouse Blue', 'category' => 'mice', 'model' => 'G102', 'type' => 'Gaming Mouse', 'connectivity' => 'Wired USB', 'colour' => 'Blue', 'price' => 2500],
            ['sku' => '910-005824', 'name' => 'Logitech G102 LightSync RGB Gaming Mouse White', 'category' => 'mice', 'model' => 'G102', 'type' => 'Gaming Mouse', 'connectivity' => 'Wired USB', 'colour' => 'White', 'price' => 2500],
            ['sku' => '910-005854', 'name' => 'Logitech G102 LightSync RGB Gaming Mouse Lilac', 'category' => 'mice', 'model' => 'G102', 'type' => 'Gaming Mouse', 'connectivity' => 'Wired USB', 'colour' => 'Lilac', 'price' => 2500],
            ['sku' => '910-004346', 'name' => 'Logitech G300S Corded Gaming Mouse', 'category' => 'mice', 'model' => 'G300S', 'type' => 'Gaming Mouse', 'connectivity' => 'Wired USB', 'colour' => 'Black', 'price' => 2400],
            ['sku' => '910-005943', 'name' => 'Logitech G Pro X Superlight Wireless Gaming Mouse', 'category' => 'mice', 'model' => 'G Pro X Superlight', 'type' => 'Wireless Gaming Mouse', 'connectivity' => 'Lightspeed Wireless', 'colour' => 'Black', 'price' => 16500],
            ['sku' => '910-005471', 'name' => 'Logitech G502 Hero High Performance Gaming Mouse', 'category' => 'mice', 'model' => 'G502 Hero', 'type' => 'Gaming Mouse', 'connectivity' => 'Wired USB', 'colour' => 'Black', 'price' => 7300],
            ['sku' => '910-006181', 'name' => 'Logitech G502 X Lightspeed Wireless Gaming Mouse Black', 'category' => 'mice', 'model' => 'G502 X Lightspeed', 'type' => 'Wireless Gaming Mouse', 'connectivity' => 'Lightspeed Wireless', 'colour' => 'Black', 'price' => 16200],
            ['sku' => '910-005673', 'name' => 'Logitech G903 Lightspeed Wireless Gaming Mouse', 'category' => 'mice', 'model' => 'G903', 'type' => 'Wireless Gaming Mouse', 'connectivity' => 'Lightspeed Wireless', 'colour' => 'Black', 'price' => 15700],
            ['sku' => '910-006139', 'name' => 'Logitech G502 X Corded Gaming Mouse Black', 'category' => 'mice', 'model' => 'G502 X', 'type' => 'Gaming Mouse', 'connectivity' => 'Wired USB', 'colour' => 'Black', 'price' => 8400],
            ['sku' => '910-005796', 'name' => 'Logitech G203 Lightsync Gaming Mouse Black', 'category' => 'mice', 'model' => 'G203', 'type' => 'Gaming Mouse', 'connectivity' => 'Wired USB', 'colour' => 'Black', 'price' => 2400],
            ['sku' => '910-005797', 'name' => 'Logitech G203 Lightsync Gaming Mouse White', 'category' => 'mice', 'model' => 'G203', 'type' => 'Gaming Mouse', 'connectivity' => 'Wired USB', 'colour' => 'White', 'price' => 2400],
            ['sku' => '910-005798', 'name' => 'Logitech G203 Lightsync Gaming Mouse Blue', 'category' => 'mice', 'model' => 'G203', 'type' => 'Gaming Mouse', 'connectivity' => 'Wired USB', 'colour' => 'Blue', 'price' => 2400],
            ['sku' => '910-004068', 'name' => 'Logitech G402 Hyperion Fury', 'category' => 'mice', 'model' => 'G402', 'type' => 'Gaming Mouse', 'connectivity' => 'Wired USB', 'colour' => 'Black', 'price' => 4000],
            ['sku' => '910-005568', 'name' => 'Logitech G502 Lightspeed Wireless Gaming Mouse Black', 'category' => 'mice', 'model' => 'G502 Lightspeed', 'type' => 'Wireless Gaming Mouse', 'connectivity' => 'Lightspeed Wireless', 'colour' => 'Black', 'price' => 15500],
            ['sku' => '910-006631', 'name' => 'Logitech G Pro X Superlight 2 Wireless Gaming Mouse Black', 'category' => 'mice', 'model' => 'G Pro X Superlight 2', 'type' => 'Wireless Gaming Mouse', 'connectivity' => 'Lightspeed Wireless', 'colour' => 'Black', 'price' => 16700],
            ['sku' => '910-006632', 'name' => 'Logitech G Pro X Superlight 2 Wireless Gaming Mouse White', 'category' => 'mice', 'model' => 'G Pro X Superlight 2', 'type' => 'Wireless Gaming Mouse', 'connectivity' => 'Lightspeed Wireless', 'colour' => 'White', 'price' => 16700],
            ['sku' => '910-005273', 'name' => 'Logitech G Pro Wireless Gaming Mouse Black', 'category' => 'mice', 'model' => 'G Pro Wireless', 'type' => 'Wireless Gaming Mouse', 'connectivity' => 'Lightspeed Wireless', 'colour' => 'Black', 'price' => 13600],
            ['sku' => '910-005881', 'name' => 'Logitech G Pro X Superlight Wireless Gaming Mouse Black', 'category' => 'mice', 'model' => 'G Pro X Superlight', 'type' => 'Wireless Gaming Mouse', 'connectivity' => 'Lightspeed Wireless', 'colour' => 'Black', 'price' => 16700],
            ['sku' => '910-007200', 'name' => 'Logitech G309 Lightspeed Wireless Mouse Graphite', 'category' => 'mice', 'model' => 'G309', 'type' => 'Wireless Gaming Mouse', 'connectivity' => 'Lightspeed Wireless', 'colour' => 'Graphite', 'price' => 8600],
            ['sku' => '910-006162', 'name' => 'Logitech G502 X Plus Wireless RGB Gaming Mouse Black', 'category' => 'mice', 'model' => 'G502 X Plus', 'type' => 'Wireless RGB Gaming Mouse', 'connectivity' => 'Lightspeed Wireless', 'colour' => 'Black', 'price' => 16200],
            ['sku' => '910-007777', 'name' => 'Logitech G Pro X2 Superstrike Lightspeed Wireless Gaming Mouse White', 'category' => 'mice', 'model' => 'G Pro X2 Superstrike', 'type' => 'Wireless Gaming Mouse', 'connectivity' => 'Lightspeed Wireless', 'colour' => 'White', 'price' => 22000],
            ['sku' => '943-000110', 'name' => 'Logitech Powerplay Wireless Charging System EWR2', 'category' => 'mice', 'model' => 'Powerplay', 'type' => 'Wireless Charging System', 'connectivity' => 'Lightspeed Wireless', 'colour' => 'Black', 'price' => 13800],

            // ============ GAMEPADS (940-*) ============
            ['sku' => '940-000142', 'name' => 'Logitech F710 Gamepad PC RF Black and Silver', 'category' => 'gamepads', 'model' => 'F710', 'type' => 'Wireless Gamepad', 'connectivity' => '2.4GHz RF', 'colour' => 'Black/Silver', 'price' => 4100],
            ['sku' => '940-000138', 'name' => 'Logitech F310 Gamepad PC Digital Black and Blue', 'category' => 'gamepads', 'model' => 'F310', 'type' => 'Wired Gamepad', 'connectivity' => 'Wired USB', 'colour' => 'Black/Blue', 'price' => 2500],
        ];
    }
}