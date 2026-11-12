<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::table('brands')->insertOrIgnore([
            'name' => 'Targus',
            'slug' => 'targus',
            'sort_order' => 290,
            'is_active' => true,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $brandId = DB::table('brands')->where('slug', 'targus')->value('id');

        if ($brandId === null) {
            if (DB::connection()->pretending()) {
                return;
            }

            throw new RuntimeException('Unable to find or create the Targus brand.');
        }

        DB::table('products')
            ->where('sku', 'TAR-BP-156')
            ->whereNull('brand_id')
            ->update(['brand_id' => $brandId]);
    }

    public function down(): void
    {
        $brandId = DB::table('brands')->where('slug', 'targus')->value('id');

        if ($brandId !== null) {
            DB::table('products')
                ->where('sku', 'TAR-BP-156')
                ->where('brand_id', $brandId)
                ->update(['brand_id' => null]);
        }
    }
};
