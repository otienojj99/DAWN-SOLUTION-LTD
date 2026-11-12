<?php

namespace Database\Seeders;

use App\Models\Brands;
use App\Models\Category;
use App\Models\Products;
use App\Models\ProductImages;
use App\Models\Promotions;
use App\Models\UseCase;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class LenovoLaptopsSeeder extends Seeder
{
    public function run(): void
    {
        DB::transaction(fn () => $this->seed());
    }

    protected function seed(): void
    {
        $brand = Brands::where('slug', 'lenovo')->firstOrFail();

        $categories = Category::whereIn('slug', [
            'lenovo-ideapads',
            'lenovo-v-series',
            'lenovo-yoga-series',
            'lenovo-thinkpad-t-series',      // ← updated
            'lenovo-thinkpad-e-series',      // ← updated
            'lenovo-thinkbook',              // ← updated
            'thinkpad-t14s-series',
            'thinkpad-tablet-series',
            'thinkpad-l-series',
            'thinkpad-x-series',
            'lenovo-gaming',
        ])->pluck('id', 'slug');

        $useCases = UseCase::pluck('id', 'slug');

        $clearancePromo = Promotions::where('kind', 'clearance')
            ->where('is_active', true)
            ->first();

        $created = 0;
        $updated = 0;
        $outOfStock = 0;

        foreach ($this->products() as $row) {
            $categoryId = $categories[$row['category']] ?? null;
            if (! $categoryId) {
                $this->command->warn("Missing category {$row['category']} — skipping {$row['sku']}");
                continue;
            }

            $margin   = $row['status'] === 'clearance' ? 0.05 : 0.10;
            $priceKes = round($row['price'] * (1 + $margin), 2);
            $inStock  = ! in_array($row['status'], ['out_of_stock', 'sold_out'], true);
            $stock    = $inStock ? $this->stockForStatus($row['status']) : 0;
            $initial  = $inStock && $row['status'] === 'clearance' ? $stock + 30 : max(1, $stock);

            $slug = Str::slug($row['model'] . ' ' . $row['sku']);

            $product = Products::updateOrCreate(
                ['sku' => $row['sku']],
                [
                    'name'              => $row['model'],
                    'slug'              => $slug,
                    'type'              => 'simple',
                    'category_id'       => $categoryId,
                    'brand_id'          => $brand->id,
                    'short_description' => $this->shortDescription($row),
                    'long_description'  => $this->longDescription($row),
                    'specifications'    => $this->specifications($row),
                    'price'             => $priceKes,
                    'compare_at_price'  => null,
                    'price_usd'         => round($priceKes / config('shop.usd_rate', 130), 2),
                    'base_currency'     => 'KES',
                    'stock_quantity'    => $stock,
                    'initial_stock_quantity' => $initial,
                    'track_inventory'   => true,
                    'allow_backorder'   => false,
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

            $ucIds = collect($row['use_cases'])->map(fn ($s) => $useCases[$s] ?? null)->filter()->values()->all();
            if (! empty($ucIds)) {
                $product->useCases()->sync($ucIds);
            }

            if ($row['status'] === 'clearance' && $clearancePromo) {
                $product->promotions()->syncWithoutDetaching([$clearancePromo->id]);
            }
        }

        $this->command->info("Lenovo laptops seeded: {$created} created, {$updated} updated, {$outOfStock} out of stock.");
    }

    protected function stockForStatus(string $status): int
    {
        return match ($status) {
            'clearance' => 8,
            'available' => 15,
            'new'       => 20,
            default     => 10,
        };
    }

    protected function shortDescription(array $row): string
    {
        return sprintf(
            '%s | %s RAM | %s | %s | %s',
            $row['cpu'], $row['ram'], $row['storage'], $row['display'], $row['os'],
        );
    }

    protected function longDescription(array $row): string
    {
        $warranty = $row['warranty'] ?? '1 Year Warranty';
        $gpu   = ! empty($row['gpu'])    ? " Graphics: {$row['gpu']}." : '';
        $extras = ! empty($row['extras']) ? ' Bundled: ' . implode(', ', $row['extras']) . '.' : '';

        return sprintf(
            'The %s pairs %s with %s RAM and %s storage in a %s chassis. '
            . 'Built for professionals who need dependable performance day after day.%s%s %s',
            $row['model'], $row['cpu'], $row['ram'], $row['storage'], $row['display'], $gpu, $extras, $warranty,
        );
    }

    protected function specifications(array $row): array
    {
        $specs = [
            ['label' => 'Processor', 'value' => $row['cpu']],
            ['label' => 'Memory',    'value' => $row['ram'] . ' RAM'],
            ['label' => 'Storage',   'value' => $row['storage']],
            ['label' => 'Display',   'value' => $row['display']],
            ['label' => 'OS',        'value' => $row['os']],
            ['label' => 'Warranty',  'value' => $row['warranty'] ?? '1 Year Warranty'],
        ];

        if (! empty($row['gpu'])) {
            $specs[] = ['label' => 'Graphics', 'value' => $row['gpu']];
        }
        if (! empty($row['extras'])) {
            $specs[] = ['label' => 'Extras', 'value' => implode(', ', $row['extras'])];
        }

        return $specs;
    }

    protected function products(): array
    {
        return [
            // ==================== IDEAPADS ====================
            ['sku' => '81W800PUUE', 'model' => 'Lenovo NoteBook S145', 'category' => 'lenovo-ideapads',
             'cpu' => 'Intel Core i7-1065G7', 'ram' => '8GB', 'storage' => '512GB SSD',
             'display' => '15.6"', 'os' => 'FreeDOS',
             'status' => 'clearance', 'price' => 70000, 'use_cases' => ['lifestyle']],

            ['sku' => '82HS01BGUE', 'model' => 'Lenovo IdeaPad Flex 5 14ITL05', 'category' => 'lenovo-ideapads',
             'cpu' => 'Intel Core i5-1135G7', 'ram' => '8GB', 'storage' => '512GB SSD',
             'display' => '14" Touch x360', 'os' => 'Windows 11 Home',
             'extras' => ['Stylus Pen'],
             'status' => 'clearance', 'price' => 83000, 'use_cases' => ['lifestyle', 'education']],

            ['sku' => '82R700DXUE', 'model' => 'Lenovo IdeaPad Flex 5 14IAU7', 'category' => 'lenovo-ideapads',
             'cpu' => 'Intel Core i5-1235U', 'ram' => '8GB', 'storage' => '512GB SSD',
             'display' => '14" Touch x360', 'os' => 'Windows 11 Home',
             'extras' => ['Stylus Pen'],
             'status' => 'available', 'price' => 83000, 'use_cases' => ['lifestyle', 'education']],

            ['sku' => '83EL002RUE', 'model' => 'Lenovo IdeaPad Slim 3 14IRH8', 'category' => 'lenovo-ideapads',
             'cpu' => 'Intel Core i5-13420H', 'ram' => '8GB', 'storage' => '512GB SSD',
             'display' => '14"', 'os' => 'FreeDOS',
             'status' => 'out_of_stock', 'price' => 65000, 'use_cases' => ['lifestyle', 'education']],

            ['sku' => '83K0004SUE', 'model' => 'Lenovo IdeaPad Slim 3 14IRH10', 'category' => 'lenovo-ideapads',
             'cpu' => 'Intel Core i5-13420H', 'ram' => '16GB', 'storage' => '512GB SSD',
             'display' => '14"', 'os' => 'Windows 11 Home',
             'status' => 'available', 'price' => 102000, 'use_cases' => ['lifestyle', 'education']],

            ['sku' => '83K000N5UE', 'model' => 'Lenovo IdeaPad Slim 3 14IRH10 i7', 'category' => 'lenovo-ideapads',
             'cpu' => 'Intel Core i7-13620H', 'ram' => '16GB', 'storage' => '512GB SSD',
             'display' => '14"', 'os' => 'No OS',
             'status' => 'available', 'price' => 123500, 'use_cases' => ['lifestyle', 'business']],

            ['sku' => '83K000N1UE', 'model' => 'Lenovo IdeaPad Slim 3 14IRH10 (Backpack)', 'category' => 'lenovo-ideapads',
             'cpu' => 'Intel Core i5-13420H', 'ram' => '16GB', 'storage' => '512GB SSD',
             'display' => '14"', 'os' => 'No OS',
             'extras' => ['Lenovo 16" Laptop ECO Backpack B210'],
             'status' => 'new', 'price' => 102000, 'use_cases' => ['lifestyle', 'education']],

            ['sku' => '83K0005WUE', 'model' => 'Lenovo IdeaPad Slim 3 14IRH10 WUXGA', 'category' => 'lenovo-ideapads',
             'cpu' => 'Intel Core i5-13420H', 'ram' => '16GB DDR5', 'storage' => '512GB SSD',
             'display' => '14" WUXGA', 'os' => 'DOS',
             'status' => 'available', 'price' => 95000, 'use_cases' => ['lifestyle', 'education']],

            ['sku' => '83KX003DUE', 'model' => 'Lenovo IdeaPad 5 2-in-1 i5', 'category' => 'lenovo-ideapads',
             'cpu' => 'Intel Core i5-13420H', 'ram' => '8GB', 'storage' => '512GB SSD',
             'display' => '14" Touch x360', 'os' => 'Windows 11 Home',
             'warranty' => '2 Years Warranty', 'extras' => ['Stylus Pen'],
             'status' => 'available', 'price' => 95000, 'use_cases' => ['lifestyle']],

            // ==================== V SERIES ====================
            ['sku' => '82C5S03S00', 'model' => 'Lenovo V15-IIL', 'category' => 'lenovo-v-series',
             'cpu' => 'Intel Core i3-1005G1', 'ram' => '4GB', 'storage' => '1TB HDD',
             'display' => '15.6"', 'os' => 'Windows 10 Pro',
             'status' => 'clearance', 'price' => 45000, 'use_cases' => ['business', 'education']],

            ['sku' => '83GU005XUE', 'model' => 'Lenovo V14 G5 IRL', 'category' => 'lenovo-v-series',
             'cpu' => 'Intel Core i5-13420H', 'ram' => '8GB', 'storage' => '512GB SSD',
             'display' => '14"', 'os' => 'No OS',
             'status' => 'new', 'price' => 93500, 'use_cases' => ['business']],

            // ==================== YOGA ====================
            ['sku' => '82SV00A6UE', 'model' => 'Lenovo Yoga 7 14IAL7', 'category' => 'lenovo-yoga-series',
             'cpu' => 'Intel Core i7-1255U', 'ram' => '16GB', 'storage' => '1TB SSD',
             'display' => '14"', 'os' => 'Windows 11 Home',
             'extras' => ['Stylus Pen'],
             'status' => 'clearance', 'price' => 170000, 'use_cases' => ['lifestyle', 'business']],

            // ==================== THINKBOOK ====================
            ['sku' => '20VD000WUE', 'model' => 'Lenovo ThinkBook 14 G2 ITL', 'category' => 'lenovo-thinkbook',
             'cpu' => 'Intel Core i5-1135G7', 'ram' => '8GB', 'storage' => '512GB SSD',
             'display' => '14"', 'os' => 'FreeDOS',
             'status' => 'clearance', 'price' => 75000, 'use_cases' => ['business']],

            ['sku' => '20VE00PBUE', 'model' => 'Lenovo ThinkBook 15 G2 ITL', 'category' => 'lenovo-thinkbook',
             'cpu' => 'Intel Core i5-1135G7', 'ram' => '8GB', 'storage' => '512GB SSD',
             'display' => '15.6"', 'os' => 'FreeDOS',
             'status' => 'clearance', 'price' => 75000, 'use_cases' => ['business']],

            ['sku' => '20VD017RUE', 'model' => 'Lenovo ThinkBook 14 G2 ITL i7', 'category' => 'lenovo-thinkbook',
             'cpu' => 'Intel Core i7-1165G7', 'ram' => '8GB', 'storage' => '512GB SSD',
             'display' => '14"', 'os' => 'FreeDOS',
             'status' => 'clearance', 'price' => 85500, 'use_cases' => ['business']],

            ['sku' => '21KG00FMUE-E', 'model' => 'Lenovo ThinkBook 14 G6 IRL', 'category' => 'lenovo-thinkbook',
             'cpu' => 'Intel Core i7-13700H', 'ram' => '16GB', 'storage' => '512GB SSD',
             'display' => '14"', 'os' => 'FreeDOS', 'gpu' => 'Intel Iris Xe',
             'status' => 'available', 'price' => 98500, 'use_cases' => ['business']],

            ['sku' => '21KH003RUE', 'model' => 'Lenovo ThinkBook 16 G6 IRL i5', 'category' => 'lenovo-thinkbook',
             'cpu' => 'Intel Core i5 13th Gen', 'ram' => '8GB', 'storage' => '512GB SSD',
             'display' => '16"', 'os' => 'FreeDOS',
             'status' => 'available', 'price' => 85000, 'use_cases' => ['business']],

            ['sku' => '21KH0056UE', 'model' => 'Lenovo ThinkBook 16 G6 IRL i7', 'category' => 'lenovo-thinkbook',
             'cpu' => 'Intel Core i7-13700H', 'ram' => '8GB', 'storage' => '512GB SSD',
             'display' => '16"', 'os' => 'FreeDOS',
             'status' => 'available', 'price' => 98000, 'use_cases' => ['business']],

            ['sku' => '21SJ000YUE', 'model' => 'Lenovo ThinkBook 14 Ultra 7 255H', 'category' => 'lenovo-thinkbook',
             'cpu' => 'Intel Core Ultra 7 255H', 'ram' => '16GB', 'storage' => '512GB SSD',
             'display' => '14" WUXGA', 'os' => 'DOS',
             'status' => 'available', 'price' => 127500, 'use_cases' => ['business', 'enterprise']],

            // ==================== THINKPAD E ====================
            ['sku' => '20TBS6RL00', 'model' => 'Lenovo ThinkPad E14 Gen 2', 'category' => 'lenovo-thinkpad-e-series',
             'cpu' => 'Intel Core i5-1135G7', 'ram' => '8GB', 'storage' => '512GB SSD',
             'display' => '14"', 'os' => 'FreeDOS',
             'status' => 'clearance', 'price' => 85000, 'use_cases' => ['business']],

            ['sku' => '20TES6MF00', 'model' => 'Lenovo ThinkPad E15 Gen 2 i5', 'category' => 'lenovo-thinkpad-e-series',
             'cpu' => 'Intel Core i5-1135G7', 'ram' => '8GB', 'storage' => '512GB SSD',
             'display' => '15.6"', 'os' => 'FreeDOS',
             'status' => 'clearance', 'price' => 88500, 'use_cases' => ['business']],

            ['sku' => '20TES6ME00', 'model' => 'Lenovo ThinkPad E15 Gen 2 i7 16GB', 'category' => 'lenovo-thinkpad-e-series',
             'cpu' => 'Intel Core i7-1135G7', 'ram' => '16GB', 'storage' => '512GB SSD',
             'display' => '15.6"', 'os' => 'FreeDOS',
             'status' => 'clearance', 'price' => 108000, 'use_cases' => ['business']],

            ['sku' => '20TES6MD00', 'model' => 'Lenovo ThinkPad E15 Gen 2 i7 8GB', 'category' => 'lenovo-thinkpad-e-series',
             'cpu' => 'Intel Core i7-1165G7', 'ram' => '8GB', 'storage' => '512GB SSD',
             'display' => '15.6"', 'os' => 'FreeDOS',
             'status' => 'clearance', 'price' => 102000, 'use_cases' => ['business']],

            ['sku' => '21E3003RUE', 'model' => 'Lenovo ThinkPad E14 Gen 4 i5', 'category' => 'lenovo-thinkpad-e-series',
             'cpu' => 'Intel Core i5-1235U', 'ram' => '8GB', 'storage' => '512GB SSD',
             'display' => '14"', 'os' => 'FreeDOS',
             'status' => 'clearance', 'price' => 95000, 'use_cases' => ['business']],

            ['sku' => '21E4S23D00', 'model' => 'Lenovo ThinkPad E14 Gen 4 i7 16GB', 'category' => 'lenovo-thinkpad-e-series',
             'cpu' => 'Intel Core i7-1255U', 'ram' => '16GB', 'storage' => '512GB SSD',
             'display' => '14"', 'os' => 'FreeDOS',
             'status' => 'available', 'price' => 120000, 'use_cases' => ['business', 'enterprise']],

            ['sku' => '21E3009BUE-E', 'model' => 'Lenovo ThinkPad E14 Gen 4 i7 8GB', 'category' => 'lenovo-thinkpad-e-series',
             'cpu' => 'Intel Core i7-1255U', 'ram' => '8GB', 'storage' => '512GB SSD',
             'display' => '14"', 'os' => 'FreeDOS',
             'status' => 'available', 'price' => 120000, 'use_cases' => ['business', 'enterprise']],

            ['sku' => '21JK009YUE', 'model' => 'Lenovo ThinkPad E14 Gen 5 i5', 'category' => 'lenovo-thinkpad-e-series',
             'cpu' => 'Intel Core i5 13th Gen', 'ram' => '8GB', 'storage' => '512GB SSD',
             'display' => '14"', 'os' => 'FreeDOS',
             'extras' => ['Free Backpack'],
             'status' => 'available', 'price' => 105000, 'use_cases' => ['business', 'enterprise']],

            ['sku' => '21JK00DGUE', 'model' => 'Lenovo ThinkPad E14 Gen 5 i7', 'category' => 'lenovo-thinkpad-e-series',
             'cpu' => 'Intel Core i7 13th Gen', 'ram' => '16GB', 'storage' => '512GB SSD',
             'display' => '14"', 'os' => 'FreeDOS',
             'status' => 'available', 'price' => 135000, 'use_cases' => ['business', 'enterprise']],

            ['sku' => '21M70027UE', 'model' => 'Lenovo ThinkPad E14 Gen 6 Ultra 7 155H', 'category' => 'lenovo-thinkpad-e-series',
             'cpu' => 'Intel Core Ultra 7 155H', 'ram' => '16GB', 'storage' => '512GB SSD',
             'display' => '14"', 'os' => 'FreeDOS',
             'status' => 'available', 'price' => 165000, 'use_cases' => ['business', 'enterprise']],

            ['sku' => '21M8S46900', 'model' => 'Lenovo ThinkPad E14 Gen 6 Ultra 7 155U Touch', 'category' => 'lenovo-thinkpad-e-series',
             'cpu' => 'Intel Core Ultra 7 155U', 'ram' => '16GB', 'storage' => '1TB SSD',
             'display' => '14" Touch', 'os' => 'FreeDOS',
             'status' => 'available', 'price' => 175000, 'use_cases' => ['business', 'enterprise']],

            ['sku' => '21SX001VUE', 'model' => 'Lenovo ThinkPad E14 Gen 7 Ultra 7 255H', 'category' => 'lenovo-thinkpad-e-series',
             'cpu' => 'Intel Core Ultra 7 255H', 'ram' => '16GB', 'storage' => '512GB SSD',
             'display' => '14"', 'os' => 'FreeDOS',
             'extras' => ['Lenovo 15.6" Laptop Casual Toploader'],
             'status' => 'available', 'price' => 165000, 'use_cases' => ['business', 'enterprise']],

            ['sku' => '21T90030UE', 'model' => 'Lenovo ThinkPad E14 Gen 7 Core 5 210H', 'category' => 'lenovo-thinkpad-e-series',
             'cpu' => 'Intel Core 5 210H', 'ram' => '8GB', 'storage' => '512GB SSD',
             'display' => '14"', 'os' => 'FreeDOS',
             'warranty' => '2 Years Warranty',
             'extras' => ['Lenovo 15.6" Laptop Casual Toploader'],
             'status' => 'available', 'price' => 135000, 'use_cases' => ['business', 'enterprise']],

            ['sku' => '21SX001DUE', 'model' => 'Lenovo ThinkPad E14 Gen 7 Ultra 5 225U', 'category' => 'lenovo-thinkpad-e-series',
             'cpu' => 'Intel Core Ultra 5 225U', 'ram' => '8GB', 'storage' => '512GB SSD',
             'display' => '14"', 'os' => 'FreeDOS',
             'warranty' => '2 Years Warranty',
             'extras' => ['Lenovo 15.6" Laptop Casual Toploader'],
             'status' => 'available', 'price' => 135000, 'use_cases' => ['business', 'enterprise']],

            ['sku' => '21T9001GUE', 'model' => 'Lenovo ThinkPad E14 Gen 7 Core 7 240H', 'category' => 'lenovo-thinkpad-e-series',
             'cpu' => 'Intel Core 7 240H', 'ram' => '16GB', 'storage' => '1TB SSD',
             'display' => '14"', 'os' => 'FreeDOS',
             'warranty' => '2 Years Warranty',
             'extras' => ['Lenovo 15.6" Laptop Casual Toploader'],
             'status' => 'available', 'price' => 170000, 'use_cases' => ['business', 'enterprise']],

            ['sku' => '21U2002WUE', 'model' => 'Lenovo ThinkPad E14 Gen 7 Ultra 7 258V', 'category' => 'lenovo-thinkpad-e-series',
             'cpu' => 'Intel Core Ultra 7 258V', 'ram' => '32GB DDR5', 'storage' => '512GB SSD',
             'display' => '14" WUXGA', 'os' => 'No OS', 'gpu' => 'Intel Arc Graphics',
             'status' => 'available', 'price' => 179500, 'use_cases' => ['business', 'enterprise']],

            ['sku' => '21JN0010UE', 'model' => 'Lenovo ThinkPad E16 Gen 1', 'category' => 'lenovo-thinkpad-e-series',
             'cpu' => 'Intel Core i5-1335U', 'ram' => '8GB', 'storage' => '512GB SSD',
             'display' => '16" WUXGA', 'os' => 'FreeDOS',
             'status' => 'available', 'price' => 105000, 'use_cases' => ['business', 'enterprise']],

            // ==================== THINKPAD T14 ====================
            ['sku' => '20W1S65U00', 'model' => 'Lenovo ThinkPad T14 Gen 2 i5', 'category' => 'lenovo-thinkpad-t-series',
             'cpu' => 'Intel Core i5-1135G7', 'ram' => '8GB', 'storage' => '512GB SSD',
             'display' => '14"', 'os' => 'Windows 10 Pro',
             'status' => 'clearance', 'price' => 130000, 'use_cases' => ['business', 'enterprise']],

            ['sku' => '20W1S86R00', 'model' => 'Lenovo ThinkPad T14 Gen 2 i7', 'category' => 'lenovo-thinkpad-t-series',
             'cpu' => 'Intel Core i7-1165G7', 'ram' => '16GB', 'storage' => '256GB SSD',
             'display' => '14"', 'os' => 'FreeDOS',
             'status' => 'clearance', 'price' => 145000, 'use_cases' => ['business', 'enterprise']],

            ['sku' => '21HD008DUE', 'model' => 'Lenovo ThinkPad T14 Gen 4', 'category' => 'lenovo-thinkpad-t-series',
             'cpu' => 'Intel Core i7-1355U', 'ram' => '16GB', 'storage' => '512GB SSD',
             'display' => '14"', 'os' => 'Windows 11 Pro', 'warranty' => '3 Years Warranty',
             'status' => 'available', 'price' => 230000, 'use_cases' => ['business', 'enterprise']],

            ['sku' => '21MLS3NV00', 'model' => 'Lenovo ThinkPad T14 Gen 5', 'category' => 'lenovo-thinkpad-t-series',
             'cpu' => 'Intel Core Ultra 7 155U', 'ram' => '16GB DDR5', 'storage' => '512GB SSD',
             'display' => '14" WUXGA', 'os' => 'DOS', 'gpu' => 'Intel Graphics',
             'status' => 'available', 'price' => 235000, 'use_cases' => ['business', 'enterprise']],

            ['sku' => '21QC001QUE', 'model' => 'Lenovo ThinkPad T14 Gen 6', 'category' => 'lenovo-thinkpad-t-series',
             'cpu' => 'Intel Core Ultra 7 255U', 'ram' => '16GB', 'storage' => '512GB SSD',
             'display' => '14"', 'os' => 'FreeDOS', 'warranty' => '3 Years Warranty',
             'status' => 'available', 'price' => 235000, 'use_cases' => ['business', 'enterprise']],

            ['sku' => '21QG00APUE', 'model' => 'Lenovo ThinkPad T14 Gen 6 Ultra 7 258V', 'category' => 'lenovo-thinkpad-t-series',
             'cpu' => 'Intel Core Ultra 7 258V', 'ram' => '32GB Soldered', 'storage' => '1TB SSD NVMe',
             'display' => '14"', 'os' => 'FreeDOS', 'warranty' => '3 Years Warranty',
             'status' => 'available', 'price' => 245000, 'use_cases' => ['business', 'enterprise']],

            // ==================== THINKPAD T14s ====================
            ['sku' => '20T00045UE', 'model' => 'Lenovo ThinkPad T14s Gen 1', 'category' => 'thinkpad-t14s-series',
             'cpu' => 'Intel Core i7-10510U', 'ram' => '16GB', 'storage' => '1TB SSD',
             'display' => '14"', 'os' => 'Windows 10 Pro',
             'status' => 'clearance', 'price' => 177500, 'use_cases' => ['business', 'enterprise']],

            ['sku' => '20WNS3R500', 'model' => 'Lenovo ThinkPad T14s Gen 2 i5', 'category' => 'thinkpad-t14s-series',
             'cpu' => 'Intel Core i5-1135G7', 'ram' => '16GB', 'storage' => '512GB SSD',
             'display' => '14"', 'os' => 'Windows 10 Pro',
             'status' => 'clearance', 'price' => 154800, 'use_cases' => ['business', 'enterprise']],

            ['sku' => '20WM008JUE', 'model' => 'Lenovo ThinkPad T14s Gen 2 i7', 'category' => 'thinkpad-t14s-series',
             'cpu' => 'Intel Core i7-1135G7', 'ram' => '16GB', 'storage' => '512GB SSD',
             'display' => '14"', 'os' => 'Windows 10 Pro',
             'status' => 'clearance', 'price' => 202000, 'use_cases' => ['business', 'enterprise']],

            ['sku' => '21LS000CUE', 'model' => 'Lenovo ThinkPad T14s Gen 5', 'category' => 'thinkpad-t14s-series',
             'cpu' => 'Intel Core Ultra 7 155U', 'ram' => '16GB', 'storage' => '512GB SSD',
             'display' => '14"', 'os' => 'Windows 11 Pro', 'warranty' => '3 Years Warranty',
             'status' => 'available', 'price' => 235000, 'use_cases' => ['business', 'enterprise']],

            ['sku' => '21R1001BUE', 'model' => 'Lenovo ThinkPad T14s Gen 6', 'category' => 'thinkpad-t14s-series',
             'cpu' => 'Intel Core Ultra 7 255U', 'ram' => '16GB', 'storage' => '1TB SSD',
             'display' => '14"', 'os' => 'Windows 11 Pro', 'warranty' => '3 Years Warranty',
             'status' => 'available', 'price' => 258000, 'use_cases' => ['business', 'enterprise']],

            ['sku' => '21R3004RUE', 'model' => 'Lenovo ThinkPad T14s 2-in-1 Gen 1', 'category' => 'thinkpad-t14s-series',
             'cpu' => 'Intel Core Ultra 7 255U', 'ram' => '32GB', 'storage' => '1TB SSD',
             'display' => '14" Touch', 'os' => 'Windows 11 Pro', 'warranty' => '3 Years Warranty',
             'status' => 'new', 'price' => 335000, 'use_cases' => ['business', 'enterprise']],

            // ==================== THINKPAD TABLET ====================
            ['sku' => '20KJ001PUE', 'model' => 'Lenovo ThinkPad X1 Tablet Gen 3', 'category' => 'thinkpad-tablet-series',
             'cpu' => 'Intel Core i5-8250U', 'ram' => '8GB', 'storage' => '256GB SSD',
             'display' => '13" Touch', 'os' => 'Windows 10 Pro',
             'status' => 'clearance', 'price' => 118000, 'use_cases' => ['business', 'enterprise']],

            // ==================== THINKPAD L ====================
            ['sku' => '20Q50003UE', 'model' => 'Lenovo ThinkPad L490 i7', 'category' => 'thinkpad-l-series',
             'cpu' => 'Intel Core i7-8565U', 'ram' => '8GB', 'storage' => '512GB SSD',
             'display' => '14"', 'os' => 'Windows 10 Pro',
             'status' => 'clearance', 'price' => 85000, 'use_cases' => ['business']],

            ['sku' => '20Q50007UE-G', 'model' => 'Lenovo ThinkPad L490 i7 (Refurb)', 'category' => 'thinkpad-l-series',
             'cpu' => 'Intel Core i7-8565U', 'ram' => '8GB', 'storage' => '512GB SSD',
             'display' => '14"', 'os' => 'Windows 10 Pro',
             'status' => 'clearance', 'price' => 85000, 'use_cases' => ['business']],

            ['sku' => '20Q70006UE', 'model' => 'Lenovo ThinkPad L590', 'category' => 'thinkpad-l-series',
             'cpu' => 'Intel Core i7-8565U', 'ram' => '8GB', 'storage' => '512GB SSD',
             'display' => '15.6"', 'os' => 'Windows 10 Pro',
             'status' => 'clearance', 'price' => 85000, 'use_cases' => ['business']],

            ['sku' => '20U3S1FR00', 'model' => 'Lenovo ThinkPad L15 Gen 1', 'category' => 'thinkpad-l-series',
             'cpu' => 'Intel Core i5 10th Gen', 'ram' => '8GB', 'storage' => '512GB SSD',
             'display' => '15.6"', 'os' => 'FreeDOS',
             'status' => 'clearance', 'price' => 92000, 'use_cases' => ['business']],

            ['sku' => '21S6004RUE-E', 'model' => 'Lenovo ThinkPad L14 Ultra 7 255U', 'category' => 'thinkpad-l-series',
             'cpu' => 'Intel Core Ultra 7 255U', 'ram' => '16GB', 'storage' => '512GB SSD',
             'display' => '14"', 'os' => 'DOS', 'warranty' => '3 Years Warranty',
             'status' => 'available', 'price' => 220000, 'use_cases' => ['business', 'enterprise']],

            // ==================== THINKPAD X (X1 Carbon, X13) ====================
            ['sku' => '20QD001JUE', 'model' => 'Lenovo ThinkPad X1 Carbon Gen 7', 'category' => 'thinkpad-x-series',
             'cpu' => 'Intel Core i7-8565U', 'ram' => '16GB', 'storage' => '512GB SSD',
             'display' => '14"', 'os' => 'Windows 10 Pro',
             'status' => 'clearance', 'price' => 145000, 'use_cases' => ['business', 'enterprise']],

            ['sku' => '21CB002SUE', 'model' => 'Lenovo ThinkPad X1 Carbon Gen 10', 'category' => 'thinkpad-x-series',
             'cpu' => 'Intel Core i7-1255U', 'ram' => '16GB', 'storage' => '512GB SSD',
             'display' => '14"', 'os' => 'Windows 11 Pro',
             'status' => 'clearance', 'price' => 200000, 'use_cases' => ['business', 'enterprise']],

            ['sku' => '21KC000YUE', 'model' => 'Lenovo ThinkPad X1 Carbon Gen 12', 'category' => 'thinkpad-x-series',
             'cpu' => 'Intel Core Ultra 7 155U', 'ram' => '16GB', 'storage' => '1TB SSD',
             'display' => '14"', 'os' => 'Windows 11 Pro', 'warranty' => '3 Years Warranty',
             'status' => 'available', 'price' => 290000, 'use_cases' => ['business', 'enterprise']],

            ['sku' => '21NS00M6UE', 'model' => 'Lenovo ThinkPad X1 Carbon Gen 13 Aura Edition', 'category' => 'thinkpad-x-series',
             'cpu' => 'Intel Core Ultra 7 258V', 'ram' => '32GB', 'storage' => '1TB SSD',
             'display' => '14" Touch', 'os' => 'Windows 11 Pro', 'warranty' => '3 Years Warranty',
             'status' => 'new', 'price' => 314000, 'use_cases' => ['business', 'enterprise']],

            ['sku' => '20WLS4E400', 'model' => 'Lenovo ThinkPad X13 Gen 2', 'category' => 'thinkpad-x-series',
             'cpu' => 'Intel Core i5-1135G7', 'ram' => '16GB', 'storage' => '512GB SSD',
             'display' => '13.3"', 'os' => 'Windows 10 Pro',
             'status' => 'clearance', 'price' => 175000, 'use_cases' => ['business', 'enterprise']],

            ['sku' => '21RK007DUE', 'model' => 'Lenovo ThinkPad X13 Gen 6', 'category' => 'thinkpad-x-series',
             'cpu' => 'Intel Core Ultra 7 255U', 'ram' => '16GB', 'storage' => '1TB SSD',
             'display' => '13.3"', 'os' => 'Windows 11 Pro', 'warranty' => '3 Years Warranty',
             'status' => 'available', 'price' => 220000, 'use_cases' => ['business', 'enterprise']],

            ['sku' => '21NU00K0UE', 'model' => 'Lenovo ThinkPad X1 2-in-1 Gen 10', 'category' => 'thinkpad-x-series',
             'cpu' => 'Intel Core Ultra 7 258V', 'ram' => '32GB', 'storage' => '1TB SSD',
             'display' => '14" Convertible Touch', 'os' => 'Windows 11 Pro', 'warranty' => '3 Years Warranty',
             'status' => 'available', 'price' => 342000, 'use_cases' => ['business', 'enterprise']],

            ['sku' => '21NS012YUE', 'model' => 'Lenovo ThinkPad X1 Carbon 13th Gen', 'category' => 'thinkpad-x-series',
             'cpu' => 'Intel Core Ultra 7 258V', 'ram' => '32GB', 'storage' => '1TB SSD',
             'display' => '14" Non-touch', 'os' => 'Windows 11 Pro', 'warranty' => '3 Years Warranty',
             'status' => 'available', 'price' => 323500, 'use_cases' => ['business', 'enterprise']],

            // ==================== GAMING ====================
            ['sku' => '83DG009GUE', 'model' => 'Lenovo Legion 5 16IRX9 RTX 4060', 'category' => 'lenovo-gaming',
             'cpu' => 'Intel Core i7-14650HX', 'ram' => '16GB', 'storage' => '1TB SSD',
             'display' => '16"', 'os' => 'Windows 11 Home',
             'gpu' => 'NVIDIA RTX 4060 8GB GDDR6',
             'status' => 'available', 'price' => 230000, 'use_cases' => ['gaming', 'lifestyle']],

            ['sku' => '82XV00TKUE', 'model' => 'Lenovo LOQ 15IRH8 RTX 4050', 'category' => 'lenovo-gaming',
             'cpu' => 'Intel Core i7-13620H', 'ram' => '16GB', 'storage' => '512GB SSD',
             'display' => '15.6"', 'os' => 'Windows 11 Home',
             'gpu' => 'NVIDIA RTX 4050 6GB GDDR6',
             'status' => 'available', 'price' => 175000, 'use_cases' => ['gaming', 'lifestyle']],

            ['sku' => '83LY00EPUE', 'model' => 'Lenovo Legion 5 15IRX10 RTX 5070', 'category' => 'lenovo-gaming',
             'cpu' => 'Intel Core i9-14900HX', 'ram' => '32GB', 'storage' => '1TB SSD',
             'display' => '15.1"', 'os' => 'Windows 11 Home',
             'gpu' => 'NVIDIA RTX 5070 8GB GDDR7',
             'status' => 'sold_out', 'price' => 265000, 'use_cases' => ['gaming']],

            ['sku' => '20LB000DUE', 'model' => 'Lenovo ThinkPad P52s', 'category' => 'lenovo-gaming',
             'cpu' => 'Intel Core i7-8550U', 'ram' => '16GB', 'storage' => '512GB SSD',
             'display' => '15.6"', 'os' => 'Windows 10 Pro',
             'gpu' => 'NVIDIA Quadro P500 2GB GDDR5',
             'status' => 'clearance', 'price' => 130000, 'use_cases' => ['business', 'enterprise']],

            ['sku' => '83LY00MEU', 'model' => 'Lenovo Legion 5 16IRX9 RTX 5060', 'category' => 'lenovo-gaming',
             'cpu' => 'Intel Core i7-14650HX', 'ram' => '32GB', 'storage' => '512GB SSD',
             'display' => '16"', 'os' => 'Windows 11 Home',
             'gpu' => 'NVIDIA RTX 5060 8GB GDDR6',
             'status' => 'available', 'price' => 235000, 'use_cases' => ['gaming', 'lifestyle']],
        ];
    }
}