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
        Schema::create('category_promotions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('category_id')
                  ->constrained('categories')
                  ->cascadeOnDelete();
            $table->foreignId('promotion_id')
                  ->constrained('promotions')
                  ->cascadeOnDelete();

            $table->decimal('override_value', 12, 2)->nullable();
            $table->decimal('override_value_usd', 12, 2)->nullable();

            $table->boolean('include_descendants')->default(true);

            $table->timestamps();

            $table->unique(['category_id', 'promotion_id'], 'uq_category_promotion');
            $table->index('promotion_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('category_promotions');
    }
};
