<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::rename('product_variantattribute_values', 'product_variant_attribute_value');
    }

    public function down(): void
    {
        Schema::rename('product_variant_attribute_value', 'product_variantattribute_values');
    }
};