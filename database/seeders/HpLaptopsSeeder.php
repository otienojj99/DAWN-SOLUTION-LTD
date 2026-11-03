<?php

namespace Database\Seeders;

use App\Models\AttributeValues;
use App\Models\Brands;
use App\Models\Category;
use App\Models\Products;
use App\Models\ProductImages;
use App\Models\Promotions;
use App\Models\UseCase;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class HpLaptopsSeeder extends Seeder
{
    /**
     * Supplier prices + 5% margin. Stock flags honour the sheet's status column.
     */
    public function run(): void
    {
        DB::transaction(function () {
            $this->seed();
        });
    }

    protected function seed(): void
    {
        $brand    = Brands::where('slug', 'hp')->firstOrFail();
        $categories = Category::whereIn('slug', [
            'hp-notebook', 'hp-envy', 'hp-omnibook', 'hp-probook',
            'hp-elitebook', 'hp-pavilion', 'hp-spectre', 'hp-omen',
        ])->pluck('id', 'slug');

        $useCases = UseCase::pluck('id', 'slug');

        $clearancePromo = Promotions::where('kind', 'clearance')
            ->where('is_active', true)
            ->first();

        $products = $this->products();
        $created  = 0;
        $updated  = 0;
        $outOfStock = 0;

        foreach ($products as $row) {
            $categoryId = $categories[$row['category']] ?? null;

            if (! $categoryId) {
                $this->command->warn("Category {$row['category']} missing — skipping {$row['sku']}");
                continue;
            }

            $priceKes = round($row['price'] * 1.05, 2);
            $inStock  = $row['status'] !== 'out_of_stock';
            $stock    = $inStock ? $this->stockForStatus($row['status']) : 0;
            $initial  = $inStock && $row['status'] === 'clearance'
                ? $stock + 30
                : $stock;

            $slug = Str::slug($row['model'] . ' ' . $row['sku']);

            $product = Products::updateOrCreate(
                ['sku' => $row['sku']],
                [
                    'name'                 => $row['model'],
                    'slug'                 => $slug,
                    'type'                 => 'simple',
                    'category_id'          => $categoryId,
                    'brand_id'             => $brand->id,
                    'short_description'    => $this->shortDescription($row),
                    'long_description'     => $this->longDescription($row),
                    'specifications'       => $this->specifications($row),
                    'price'                => $priceKes,
                    'compare_at_price'     => null,
                    'price_usd'            => round($priceKes / config('shop.usd_rate', 130), 2),
                    'base_currency'        => 'KES',
                    'stock_quantity'       => $stock,
                    'initial_stock_quantity' => $initial,
                    'track_inventory'      => true,
                    'allow_backorder'      => $row['status'] === 'expected',
                    'is_active'            => true,
                    'is_featured'          => false,
                    'published_at'         => now(),
                ]
            );

            $product->wasRecentlyCreated ? $created++ : $updated++;

            if (! $inStock) {
                $outOfStock++;
            }

            // Image placeholder — accessor returns null until file exists
            if ($product->images()->count() === 0) {
                ProductImages::create([
                    'product_id' => $product->id,
                    'path'       => 'demo/' . $slug . '.jpg',
                    'alt_text'   => $product->name,
                    'is_primary' => true,
                    'sort_order' => 0,
                ]);
            }

            // Use cases
            $ucIds = collect($row['use_cases'])
                ->map(fn ($slug) => $useCases[$slug] ?? null)
                ->filter()
                ->values()
                ->all();

            if (! empty($ucIds)) {
                $product->useCases()->sync($ucIds);
            }

            // Clearance promotion
            if ($row['status'] === 'clearance' && $clearancePromo) {
                $product->promotions()->syncWithoutDetaching([$clearancePromo->id]);
            }
        }

        $this->command->info("HP laptops seeded: {$created} created, {$updated} updated, {$outOfStock} out of stock.");
    }

    protected function stockForStatus(string $status): int
    {
        return match ($status) {
            'clearance'   => 8,
            'available'   => 15,
            'new'         => 20,
            'expected'    => 5,
            default       => 10,
        };
    }

    protected function shortDescription(array $row): string
    {
        return sprintf(
            '%s | %s RAM | %s | %s | %s',
            $row['cpu'],
            $row['ram'],
            $row['storage'],
            $row['display'],
            $row['os'],
        );
    }

    protected function longDescription(array $row): string
    {
        $warranty = $row['warranty'] ?? '1 Year Warranty';
        $extra    = ! empty($row['extras']) ? ' Extras: ' . implode(', ', $row['extras']) . '.' : '';

        return sprintf(
            "The %s brings %s performance in a %s chassis. With %s RAM and %s storage, "
            . "it is built for professionals who need reliability without compromise. "
            . "%s%s",
            $row['model'],
            $row['cpu'],
            $row['display'],
            $row['ram'],
            $row['storage'],
            $warranty,
            $extra,
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
            // ============================================================
            // HP NOTEBOOK (Consumer)
            // ============================================================
            [
                'sku' => '64G70EA', 'model' => 'HP NoteBook 15 DW3191NIA',
                'category' => 'hp-notebook',
                'cpu' => 'Intel Core i3-1125G4 quad', 'ram' => '4GB', 'storage' => '1TB HDD',
                'display' => '15.6"', 'os' => 'FreeDOS',
                'status' => 'clearance', 'price' => 45000, 'use_cases' => ['lifestyle', 'education'],
            ],
            [
                'sku' => '3B9Y2EA', 'model' => 'HP NoteBook 15 DW3037NIA',
                'category' => 'hp-notebook',
                'cpu' => 'Intel Core i7-1135G7 quad', 'ram' => '8GB', 'storage' => '512GB SSD',
                'display' => '15.6"', 'os' => 'Windows 11 Home',
                'status' => 'clearance', 'price' => 79000, 'use_cases' => ['lifestyle', 'business'],
            ],
            [
                'sku' => '3A9J9EA', 'model' => 'HP NoteBook 15 DW1279NIA',
                'category' => 'hp-notebook',
                'cpu' => 'Intel Core i5-10210U quad', 'ram' => '8GB', 'storage' => '512GB SSD',
                'display' => '15.6"', 'os' => 'FreeDOS',
                'status' => 'clearance', 'price' => 65000, 'use_cases' => ['lifestyle', 'education'],
            ],
            [
                'sku' => '4H2A6EA', 'model' => 'HP NoteBook 15 DW211NIA',
                'category' => 'hp-notebook',
                'cpu' => 'Intel Core i7-1165G7 quad', 'ram' => '8GB', 'storage' => '512GB SSD',
                'display' => '15.6"', 'os' => 'FreeDOS',
                'status' => 'clearance', 'price' => 79000, 'use_cases' => ['lifestyle', 'business'],
            ],
            [
                'sku' => '3B9Q7EA', 'model' => 'HP NoteBook 14s',
                'category' => 'hp-notebook',
                'cpu' => 'Intel Core i7-1165G7 quad', 'ram' => '8GB', 'storage' => '512GB SSD',
                'display' => '14"', 'os' => 'FreeDOS',
                'status' => 'clearance', 'price' => 79000, 'use_cases' => ['lifestyle'],
            ],
            [
                'sku' => '7K167EA', 'model' => 'HP NoteBook 15s fd0211nia',
                'category' => 'hp-notebook',
                'cpu' => 'Intel Core i5-1235U', 'ram' => '8GB', 'storage' => '512GB SSD',
                'display' => '15.6" FHD', 'os' => 'FreeDOS',
                'status' => 'available', 'price' => 65000, 'use_cases' => ['lifestyle', 'education'],
            ],
            [
                'sku' => '8A6B8EA', 'model' => 'HP NoteBook 255 G10 Ryzen 5',
                'category' => 'hp-notebook',
                'cpu' => 'AMD Ryzen 5 7520U', 'ram' => '8GB', 'storage' => '512GB SSD',
                'display' => '15.6" FHD', 'os' => 'FreeDOS', 'gpu' => 'AMD Radeon Graphics',
                'status' => 'new', 'price' => 58000, 'use_cases' => ['lifestyle', 'education'],
            ],

            // ============================================================
            // HP ENVY
            // ============================================================
            [
                'sku' => '61U03EA', 'model' => 'HP Envy 13-BA1045NE',
                'category' => 'hp-envy',
                'cpu' => 'Intel Core i7-1165G7', 'ram' => '8GB', 'storage' => '512GB SSD',
                'display' => '13.3" FHD', 'os' => 'Windows 11 Home',
                'status' => 'out_of_stock', 'price' => 90000, 'use_cases' => ['lifestyle'],
            ],
            [
                'sku' => '61U05EA', 'model' => 'HP Envy 13-BA1067NE',
                'category' => 'hp-envy',
                'cpu' => 'Intel Core i5-1135G7', 'ram' => '8GB', 'storage' => '512GB SSD',
                'display' => '13.3" FHD', 'os' => 'Windows 11 Home',
                'status' => 'out_of_stock', 'price' => 85000, 'use_cases' => ['lifestyle'],
            ],

            // ============================================================
            // HP OMNIBOOK
            // ============================================================
            [
                'sku' => 'B84RBEA', 'model' => 'HP OmniBook Ultra Flip 14 fh0016nia',
                'category' => 'hp-omnibook',
                'cpu' => 'Intel Core Ultra 7', 'ram' => '16GB', 'storage' => '1TB SSD',
                'display' => '14"', 'os' => 'Windows 11 Home', 'warranty' => '3 Years Warranty',
                'status' => 'out_of_stock', 'price' => 210000, 'use_cases' => ['lifestyle', 'business'],
            ],
            [
                'sku' => 'B67DBEA', 'model' => 'HP OmniBook Ultra Flip 14 fh0002nia',
                'category' => 'hp-omnibook',
                'cpu' => 'Intel Core Ultra 7', 'ram' => '16GB', 'storage' => '512GB SSD',
                'display' => '14"', 'os' => 'Windows 11 Home',
                'status' => 'new', 'price' => 197000, 'use_cases' => ['lifestyle', 'business'],
            ],

            // ============================================================
            // HP PROBOOK
            // ============================================================
            [
                'sku' => '816N5EA', 'model' => 'HP ProBook 440 G10',
                'category' => 'hp-probook',
                'cpu' => 'Intel Core i7 12th Gen', 'ram' => '8GB', 'storage' => '512GB SSD',
                'display' => '14"', 'os' => 'FreeDOS',
                'status' => 'clearance', 'price' => 112000, 'use_cases' => ['business'],
            ],
            [
                'sku' => '5Y3T8EA', 'model' => 'HP ProBook 450 G9',
                'category' => 'hp-probook',
                'cpu' => 'Intel Core i5 12th Gen', 'ram' => '8GB', 'storage' => '512GB SSD',
                'display' => '15.6"', 'os' => 'FreeDOS', 'gpu' => 'NVIDIA MX570 2GB',
                'status' => 'clearance', 'price' => 95000, 'use_cases' => ['business'],
            ],
            [
                'sku' => '725J4EA', 'model' => 'HP ProBook 450 G10',
                'category' => 'hp-probook',
                'cpu' => 'Intel Core i5 13th Gen', 'ram' => '8GB', 'storage' => '512GB SSD',
                'display' => '15.6"', 'os' => 'FreeDOS',
                'status' => 'clearance', 'price' => 98500, 'use_cases' => ['business'],
            ],
            [
                'sku' => '9Y7Q2ET', 'model' => 'HP ProBook 440 G11 Ultra 5 8GB',
                'category' => 'hp-probook',
                'cpu' => 'Intel Core Ultra 5 125U', 'ram' => '8GB', 'storage' => '512GB SSD',
                'display' => '14" FHD', 'os' => 'FreeDOS',
                'status' => 'available', 'price' => 125000, 'use_cases' => ['business'],
            ],
            [
                'sku' => 'A22YCEA', 'model' => 'HP ProBook 440 G11 Ultra 5 8GB (2024)',
                'category' => 'hp-probook',
                'cpu' => 'Intel Core Ultra 5 125U', 'ram' => '8GB', 'storage' => '512GB SSD',
                'display' => '14" FHD', 'os' => 'FreeDOS',
                'status' => 'new', 'price' => 125000, 'use_cases' => ['business'],
            ],
            [
                'sku' => 'A38BCET', 'model' => 'HP ProBook 440 G11 Ultra 7 16GB',
                'category' => 'hp-probook',
                'cpu' => 'Intel Core Ultra 7 155U', 'ram' => '16GB', 'storage' => '512GB SSD',
                'display' => '14" FHD', 'os' => 'FreeDOS',
                'status' => 'expected', 'price' => 145000, 'use_cases' => ['business'],
            ],
            [
                'sku' => 'A38B9ET', 'model' => 'HP ProBook 440 G11 Ultra 5 16GB',
                'category' => 'hp-probook',
                'cpu' => 'Intel Core Ultra 5 125U', 'ram' => '16GB', 'storage' => '512GB SSD',
                'display' => '14" FHD', 'os' => 'FreeDOS',
                'status' => 'new', 'price' => 135000, 'use_cases' => ['business'],
            ],
            [
                'sku' => 'CT9P4ET', 'model' => 'HP ProBook 440 G11 Ultra 5 8GB + Case',
                'category' => 'hp-probook',
                'cpu' => 'Intel Core Ultra 5 125U', 'ram' => '8GB', 'storage' => '512GB SSD',
                'display' => '14" FHD', 'os' => 'FreeDOS',
                'extras' => ['Carry Case 15.6"'],
                'status' => 'new', 'price' => 125000, 'use_cases' => ['business'],
            ],
            [
                'sku' => 'CT9P0ET', 'model' => 'HP ProBook 440 G11 Ultra 7 8GB + Case',
                'category' => 'hp-probook',
                'cpu' => 'Intel Core Ultra 7 155U', 'ram' => '8GB', 'storage' => '512GB SSD',
                'display' => '14" FHD', 'os' => 'FreeDOS',
                'extras' => ['Carry Case 15.6"'],
                'status' => 'new', 'price' => 128000, 'use_cases' => ['business'],
            ],
            [
                'sku' => 'CT9P1ET', 'model' => 'HP ProBook 440 G11 Ultra 7 16GB + Case',
                'category' => 'hp-probook',
                'cpu' => 'Intel Core Ultra 7 155U', 'ram' => '16GB', 'storage' => '512GB SSD',
                'display' => '14" FHD', 'os' => 'FreeDOS',
                'extras' => ['Carry Case 15.6"'],
                'status' => 'expected', 'price' => 139000, 'use_cases' => ['business'],
            ],
            [
                'sku' => 'A38F9ET', 'model' => 'HP ProBook 460 G11 Ultra 7 8GB',
                'category' => 'hp-probook',
                'cpu' => 'Intel Core Ultra 7 155U', 'ram' => '8GB', 'storage' => '512GB SSD',
                'display' => '16" FHD', 'os' => 'FreeDOS',
                'status' => 'new', 'price' => 128000, 'use_cases' => ['business'],
            ],
            [
                'sku' => 'A38BGET', 'model' => 'HP ProBook 460 G11 Ultra 5 16GB',
                'category' => 'hp-probook',
                'cpu' => 'Intel Core Ultra 5 125U', 'ram' => '16GB', 'storage' => '512GB SSD',
                'display' => '16" FHD', 'os' => 'FreeDOS',
                'status' => 'new', 'price' => 123000, 'use_cases' => ['business'],
            ],
            [
                'sku' => 'A38FBET', 'model' => 'HP ProBook 460 G11 Ultra 5 8GB',
                'category' => 'hp-probook',
                'cpu' => 'Intel Core Ultra 5 125U', 'ram' => '8GB', 'storage' => '512GB SSD',
                'display' => '16" FHD', 'os' => 'FreeDOS',
                'status' => 'new', 'price' => 106000, 'use_cases' => ['business'],
            ],
            [
                'sku' => 'CT9N7ET', 'model' => 'HP ProBook 460 G11 Ultra 5 16GB + Case',
                'category' => 'hp-probook',
                'cpu' => 'Intel Core Ultra 5 125U', 'ram' => '16GB', 'storage' => '512GB SSD',
                'display' => '16" FHD', 'os' => 'FreeDOS',
                'extras' => ['Carry Case 17.3"'],
                'status' => 'new', 'price' => 123000, 'use_cases' => ['business'],
            ],
            [
                'sku' => 'CT9N1ET', 'model' => 'HP ProBook 460 G11 Ultra 7 16GB + Case',
                'category' => 'hp-probook',
                'cpu' => 'Intel Core Ultra 7 155U', 'ram' => '16GB', 'storage' => '512GB SSD',
                'display' => '16" FHD', 'os' => 'FreeDOS',
                'extras' => ['Carry Case 17.3"'],
                'status' => 'new', 'price' => 139000, 'use_cases' => ['business'],
            ],
            [
                'sku' => 'CT1H8ET', 'model' => 'HP ProBook 4 G1i 14" Ultra 7 16GB',
                'category' => 'hp-probook',
                'cpu' => 'Intel Core Ultra 7 255U', 'ram' => '16GB', 'storage' => '512GB SSD',
                'display' => '14" FHD', 'os' => 'FreeDOS',
                'status' => 'new', 'price' => 145000, 'use_cases' => ['business'],
            ],
            [
                'sku' => 'CT1H7ET', 'model' => 'HP ProBook 4 G1i 14" Ultra 5 16GB',
                'category' => 'hp-probook',
                'cpu' => 'Intel Core Ultra 5 255U', 'ram' => '16GB', 'storage' => '512GB SSD',
                'display' => '14" FHD', 'os' => 'FreeDOS',
                'status' => 'new', 'price' => 125000, 'use_cases' => ['business'],
            ],
            [
                'sku' => 'CT1G9ET', 'model' => 'HP ProBook 4 G1i 16" Ultra 7 16GB',
                'category' => 'hp-probook',
                'cpu' => 'Intel Core Ultra 7 255U', 'ram' => '16GB', 'storage' => '512GB SSD',
                'display' => '16" FHD', 'os' => 'FreeDOS',
                'status' => 'new', 'price' => 145000, 'use_cases' => ['business'],
            ],
            [
                'sku' => 'CT9P2ET', 'model' => 'HP ProBook 440 G11 Ultra 5 16GB + Case',
                'category' => 'hp-probook',
                'cpu' => 'Intel Core Ultra 5 125U', 'ram' => '16GB', 'storage' => '512GB SSD',
                'display' => '14" FHD', 'os' => 'FreeDOS',
                'extras' => ['Carry Case 15.6"'],
                'status' => 'new', 'price' => 125000, 'use_cases' => ['business'],
            ],
            [
                'sku' => 'CW9E4AT', 'model' => 'HP ProBook 4 G1iR 14" Ultra 5',
                'category' => 'hp-probook',
                'cpu' => 'Intel Core Ultra 5 120U', 'ram' => '8GB', 'storage' => '512GB SSD',
                'display' => '14" FHD', 'os' => 'FreeDOS',
                'status' => 'new', 'price' => 125000, 'use_cases' => ['business'],
            ],

            // ============================================================
            // HP ELITEBOOK
            // ============================================================
            [
                'sku' => '2Y2R9EA', 'model' => 'HP EliteBook 830 G8 i5',
                'category' => 'hp-elitebook',
                'cpu' => 'Intel Core i5 11th Gen', 'ram' => '16GB', 'storage' => '512GB SSD',
                'display' => '13.3"', 'os' => 'Windows 10 Pro',
                'status' => 'clearance', 'price' => 149000, 'use_cases' => ['business', 'enterprise'],
            ],
            [
                'sku' => '336K5EA', 'model' => 'HP EliteBook 830 G8 i7',
                'category' => 'hp-elitebook',
                'cpu' => 'Intel Core i7 11th Gen', 'ram' => '16GB', 'storage' => '512GB SSD',
                'display' => '13.3"', 'os' => 'Windows 10 Pro',
                'status' => 'clearance', 'price' => 156000, 'use_cases' => ['business', 'enterprise'],
            ],
            [
                'sku' => '6V443AV', 'model' => 'HP EliteBook 830 G10 x360 i7',
                'category' => 'hp-elitebook',
                'cpu' => 'Intel Core i7 13th Gen', 'ram' => '16GB', 'storage' => '1TB SSD',
                'display' => '13.3" Touch', 'os' => 'FreeDOS',
                'status' => 'clearance', 'price' => 230000, 'use_cases' => ['business', 'enterprise'],
            ],
            [
                'sku' => '6V447AV', 'model' => 'HP EliteBook 830 G10 x360 2-in-1 i7',
                'category' => 'hp-elitebook',
                'cpu' => 'Intel Core i7 13th Gen', 'ram' => '16GB', 'storage' => '512GB SSD',
                'display' => '13.3" Touch', 'os' => 'Windows 11 Pro',
                'extras' => ['HP Prelude Pro 15.6" Bag'],
                'status' => 'available', 'price' => 230000, 'use_cases' => ['business', 'enterprise'],
            ],
            [
                'sku' => '5P747EA', 'model' => 'HP EliteBook 830 G9 i7',
                'category' => 'hp-elitebook',
                'cpu' => 'Intel Core i7 12th Gen', 'ram' => '16GB', 'storage' => '1TB SSD',
                'display' => '13.3"', 'os' => 'Windows 10 Pro',
                'status' => 'clearance', 'price' => 153000, 'use_cases' => ['business', 'enterprise'],
            ],
            [
                'sku' => '6V5W7AV', 'model' => 'HP EliteBook 840 G10 i5',
                'category' => 'hp-elitebook',
                'cpu' => 'Intel Core i5 13th Gen', 'ram' => '16GB', 'storage' => '512GB SSD',
                'display' => '14"', 'os' => 'Windows 11 Pro',
                'status' => 'clearance', 'price' => 145000, 'use_cases' => ['business', 'enterprise'],
            ],
            [
                'sku' => '336H9EA', 'model' => 'HP EliteBook 850 G8 i5',
                'category' => 'hp-elitebook',
                'cpu' => 'Intel Core i5 11th Gen', 'ram' => '16GB', 'storage' => '512GB SSD',
                'display' => '15.6"', 'os' => 'Windows 10 Pro',
                'status' => 'clearance', 'price' => 113500, 'use_cases' => ['business', 'enterprise'],
            ],
            [
                'sku' => '5P6Q0EA', 'model' => 'HP EliteBook 1040 G9 i7',
                'category' => 'hp-elitebook',
                'cpu' => 'Intel Core i7 12th Gen', 'ram' => '16GB', 'storage' => '512GB SSD',
                'display' => '14"', 'os' => 'Windows 11 Pro',
                'status' => 'clearance', 'price' => 185000, 'use_cases' => ['business', 'enterprise'],
            ],
            [
                'sku' => '8L3P5AV', 'model' => 'HP EliteBook 830 G11 x360 Ultra 7',
                'category' => 'hp-elitebook',
                'cpu' => 'Intel Core Ultra 7 155U (Evo)', 'ram' => '32GB', 'storage' => '512GB SSD',
                'display' => '13.3" Touch', 'os' => 'Windows 11 Pro', 'warranty' => '3 Years Warranty',
                'status' => 'available', 'price' => 250000, 'use_cases' => ['business', 'enterprise'],
            ],
            [
                'sku' => '901D7AV', 'model' => 'HP EliteBook 640 G11 Ultra 7',
                'category' => 'hp-elitebook',
                'cpu' => 'Intel Core Ultra 7 155U', 'ram' => '16GB', 'storage' => '512GB SSD',
                'display' => '14"', 'os' => 'FreeDOS',
                'status' => 'out_of_stock', 'price' => 138000, 'use_cases' => ['business', 'enterprise'],
            ],
            [
                'sku' => 'AP6K1AV', 'model' => 'HP EliteBook 8 G1i 14" Ultra 7 255H',
                'category' => 'hp-elitebook',
                'cpu' => 'Intel Core Ultra 7 255H', 'ram' => '16GB', 'storage' => '512GB SSD',
                'display' => '14"', 'os' => 'Windows 11 Pro',
                'status' => 'new', 'price' => 235000, 'use_cases' => ['business', 'enterprise'],
            ],
            [
                'sku' => 'AQ0H8AV', 'model' => 'HP EliteBook 8 G1i 16" Ultra 7 255H',
                'category' => 'hp-elitebook',
                'cpu' => 'Intel Core Ultra 7 255H', 'ram' => '16GB', 'storage' => '512GB SSD',
                'display' => '16"', 'os' => 'Windows 11 Pro',
                'status' => 'new', 'price' => 227000, 'use_cases' => ['business', 'enterprise'],
            ],
            [
                'sku' => 'AV3Q4AV', 'model' => 'HP EliteBook 6 G1i 14" Ultra 7 255U',
                'category' => 'hp-elitebook',
                'cpu' => 'Intel Core Ultra 7 255U', 'ram' => '8GB', 'storage' => '512GB SSD',
                'display' => '14"', 'os' => 'Windows 11 Pro',
                'status' => 'new', 'price' => 150000, 'use_cases' => ['business', 'enterprise'],
            ],

            // ============================================================
            // HP PAVILION
            // ============================================================
            [
                'sku' => '781V2EA', 'model' => 'HP Pavilion x360 i7',
                'category' => 'hp-pavilion',
                'cpu' => 'Intel Core i7 12th Gen', 'ram' => '16GB', 'storage' => '512GB SSD',
                'display' => '14" Touch', 'os' => 'Windows 11 Home',
                'status' => 'clearance', 'price' => 115000, 'use_cases' => ['lifestyle'],
            ],

            // ============================================================
            // HP SPECTRE
            // ============================================================
            [
                'sku' => '7P0Q7UA', 'model' => 'HP Spectre x360 14 ef2013dx',
                'category' => 'hp-spectre',
                'cpu' => 'Intel Core i7 1355U', 'ram' => '16GB', 'storage' => '512GB SSD',
                'display' => '13.5" WUXGA+ Touch', 'os' => 'Windows 11 Home',
                'status' => 'available', 'price' => 185000, 'use_cases' => ['lifestyle', 'business'],
            ],
            [
                'sku' => 'A23XJEA', 'model' => 'HP Spectre x360 16 aa0000nia',
                'category' => 'hp-spectre',
                'cpu' => 'Intel Core Ultra 7 155H', 'ram' => '16GB', 'storage' => '1TB SSD',
                'display' => '16" WQXGA+ Touch', 'os' => 'Windows 11 Home',
                'status' => 'new', 'price' => 235000, 'use_cases' => ['lifestyle', 'business'],
            ],

            // ============================================================
            // HP OMEN
            // ============================================================
            [
                'sku' => 'B7HV2UA', 'model' => 'HP Omen Max 16 ah0043dx',
                'category' => 'hp-omen',
                'cpu' => 'Intel Core Ultra 9 275HX', 'ram' => '32GB', 'storage' => '2TB SSD',
                'display' => '16"', 'os' => 'Windows 11',
                'gpu' => 'NVIDIA GeForce RTX 5080 16GB GDDR7',
                'status' => 'new', 'price' => 455000, 'use_cases' => ['gaming', 'lifestyle'],
            ],
        ];
    }
}