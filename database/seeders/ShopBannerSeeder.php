<?php

namespace Database\Seeders;

use App\Models\ShopBanner;
use Illuminate\Database\Seeder;

class ShopBannerSeeder extends Seeder
{
    public function run(): void
    {
        $banners = [
            [
                'slug'         => 'bundle-office-setup',
                'kind'         => 'bundle',
                'tag'          => 'Bundle deal',
                'icon'         => 'package',
                'title'        => 'Complete Office Setup Bundle',
                'description'  => 'Latitude laptop, 24 inch monitor, wireless keyboard and mouse, and a laptop bag, ready to deploy for new hires.',
                'highlight'    => 'Save KES 12,500',
                'cta_label'    => 'Shop the bundle',
                'href'         => '/shop/products/best-deals',
                'image_path'   => 'banners/office-setup.jpg',
                'image_alt'    => 'Keyboard and mouse on a work desk',
                'gradient_key' => 'blue-deep',
                'sort_order'   => 10,
            ],
            [
                'slug'         => 'discount-thinkpad',
                'kind'         => 'discount',
                'tag'          => 'Limited discount',
                'icon'         => 'percent',
                'title'        => 'Up to 25% off ThinkPad & IdeaPad',
                'description'  => 'Business-grade Lenovo laptops at their lowest prices this quarter. Genuine stock with full warranty.',
                'highlight'    => 'Up to 25% off',
                'cta_label'    => 'See Lenovo laptops',
                'href'         => '/shop/laptops/lenovo-laptops',
                'image_path'   => 'banners/thinkpad.jpg',
                'image_alt'    => 'Business laptop open on a desk',
                'gradient_key' => 'purple',
                'sort_order'   => 20,
            ],
            [
                'slug'         => 'promo-corporate-bulk',
                'kind'         => 'promotion',
                'tag'          => 'Corporate promotion',
                'icon'         => 'gift',
                'title'        => 'Buying 10 or more? Get a bulk price',
                'description'  => 'Companies, schools and NGOs get special pricing, free configuration and delivery on orders of ten machines or more.',
                'highlight'    => 'Free setup on 10+',
                'cta_label'    => 'Request a quotation',
                'href'         => '/#quote',
                'image_path'   => 'banners/corporate.jpg',
                'image_alt'    => 'Row of monitors in an office',
                'gradient_key' => 'green',
                'sort_order'   => 30,
            ],
            [
                'slug'         => 'seasonal-festive',
                'kind'         => 'seasonal',
                'tag'          => 'Seasonal sale',
                'icon'         => 'sparkles',
                'title'        => 'End-of-Year Tech Sale',
                'description'  => 'Laptops, desktops and accessories marked down for the festive season. Pay with M-PESA and we deliver nationwide.',
                'highlight'    => 'Prices cut across the shop',
                'cta_label'    => 'Shop the sale',
                'href'         => '/shop/deals/clearance',
                'image_path'   => 'banners/seasonal.jpg',
                'image_alt'    => 'Gaming laptop with a lit keyboard',
                'gradient_key' => 'orange',
                'sort_order'   => 40,
            ],
        ];

        foreach ($banners as $row) {
            ShopBanner::updateOrCreate(
                ['slug' => $row['slug']],
                $row + ['is_active' => true]
            );
        }

        $this->command->info('Shop banners seeded: ' . count($banners));
    }
}