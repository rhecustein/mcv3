<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('psychology_vouchers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained('tenants')->onDelete('cascade');
            $table->foreignId('subscription_id')->constrained('psychology_subscriptions')->onDelete('cascade');
            $table->foreignId('company_id')->constrained('companies')->onDelete('cascade');

            // Voucher Information
            $table->string('voucher_code')->unique();
            $table->string('batch_number')->nullable(); // For bulk generation
            $table->enum('type', ['session', 'emergency', 'unlimited'])->default('session');

            // Allocation
            $table->foreignId('assigned_to_user')->nullable()->constrained('users')->onDelete('cascade');
            $table->string('assigned_to_email')->nullable(); // If not yet registered
            $table->string('assigned_to_employee_id')->nullable(); // Employee ID
            $table->timestamp('assigned_at')->nullable();

            // Value
            $table->integer('sessions_allowed')->default(1);
            $table->integer('sessions_used')->default(0);
            $table->integer('sessions_remaining')->virtualAs('sessions_allowed - sessions_used');

            // Validity
            $table->date('valid_from');
            $table->date('valid_until');
            $table->enum('status', ['active', 'used', 'expired', 'cancelled', 'pending_assignment'])->default('pending_assignment');

            // Usage Tracking
            $table->foreignId('used_in_session_id')->nullable()->constrained('psychology_sessions')->onDelete('set null');
            $table->timestamp('first_used_at')->nullable();
            $table->timestamp('last_used_at')->nullable();

            // Restrictions
            $table->json('allowed_psychologists')->nullable(); // Specific psychologists only
            $table->json('allowed_session_types')->nullable(); // [video, audio, chat]
            $table->boolean('allows_emergency')->default(false);

            // Metadata
            $table->text('notes')->nullable(); // Internal notes
            $table->json('metadata')->nullable();

            $table->timestamps();
            $table->softDeletes();

            // Indexes
            $table->index(['tenant_id', 'company_id', 'status']);
            $table->index(['assigned_to_user', 'status']);
            $table->index('voucher_code');
            $table->index(['valid_from', 'valid_until']);
        });

        // Voucher Usage History
        Schema::create('psychology_voucher_usage', function (Blueprint $table) {
            $table->id();
            $table->foreignId('voucher_id')->constrained('psychology_vouchers')->onDelete('cascade');
            $table->foreignId('session_id')->constrained('psychology_sessions')->onDelete('cascade');
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->decimal('session_value', 12, 2); // Value of session
            $table->timestamp('used_at');

            $table->index(['voucher_id', 'used_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('psychology_voucher_usage');
        Schema::dropIfExists('psychology_vouchers');
    }
};
