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
        Schema::create('users', function (Blueprint $table) {
            $table->id();

            // === BASIC IDENTITY ===
            $table->string('name');
            $table->string('email')->unique();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');

            // === ROLE MANAGEMENT ===
            $table->enum('role_type', [
                'superadmin',
                'admin',
                'outlet', 
                'doctor',
                'companies',
                'patient'
            ])->default('patient'); // Set default role

            // === PROFILE INFORMATION ===
            $table->string('phone', 50)->nullable();
            $table->string('avatar')->nullable();
            $table->date('birth_date')->nullable(); // Useful for patients/doctors
            $table->enum('gender', ['male', 'female'])->nullable();
            $table->text('address')->nullable(); // Use text for longer addresses

            // === SECURITY & TRACKING ===
            $table->string('last_ip', 45)->nullable(); // Support IPv6 (max 45 chars)
            $table->timestamp('last_login_at')->nullable();
            $table->string('last_location')->nullable();
            $table->timestamp('last_activity_at')->nullable(); // Better naming
            $table->boolean('allow_multiple_sessions')->default(false);
            $table->string('last_login_ip')->nullable();
                    
            // === 2FA & OTP ===
            $table->string('otp_code', 6)->nullable(); // Limit OTP to 6 digits
            $table->timestamp('otp_expires_at')->nullable(); // Better naming
            $table->boolean('two_factor_enabled')->default(false);
            $table->text('two_factor_secret')->nullable();

            // === STATUS & PREFERENCES ===
            $table->boolean('is_active')->default(true);
            $table->boolean('is_banned')->default(false); // Separate ban status
            $table->timestamp('banned_at')->nullable();
            $table->string('ban_reason')->nullable();
            $table->string('timezone', 50)->default('Asia/Jakarta');
            $table->string('locale', 5)->default('id');

            // === SESSION MANAGEMENT ===
            $table->rememberToken();
            $table->timestamps();
            $table->softDeletes();

            // === INDEXES FOR PERFORMANCE ===
            $table->index(['role_type', 'is_active']); // Query by role + status
            $table->index('last_login_at'); // For activity reports
            $table->index('email_verified_at'); // For verification checks
        });

        Schema::create('password_reset_tokens', function (Blueprint $table) {
            $table->string('email')->primary();
            $table->string('token');
            $table->timestamp('created_at')->nullable();
        });

        Schema::create('sessions', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->foreignId('user_id')->nullable()->index();
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->longText('payload');
            $table->integer('last_activity')->index();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
        Schema::dropIfExists('password_reset_tokens');
        Schema::dropIfExists('sessions');
    }
};
