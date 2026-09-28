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
        Schema::create('product_promotions', function (Blueprint $table) {
             $table->id();
            $table->foreignId('product_id')
                  ->constrained('products')
                  ->cascadeOnDelete();
            $table->foreignId('promotion_id')
                  ->constrained('promotions')
                  ->cascadeOnDelete();

            // Optional: override the promotion's default value for this product
            $table->decimal('override_value', 12, 2)->nullable();
            $table->decimal('override_value_usd', 12, 2)->nullable();

            // Optional: per-product use limit
            $table->unsignedInteger('max_uses_per_product')->nullable();
            $table->unsignedInteger('uses_count')->default(0);

            $table->timestamps();

            $table->unique(['product_id', 'promotion_id'], 'uq_product_promotion');
            $table->index('promotion_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('product_promotions');
    }
};
