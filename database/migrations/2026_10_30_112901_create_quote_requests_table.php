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
        Schema::create('quote_requests', function (Blueprint $table) {
            $table->id();
            $table->string('reference', 20)->unique();   // QR-2026-0001

            // Contact
            $table->string('full_name', 150);
            $table->string('phone', 30);
            $table->string('email', 255);
            $table->string('company', 150)->nullable();

            // What they want
            $table->string('interest', 100);
            $table->text('details')->nullable();

            // Lifecycle
            $table->enum('status', [
                'new', 'reviewing', 'contacted', 'quoted',
                'won', 'lost', 'spam',
            ])->default('new');
            $table->string('spam_reason', 255)->nullable();
            $table->timestamp('emailed_at')->nullable();
            $table->foreignId('handled_by')->nullable()->constrained('users')->nullOnDelete();
            $table->text('internal_notes')->nullable();

            // Request metadata
            $table->string('ip_address', 45)->nullable();
            $table->string('user_agent', 500)->nullable();

            $table->timestamps();
            $table->softDeletes();

            $table->index(['status', 'created_at']);
            $table->index('email');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('quote_requests');
    }
};
