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
        Schema::create('shop_banners', function (Blueprint $table) {
            $table->id();

            // Identity
            $table->string('slug', 150)->unique();
            $table->string('kind', 30);   // bundle | discount | promotion | seasonal

            // Content
            $table->string('tag', 60);
            $table->string('icon', 60)->nullable();        // lucide icon key
            $table->string('title', 200);
            $table->text('description')->nullable();
            $table->string('highlight', 100)->nullable();
            $table->string('cta_label', 60);
            $table->string('href', 500);

            // Media
            $table->string('image_path', 500)->nullable();
            $table->string('image_alt', 200)->nullable();
            $table->string('gradient_key', 40)->default('blue-deep');

            // Display
            $table->unsignedInteger('sort_order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamp('starts_at')->nullable();
            $table->timestamp('ends_at')->nullable();

            $table->timestamps();

            $table->index(['is_active', 'sort_order']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('shop_banners');
    }
};
