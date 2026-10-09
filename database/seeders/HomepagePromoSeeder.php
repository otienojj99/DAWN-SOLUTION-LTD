<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Promotions;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class HomepagePromoSeeder extends Seeder
{
    public function run(): void
    {
        $cards = [
            [
                'name'        => 'Clearance Sale',
                'kind'        => 'clearance',
                'type'        => 'percentage',
                'value'       => 25,
                'priority'    => 10,
                'badge_label' => 'Clearance Sale',
                'badge_color' => 'red',
                'starts_at'   => now()->subDays(3),
                'ends_at'     => now()->addDays(30),
                'homepage' => [
                    'category_slug' => 'laptops',
                    'sort_order'    => 10,
                    'size'          => 'featured',
                    'tag'           => 'Clearance Sale',
                    'title'         => 'Up to 30% Off Business Laptops',
                    'subtitle'      => 'Lenovo ThinkPad, Dell Latitude & HP EliteBook',
                    'cta_label'     => 'Shop Laptops',
                    'image_path'    => 'promotions/laptops-clearance.jpg',
                    'image_alt'     => 'Business laptop on a desk',
                ],
            ],
            [
                'name'        => 'Gaming Desktops Promotion',
                'kind'        => 'seasonal',
                'type'        => 'percentage',
                'value'       => 0,
                'priority'    => 20,
                'badge_label' => 'New Stock',
                'badge_color' => 'blue',
                'starts_at'   => now()->subDay(),
                'ends_at'     => now()->addDays(60),
                'homepage' => [
                    'category_slug' => 'custom-gaming-pcs',
                    'sort_order'    => 20,
                    'size'          => 'standard',
                    'tag'           => 'New Stock',
                    'title'         => 'Gaming Desktops',
                    'subtitle'      => 'RTX-powered rigs, ready to ship',
                    'cta_label'     => 'Explore',
                    'image_path'    => 'promotions/gaming-desktops.jpg',
                    'image_alt'     => 'Gaming desktop PC with RGB lighting',
                ],
            ],
            [
                'name'        => 'Servers & Racks',
                'kind'        => 'seasonal',
                'type'        => 'percentage',
                'value'       => 0,
                'priority'    => 30,
                'badge_label' => 'For Enterprise',
                'badge_color' => 'blue',
                'starts_at'   => now(),
                'ends_at'     => now()->addDays(90),
                'homepage' => [
                    'category_slug' => 'servers-networking',
                    'sort_order'    => 30,
                    'size'          => 'standard',
                    'tag'           => 'For Enterprise',
                    'title'         => 'Servers & Racks',
                    'subtitle'      => 'Dell PowerEdge, HP ProLiant',
                    'cta_label'     => 'Get a Quote',
                    'image_path'    => 'promotions/servers.jpg',
                    'image_alt'     => 'Server rack in a data center',
                ],
            ],
            [
                'name'        => 'Accessories Deal',
                'kind'        => 'discount',
                'type'        => 'percentage',
                'value'       => 10,
                'priority'    => 40,
                'badge_label' => 'Best Deals',
                'badge_color' => 'green',
                'starts_at'   => now(),
                'ends_at'     => now()->addDays(30),
                'homepage' => [
                    'category_slug' => 'accessories',
                    'sort_order'    => 40,
                    'size'          => 'standard',
                    'tag'           => 'Best Deals',
                    'title'         => 'Accessories',
                    'subtitle'      => 'Logitech keyboards, mice & headsets',
                    'cta_label'     => 'Shop Now',
                    'image_path'    => 'promotions/accessories.jpg',
                    'image_alt'     => 'Keyboard and mouse',
                ],
            ],
            [
                'name'        => 'Monitors Discount',
                'kind'        => 'discount',
                'type'        => 'percentage',
                'value'       => 15,
                'priority'    => 50,
                'badge_label' => 'Discounts',
                'badge_color' => 'amber',
                'starts_at'   => now(),
                'ends_at'     => now()->addDays(30),
                'homepage' => [
                    'category_slug' => 'monitors',
                    'sort_order'    => 50,
                    'size'          => 'standard',
                    'tag'           => 'Discounts',
                    'title'         => 'Monitors',
                    'subtitle'      => 'Curved, 4K & gaming displays',
                    'cta_label'     => 'View Range',
                    'image_path'    => 'promotions/monitors.jpg',
                    'image_alt'     => 'Computer monitor on a desk',
                ],
            ],
        ];

        foreach ($cards as $data) {
            $category = Category::where('slug', $data['homepage']['category_slug'])->first();

            if (! $category) {
                $this->command->warn("Category '{$data['homepage']['category_slug']}' not found — skipping '{$data['name']}'");
                continue;
            }

            $promo = Promotions::updateOrCreate(
                ['slug' => Str::slug($data['name'])],
                [
                    'name'                => $data['name'],
                    'kind'                => $data['kind'],
                    'type'                => $data['type'],
                    'value'               => $data['value'],
                    'priority'            => $data['priority'],
                    'badge_label'         => $data['badge_label'],
                    'badge_color'         => $data['badge_color'],
                    'starts_at'           => $data['starts_at'],
                    'ends_at'             => $data['ends_at'],
                    'is_active'           => true,
                    'show_on_storefront'  => true,
                    'show_on_homepage'    => true,
                    'homepage_category_id'=> $category->id,
                    'homepage_sort_order' => $data['homepage']['sort_order'],
                    'homepage_size'       => $data['homepage']['size'],
                    'hero_image_path'     => $data['homepage']['image_path'],
                    'hero_image_alt'      => $data['homepage']['image_alt'],
                    'hero_tag'            => $data['homepage']['tag'],
                    'hero_title'          => $data['homepage']['title'],
                    'hero_subtitle'       => $data['homepage']['subtitle'],
                    'hero_cta_label'      => $data['homepage']['cta_label'],
                ]
            );
        }

        $this->command->info('Homepage promotions seeded.');
    }
}