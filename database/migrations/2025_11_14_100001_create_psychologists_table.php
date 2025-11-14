<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('psychologists', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained('tenants')->onDelete('cascade');
            $table->foreignId('user_id')->nullable()->constrained('users')->onDelete('set null');

            // Personal Information
            $table->string('name');
            $table->string('slug')->unique();
            $table->string('email')->unique();
            $table->string('phone');
            $table->text('bio')->nullable();
            $table->string('photo')->nullable();

            // Professional Credentials
            $table->string('license_number')->unique(); // SIPP (Surat Izin Praktik Psikolog)
            $table->string('str_number')->unique(); // STR (Surat Tanda Registrasi)
            $table->date('str_valid_until')->nullable();
            $table->string('degree'); // S1, S2, S3
            $table->string('specialization')->nullable(); // Clinical, Industrial, Child, etc.
            $table->json('certifications')->nullable(); // Additional certifications
            $table->integer('years_of_experience')->default(0);

            // Practice Information
            $table->text('practice_address')->nullable();
            $table->string('city');
            $table->string('province');
            $table->json('languages')->nullable(); // [Indonesian, English, etc.]
            $table->json('expertise')->nullable(); // [Anxiety, Depression, Burnout, etc.]
            $table->json('approaches')->nullable(); // [CBT, Psychodynamic, Humanistic, etc.]

            // Availability
            $table->json('available_days')->nullable(); // [Monday, Tuesday, etc.]
            $table->time('available_from')->nullable();
            $table->time('available_until')->nullable();
            $table->boolean('accepts_emergency')->default(false);

            // Session Types
            $table->boolean('offers_video')->default(true);
            $table->boolean('offers_audio')->default(true);
            $table->boolean('offers_chat')->default(true);
            $table->boolean('offers_onsite')->default(false);

            // Pricing
            $table->decimal('price_per_session', 12, 2); // Base price
            $table->decimal('price_video', 12, 2)->nullable();
            $table->decimal('price_audio', 12, 2)->nullable();
            $table->decimal('price_chat', 12, 2)->nullable();
            $table->integer('session_duration_minutes')->default(60);

            // Platform Settings
            $table->integer('commission_percentage')->default(30); // Platform commission
            $table->decimal('minimum_payout', 12, 2)->default(500000); // Minimum withdraw
            $table->string('bank_name')->nullable();
            $table->string('bank_account_number')->nullable();
            $table->string('bank_account_name')->nullable();

            // Statistics
            $table->integer('total_sessions')->default(0);
            $table->integer('completed_sessions')->default(0);
            $table->integer('cancelled_sessions')->default(0);
            $table->decimal('rating', 3, 2)->default(0);
            $table->integer('total_reviews')->default(0);
            $table->decimal('total_earnings', 15, 2)->default(0);
            $table->decimal('pending_payout', 15, 2)->default(0);

            // Status
            $table->boolean('is_verified')->default(false);
            $table->boolean('is_active')->default(true);
            $table->boolean('is_available')->default(true); // Currently taking appointments
            $table->boolean('is_featured')->default(false);
            $table->timestamp('verified_at')->nullable();
            $table->foreignId('verified_by')->nullable()->constrained('users')->onDelete('set null');

            $table->timestamps();
            $table->softDeletes();

            // Indexes
            $table->index(['tenant_id', 'is_active']);
            $table->index(['tenant_id', 'is_available']);
            $table->index('city');
            $table->index('rating');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('psychologists');
    }
};
