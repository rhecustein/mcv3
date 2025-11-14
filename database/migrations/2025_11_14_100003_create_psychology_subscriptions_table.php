<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('psychology_subscriptions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained('tenants')->onDelete('cascade');
            $table->foreignId('package_id')->constrained('psychology_packages')->onDelete('restrict');

            // Subscriber
            $table->foreignId('user_id')->nullable()->constrained('users')->onDelete('cascade'); // For B2C
            $table->foreignId('company_id')->nullable()->constrained('companies')->onDelete('cascade'); // For B2B
            $table->enum('subscriber_type', ['individual', 'corporate']);

            // Subscription Details
            $table->string('subscription_number')->unique();
            $table->date('start_date');
            $table->date('end_date');
            $table->enum('status', ['active', 'expired', 'cancelled', 'suspended'])->default('active');

            // Usage Tracking
            $table->integer('total_sessions_allowed');
            $table->integer('sessions_used')->default(0);
            $table->integer('sessions_remaining')->virtualAs('total_sessions_allowed - sessions_used');
            $table->integer('emergency_calls_allowed')->nullable();
            $table->integer('emergency_calls_used')->default(0);

            // Corporate Specific
            $table->integer('total_employees')->nullable(); // For B2B
            $table->integer('employees_enrolled')->default(0);

            // Pricing
            $table->decimal('amount_paid', 12, 2);
            $table->decimal('amount_per_period', 12, 2); // For recurring subscriptions
            $table->enum('billing_period', ['monthly', 'quarterly', 'yearly', 'one_time']);
            $table->date('next_billing_date')->nullable();

            // Auto-renewal
            $table->boolean('auto_renew')->default(false);
            $table->boolean('renewal_reminder_sent')->default(false);

            // Cancellation
            $table->text('cancellation_reason')->nullable();
            $table->timestamp('cancelled_at')->nullable();
            $table->foreignId('cancelled_by')->nullable()->constrained('users')->onDelete('set null');

            // Contract (for B2B)
            $table->string('contract_file')->nullable();
            $table->date('contract_signed_date')->nullable();
            $table->string('contract_signed_by')->nullable();

            $table->timestamps();
            $table->softDeletes();

            // Indexes
            $table->index(['tenant_id', 'status']);
            $table->index(['tenant_id', 'subscriber_type']);
            $table->index('end_date');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('psychology_subscriptions');
    }
};
