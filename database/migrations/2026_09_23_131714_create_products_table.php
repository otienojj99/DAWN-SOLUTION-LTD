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
        Schema::create('products', function (Blueprint $table) {
            $table->id();

            // Identity
            $table->string('name', 200);
            $table->string('slug', 220)->unique();
            $table->string('sku', 100)->unique();
            $table->string('barcode', 100)->nullable();
            $table->enum('type', ['simple', 'variable', 'bundle'])->default('simple');

            // Relations
            $table->foreignId('category_id')
                  ->constrained('categories')
                  ->restrictOnDelete();
            $table->foreignId('brand_id')
                  ->nullable()
                  ->constrained('brands')
                  ->nullOnDelete();

            // Content
            $table->string('short_description', 500)->nullable();
            $table->longText('long_description')->nullable();
            $table->json('specifications')->nullable(); // display-only specs (never filtered)

            // Pricing — base currency is KES
            $table->decimal('price', 12, 2);                          // base price in KES
            $table->decimal('compare_at_price', 12, 2)->nullable();   // "was" price for strikethrough
            $table->decimal('cost_price', 12, 2)->nullable();         // internal margin
            $table->decimal('price_usd', 12, 2)->nullable();          // manual USD override
            $table->decimal('compare_at_price_usd', 12, 2)->nullable();
            $table->char('base_currency', 3)->default('KES');

            // Stock
            $table->integer('stock_quantity')->default(0);
            $table->integer('low_stock_threshold')->default(5);
            $table->boolean('track_inventory')->default(true);
            $table->boolean('allow_backorder')->default(false);

            // Shipping
            $table->decimal('weight', 8, 2)->nullable();
            $table->json('dimensions')->nullable(); // {"length":..,"width":..,"height":..}

            // Visibility
            $table->boolean('is_active')->default(true);
            $table->boolean('is_featured')->default(false);
            $table->timestamp('published_at')->nullable();

            // SEO
            $table->string('meta_title', 200)->nullable();
            $table->string('meta_description', 300)->nullable();

            $table->timestamps();
            $table->softDeletes();

            // Indexes — every filter you'll actually run
            $table->index(['is_active', 'published_at']);
            $table->index(['category_id', 'is_active']);
            $table->index(['brand_id', 'is_active']);
            $table->index(['type', 'is_active']);
            $table->index('price');
            $table->index('stock_quantity');
            $table->index('is_featured');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
