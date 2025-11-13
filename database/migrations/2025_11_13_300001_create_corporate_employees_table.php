<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('corporate_employees', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained('tenants')->onDelete('cascade');
            $table->foreignId('company_id')->constrained('companies')->onDelete('cascade');
            $table->foreignId('patient_id')->nullable()->constrained('patients')->onDelete('set null');

            $table->string('employee_id')->unique();
            $table->string('full_name');
            $table->string('email');
            $table->string('phone')->nullable();
            $table->enum('gender', ['male', 'female']);
            $table->date('birth_date');
            $table->string('nik')->nullable();

            $table->string('department');
            $table->string('position');
            $table->date('join_date');
            $table->enum('employment_status', ['active', 'inactive', 'terminated'])->default('active');

            $table->string('health_insurance_number')->nullable();
            $table->date('last_mcu_date')->nullable();
            $table->date('next_mcu_due')->nullable();
            $table->enum('health_status', ['fit', 'fit_with_notes', 'unfit', 'pending'])->default('pending');

            $table->json('emergency_contact')->nullable();
            $table->text('medical_history')->nullable();
            $table->text('notes')->nullable();

            $table->timestamps();
            $table->softDeletes();

            $table->index(['tenant_id', 'company_id']);
            $table->index('employee_id');
            $table->index('employment_status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('corporate_employees');
    }
};
