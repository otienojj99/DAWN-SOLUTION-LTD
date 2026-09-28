<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Promotions;
use App\Models\Product;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class PromotionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
          $promos = [
            [
                'name' => 'Clearance Sale',
                'kind' => 'clearance',
                'type' => 'percentage',
                'value' => 25,
                'priority' => 10,
                'badge_label' => 'Clearance Sale',
                'badge_color' => 'red',
                'starts_at' => now()->subDays(3),
                'ends_at' => now()->addDays(30),
            ],
            [
                'name' => 'Black Friday 2026',
                'kind' => 'discount',
                'type' => 'percentage',
                'value' => 15,
                'priority' => 20,
                'badge_label' => 'Black Friday',
                'badge_color' => 'amber',
                'starts_at' => now()->subDay(),
                'ends_at' => now()->addDays(14),
            ],
            [
                'name' => 'Back to School',
                'kind' => 'seasonal',
                'type' => 'fixed',
                'value' => 5000,          // KSh 5,000 off
                'value_usd' => 40,
                'priority' => 30,
                'badge_label' => 'Back to School',
                'badge_color' => 'purple',
                'starts_at' => now(),
                'ends_at' => now()->addDays(60),
            ],
            [
                'name' => 'Flash Friday Deals',
                'kind' => 'flash_sale',
                'type' => 'percentage',
                'value' => 20,
                'priority' => 5,           // highest priority
                'badge_label' => 'Flash Deal',
                'badge_color' => 'orange',
                'starts_at' => now(),
                'ends_at' => now()->addHours(24),
                'max_uses' => 100,
            ],
        ];

        foreach($promos as $data){
            Promotions::updateOrCreate(
                ['slug' => Str::slug($data['name'])],
                $data + ['slug' => Str::slug($data['name'])]
            );
        }
    }
}
