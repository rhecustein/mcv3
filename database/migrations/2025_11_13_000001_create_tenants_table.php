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
        Schema::create('tenants', function (Blueprint $table) {
            $table->id();

            // Basic Info
            $table->string('name');
            $table->string('slug')->unique();
            $table->string('domain')->unique()->nullable();
            $table->string('logo')->nullable();

            // Subscription
            $table->enum('subscription_plan', ['starter', 'professional', 'enterprise'])->default('starter');
            $table->enum('subscription_status', ['trial', 'active', 'suspended', 'cancelled'])->default('trial');
            $table->timestamp('trial_ends_at')->nullable();
            $table->timestamp('subscription_ends_at')->nullable();

            // Quotas
            $table->integer('max_users')->default(5);
            $table->integer('max_documents_per_month')->default(500);
            $table->integer('max_storage_mb')->default(2000);

            // Usage Tracking
            $table->integer('current_users')->default(0);
            $table->integer('current_documents_this_month')->default(0);
            $table->decimal('current_storage_mb', 10, 2)->default(0);

            // Settings & Features
            $table->json('settings')->nullable();
            $table->json('enabled_features')->nullable();

            // Contact Info
            $table->string('contact_email')->nullable();
            $table->string('contact_phone')->nullable();

            // Status
            $table->boolean('is_active')->default(true);

            $table->timestamps();
            $table->softDeletes();

            // Indexes
            $table->index('slug');
            $table->index('subscription_status');
            $table->index('is_active');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tenants');
    }
};
