<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('product_use_case', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained()->cascadeOnDelete();
            $table->foreignId('use_case_id')->constrained()->cascadeOnDelete();
            $table->unique(['product_id', 'use_case_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('product_use_case');
    }
};