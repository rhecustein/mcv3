<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('promo_codes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained('tenants')->onDelete('cascade');

            // Code Details
            $table->string('code')->unique();
            $table->string('name');
            $table->text('description')->nullable();

            // Discount Type & Value
            $table->enum('discount_type', ['percentage', 'fixed']); // Percentage or fixed amount
            $table->decimal('discount_value', 12, 2); // Percentage (0-100) or fixed amount
            $table->decimal('max_discount_amount', 12, 2)->nullable(); // Max discount for percentage type
            $table->decimal('min_purchase_amount', 12, 2)->default(0); // Minimum purchase required

            // Usage Limits
            $table->integer('usage_limit')->nullable(); // Total usage limit (null = unlimited)
            $table->integer('usage_limit_per_user')->default(1); // Per user usage limit
            $table->integer('usage_count')->default(0); // Current usage count

            // Validity Period
            $table->timestamp('valid_from')->nullable();
            $table->timestamp('valid_until')->nullable();

            // Applicability
            $table->enum('applicable_to', ['all', 'packages', 'providers', 'categories'])->default('all');
            $table->json('applicable_ids')->nullable(); // Package IDs, Provider IDs, or Categories
            $table->enum('user_eligibility', ['all', 'new', 'existing'])->default('all');

            // Status & Visibility
            $table->boolean('is_active')->default(true);
            $table->boolean('is_public')->default(false); // Public or private (invite-only) code

            // Creator
            $table->foreignId('created_by')->nullable()->constrained('users')->onDelete('set null');

            $table->timestamps();

            // Indexes
            $table->index(['tenant_id', 'code']);
            $table->index('is_active');
            $table->index(['valid_from', 'valid_until']);
        });

        // Promo code usage tracking
        Schema::create('promo_code_usage', function (Blueprint $table) {
            $table->id();
            $table->foreignId('promo_code_id')->constrained('promo_codes')->onDelete('cascade');
            $table->foreignId('user_id')->nullable()->constrained('users')->onDelete('set null');
            $table->foreignId('booking_id')->nullable()->constrained('mcu_bookings')->onDelete('set null');
            $table->decimal('discount_amount', 12, 2);
            $table->decimal('original_amount', 12, 2);
            $table->decimal('final_amount', 12, 2);
            $table->timestamps();

            $table->index(['promo_code_id', 'user_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('promo_code_usage');
        Schema::dropIfExists('promo_codes');
    }
};
