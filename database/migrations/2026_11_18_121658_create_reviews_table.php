<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('reviews', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained('products')->cascadeOnDelete();

            // Author
            $table->string('author_name', 100);
            $table->string('author_email', 255)->nullable();
            $table->string('ip_address', 45)->nullable();
            $table->string('user_agent', 500)->nullable();

            // Content
            $table->unsignedTinyInteger('rating');  // 1-5
            $table->text('text');

            // Moderation
            $table->boolean('is_approved')->default(false);
            $table->boolean('verified_purchase')->default(false);
            $table->text('moderation_note')->nullable();

            // Admin reply (visible publicly next to the review)
            $table->text('admin_reply')->nullable();
            $table->timestamp('admin_replied_at')->nullable();
            $table->foreignId('admin_replied_by')->nullable()->constrained('users')->nullOnDelete();

            $table->timestamps();
            $table->softDeletes();

            $table->index(['product_id', 'is_approved', 'created_at']);
            $table->index('is_approved');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('reviews');
    }
};