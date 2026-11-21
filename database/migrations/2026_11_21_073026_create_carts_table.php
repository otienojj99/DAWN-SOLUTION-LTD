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
        Schema::create('carts', function (Blueprint $table) {
          $table->id();
            $table->foreignId('user_id')->nullable()->constrained('users')->cascadeOnDelete();
            $table->string('session_id', 100)->nullable()->index();
            $table->string('status', 20)->default('active'); // active | converted | abandoned
            $table->char('currency', 3)->default('KES');
            $table->timestamp('last_activity_at')->useCurrent();
            $table->timestamps();

            $table->index(['user_id', 'status']);
            $table->index(['session_id', 'status']);
            $table->index(['status', 'last_activity_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('carts');
    }
};
