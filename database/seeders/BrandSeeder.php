<?php

namespace Database\Seeders;

use App\Models\Brands;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class BrandSeeder extends Seeder
{
    public function run(): void
    {
        $brands = [
            'Lenovo', 'Dell', 'HP', 'Asus', 'Apple', 'Acer', 'MSI', 'Samsung',
            'Logitech', 'Jabra', 'JBL', 'Soundcore', 'UGREEN', 'Corsair', 'EVGA',
            'Seasonic', 'Intel', 'AMD', 'NVIDIA', 'Canon', 'Epson', 'Brother',
            'Synology', 'QNAP', 'Cisco', 'TP-Link', 'Ubiquiti', 'Fortinet',
        ];

        foreach ($brands as $i => $name) {
            Brands::updateOrCreate(
                ['slug' => Str::slug($name)],
                [
                    'name'       => $name,
                    'sort_order' => ($i + 1) * 10,
                    'is_active'  => true,
                ]
            );
        }
    }
}