<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('reviews', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained('tenants')->onDelete('cascade');
            $table->foreignId('booking_id')->constrained('mcu_bookings')->onDelete('cascade');
            $table->foreignId('package_id')->constrained('mcu_packages')->onDelete('cascade');
            $table->foreignId('provider_id')->constrained('mcu_providers')->onDelete('cascade');
            $table->foreignId('user_id')->nullable()->constrained('users')->onDelete('set null');

            // Rating (1-5 stars)
            $table->unsignedTinyInteger('rating');

            // Review Content
            $table->string('title')->nullable();
            $table->text('comment')->nullable();

            // Detailed Ratings (optional)
            $table->unsignedTinyInteger('service_rating')->nullable(); // Quality of service
            $table->unsignedTinyInteger('cleanliness_rating')->nullable(); // Facility cleanliness
            $table->unsignedTinyInteger('staff_rating')->nullable(); // Staff friendliness
            $table->unsignedTinyInteger('value_rating')->nullable(); // Value for money

            // Verification
            $table->boolean('is_verified')->default(true); // Verified purchase
            $table->boolean('is_anonymous')->default(false);

            // Moderation
            $table->enum('status', ['pending', 'approved', 'rejected'])->default('approved');
            $table->text('moderation_notes')->nullable();
            $table->foreignId('moderated_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamp('moderated_at')->nullable();

            // Provider Response
            $table->text('provider_response')->nullable();
            $table->timestamp('provider_responded_at')->nullable();

            // Helpfulness
            $table->integer('helpful_count')->default(0);
            $table->integer('not_helpful_count')->default(0);

            $table->timestamps();

            // Indexes
            $table->index(['tenant_id', 'package_id']);
            $table->index(['tenant_id', 'provider_id']);
            $table->index('rating');
            $table->index('status');
            $table->unique('booking_id'); // One review per booking
        });

        // Review helpfulness votes
        Schema::create('review_votes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('review_id')->constrained('reviews')->onDelete('cascade');
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->boolean('is_helpful'); // true = helpful, false = not helpful
            $table->timestamps();

            $table->unique(['review_id', 'user_id']); // One vote per user per review
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('review_votes');
        Schema::dropIfExists('reviews');
    }
};
