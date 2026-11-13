<?php

namespace Database\Seeders;

use App\Models\Brands;
use App\Models\Category;
use App\Models\Products;
use App\Models\ProductImages;
use App\Models\UseCase;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class LogitechVideoConferencingSeeder extends Seeder
{
    protected float $marginRate = 0.10;

    public function run(): void
    {
        DB::transaction(fn () => $this->seed());
    }

    protected function seed(): void
    {
        $brand    = Brands::where('slug', 'logitech')->firstOrFail();
        $category = Category::where('path', 'accessories/video-conferencing/logitech')->firstOrFail();
        $useCases = UseCase::pluck('id', 'slug');

        $created = 0;
        $updated = 0;

        foreach ($this->products() as $row) {
            $priceKes = round($row['price'] * (1 + $this->marginRate), 2);
            $stock    = 10;

            $slug = Str::slug($row['name'] . ' ' . $row['sku']);

            $product = Products::updateOrCreate(
                ['sku' => $row['sku']],
                [
                    'name'              => $row['name'],
                    'slug'              => $slug,
                    'type'              => 'simple',
                    'category_id'       => $category->id,
                    'brand_id'          => $brand->id,
                    'short_description' => $this->shortDescription($row),
                    'long_description'  => $this->longDescription($row),
                    'specifications'    => $this->specifications($row),
                    'price'             => $priceKes,
                    'compare_at_price'  => null,
                    'price_usd'         => round($priceKes / config('shop.usd_rate', 130), 2),
                    'base_currency'     => 'KES',
                    'stock_quantity'    => $stock,
                    'initial_stock_quantity' => $stock,
                    'track_inventory'   => true,
                    'allow_backorder'   => false,
                    'is_active'         => true,
                    'is_featured'       => false,
                    'published_at'      => now(),
                ]
            );

            $product->wasRecentlyCreated ? $created++ : $updated++;

            if ($product->images()->count() === 0) {
                ProductImages::create([
                    'product_id' => $product->id,
                    'path'       => 'demo/' . $slug . '.jpg',
                    'alt_text'   => $product->name,
                    'is_primary' => true,
                    'sort_order' => 0,
                ]);
            }

            $tagIds = array_filter([
                $useCases['business'] ?? null,
                $useCases['enterprise'] ?? null,
            ]);
            if (! empty($tagIds)) {
                $product->useCases()->sync($tagIds);
            }
        }

        $this->command->info("Logitech video conferencing seeded: {$created} created, {$updated} updated.");
    }

    protected function shortDescription(array $row): string
    {
        return sprintf('%s | %s', $row['model'], $row['type']);
    }

    protected function longDescription(array $row): string
    {
        return sprintf(
            'The Logitech %s is a professional-grade %s for modern meeting rooms. '
            . 'Designed for hybrid teams and enterprise deployments. Genuine stock, backed by Logitech warranty.',
            $row['model'],
            strtolower($row['type']),
        );
    }

    protected function specifications(array $row): array
    {
        return [
            ['label' => 'Brand', 'value' => 'Logitech'],
            ['label' => 'Model', 'value' => $row['model']],
            ['label' => 'Type',  'value' => $row['type']],
        ];
    }

    protected function products(): array
    {
        return [
            // ============ SYSTEMS & BARS ============
            ['sku' => '960-001057', 'name' => 'Logitech Group Video Conferencing System', 'model' => 'Group', 'type' => 'Video Conferencing System', 'price' => 69500],
            ['sku' => '960-001102', 'name' => 'Logitech MeetUp Video Conference Camera', 'model' => 'MeetUp', 'type' => 'Conference Camera System', 'price' => 69000],
            ['sku' => '960-001242', 'name' => 'Logitech Rally Plus Ultra-HD Conference Cam Black', 'model' => 'Rally Plus', 'type' => 'Video Conferencing System', 'price' => 280000],
            ['sku' => '960-001312', 'name' => 'Logitech Rally Bar All-In-One Video Conferencing System', 'model' => 'Rally Bar', 'type' => 'Video Bar System', 'price' => 379000],
            ['sku' => '960-001340', 'name' => 'Logitech Rally Bar Mini All-in-one Video Bar Small/Medium Rooms Graphite', 'model' => 'Rally Bar Mini', 'type' => 'Video Bar System', 'price' => 335000],
            ['sku' => '960-001341', 'name' => 'Logitech Rally Bar Mini All-in-one Video Bar Small/Medium Rooms Graphite (Bundle)', 'model' => 'Rally Bar Mini', 'type' => 'Video Bar System', 'price' => 335000],
            ['sku' => '960-001681', 'name' => 'Logitech MeetUp 2 Graphite USB Video Conferencing Camera', 'model' => 'MeetUp 2', 'type' => 'Conference Camera System', 'price' => 120500],

            // ============ CAMERAS ============
            ['sku' => '960-001186', 'name' => 'Logitech PTZ Pro 2 Video Conference Camera & Remote', 'model' => 'PTZ Pro 2', 'type' => 'PTZ Camera', 'price' => 62500],
            ['sku' => '960-001332', 'name' => 'Logitech Scribe Whiteboard Camera for Video Conferencing Rooms', 'model' => 'Scribe', 'type' => 'Whiteboard Camera', 'price' => 105000],
            ['sku' => '960-001510', 'name' => 'Logitech Sight Tabletop Multi-Participant Conference Webcam', 'model' => 'Sight', 'type' => 'Tabletop Conference Camera', 'price' => 274000],

            // ============ CONTROLLERS ============
            ['sku' => '939-001950', 'name' => 'Logitech Tap Touch Controller with Cat5e Kit', 'model' => 'Tap Touch', 'type' => 'Room Controller', 'price' => 118000],
            ['sku' => '952-000085', 'name' => 'Logitech Tap IP Meeting Room Touch Controller', 'model' => 'Tap IP', 'type' => 'Room Controller', 'price' => 90000],
            ['sku' => '952-000091', 'name' => 'Logitech Tap Scheduler for Meeting Rooms', 'model' => 'Tap Scheduler', 'type' => 'Scheduling Display', 'price' => 90000],

            // ============ AUDIO ============
            ['sku' => '960-001230', 'name' => 'Logitech Rally Speaker for Video Conferencing', 'model' => 'Rally Speaker', 'type' => 'Conference Speaker', 'price' => 32000],
            ['sku' => '989-000171', 'name' => 'Logitech Group Expansion Mic Black', 'model' => 'Group Expansion Mic', 'type' => 'Expansion Microphone', 'price' => 28000],
            ['sku' => '989-000405', 'name' => 'Logitech Expansion Mic for MeetUp', 'model' => 'MeetUp Expansion Mic', 'type' => 'Expansion Microphone', 'price' => 28500],
            ['sku' => '989-000430', 'name' => 'Logitech Rally Expansion Mic Pod Black', 'model' => 'Rally Mic Pod', 'type' => 'Expansion Microphone', 'price' => 50500],

            // ============ CABLES ============
            ['sku' => '939-001490', 'name' => 'Logitech Group 15m Extender Cable', 'model' => 'Group Extender Cable', 'type' => 'Extender Cable 15m', 'price' => 24500],
            ['sku' => '939-001799', 'name' => 'Logitech Strong USB 3.1 Cable Graphite 10m', 'model' => 'Strong USB 3.1', 'type' => 'USB Cable 10m', 'price' => 32000],
            ['sku' => '939-001802', 'name' => 'Logitech 25 Meter Strong USB Cable', 'model' => 'Strong USB 25m', 'type' => 'USB Cable 25m', 'price' => 72000],
            ['sku' => '950-000005', 'name' => 'Logitech MeetUp Mic Extension Cable 10m', 'model' => 'MeetUp Mic Extension', 'type' => 'Microphone Extension Cable', 'price' => 16500],
            ['sku' => '952-000181', 'name' => 'Logitech MicPod CAT Coupler', 'model' => 'MicPod CAT Coupler', 'type' => 'Cable Coupler', 'price' => 13000],

            // ============ MOUNTS ============
            ['sku' => '952-000041', 'name' => 'Logitech TV Mount for Video Bars', 'model' => 'TV Mount', 'type' => 'TV Mount', 'price' => 23000],
            ['sku' => '952-000044', 'name' => 'Logitech Wall Mount for Video Bars', 'model' => 'Wall Mount', 'type' => 'Wall Mount', 'price' => 15000],
            ['sku' => '952-000002', 'name' => 'Logitech Rally Mic Pod Mount', 'model' => 'Rally Mic Pod Mount', 'type' => 'Microphone Mount', 'price' => 9500],
            ['sku' => '952-000080', 'name' => 'Logitech Tap Riser Mount Video Conferencing Controller Mounting Kit', 'model' => 'Tap Riser Mount', 'type' => 'Controller Mount', 'price' => 27000],
            ['sku' => '939-001644', 'name' => 'Logitech Rally Mounting Kit for Rally Video Conferencing System', 'model' => 'Rally Mounting Kit', 'type' => 'System Mounting Kit', 'price' => 35000],

            // ============ COMPUTE ============
            ['sku' => 'GQE20C', 'name' => 'Logitech CTL Google Meet Compute System GQE20C Tap', 'model' => 'CTL Google Meet Compute', 'type' => 'Compute System', 'price' => 176000],
        ];
    }
}