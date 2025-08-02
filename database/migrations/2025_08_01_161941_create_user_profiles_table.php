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
        Schema::create('user_profiles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            
            // === EXTENDED PROFILE INFO ===
            $table->string('nik', 16)->nullable(); // Indonesian ID number
            $table->string('passport_number')->nullable();
            $table->date('passport_expiry')->nullable();
            $table->string('emergency_contact_name')->nullable();
            $table->string('emergency_contact_phone', 20)->nullable();
            
            // === PROFESSIONAL INFO (for doctors) ===
            $table->string('str_number')->nullable(); // Doctor's license
            $table->date('str_expiry')->nullable();
            $table->string('sip_number')->nullable(); // Practice license
            $table->date('sip_expiry')->nullable();
            $table->string('specialization')->nullable();
            $table->text('qualifications')->nullable();
            
            // === COMPANY INFO (for company users) ===
            $table->string('company_name')->nullable();
            $table->string('company_position')->nullable();
            $table->string('employee_id')->nullable();
            
            // === PREFERENCES ===
            $table->json('notification_preferences')->nullable();
            $table->json('privacy_settings')->nullable();
            
            $table->timestamps();
            
            // === INDEXES ===
            $table->index('nik'); // For NIK searches
            $table->index('str_number'); // For doctor license searches
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_profiles');
    }
};
