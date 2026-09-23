<?php

namespace Database\Seeders;


use Illuminate\Database\Seeder;
use App\Models\UseCase;
use Illuminate\Support\Str;

class UseCasesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
         $rows = [
            ['name' => 'For Business', 'sort_order' => 10, 'icon' => 'briefcase'],
            ['name' => 'Enterprise',   'sort_order' => 20, 'icon' => 'building'],
            ['name' => 'Education',    'sort_order' => 30, 'icon' => 'academic-cap'],
            ['name' => 'Gaming',       'sort_order' => 40, 'icon' => 'gamepad'],
            ['name' => 'Lifestyle',    'sort_order' => 50, 'icon' => 'sparkles'],
        ];

        foreach($rows as $row){
            UseCase::updateOrCreate(
                ['slug' => Str::slug($row['name'])],
                $row + ['is_active' => true]
            );
        }
    }
}
