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
        Schema::create('attributes', function (Blueprint $table) {
            $table->id();
            $table->string('name', 100);              // "CPU Family"
            $table->string('slug', 120)->unique();    // "cpu-family"
            $table->string('group', 80)->nullable();  // "Processor", "Memory", "Graphics"
            $table->enum('type', ['select', 'multiselect', 'text', 'number', 'color', 'boolean'])
                  ->default('select');

            $table->boolean('is_filterable')->default(true);   // show in filter sidebar
            $table->boolean('is_variant_attribute')->default(false); // varies between variants
            $table->string('unit', 30)->nullable();            // "GB", "TB", "inch"
            $table->unsignedInteger('sort_order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->index(['is_active', 'is_filterable', 'sort_order']);
            $table->index('group');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('attributes');
    }
};
