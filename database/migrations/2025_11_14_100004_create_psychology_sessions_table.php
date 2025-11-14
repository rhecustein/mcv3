<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('psychology_sessions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained('tenants')->onDelete('cascade');

            // Session Participants
            $table->foreignId('psychologist_id')->constrained('psychologists')->onDelete('restrict');
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade'); // Client/Patient
            $table->foreignId('subscription_id')->nullable()->constrained('psychology_subscriptions')->onDelete('set null');

            // Session Information
            $table->string('session_number')->unique();
            $table->enum('session_type', ['video', 'audio', 'chat', 'onsite'])->default('video');
            $table->enum('category', ['first_session', 'follow_up', 'emergency', 'consultation'])->default('first_session');

            // Scheduling
            $table->dateTime('scheduled_at');
            $table->dateTime('started_at')->nullable();
            $table->dateTime('ended_at')->nullable();
            $table->integer('duration_minutes')->default(60);
            $table->integer('actual_duration_minutes')->nullable();

            // Session Details
            $table->text('client_concern')->nullable(); // What client wants to discuss
            $table->enum('urgency_level', ['normal', 'moderate', 'high', 'emergency'])->default('normal');
            $table->boolean('is_anonymous')->default(false); // For corporate employees
            $table->boolean('is_emergency')->default(false);

            // Video Call Integration
            $table->string('room_id')->nullable(); // Agora/Twilio room ID
            $table->string('room_token')->nullable();
            $table->text('join_url')->nullable();
            $table->json('call_metadata')->nullable(); // Recording URL, quality metrics, etc.

            // Status
            $table->enum('status', [
                'scheduled',
                'confirmed',
                'in_progress',
                'completed',
                'cancelled',
                'no_show',
                'rescheduled'
            ])->default('scheduled');

            // Payment
            $table->decimal('price', 12, 2);
            $table->enum('payment_method', ['subscription', 'pay_per_session', 'voucher', 'free'])->default('pay_per_session');
            $table->foreignId('payment_id')->nullable()->constrained('payments')->onDelete('set null');
            $table->boolean('is_paid')->default(false);

            // Rating & Feedback
            $table->unsignedTinyInteger('client_rating')->nullable(); // 1-5 stars
            $table->text('client_feedback')->nullable();
            $table->unsignedTinyInteger('psychologist_rating')->nullable(); // Psychologist rates client (optional)

            // Cancellation
            $table->text('cancellation_reason')->nullable();
            $table->timestamp('cancelled_at')->nullable();
            $table->foreignId('cancelled_by')->nullable()->constrained('users')->onDelete('set null');
            $table->enum('cancelled_by_role', ['client', 'psychologist', 'system'])->nullable();

            // Rescheduling
            $table->foreignId('rescheduled_from_id')->nullable()->constrained('psychology_sessions')->onDelete('set null');
            $table->foreignId('rescheduled_to_id')->nullable()->constrained('psychology_sessions')->onDelete('set null');

            // Reminders
            $table->boolean('reminder_sent_24h')->default(false);
            $table->boolean('reminder_sent_1h')->default(false);
            $table->boolean('feedback_requested')->default(false);

            // Metadata
            $table->json('metadata')->nullable(); // Custom data

            $table->timestamps();
            $table->softDeletes();

            // Indexes
            $table->index(['tenant_id', 'status']);
            $table->index(['psychologist_id', 'scheduled_at']);
            $table->index(['user_id', 'scheduled_at']);
            $table->index(['scheduled_at', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('psychology_sessions');
    }
};
