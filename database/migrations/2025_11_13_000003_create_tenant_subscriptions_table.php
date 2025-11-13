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
        Schema::create('tenant_subscriptions', function (Blueprint $table) {
            $table->id();

            // Relations
            $table->foreignId('tenant_id')->constrained('tenants')->onDelete('cascade');
            $table->foreignId('subscription_plan_id')->constrained('subscription_plans')->onDelete('restrict');

            // Period
            $table->timestamp('starts_at');
            $table->timestamp('ends_at');
            $table->enum('billing_cycle', ['monthly', 'yearly'])->default('monthly');

            // Pricing
            $table->decimal('price', 10, 2);
            $table->string('currency', 3)->default('IDR');

            // Payment
            $table->enum('status', ['pending', 'active', 'cancelled', 'expired'])->default('pending');
            $table->boolean('auto_renew')->default(true);
            $table->string('payment_method')->nullable();
            $table->string('payment_reference')->nullable();
            $table->timestamp('paid_at')->nullable();

            // Trial
            $table->boolean('is_trial')->default(false);

            $table->timestamps();

            // Indexes
            $table->index('tenant_id');
            $table->index('status');
            $table->index(['starts_at', 'ends_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tenant_subscriptions');
    }
};
