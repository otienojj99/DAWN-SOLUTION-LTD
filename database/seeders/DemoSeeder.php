<?php

namespace Database\Seeders;

use App\Models\AttributeValues;
use App\Models\Brands;
use App\Models\BundleItems;
use App\Models\Category;
use App\Models\Products;
use App\Models\ProductImages;
use App\Models\ProductVariants;
use App\Models\Promotions;
use App\Models\UseCase;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class DemoSeeder extends Seeder
{
    public function run(): void
    {
        DB::transaction(function () {
            $this->seedSimpleLaptops();
            $this->seedVariableLaptops();
            $this->seedAccessories();
            $this->seedBundles();
            $this->attachPromotions();
        });
    }

    /* =========================================================
     |  SIMPLE LAPTOPS
     ========================================================= */

    protected function seedSimpleLaptops(): void
    {
        $lenovoLaptops   = Category::where('slug', 'lenovo-laptops')->value('id');
        $dellLaptops     = Category::where('slug', 'dell-laptops')->value('id');
        $hpLaptops       = Category::where('slug', 'hp-laptops')->value('id');
        $asusApple       = Category::where('slug', 'asus-apple')->value('id');

        $lenovo = Brands::where('slug', 'lenovo')->value('id');
        $dell   = Brands::where('slug', 'dell')->value('id');
        $hp     = Brands::where('slug', 'hp')->value('id');
        $asus   = Brands::where('slug', 'asus')->value('id');
        $apple  = Brands::where('slug', 'apple')->value('id');

        $business = UseCase::where('slug', 'for-business')->value('id');
        $gaming   = UseCase::where('slug', 'gaming')->value('id');
        $lifestyle= UseCase::where('slug', 'lifestyle')->value('id');
        $student  = UseCase::where('slug', 'education')->value('id');
        $enterprise = UseCase::where('slug', 'enterprise')->value('id');

        $rows = [
            [
                'name' => 'Lenovo ThinkPad T14 Gen 4',
                'sku'  => 'LEN-TP-T14-G4',
                'category_id' => $lenovoLaptops,
                'brand_id'    => $lenovo,
                'price'       => 145000,
                'compare_at_price' => 165000,
                'price_usd'   => 1115,
                'stock'       => 12,
                'use_cases'   => [$business, $enterprise],
                'brand_slug'  => 'lenovo',
            ],
            [
                'name' => 'Dell Latitude 5440',
                'sku'  => 'DELL-LAT-5440',
                'category_id' => $dellLaptops,
                'brand_id'    => $dell,
                'price'       => 138000,
                'compare_at_price' => 155000,
                'price_usd'   => 1060,
                'stock'       => 8,
                'use_cases'   => [$business, $enterprise],
                'brand_slug'  => 'dell',
            ],
            [
                'name' => 'HP EliteBook 840 G10',
                'sku'  => 'HP-EB-840-G10',
                'category_id' => $hpLaptops,
                'brand_id'    => $hp,
                'price'       => 152000,
                'compare_at_price' => 172000,
                'price_usd'   => 1170,
                'stock'       => 6,
                'use_cases'   => [$business, $enterprise],
                'brand_slug'  => 'hp',
            ],
            [
                'name' => 'HP Omen 16 Gaming Laptop',
                'sku'  => 'HP-OMEN-16',
                'category_id' => $hpLaptops,
                'brand_id'    => $hp,
                'price'       => 189000,
                'compare_at_price' => 215000,
                'price_usd'   => 1450,
                'stock'       => 10,
                'use_cases'   => [$gaming, $lifestyle],
                'brand_slug'  => 'hp',
            ],
            [
                'name' => 'Asus ROG Strix G16',
                'sku'  => 'ASUS-ROG-G16',
                'category_id' => $asusApple,
                'brand_id'    => $asus,
                'price'       => 225000,
                'compare_at_price' => 245000,
                'price_usd'   => 1730,
                'stock'       => 5,
                'use_cases'   => [$gaming, $lifestyle],
                'brand_slug'  => 'asus',
            ],
            [
                'name' => 'Apple MacBook Air M3 13"',
                'sku'  => 'APPLE-MBA-M3-13',
                'category_id' => $asusApple,
                'brand_id'    => $apple,
                'price'       => 175000,
                'compare_at_price' => null,
                'price_usd'   => 1345,
                'stock'       => 15,
                'use_cases'   => [$lifestyle, $business],
                'brand_slug'  => 'apple',
            ],
            [
                'name' => 'Lenovo IdeaPad Slim 3',
                'sku'  => 'LEN-IP-SLIM3',
                'category_id' => $lenovoLaptops,
                'brand_id'    => $lenovo,
                'price'       => 58000,
                'compare_at_price' => 68000,
                'price_usd'   => 445,
                'stock'       => 25,
                'use_cases'   => [$student, $lifestyle],
                'brand_slug'  => 'lenovo',
            ],
            [
                'name' => 'Dell Vostro 3520',
                'sku'  => 'DELL-VOS-3520',
                'category_id' => $dellLaptops,
                'brand_id'    => $dell,
                'price'       => 62000,
                'compare_at_price' => null,
                'price_usd'   => 475,
                'stock'       => 20,
                'use_cases'   => [$student, $business],
                'brand_slug'  => 'dell',
            ],
        ];

        foreach ($rows as $i => $row) {
            $product = Products::create([
                'name'              => $row['name'],
                'slug'              => Str::slug($row['name']),
                'sku'               => $row['sku'],
                'type'              => 'simple',
                'category_id'       => $row['category_id'],
                'brand_id'          => $row['brand_id'],
                'short_description' => 'High-performance laptop from ' . ucfirst($row['brand_slug']),
                'long_description'  => 'Detailed description for ' . $row['name'],
                'price'             => $row['price'],
                'compare_at_price'  => $row['compare_at_price'],
                'cost_price'        => $row['price'] * 0.82,
                'price_usd'         => $row['price_usd'],
                'stock_quantity'    => $row['stock'],
                'track_inventory'   => true,
                'allow_backorder'   => false,
                'is_active'         => true,
                'is_featured'       => $i < 3,
                'published_at'      => now()->subDays(rand(1, 45)),
            ]);

            // Image
            ProductImages::create([
                'product_id' => $product->id,
                'path'       => 'demo/' . $product->slug . '.jpg',
                'alt_text'   => $product->name,
                'is_primary' => true,
                'sort_order' => 0,
            ]);

            // Use cases
            $product->useCases()->sync($row['use_cases']);
        }
    }

    /* =========================================================
     |  VARIABLE LAPTOPS (with real attribute variants)
     ========================================================= */

    protected function seedVariableLaptops(): void
    {
        $lenovoLaptops = Category::where('slug', 'lenovo-laptops')->value('id');
        $dellLaptops   = Category::where('slug', 'dell-laptops')->value('id');

        $lenovo = Brands::where('slug', 'lenovo')->value('id');
        $dell   = Brands::where('slug', 'dell')->value('id');

        $business   = UseCase::where('slug', 'for-business')->value('id');
        $gaming     = UseCase::where('slug', 'gaming')->value('id');
        $lifestyle  = UseCase::where('slug', 'lifestyle')->value('id');

        // Preload attribute values once
        $getValue = function (string $attributeSlug, string $valueSlug): ?int {
            return AttributeValues::query()
                ->where('slug', $valueSlug)
                ->whereHas('attribute', fn ($q) => $q->where('slug', $attributeSlug))
                ->value('id');
        };

        // Lenovo ThinkPad T14 Gen 4 — variable
        $t14 = Products::create([
            'name'              => 'Lenovo ThinkPad T14 Gen 4 (Configurable)',
            'slug'              => 'lenovo-thinkpad-t14-gen-4-configurable',
            'sku'               => 'LEN-TP-T14-G4-VAR',
            'type'              => 'variable',
            'category_id'       => $lenovoLaptops,
            'brand_id'          => $lenovo,
            'short_description' => 'Business-grade laptop, choice of CPU and RAM.',
            'price'             => 145000,
            'price_usd'         => 1115,
            'stock_quantity'    => 0, // tracked per variant
            'is_active'         => true,
            'published_at'      => now()->subDays(10),
        ]);

        ProductImages::create([
            'product_id' => $t14->id,
            'path'       => 'demo/lenovo-t14-var.jpg',
            'alt_text'   => $t14->name,
            'is_primary' => true,
        ]);

        $t14->useCases()->sync([$business]);

        $t14Variants = [
            ['i5 13th Gen / 16GB / 512GB', 'LEN-T14-I5-16-512', 'i5', '13th-gen', '16gb', '512gb', 145000, 10],
            ['i7 13th Gen / 16GB / 512GB', 'LEN-T14-I7-16-512', 'i7', '13th-gen', '16gb', '512gb', 168000, 6],
            ['i7 13th Gen / 32GB / 1TB',   'LEN-T14-I7-32-1TB', 'i7', '13th-gen', '32gb', '1tb',   195000, 3],
        ];

        foreach ($t14Variants as $idx => [$name, $sku, $cpu, $gen, $ram, $storage, $price, $stock]) {
            $variant = ProductVariants::create([
                'product_id'     => $t14->id,
                'sku'            => $sku,
                'name'           => $name,
                'price'          => $price,
                'stock_quantity' => $stock,
                'is_default'     => $idx === 0,
                'is_active'      => true,
                'sort_order'     => $idx * 10,
            ]);

            $ids = array_filter([
                $getValue('cpu-model', $cpu),
                $getValue('cpu-generation', $gen),
                $getValue('ram', $ram),
                $getValue('storage', $storage),
            ]);

            $variant->attributeValues()->sync($ids);
        }

        // Dell XPS 15 — variable, gaming-ish
        $xps = Products::create([
            'name'              => 'Dell XPS 15 (Configurable)',
            'slug'              => 'dell-xps-15-configurable',
            'sku'               => 'DELL-XPS15-VAR',
            'type'              => 'variable',
            'category_id'       => $dellLaptops,
            'brand_id'          => $dell,
            'short_description' => 'Premium creator and gaming laptop.',
            'price'             => 220000,
            'price_usd'         => 1690,
            'is_active'         => true,
            'published_at'      => now()->subDays(5),
        ]);

        ProductImages::create([
            'product_id' => $xps->id,
            'path'       => 'demo/dell-xps15-var.jpg',
            'alt_text'   => $xps->name,
            'is_primary' => true,
        ]);

        $xps->useCases()->sync([$gaming, $lifestyle]);

        $xpsVariants = [
            ['i7 14th Gen / 16GB / 1TB / RTX 4060',  'DELL-XPS15-I7-16-1TB-4060',  'i7', '14th-gen', '16gb', '1tb', 220000, 4],
            ['i9 14th Gen / 32GB / 1TB / RTX 4070',  'DELL-XPS15-I9-32-1TB-4070',  'i9', '14th-gen', '32gb', '1tb', 295000, 2],
            ['i9 14th Gen / 64GB / 2TB / RTX 4080',  'DELL-XPS15-I9-64-2TB-4080',  'i9', '14th-gen', '64gb', '2tb', 385000, 1],
        ];

        foreach ($xpsVariants as $idx => [$name, $sku, $cpu, $gen, $ram, $storage, $price, $stock]) {
            $variant = ProductVariants::create([
                'product_id'     => $xps->id,
                'sku'            => $sku,
                'name'           => $name,
                'price'          => $price,
                'stock_quantity' => $stock,
                'is_default'     => $idx === 0,
                'is_active'      => true,
                'sort_order'     => $idx * 10,
            ]);

            $ids = array_filter([
                $getValue('cpu-model', $cpu),
                $getValue('cpu-generation', $gen),
                $getValue('ram', $ram),
                $getValue('storage', $storage),
            ]);

            $variant->attributeValues()->sync($ids);
        }
    }

    /* =========================================================
     |  ACCESSORIES
     ========================================================= */

    protected function seedAccessories(): void
    {
        $miceCat   = Category::where('slug', 'mice')->value('id');
        $bagsCat   = Category::where('slug', 'bags-cases')->value('id');

        $logitech = Brands::where('slug', 'logitech')->value('id');
        $targus   = Brands::where('slug', 'targus')->value('id');
        $ugreen   = Brands::where('slug', 'ugreen')->value('id');

        $lifestyle = UseCase::where('slug', 'lifestyle')->value('id');
        $business  = UseCase::where('slug', 'for-business')->value('id');

        $items = [
            [
                'name' => 'Logitech MX Master 3S',
                'sku'  => 'LOG-MX3S',
                'category_id' => $miceCat,
                'brand_id' => $logitech,
                'price' => 14500,
                'price_usd' => 112,
                'stock' => 40,
                'use_cases' => [$lifestyle, $business],
            ],
            [
                'name' => 'Targus 15.6" Laptop Backpack',
                'sku'  => 'TAR-BP-156',
                'category_id' => $bagsCat,
                'brand_id' => $targus,
                'price' => 6500,
                'price_usd' => 50,
                'stock' => 60,
                'use_cases' => [$business],
            ],
            [
                'name' => 'UGREEN USB-C Hub 7-in-1',
                'sku'  => 'UGR-HUB-7',
                'category_id' => $bagsCat,
                'brand_id' => $ugreen,
                'price' => 4800,
                'price_usd' => 37,
                'stock' => 80,
                'use_cases' => [$business, $lifestyle],
            ],
        ];

        foreach ($items as $row) {
            $product = Products::create([
                'name'              => $row['name'],
                'slug'              => Str::slug($row['name']),
                'sku'               => $row['sku'],
                'type'              => 'simple',
                'category_id'       => $row['category_id'],
                'brand_id'          => $row['brand_id'],
                'price'             => $row['price'],
                'price_usd'         => $row['price_usd'],
                'stock_quantity'    => $row['stock'],
                'is_active'         => true,
                'published_at'      => now()->subDays(rand(1, 30)),
            ]);

            ProductImages::create([
                'product_id' => $product->id,
                'path'       => 'demo/' . $product->slug . '.jpg',
                'alt_text'   => $product->name,
                'is_primary' => true,
            ]);

            $product->useCases()->sync($row['use_cases']);
        }
    }

    /* =========================================================
     |  BUNDLES (Best Deals)
     ========================================================= */

    protected function seedBundles(): void
    {
        $gamingCat = Category::where('slug', 'gaming-laptops')->value('id');
        $hp        = Brands::where('slug', 'hp')->value('id');
        $dell      = Brands::where('slug', 'dell')->value('id');

        $gaming    = UseCase::where('slug', 'gaming')->value('id');
        $lifestyle = UseCase::where('slug', 'lifestyle')->value('id');

        $omen = Products::where('sku', 'HP-OMEN-16')->first();
        $bag  = Products::where('sku', 'TAR-BP-156')->first();
        $hub  = Products::where('sku', 'UGR-HUB-7')->first();

        if ($omen && $bag) {
            $bundle = Products::create([
                'name'              => 'HP Omen 16 + Targus Backpack Combo',
                'slug'              => 'hp-omen-16-targus-backpack-combo',
                'sku'               => 'BUNDLE-OMEN16-BAG',
                'type'              => 'bundle',
                'category_id'       => $gamingCat,
                'brand_id'          => $hp,
                'short_description' => 'Gaming laptop bundled with premium backpack.',
                'long_description'  => 'Save on this bundle vs buying separately.',
                'price'             => 192000,
                'compare_at_price'  => 195500,
                'price_usd'         => 1475,
                'stock_quantity'    => 5,
                'is_active'         => true,
                'is_featured'       => true,
                'published_at'      => now()->subDays(2),
            ]);

            ProductImages::create([
                'product_id' => $bundle->id,
                'path'       => 'demo/bundle-omen16-bag.jpg',
                'alt_text'   => $bundle->name,
                'is_primary' => true,
            ]);

            $bundle->useCases()->sync([$gaming, $lifestyle]);

            BundleItems::create([
                'bundle_product_id'        => $bundle->id,
                'component_product_id'     => $omen->id,
                'quantity'                 => 1,
                'sort_order'               => 10,
                'component_price_snapshot' => $omen->price,
            ]);

            BundleItems::create([
                'bundle_product_id'        => $bundle->id,
                'component_product_id'     => $bag->id,
                'quantity'                 => 1,
                'sort_order'               => 20,
                'component_price_snapshot' => $bag->price,
            ]);
        }

        if ($hub) {
            $bundle2 = Products::create([
                'name'              => 'Logitech MX Master 3S + UGREEN Hub Kit',
                'slug'              => 'logitech-mx-master-ugreen-hub-kit',
                'sku'               => 'BUNDLE-MX3S-HUB',
                'type'              => 'bundle',
                'category_id'       => Category::where('slug', 'mice')->value('id'),
                'brand_id'          => Brands::where('slug', 'logitech')->value('id'),
                'short_description' => 'Work-from-anywhere combo.',
                'price'             => 17500,
                'compare_at_price'  => 19300,
                'price_usd'         => 135,
                'stock_quantity'    => 20,
                'is_active'         => true,
                'published_at'      => now()->subDays(7),
            ]);

            ProductImages::create([
                'product_id' => $bundle2->id,
                'path'       => 'demo/bundle-mx-hub.jpg',
                'alt_text'   => $bundle2->name,
                'is_primary' => true,
            ]);

            $bundle2->useCases()->sync([$lifestyle]);

            BundleItems::create([
                'bundle_product_id'        => $bundle2->id,
                'component_product_id'     => Products::where('sku', 'LOG-MX3S')->value('id'),
                'quantity'                 => 1,
                'sort_order'               => 10,
                'component_price_snapshot' => 14500,
            ]);

            BundleItems::create([
                'bundle_product_id'        => $bundle2->id,
                'component_product_id'     => $hub->id,
                'quantity'                 => 1,
                'sort_order'               => 20,
                'component_price_snapshot' => $hub->price,
            ]);
        }
    }

    /* =========================================================
     |  ATTACH PROMOTIONS
     ========================================================= */

    protected function attachPromotions(): void
    {
        $discount  = Promotions::where('kind', 'discount')->first();
        $clearance = Promotions::where('kind', 'clearance')->first();
        $flash     = Promotions::where('kind', 'flash_sale')->first();
        $seasonal  = Promotions::where('kind', 'seasonal')->first();

        // Clearance: the older IdeaPad + Vostro
        if ($clearance) {
            $clearanceTargets = Products::whereIn('sku', ['LEN-IP-SLIM3', 'DELL-VOS-3520'])->pluck('id');
            if ($clearanceTargets->isNotEmpty()) {
                $clearance->products()->syncWithoutDetaching($clearanceTargets);
            }
        }

        // Discount: HP EliteBook + Lenovo T14
        if ($discount) {
            $discountTargets = Products::whereIn('sku', ['HP-EB-840-G10', 'LEN-TP-T14-G4'])->pluck('id');
            if ($discountTargets->isNotEmpty()) {
                $discount->products()->syncWithoutDetaching($discountTargets);
            }
        }

        // Flash sale: HP Omen
        if ($flash) {
            $omen = Products::where('sku', 'HP-OMEN-16')->value('id');
            if ($omen) {
                $flash->products()->syncWithoutDetaching([$omen]);
            }
        }

        // Seasonal: category-scoped to Asus / Apple
        if ($seasonal) {
            $asusApple = Category::where('slug', 'asus-apple')->value('id');
            if ($asusApple) {
                $seasonal->categories()->syncWithoutDetaching([$asusApple]);
            }
        }
    }
}