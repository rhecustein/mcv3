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
        Schema::create('tenant_usage', function (Blueprint $table) {
            $table->id();

            // Relations
            $table->foreignId('tenant_id')->constrained('tenants')->onDelete('cascade');

            // Period
            $table->date('period_start');
            $table->date('period_end');
            $table->string('period_type')->default('monthly'); // daily, weekly, monthly

            // Usage Metrics
            $table->integer('documents_generated')->default(0);
            $table->integer('mcu_bookings')->default(0);
            $table->decimal('storage_used_mb', 10, 2)->default(0);
            $table->integer('api_calls')->default(0);
            $table->integer('active_users')->default(0);

            // Cost Tracking
            $table->decimal('estimated_cost', 10, 2)->default(0);

            $table->timestamps();

            // Indexes
            $table->index('tenant_id');
            $table->index(['period_start', 'period_end']);
            $table->unique(['tenant_id', 'period_start', 'period_type']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tenant_usage');
    }
};
