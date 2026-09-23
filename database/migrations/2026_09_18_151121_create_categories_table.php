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
        Schema::create('categories', function (Blueprint $table) {
             // Primary key
            $table->id();

            // Tree linkage
            $table->foreignId('parent_id')
                  ->nullable()
                  ->constrained('categories')
                  ->restrictOnDelete()
                  ->cascadeOnUpdate();

            // Identity
            $table->string('name', 150);
            $table->string('slug', 160);

            // Materialized path + depth
            $table->string('path', 500);
            $table->unsignedTinyInteger('level'); // 1 = parent, 2 = child, 3 = subcategory

            // Presentation / ordering
            $table->integer('sort_order')->default(0);

            // Active toggle — defaults to active
            $table->boolean('is_active')->default(true);

            // created_at + updated_at
            $table->timestamps();

            // Optional: track who made changes (uncomment if you have users)
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();

            // Optional: soft deletes (uncomment if you want "deleted" categories kept for history)
            // $table->softDeletes();

            // Indexes
            $table->unique('path', 'uq_categories_path');
            $table->unique(['parent_id', 'slug'], 'uq_categories_parent_slug');
            $table->index(['parent_id', 'sort_order'], 'idx_categories_parent_sort');
            $table->index(['level', 'sort_order'], 'idx_categories_level');
            $table->index('is_active', 'idx_categories_active');
            
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('categories');
    }
};
