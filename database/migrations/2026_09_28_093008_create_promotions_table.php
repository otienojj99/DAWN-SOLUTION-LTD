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
        Schema::create('promotions', function (Blueprint $table) {
             $table->id();

            // Identity
            $table->string('name', 150);                       // "Black Friday 2026"
            $table->string('slug', 170)->unique();
            $table->text('description')->nullable();

            // Classification
            $table->enum('kind', [
                'discount', 'clearance', 'flash_sale',
                'seasonal', 'bundle', 'bogo',
            ])->default('discount');

            $table->enum('type', [
                'percentage', 'fixed',
            ])->default('percentage');

            // Value — in KES base currency
            $table->decimal('value', 12, 2);                   // 15 = 15% or 500 = KSh 500 off
            $table->decimal('value_usd', 12, 2)->nullable();   // optional USD override

            // BOGO support
            $table->unsignedInteger('buy_quantity')->nullable();  // buy 2
            $table->unsignedInteger('get_quantity')->nullable();  // get 1
            $table->decimal('get_discount_percent', 5, 2)->nullable(); // 100 = free, 50 = half

            // Limits
            $table->unsignedInteger('max_uses')->nullable();              // total uses across all
            $table->unsignedInteger('max_uses_per_customer')->nullable(); // per user
            $table->unsignedInteger('uses_count')->default(0);            // denormalized counter
            $table->unsignedInteger('priority')->default(100);            // lower = higher priority

            // Rules
            $table->boolean('is_stackable')->default(false);   // combine with other promos?
            $table->boolean('applies_to_variants')->default(true); // apply to variant prices too
            $table->decimal('min_order_amount', 12, 2)->nullable(); // cart threshold

            // Scope (which products/categories it naturally applies to, if not pivoted)
            $table->enum('scope', [
                'all', 'products', 'categories', 'brands',
            ])->default('products');

            // Display
            $table->string('badge_label', 60)->nullable();     // "Clearance Sale"
            $table->string('badge_color', 30)->nullable();     // "red" / "#dc2626"
            $table->boolean('show_on_storefront')->default(true);

            // Schedule
            $table->timestamp('starts_at')->nullable();
            $table->timestamp('ends_at')->nullable();

            // Visibility
            $table->boolean('is_active')->default(true);

            $table->timestamps();
            $table->softDeletes();

            $table->index(['is_active', 'starts_at', 'ends_at']);
            $table->index(['kind', 'is_active']);
            $table->index('priority');
            $table->index('show_on_storefront');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('promotions');
    }
};
