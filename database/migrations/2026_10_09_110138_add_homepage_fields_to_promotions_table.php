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
        Schema::table('promotions', function (Blueprint $table) {
             // ---------- Homepage placement ----------
             $table->boolean('show_on_homepage')->default(false)->after('show_on_storefront');
             $table->foreignId('homepage_category_id')
                  ->nullable()
                  ->after('show_on_homepage')
                  ->constrained('categories')
                  ->nullOnDelete();
             $table->unsignedInteger('homepage_sort_order')
                  ->default(0)
                  ->after('homepage_category_id');
            $table->string('homepage_size', 20)
            ->default('standard')
            ->after('homepage_sort_order'); 

            $table->string('hero_image_path', 500)->nullable()->after('homepage_size');
            $table->string('hero_image_alt', 200)->nullable()->after('hero_image_path');
            $table->string('hero_tag', 60)->nullable()->after('hero_image_alt');
            $table->string('hero_title', 200)->nullable()->after('hero_tag');
            $table->string('hero_subtitle', 300)->nullable()->after('hero_title');
            $table->string('hero_cta_label', 60)->nullable()->after('hero_subtitle');
            $table->index(
                ['show_on_homepage', 'homepage_sort_order'],
                'idx_promotions_homepage'
            );
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('promotions', function (Blueprint $table) {
             // ---------- Homepage placement ----------
            $table->dropForeign(['homepage_category_id']);
            $table->dropIndex('idx_promotions_homepage');
            $table->dropColumn([
                'show_on_homepage',
                'homepage_category_id',
                'homepage_sort_order',
                'homepage_size',
                'hero_image_path',
                'hero_image_alt',
                'hero_tag',
                'hero_title',
                'hero_subtitle',
                'hero_cta_label',
            ]);
        });
    }
};
