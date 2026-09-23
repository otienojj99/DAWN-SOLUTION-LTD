<?php

namespace Database\Seeders;

use App\Models\Attributes;
use App\Models\AttributeValues;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class AttributeSeeder extends Seeder
{
    public function run(): void
    {
        /**
         * structure:
         *  name => [
         *    group, type, is_filterable, is_variant_attribute, unit,
         *    values => [ 'Display' => 'slug', ... ]
         *  ]
         */
        $tree = [
            'CPU Family' => [
                'group' => 'Processor', 'type' => 'select',
                'is_filterable' => true, 'is_variant_attribute' => true,
                'values' => ['Intel Core', 'Intel Ultra', 'Intel Xeon', 'AMD Ryzen', 'AMD Athlon', 'Apple M'],
            ],
            'CPU Model' => [
                'group' => 'Processor', 'type' => 'select',
                'is_filterable' => true, 'is_variant_attribute' => true,
                'values' => [
                    'i3', 'i5', 'i7', 'i9',
                    'Ultra 5', 'Ultra 7', 'Ultra 9',
                    'Ryzen 3', 'Ryzen 5', 'Ryzen 7', 'Ryzen 9',
                ],
            ],
            'CPU Generation' => [
                'group' => 'Processor', 'type' => 'select',
                'is_filterable' => true, 'is_variant_attribute' => true,
                'values' => [
                    '10th Gen', '11th Gen', '12th Gen', '13th Gen', '14th Gen',
                    'Ryzen 5000 Series', 'Ryzen 7000 Series', 'Ryzen 8000 Series',
                ],
            ],
            'GPU' => [
                'group' => 'Graphics', 'type' => 'select',
                'is_filterable' => true, 'is_variant_attribute' => true,
                'values' => [
                    'Integrated Graphics',
                    'GTX 1650', 'GTX 1660 Ti',
                    'RTX 3050', 'RTX 3060', 'RTX 4050', 'RTX 4060', 'RTX 4070', 'RTX 4080', 'RTX 4090',
                    'Radeon RX 6600M', 'Radeon RX 7600M', 'Radeon RX 7700S',
                    'Apple M2 GPU', 'Apple M3 GPU',
                ],
            ],
            'RAM' => [
                'group' => 'Memory', 'type' => 'select',
                'is_filterable' => true, 'is_variant_attribute' => true,
                'unit' => 'GB',
                'values' => ['4GB', '8GB', '16GB', '32GB', '64GB', '128GB'],
            ],
            'Storage' => [
                'group' => 'Storage', 'type' => 'select',
                'is_filterable' => true, 'is_variant_attribute' => true,
                'values' => ['128GB', '256GB', '512GB', '1TB', '2TB', '4TB', '8TB'],
            ],
            'Storage Type' => [
                'group' => 'Storage', 'type' => 'select',
                'is_filterable' => true, 'is_variant_attribute' => true,
                'values' => ['HDD', 'SSD', 'NVMe SSD', 'eMMC'],
            ],
            'Screen Size' => [
                'group' => 'Display', 'type' => 'select',
                'is_filterable' => true, 'is_variant_attribute' => false,
                'unit' => 'inch',
                'values' => ['11.6"', '13.3"', '14"', '15.6"', '16"', '17.3"', '21.5"', '24"', '27"', '32"'],
            ],
            'Color' => [
                'group' => 'Appearance', 'type' => 'color',
                'is_filterable' => true, 'is_variant_attribute' => true,
                'values' => ['Black', 'Silver', 'Gray', 'White', 'Blue', 'Gold', 'Space Gray', 'Midnight'],
            ],
            'Operating System' => [
                'group' => 'Software', 'type' => 'select',
                'is_filterable' => true, 'is_variant_attribute' => false,
                'values' => ['Windows 11 Home', 'Windows 11 Pro', 'Windows 10 Pro', 'macOS', 'Ubuntu', 'No OS'],
            ],
        ];

        foreach ($tree as $name => $meta) {
            $attribute = Attributes::updateOrCreate(
                ['slug' => Str::slug($name)],
                [
                    'name'                 => $name,
                    'group'                => $meta['group'],
                    'type'                 => $meta['type'],
                    'is_filterable'        => $meta['is_filterable'],
                    'is_variant_attribute' => $meta['is_variant_attribute'],
                    'unit'                 => $meta['unit'] ?? null,
                    'is_active'            => true,
                ]
            );

            foreach ($meta['values'] as $i => $value) {
                AttributeValues::updateOrCreate(
                    [
                        'attribute_id' => $attribute->id,
                        'slug'         => Str::slug($value),
                    ],
                    [
                        'value'      => $value,
                        'sort_order' => ($i + 1) * 10,
                        'is_active'  => true,
                    ]
                );
            }
        }
    }
}