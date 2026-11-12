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

class DellLaptopsSeeder extends Seeder
{
    public function run(): void
    {
        DB::transaction(fn () => $this->seed());
    }

    protected function seed(): void
    {
        $brand = Brands::where('slug', 'dell')->firstOrFail();

        $categories = Category::whereIn('slug', [
            'dell-chromebook', 'dell-latitude', 'dell-vostro', 'dell-pro',
            'dell-pro-plus', 'dell-pro-max', 'dell-g-series',
            'dell-inspiron', 'dell-xps',
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

            $priceKes = round($row['price'] * 1.05, 2);
            $inStock  = $row['status'] !== 'out_of_stock';
            $stock    = $inStock ? $this->stockForStatus($row['status']) : 0;
            $initial  = $inStock && in_array($row['status'], ['clearance', 'price_drop'], true)
                ? $stock + 30
                : max(1, $stock);

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

            if (in_array($row['status'], ['clearance', 'price_drop'], true) && $clearancePromo) {
                $product->promotions()->syncWithoutDetaching([$clearancePromo->id]);
            }
        }

        $this->command->info("Dell laptops seeded: {$created} created, {$updated} updated, {$outOfStock} out of stock.");
    }

    protected function stockForStatus(string $status): int
    {
        return match ($status) {
            'clearance', 'price_drop' => 8,
            'available'               => 15,
            'new'                     => 20,
            'expected'                => 5,
            default                   => 10,
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
        $gpu = ! empty($row['gpu']) ? " Graphics: {$row['gpu']}." : '';

        return sprintf(
            'The %s delivers %s performance in a %s chassis. With %s RAM and %s storage, '
            . 'it handles demanding business workloads reliably.%s %s',
            $row['model'], $row['cpu'], $row['display'], $row['ram'], $row['storage'], $gpu, $warranty,
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

        return $specs;
    }

    protected function products(): array
    {
        return [
            // ===== CHROMEBOOKS =====
            ['sku' => 'CMB-3110-1-BT0', 'model' => 'Dell Chromebook 3110', 'category' => 'dell-chromebook',
             'cpu' => 'Intel Celeron N4500', 'ram' => '4GB', 'storage' => '32GB eMMC',
             'display' => '11.6"', 'os' => 'Chrome OS',
             'status' => 'clearance', 'price' => 43000, 'use_cases' => ['education', 'lifestyle']],

            // ===== LATITUDES =====
            ['sku' => '30W5N', 'model' => 'Dell Latitude 3420', 'category' => 'dell-latitude',
             'cpu' => 'Intel Core i3-1115G4', 'ram' => '8GB', 'storage' => '256GB SSD',
             'display' => '14" FHD', 'os' => 'Windows 10 Pro',
             'status' => 'clearance', 'price' => 64000, 'use_cases' => ['business']],

            ['sku' => 'CDP66-I7', 'model' => 'Dell Latitude 3440 i7', 'category' => 'dell-latitude',
             'cpu' => 'Intel Core i7-1355U', 'ram' => '16GB', 'storage' => '512GB SSD',
             'display' => '14" FHD', 'os' => 'Windows 11 Pro',
             'status' => 'price_drop', 'price' => 137500, 'use_cases' => ['business', 'enterprise']],

            ['sku' => '05A61', 'model' => 'Dell Latitude 3440 i7 Ubuntu', 'category' => 'dell-latitude',
             'cpu' => 'Intel Core i7-1355U', 'ram' => '16GB', 'storage' => '512GB SSD',
             'display' => '14" FHD', 'os' => 'Ubuntu',
             'status' => 'price_drop', 'price' => 135000, 'use_cases' => ['business', 'enterprise']],

            ['sku' => 'LAT-3520-21-BTX', 'model' => 'Dell Latitude 3520 i7', 'category' => 'dell-latitude',
             'cpu' => 'Intel Core i7-1165G7', 'ram' => '8GB', 'storage' => '512GB SSD',
             'display' => '15.6" FHD', 'os' => 'Windows 10 Pro',
             'status' => 'clearance', 'price' => 113000, 'use_cases' => ['business']],

            ['sku' => 'LAT-3520-0003', 'model' => 'Dell Latitude 3520 i5', 'category' => 'dell-latitude',
             'cpu' => 'Intel Core i5-1165G7', 'ram' => '8GB', 'storage' => '512GB SSD',
             'display' => '15.6" FHD', 'os' => 'Windows 10 Pro',
             'status' => 'clearance', 'price' => 102000, 'use_cases' => ['business']],

            ['sku' => 'LAT-3540-3-UBU', 'model' => 'Dell Latitude 3540 i7', 'category' => 'dell-latitude',
             'cpu' => 'Intel Core i7-1355U', 'ram' => '16GB', 'storage' => '512GB SSD',
             'display' => '15.6" FHD', 'os' => 'Ubuntu',
             'status' => 'clearance', 'price' => 125000, 'use_cases' => ['business', 'enterprise']],

            ['sku' => 'LAT-3450-002-I5', 'model' => 'Dell Latitude 3450 i5', 'category' => 'dell-latitude',
             'cpu' => 'Intel Core i5-1335U', 'ram' => '16GB', 'storage' => '512GB SSD',
             'display' => '14" FHD', 'os' => 'Ubuntu',
             'status' => 'new', 'price' => 125000, 'use_cases' => ['business', 'enterprise']],

            ['sku' => 'TV885', 'model' => 'Dell Latitude 5340', 'category' => 'dell-latitude',
             'cpu' => 'Intel Core i5-1345U', 'ram' => '16GB', 'storage' => '512GB SSD',
             'display' => '13.3" FHD', 'os' => 'Windows 11 Pro',
             'status' => 'clearance', 'price' => 138000, 'use_cases' => ['business', 'enterprise']],

            ['sku' => 'LAT-5350-001-UB', 'model' => 'Dell Latitude 5350 Ultra 7', 'category' => 'dell-latitude',
             'cpu' => 'Intel Core Ultra 7-165U', 'ram' => '16GB', 'storage' => '512GB SSD',
             'display' => '13.3" FHD', 'os' => 'Ubuntu',
             'status' => 'out_of_stock', 'price' => 179000, 'use_cases' => ['business', 'enterprise']],

            ['sku' => 'LAT-5350-002-UB', 'model' => 'Dell Latitude 5350 Ultra 5', 'category' => 'dell-latitude',
             'cpu' => 'Intel Core Ultra 5-125U', 'ram' => '16GB', 'storage' => '512GB SSD',
             'display' => '13.3" FHD', 'os' => 'Ubuntu',
             'status' => 'new', 'price' => 158000, 'use_cases' => ['business', 'enterprise']],

            ['sku' => 'LAT-5350-006-XCTO', 'model' => 'Dell Latitude 5350 Ultra 7 (Win)', 'category' => 'dell-latitude',
             'cpu' => 'Intel Core Ultra 7-165U', 'ram' => '16GB', 'storage' => '512GB SSD',
             'display' => '13.3" FHD', 'os' => 'Windows 11 Pro',
             'status' => 'new', 'price' => 189000, 'use_cases' => ['business', 'enterprise']],

            ['sku' => 'LAT-5350-W11P', 'model' => 'Dell Latitude 5350 Ultra 5 (Win)', 'category' => 'dell-latitude',
             'cpu' => 'Intel Core Ultra 5-125U', 'ram' => '16GB', 'storage' => '512GB SSD',
             'display' => '13.3" FHD', 'os' => 'Windows 11 Pro',
             'status' => 'new', 'price' => 161000, 'use_cases' => ['business', 'enterprise']],

            ['sku' => '368G3', 'model' => 'Dell Latitude 5430 i5', 'category' => 'dell-latitude',
             'cpu' => 'Intel Core i5-1235U', 'ram' => '16GB', 'storage' => '512GB SSD',
             'display' => '14" FHD', 'os' => 'Windows 11 Pro',
             'status' => 'new', 'price' => 132000, 'use_cases' => ['business', 'enterprise']],

            ['sku' => 'RCHX6', 'model' => 'Dell Latitude 5430 i7', 'category' => 'dell-latitude',
             'cpu' => 'Intel Core i7-1255U', 'ram' => '16GB', 'storage' => '512GB SSD',
             'display' => '14" FHD', 'os' => 'Ubuntu',
             'status' => 'new', 'price' => 148000, 'use_cases' => ['business', 'enterprise']],

            ['sku' => '47VKD-W', 'model' => 'Dell Latitude 5440 i5', 'category' => 'dell-latitude',
             'cpu' => 'Intel Core i5-1335U', 'ram' => '16GB', 'storage' => '512GB SSD',
             'display' => '14" FHD', 'os' => 'Windows 11 Pro',
             'status' => 'clearance', 'price' => 135000, 'use_cases' => ['business', 'enterprise']],

            ['sku' => 'LAT-5550-U-001', 'model' => 'Dell Latitude 5550 Ultra 7', 'category' => 'dell-latitude',
             'cpu' => 'Intel Core Ultra 7-155U', 'ram' => '16GB', 'storage' => '512GB SSD',
             'display' => '15.6" FHD', 'os' => 'Ubuntu',
             'status' => 'new', 'price' => 158000, 'use_cases' => ['business', 'enterprise']],

            ['sku' => 'LAT-5550-004-UB', 'model' => 'Dell Latitude 5550 Ultra 5', 'category' => 'dell-latitude',
             'cpu' => 'Intel Core Ultra 5-125U', 'ram' => '16GB', 'storage' => '512GB SSD',
             'display' => '15.6" FHD', 'os' => 'Ubuntu',
             'status' => 'new', 'price' => 135000, 'use_cases' => ['business', 'enterprise']],

            ['sku' => 'LAT-5450-U-002', 'model' => 'Dell Latitude 5450 Ultra 7', 'category' => 'dell-latitude',
             'cpu' => 'Intel Core Ultra 7-155U', 'ram' => '16GB', 'storage' => '512GB SSD',
             'display' => '14" FHD', 'os' => 'Ubuntu',
             'status' => 'out_of_stock', 'price' => 160000, 'use_cases' => ['business', 'enterprise']],

            ['sku' => 'LAT-5520-00013-BLK', 'model' => 'Dell Latitude 5520', 'category' => 'dell-latitude',
             'cpu' => 'Intel Core i5-1145G7', 'ram' => '16GB', 'storage' => '512GB SSD',
             'display' => '15.6" FHD', 'os' => 'Windows 11 Pro',
             'status' => 'clearance', 'price' => 129000, 'use_cases' => ['business', 'enterprise']],

            ['sku' => 'LAT-5530-006-BTO', 'model' => 'Dell Latitude 5530', 'category' => 'dell-latitude',
             'cpu' => 'Intel Core i5-1235U', 'ram' => '16GB', 'storage' => '512GB SSD',
             'display' => '15.6" FHD', 'os' => 'Windows 11 Pro',
             'status' => 'clearance', 'price' => 119500, 'use_cases' => ['business', 'enterprise']],

            ['sku' => 'LAT-5540-6-XCTO', 'model' => 'Dell Latitude 5540 i7 Win', 'category' => 'dell-latitude',
             'cpu' => 'Intel Core i7-1335U', 'ram' => '16GB', 'storage' => '512GB SSD',
             'display' => '15.6" FHD', 'os' => 'Windows 11 Pro',
             'status' => 'clearance', 'price' => 160000, 'use_cases' => ['business', 'enterprise']],

            ['sku' => 'LAT-5540-7-UBU', 'model' => 'Dell Latitude 5540 i5 Ubuntu', 'category' => 'dell-latitude',
             'cpu' => 'Intel Core i5-1335U', 'ram' => '16GB', 'storage' => '512GB SSD',
             'display' => '15.6" FHD', 'os' => 'Ubuntu',
             'status' => 'clearance', 'price' => 131500, 'use_cases' => ['business', 'enterprise']],

            ['sku' => 'LAT-5540-8-UBU', 'model' => 'Dell Latitude 5540 i7 Ubuntu', 'category' => 'dell-latitude',
             'cpu' => 'Intel Core i7-1355U', 'ram' => '16GB', 'storage' => '512GB SSD',
             'display' => '15.6" FHD', 'os' => 'Ubuntu',
             'status' => 'clearance', 'price' => 155000, 'use_cases' => ['business', 'enterprise']],

            ['sku' => '74VVF', 'model' => 'Dell Latitude 7200 2-in-1', 'category' => 'dell-latitude',
             'cpu' => 'Intel Core i7-8665U', 'ram' => '16GB', 'storage' => '512GB SSD',
             'display' => '12.3" FHD Touch', 'os' => 'Windows 10 Pro',
             'status' => 'clearance', 'price' => 151000, 'use_cases' => ['business', 'enterprise']],

            ['sku' => 'LAT-7420-00012-GLD', 'model' => 'Dell Latitude 7420', 'category' => 'dell-latitude',
             'cpu' => 'Intel Core i5-1145G7', 'ram' => '8GB', 'storage' => '512GB SSD',
             'display' => '14" FHD', 'os' => 'Windows 10 Pro',
             'status' => 'clearance', 'price' => 108000, 'use_cases' => ['business', 'enterprise']],

            ['sku' => 'KJYR2', 'model' => 'Dell Latitude 7440', 'category' => 'dell-latitude',
             'cpu' => 'Intel Core i7-1355U', 'ram' => '16GB', 'storage' => '512GB SSD',
             'display' => '14" FHD', 'os' => 'Windows 11 Pro', 'warranty' => '3 Years Warranty',
             'status' => 'clearance', 'price' => 100000, 'use_cases' => ['business', 'enterprise']],

            // ===== VOSTRO =====
            ['sku' => 'VOS-3400-00005-NB', 'model' => 'Dell Vostro 3400 i5 4GB', 'category' => 'dell-vostro',
             'cpu' => 'Intel Core i5-1135G7', 'ram' => '4GB', 'storage' => '512GB SSD',
             'display' => '14" FHD', 'os' => 'Ubuntu',
             'status' => 'clearance', 'price' => 68000, 'use_cases' => ['business']],

            ['sku' => 'VOS-3400-00018-NB', 'model' => 'Dell Vostro 3400 i5 8GB', 'category' => 'dell-vostro',
             'cpu' => 'Intel Core i5-1135G7', 'ram' => '8GB', 'storage' => '512GB SSD',
             'display' => '14" FHD', 'os' => 'Ubuntu',
             'status' => 'clearance', 'price' => 78000, 'use_cases' => ['business']],

            ['sku' => 'VOS-3400-00014-NB', 'model' => 'Dell Vostro 3400 i5 8GB + HDD', 'category' => 'dell-vostro',
             'cpu' => 'Intel Core i5-1135G7', 'ram' => '8GB', 'storage' => '256GB SSD + 1TB HDD',
             'display' => '14" FHD', 'os' => 'Ubuntu', 'gpu' => '2GB Graphics',
             'status' => 'clearance', 'price' => 78000, 'use_cases' => ['business']],

            ['sku' => 'VOS-3400-0019-NB', 'model' => 'Dell Vostro 3400 i5 Win', 'category' => 'dell-vostro',
             'cpu' => 'Intel Core i5-1135G7', 'ram' => '8GB', 'storage' => '512GB SSD',
             'display' => '14" FHD', 'os' => 'Windows 11 Home',
             'status' => 'clearance', 'price' => 75000, 'use_cases' => ['business']],

            ['sku' => 'VOS-3420-5-UBUN', 'model' => 'Dell Vostro 3420', 'category' => 'dell-vostro',
             'cpu' => 'Intel Core i7-1255U', 'ram' => '16GB', 'storage' => '512GB SSD',
             'display' => '14" FHD', 'os' => 'Ubuntu',
             'status' => 'clearance', 'price' => 81000, 'use_cases' => ['business']],

            ['sku' => 'N6501VN3500EMEA03', 'model' => 'Dell Vostro 3500 i3', 'category' => 'dell-vostro',
             'cpu' => 'Intel Core i3-1115G4', 'ram' => '4GB', 'storage' => '256GB SSD',
             'display' => '15.6"', 'os' => 'Ubuntu',
             'status' => 'clearance', 'price' => 45000, 'use_cases' => ['business', 'education']],

            ['sku' => 'VOS-3510-0001', 'model' => 'Dell Vostro 3510', 'category' => 'dell-vostro',
             'cpu' => 'Intel Core i5-1135G7', 'ram' => '8GB', 'storage' => '512GB SSD',
             'display' => '15.6" FHD', 'os' => 'Ubuntu', 'gpu' => '2GB Graphics',
             'status' => 'clearance', 'price' => 83000, 'use_cases' => ['business']],

            ['sku' => 'VOS-3500-00012-NB', 'model' => 'Dell Vostro 3500 i7', 'category' => 'dell-vostro',
             'cpu' => 'Intel Core i7-1165G7', 'ram' => '8GB', 'storage' => '512GB SSD',
             'display' => '15.6" FHD', 'os' => 'Ubuntu', 'gpu' => '2GB Graphics',
             'status' => 'clearance', 'price' => 88000, 'use_cases' => ['business']],

            ['sku' => 'VOS-3510-26', 'model' => 'Dell Vostro 3510 i7 Win', 'category' => 'dell-vostro',
             'cpu' => 'Intel Core i7-1165G7', 'ram' => '8GB', 'storage' => '512GB SSD',
             'display' => '15.6" FHD', 'os' => 'Windows 10 Pro',
             'status' => 'clearance', 'price' => 88000, 'use_cases' => ['business']],

            // ===== PRO =====
            ['sku' => '6XJOC', 'model' => 'Dell Pro 14 PC14250 Core 5 16GB', 'category' => 'dell-pro',
             'cpu' => 'Intel Core 5 120U', 'ram' => '16GB', 'storage' => '512GB SSD',
             'display' => '14" FHD', 'os' => 'Ubuntu',
             'status' => 'available', 'price' => 161000, 'use_cases' => ['business', 'enterprise']],

            ['sku' => '6XJ0C', 'model' => 'Dell Pro 14 PC14250 Core 5 8GB', 'category' => 'dell-pro',
             'cpu' => 'Intel Core 5 120U', 'ram' => '8GB', 'storage' => '512GB SSD',
             'display' => '14" FHD', 'os' => 'Ubuntu',
             'status' => 'available', 'price' => 151500, 'use_cases' => ['business', 'enterprise']],

            ['sku' => 'J17GG-16', 'model' => 'Dell Pro 14 PC14250 Core 7 16GB', 'category' => 'dell-pro',
             'cpu' => 'Intel Core 7 255U', 'ram' => '16GB', 'storage' => '512GB SSD',
             'display' => '14" FHD', 'os' => 'Ubuntu',
             'status' => 'available', 'price' => 185000, 'use_cases' => ['business', 'enterprise']],

            ['sku' => 'J17GG-8', 'model' => 'Dell Pro 14 PC14250 Core 7 8GB', 'category' => 'dell-pro',
             'cpu' => 'Intel Core 7 255U', 'ram' => '8GB', 'storage' => '512GB SSD',
             'display' => '14" FHD', 'os' => 'Ubuntu',
             'status' => 'available', 'price' => 168000, 'use_cases' => ['business', 'enterprise']],

            ['sku' => 'PC14250U716GUS', 'model' => 'Dell Pro 14 PC14250 Ultra 7', 'category' => 'dell-pro',
             'cpu' => 'Intel Core Ultra 7 255U', 'ram' => '16GB', 'storage' => '512GB SSD',
             'display' => '14" FHD+', 'os' => 'Ubuntu',
             'status' => 'available', 'price' => 185000, 'use_cases' => ['business', 'enterprise']],

            ['sku' => 'BTO109_PC14250', 'model' => 'Dell Pro 14 PC14250 Ultra 7 Win', 'category' => 'dell-pro',
             'cpu' => 'Intel Core Ultra 7 255U', 'ram' => '16GB', 'storage' => '512GB SSD',
             'display' => '14" FHD+', 'os' => 'Windows 11 Pro',
             'status' => 'available', 'price' => 198000, 'use_cases' => ['business', 'enterprise']],

            ['sku' => 'PC14250U58GAR', 'model' => 'Dell Pro 14 PC14250 Ultra 5', 'category' => 'dell-pro',
             'cpu' => 'Intel Core Ultra 5 235U', 'ram' => '8GB', 'storage' => '512GB SSD',
             'display' => '14" FHD+', 'os' => 'Ubuntu',
             'status' => 'available', 'price' => 148500, 'use_cases' => ['business', 'enterprise']],

            ['sku' => 'NBPRO14-203-UB', 'model' => 'Dell Pro 14 PC14250 (NBPRO)', 'category' => 'dell-pro',
             'cpu' => 'Intel Core Ultra 7 255U', 'ram' => '16GB', 'storage' => '512GB SSD',
             'display' => '14" FHD', 'os' => 'Ubuntu',
             'status' => 'available', 'price' => 185500, 'use_cases' => ['business', 'enterprise']],

            ['sku' => '210-BTGW-AMDULCA', 'model' => 'Dell Pro 14 Essential AMD', 'category' => 'dell-pro',
             'cpu' => 'AMD Ryzen AI 7 350', 'ram' => '16GB', 'storage' => '512GB SSD',
             'display' => '14"', 'os' => 'Ubuntu', 'gpu' => 'Radeon 860M',
             'status' => 'available', 'price' => 165500, 'use_cases' => ['business', 'enterprise']],

            ['sku' => '210-BTGV-I5U-LCA', 'model' => 'Dell Pro 14 Essential Intel', 'category' => 'dell-pro',
             'cpu' => 'Intel Core 5 220U', 'ram' => '16GB', 'storage' => '512GB SSD',
             'display' => '14"', 'os' => 'Ubuntu',
             'status' => 'available', 'price' => 153000, 'use_cases' => ['business', 'enterprise']],

            ['sku' => '210-BWMQ-U7WLCA', 'model' => 'Dell Pro 7 13 2-in-1 Ultra 7 vPro', 'category' => 'dell-pro',
             'cpu' => 'Intel Core Ultra 7 365 vPro', 'ram' => '16GB', 'storage' => '512GB SSD',
             'display' => '13"', 'os' => 'Windows 11 Pro',
             'status' => 'available', 'price' => 296000, 'use_cases' => ['business', 'enterprise']],

            // ===== PRO PLUS =====
            ['sku' => 'XCTO_PB14250_EMEA_U5', 'model' => 'Dell Pro 14 Plus PB14250 Ultra 5 8GB', 'category' => 'dell-pro-plus',
             'cpu' => 'Intel Core Ultra 5 235U', 'ram' => '8GB', 'storage' => '512GB SSD',
             'display' => '14" FHD+', 'os' => 'Ubuntu',
             'status' => 'available', 'price' => 166500, 'use_cases' => ['business', 'enterprise']],

            ['sku' => 'NBPRO14+202-UB', 'model' => 'Dell Pro 14 Plus PB14250 Ultra 5 16GB', 'category' => 'dell-pro-plus',
             'cpu' => 'Intel Core Ultra 5 235U', 'ram' => '16GB', 'storage' => '512GB SSD',
             'display' => '14" FHD+', 'os' => 'Ubuntu',
             'status' => 'available', 'price' => 175500, 'use_cases' => ['business', 'enterprise']],

            ['sku' => 'PB14250U716GUS', 'model' => 'Dell Pro 14 Plus PB14250 Ultra 7', 'category' => 'dell-pro-plus',
             'cpu' => 'Intel Core Ultra 7 255U', 'ram' => '16GB', 'storage' => '512GB SSD',
             'display' => '14" FHD', 'os' => 'Ubuntu',
             'status' => 'available', 'price' => 220000, 'use_cases' => ['business', 'enterprise']],

            ['sku' => '210-BPDR-U7U-LCA', 'model' => 'Dell Pro 14 Plus PB14250 Ultra 7 (16GB)', 'category' => 'dell-pro-plus',
             'cpu' => 'Intel Core Ultra 7 255U', 'ram' => '16GB', 'storage' => '512GB SSD',
             'display' => '14"', 'os' => 'Ubuntu',
             'status' => 'available', 'price' => 213000, 'use_cases' => ['business', 'enterprise']],

            ['sku' => '210-BPDR-U7U-LCA1', 'model' => 'Dell Pro 14 Plus PB14250 Ultra 7 (32GB/1TB)', 'category' => 'dell-pro-plus',
             'cpu' => 'Intel Core Ultra 7 255U', 'ram' => '32GB', 'storage' => '1TB SSD',
             'display' => '14"', 'os' => 'Ubuntu',
             'status' => 'available', 'price' => 295000, 'use_cases' => ['business', 'enterprise']],

            ['sku' => '210-BPDN-U7W-LCA', 'model' => 'Dell Pro 14 Plus PB14250 Ultra 7 vPro 2-in-1', 'category' => 'dell-pro-plus',
             'cpu' => 'Intel Core Ultra 7 266V vPro', 'ram' => '16GB', 'storage' => '512GB SSD',
             'display' => '14" Touch 2-in-1', 'os' => 'Windows 11 Pro',
             'status' => 'available', 'price' => 226500, 'use_cases' => ['business', 'enterprise']],

            ['sku' => '210-BPFF-U7W-LCA', 'model' => 'Dell Pro 14 Premium Ultra 7 vPro', 'category' => 'dell-pro-plus',
             'cpu' => 'Intel Core Ultra 7 266V vPro', 'ram' => '16GB', 'storage' => '512GB SSD',
             'display' => '14"', 'os' => 'Windows 11 Pro',
             'status' => 'available', 'price' => 267500, 'use_cases' => ['business', 'enterprise']],

            // ===== PRO MAX =====
            ['sku' => '210-BPVP', 'model' => 'Dell Pro Max MC14250 Ultra 7 vPro', 'category' => 'dell-pro-max',
             'cpu' => 'Intel Core Ultra 7 255H vPro', 'ram' => '16GB', 'storage' => '512GB SSD',
             'display' => '14" FHD', 'os' => 'Windows 11 Pro', 'warranty' => '3 Years Warranty',
             'status' => 'available', 'price' => 238000, 'use_cases' => ['business', 'enterprise']],

            ['sku' => '210-BPBS', 'model' => 'Dell Pro Max MC14250 Core 5', 'category' => 'dell-pro-max',
             'cpu' => 'Intel Core 5 120U', 'ram' => '16GB', 'storage' => '512GB SSD',
             'display' => '14" FHD', 'os' => 'Windows 11 Pro',
             'status' => 'available', 'price' => 185000, 'use_cases' => ['business', 'enterprise']],

            ['sku' => '210-BRXT-U9NVW-LCA', 'model' => 'Dell Pro Max 16 Plus MB16250 Ultra 9 RTX PRO', 'category' => 'dell-pro-max',
             'cpu' => 'Intel Core Ultra 9 285HX', 'ram' => '32GB', 'storage' => '1TB SSD',
             'display' => '16"', 'os' => 'Windows 11 Pro', 'warranty' => '3 Years Warranty',
             'gpu' => 'NVIDIA RTX PRO 3000 Blackwell 12GB GDDR7',
             'status' => 'available', 'price' => 595000, 'use_cases' => ['business', 'enterprise', 'gaming']],

            // ===== G SERIES =====
            ['sku' => '210-BGJW', 'model' => 'Dell G15 5530', 'category' => 'dell-g-series',
             'cpu' => 'Intel Core i5-13450HX', 'ram' => '16GB', 'storage' => '1TB SSD',
             'display' => '15.6" FHD', 'os' => 'Ubuntu', 'gpu' => 'NVIDIA RTX 4050 6GB',
             'status' => 'new', 'price' => 192000, 'use_cases' => ['gaming', 'lifestyle']],

            // ===== INSPIRON =====
            ['sku' => 'INS-5482-00005-UGR', 'model' => 'Dell Inspiron 5482 2-in-1', 'category' => 'dell-inspiron',
             'cpu' => 'Intel Core i5-8265U', 'ram' => '8GB', 'storage' => '512GB SSD',
             'display' => '14" FHD Touch x360', 'os' => 'Windows 10 Home',
             'status' => 'clearance', 'price' => 79000, 'use_cases' => ['lifestyle']],

            ['sku' => 'INS-GAMING-G3-2', 'model' => 'Dell Inspiron Gaming G3 3500', 'category' => 'dell-inspiron',
             'cpu' => 'Intel Core i7-10750H', 'ram' => '16GB', 'storage' => '512GB SSD',
             'display' => '15.6" FHD', 'os' => 'Windows 10 Home',
             'gpu' => 'NVIDIA GTX 1660 Ti 6GB GDDR6',
             'status' => 'clearance', 'price' => 1497000, 'use_cases' => ['gaming', 'lifestyle']],

            // ===== XPS =====
            ['sku' => '210-BNHC-U7W-LCA', 'model' => 'Dell XPS 13 9350 Ultra 7', 'category' => 'dell-xps',
             'cpu' => 'Intel Core Ultra 7', 'ram' => '16GB', 'storage' => '512GB SSD',
             'display' => '13.3" Touch', 'os' => 'Windows 11 Pro', 'warranty' => '3 Years Warranty',
             'status' => 'out_of_stock', 'price' => 335000, 'use_cases' => ['business', 'lifestyle']],
        ];
    }
}