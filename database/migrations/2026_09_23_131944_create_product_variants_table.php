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
        Schema::create('product_variants', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')
                  ->constrained('products')
                  ->cascadeOnDelete();

            $table->string('sku', 100)->unique();
            $table->string('name', 200); // "i7 13th Gen / 16GB / 512GB / RTX 4060"

            // Price override (null = use product's price)
            $table->decimal('price', 12, 2)->nullable();
            $table->decimal('compare_at_price', 12, 2)->nullable();
            $table->decimal('price_usd', 12, 2)->nullable();
            $table->decimal('compare_at_price_usd', 12, 2)->nullable();

            // Stock per variant
            $table->integer('stock_quantity')->default(0);
            $table->boolean('allow_backorder')->default(false);

            // Physical
            $table->decimal('weight', 8, 2)->nullable();
            $table->json('dimensions')->nullable();

            // Display
            $table->foreignId('image_id')
                  ->nullable()
                  ->constrained('product_images')
                  ->nullOnDelete();

            $table->boolean('is_default')->default(false);
            $table->boolean('is_active')->default(true);
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();

            $table->index(['product_id', 'is_active', 'sort_order']);
            $table->index(['product_id', 'is_default']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('product_variants');
    }
};
