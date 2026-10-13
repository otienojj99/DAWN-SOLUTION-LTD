<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->boolean('is_best_seller')
                  ->default(false)
                  ->after('is_featured');

            $table->index(['is_best_seller', 'is_active'], 'idx_products_best_seller');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
             $table->dropIndex('idx_products_best_seller');
            $table->dropColumn('is_best_seller');
        });
    }
};
