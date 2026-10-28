<?php

namespace Database\Seeders;

use App\Models\UseCase;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class UseCasesSeeders extends Seeder
{
    public function run(): void
    {
        $rows = [
            [
                'name'        => 'For Business',
                'description' => 'Reliable laptops, monitors & accessories to keep your team productive',
                'icon'        => 'briefcase',
                'color'       => 'blue',
                'cta_label'   => 'Shop Business',
                'image_path'  => 'use-cases/business.jpg',
                'image_alt'   => 'Business professionals working together',
                'sort_order'  => 10,
            ],
            [
                'name'        => 'Enterprise',
                'description' => 'Servers, networking & infrastructure built to scale with you',
                'icon'        => 'building',
                'color'       => 'blue',
                'cta_label'   => 'Shop Enterprise',
                'image_path'  => 'use-cases/enterprise.jpg',
                'image_alt'   => 'Enterprise server room',
                'sort_order'  => 20,
            ],
            [
                'name'        => 'Education',
                'description' => 'Affordable, durable laptops built for students & educators',
                'icon'        => 'graduation-cap',
                'color'       => 'purple',
                'cta_label'   => 'Shop Education',
                'image_path'  => 'use-cases/education.jpg',
                'image_alt'   => 'Students studying with laptops',
                'sort_order'  => 30,
            ],
            [
                'name'        => 'Gaming',
                'description' => 'High-refresh displays, RTX power & gear built to dominate',
                'icon'        => 'gamepad',
                'color'       => 'orange',
                'cta_label'   => 'Shop Gaming',
                'image_path'  => 'use-cases/gaming.jpg',
                'image_alt'   => 'Gaming setup with RGB lighting',
                'sort_order'  => 40,
            ],
            [
                'name'        => 'Lifestyle',
                'description' => 'Everyday tech that fits the way you live and work',
                'icon'        => 'sparkles',
                'color'       => 'green',
                'cta_label'   => 'Shop Lifestyle',
                'image_path'  => 'use-cases/lifestyle.jpg',
                'image_alt'   => 'Lifestyle workspace',
                'sort_order'  => 50,
            ],
        ];

        foreach ($rows as $row) {
            UseCase::updateOrCreate(
                ['slug' => Str::slug($row['name'])],
                $row + ['is_active' => true]
            );
        }
    }
}