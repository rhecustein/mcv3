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
        Schema::create('outlets', function (Blueprint $table) {
            $table->id();

            // === RELATIONSHIPS ===
            $table->foreignId('admin_id')
                ->nullable()
                ->constrained('users') // Reference users table instead of admins
                ->nullOnDelete();
            
            $table->foreignId('user_id')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            // === BASIC INFORMATION ===
            $table->string('name');
            $table->string('code', 20)->unique()->nullable(); // Limit code length
            $table->string('phone', 20)->nullable();
            $table->string('email')->nullable();
            $table->text('description')->nullable(); // Outlet description

            // === ADDRESS & LOCATION ===
            $table->text('address')->nullable(); // Use text for full address
            $table->string('city', 100)->nullable();
            $table->string('province', 100)->nullable();
            $table->string('postal_code', 10)->nullable();
            $table->string('country', 100)->default('Indonesia');
            
            // Location coordinates - use DECIMAL for precision
            $table->decimal('latitude', 10, 8)->nullable(); // Better precision for GPS
            $table->decimal('longitude', 11, 8)->nullable(); // Better precision for GPS
            $table->string('maps_url')->nullable(); // Google Maps URL

            // === MEDIA & BRANDING ===
            $table->string('logo')->nullable();
            $table->string('banner')->nullable();
            $table->string('qr_code')->nullable();
            $table->json('social_media')->nullable(); // Store social media links as JSON

            // === OPERATIONAL SETTINGS ===
            $table->boolean('is_active')->default(true);
            $table->boolean('is_verified')->default(false); // Verification status
            $table->string('timezone', 50)->default('Asia/Jakarta');
            $table->json('operating_hours')->nullable(); // Store as JSON
            $table->json('services')->nullable(); // Available services as JSON
            
            // === BUSINESS INFO ===
            $table->string('license_number')->nullable(); // Business license
            $table->date('license_expiry')->nullable();
            $table->decimal('service_fee', 10, 2)->nullable(); // Default service fee
            
            // === STATISTICS CACHE ===
            $table->unsignedInteger('total_doctors')->default(0);
            $table->unsignedInteger('total_patients')->default(0);
            $table->unsignedInteger('total_certificates')->default(0);
            $table->timestamp('stats_updated_at')->nullable();

            $table->timestamps();
            $table->softDeletes();

            // === INDEXES FOR PERFORMANCE ===
            $table->index(['admin_id', 'is_active']); // Query by admin + status
            $table->index(['city', 'province']); // Location-based queries
            $table->index('is_active'); // Active outlets
            $table->index('is_verified'); // Verified outlets
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('outlets');
    }
};
