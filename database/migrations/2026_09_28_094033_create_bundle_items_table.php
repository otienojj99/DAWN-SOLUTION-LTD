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
        Schema::create('bundle_items', function (Blueprint $table) {
           $table->id();

            // The bundle product (type = 'bundle')
            $table->foreignId('bundle_product_id')
                  ->constrained('products')
                  ->cascadeOnDelete();

            // The component that goes inside the bundle
            $table->foreignId('component_product_id')
                  ->constrained('products')
                  ->restrictOnDelete();

            // Optional: pin to a specific variant
            $table->foreignId('variant_id')
                  ->nullable()
                  ->constrained('product_variants')
                  ->nullOnDelete();

            $table->unsignedInteger('quantity')->default(1);
            $table->unsignedInteger('sort_order')->default(0);

            // Snapshot of component price at time of bundle creation (for margin reporting)
            $table->decimal('component_price_snapshot', 12, 2)->nullable();

            $table->timestamps();

            $table->unique(
                ['bundle_product_id', 'component_product_id', 'variant_id'],
                'uq_bundle_component_variant'
            );
            $table->index(['bundle_product_id', 'sort_order'], 'idx_bundle_items_order');
            $table->index('component_product_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bundle_items');
    }
};
