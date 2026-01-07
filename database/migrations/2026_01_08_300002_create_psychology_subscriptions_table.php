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
        Schema::create('psychology_subscriptions', function (Blueprint $table) {
            $table->id();

            // Foreign keys
            $table->foreignId('patient_id')->constrained('patients')->onDelete('cascade');
            $table->foreignId('package_id')->nullable()->constrained('packages')->onDelete('set null');

            // Subscription details
            $table->enum('status', ['active', 'paused', 'completed', 'expired', 'cancelled'])->default('active');
            $table->date('start_date');
            $table->date('end_date');

            // Session tracking
            $table->integer('sessions_total')->default(0);
            $table->integer('sessions_used')->default(0);
            $table->integer('sessions_remaining')->default(0);

            // Payment information
            $table->decimal('amount_paid', 12, 2)->nullable();
            $table->string('payment_method')->nullable(); // cash, transfer, credit_card, etc.
            $table->string('payment_reference')->nullable();

            // Additional information
            $table->text('notes')->nullable();

            // Timestamps
            $table->timestamps();

            // Indexes for performance
            $table->index('patient_id');
            $table->index('package_id');
            $table->index('status');
            $table->index('start_date');
            $table->index('end_date');
            $table->index('created_at');

            // Composite indexes
            $table->index(['patient_id', 'status']);
            $table->index(['status', 'end_date']);
            $table->index(['patient_id', 'start_date', 'end_date']);

            // Index for active subscriptions query
            $table->index(['status', 'start_date', 'end_date'], 'idx_active_subscriptions');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('psychology_subscriptions');
    }
};
