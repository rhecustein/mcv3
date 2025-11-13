<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('mcu_bookings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained('tenants')->onDelete('cascade');
            $table->foreignId('package_id')->constrained('mcu_packages')->onDelete('restrict');
            $table->foreignId('provider_id')->constrained('mcu_providers')->onDelete('restrict');
            $table->foreignId('patient_id')->nullable()->constrained('patients')->onDelete('set null');
            $table->foreignId('company_id')->nullable()->constrained('companies')->onDelete('set null');
            $table->string('booking_number')->unique();

            // Patient Information (snapshot for non-registered patients)
            $table->string('patient_name');
            $table->string('patient_email');
            $table->string('patient_phone');
            $table->date('patient_birth_date');
            $table->enum('patient_gender', ['male', 'female']);
            $table->string('patient_nik')->nullable();

            // Booking Details
            $table->date('booking_date');
            $table->time('booking_time');
            $table->enum('status', [
                'pending',
                'confirmed',
                'rescheduled',
                'completed',
                'cancelled',
                'no_show'
            ])->default('pending');

            // Pricing
            $table->decimal('original_price', 12, 2);
            $table->decimal('final_price', 12, 2);
            $table->decimal('discount_amount', 12, 2)->default(0);
            $table->string('promo_code')->nullable();

            // Payment
            $table->enum('payment_status', [
                'pending',
                'paid',
                'refunded',
                'failed'
            ])->default('pending');
            $table->foreignId('payment_id')->nullable()->constrained('payments')->onDelete('set null');
            $table->timestamp('paid_at')->nullable();

            // Notes & Instructions
            $table->text('special_notes')->nullable();
            $table->text('cancellation_reason')->nullable();
            $table->timestamp('cancelled_at')->nullable();
            $table->foreignId('cancelled_by')->nullable()->constrained('users')->onDelete('set null');

            // Results
            $table->foreignId('result_id')->nullable()->constrained('results')->onDelete('set null');
            $table->timestamp('result_uploaded_at')->nullable();

            $table->timestamps();

            $table->index(['tenant_id', 'status']);
            $table->index(['provider_id', 'booking_date']);
            $table->index('booking_number');
            $table->index('payment_status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('mcu_bookings');
    }
};
