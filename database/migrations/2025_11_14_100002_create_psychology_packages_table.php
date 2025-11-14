<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('psychology_packages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained('tenants')->onDelete('cascade');

            // Package Information
            $table->string('name');
            $table->string('slug');
            $table->string('code')->unique();
            $table->text('description')->nullable();
            $table->enum('type', ['b2b', 'b2c']); // B2B corporate or B2C individual

            // Session Details
            $table->integer('total_sessions'); // Number of sessions included
            $table->integer('session_duration_minutes')->default(60);
            $table->boolean('includes_video')->default(true);
            $table->boolean('includes_audio')->default(true);
            $table->boolean('includes_chat')->default(true);
            $table->boolean('includes_emergency_support')->default(false);

            // Pricing
            $table->decimal('price', 12, 2); // Total price
            $table->decimal('price_per_employee', 12, 2)->nullable(); // For B2B only
            $table->decimal('discounted_price', 12, 2)->nullable();
            $table->integer('discount_percentage')->nullable();

            // Billing Period (for B2B/subscription)
            $table->enum('billing_period', ['monthly', 'quarterly', 'yearly', 'one_time'])->default('one_time');
            $table->integer('validity_days')->default(365); // Package valid for

            // Features
            $table->json('features')->nullable(); // Additional features
            $table->boolean('includes_mood_tracker')->default(true);
            $table->boolean('includes_self_assessment')->default(true);
            $table->boolean('includes_webinar')->default(false);
            $table->boolean('includes_onsite_psychologist')->default(false);
            $table->boolean('includes_hr_dashboard')->default(false);

            // B2B Specific
            $table->integer('minimum_employees')->nullable(); // Minimum for B2B
            $table->integer('maximum_employees')->nullable(); // Maximum for B2B
            $table->boolean('requires_contract')->default(false);

            // Limits
            $table->integer('max_sessions_per_month')->nullable();
            $table->integer('max_emergency_calls')->nullable();

            // Status
            $table->boolean('is_active')->default(true);
            $table->boolean('is_featured')->default(false);
            $table->boolean('is_public')->default(true); // Visible to public or private

            // Metadata
            $table->string('image')->nullable();
            $table->integer('popularity_score')->default(0);
            $table->integer('total_subscriptions')->default(0);

            $table->timestamps();
            $table->softDeletes();

            // Indexes
            $table->index(['tenant_id', 'type', 'is_active']);
            $table->index('is_featured');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('psychology_packages');
    }
};
