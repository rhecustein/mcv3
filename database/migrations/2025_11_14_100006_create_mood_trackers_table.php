<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('mood_trackers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained('tenants')->onDelete('cascade');
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');

            // Date & Time
            $table->date('tracking_date');
            $table->time('tracking_time');

            // Mood Assessment
            $table->enum('mood', [
                'very_bad',      // 1 - Sangat buruk
                'bad',           // 2 - Buruk
                'neutral',       // 3 - Biasa saja
                'good',          // 4 - Baik
                'very_good'      // 5 - Sangat baik
            ]);
            $table->unsignedTinyInteger('mood_score'); // 1-5

            // Emotions (can select multiple)
            $table->json('emotions')->nullable(); // [happy, sad, anxious, angry, calm, stressed, etc.]

            // Energy Level
            $table->unsignedTinyInteger('energy_level')->nullable(); // 1-5
            $table->unsignedTinyInteger('stress_level')->nullable(); // 1-5
            $table->unsignedTinyInteger('anxiety_level')->nullable(); // 1-5

            // Activities
            $table->json('activities')->nullable(); // [work, exercise, socialize, relax, etc.]
            $table->json('triggers')->nullable(); // What triggered the mood (work stress, relationship, etc.)

            // Sleep Quality (night before)
            $table->unsignedTinyInteger('sleep_hours')->nullable();
            $table->enum('sleep_quality', ['poor', 'fair', 'good', 'excellent'])->nullable();

            // Notes
            $table->text('notes')->nullable();
            $table->text('gratitude')->nullable(); // What are they grateful for today

            // Symptoms
            $table->boolean('has_physical_symptoms')->default(false);
            $table->json('physical_symptoms')->nullable(); // [headache, fatigue, stomach_ache, etc.]

            // Context
            $table->enum('time_of_day', ['morning', 'afternoon', 'evening', 'night'])->nullable();
            $table->string('location_type')->nullable(); // home, work, outside

            // Tracking Streak
            $table->integer('streak_days')->default(1);

            $table->timestamps();

            // Indexes
            $table->index(['tenant_id', 'user_id', 'tracking_date']);
            $table->index(['user_id', 'tracking_date']);
            $table->unique(['user_id', 'tracking_date', 'tracking_time']); // One entry per time
        });

        // Mood patterns/insights (aggregated data)
        Schema::create('mood_patterns', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');

            // Pattern Period
            $table->enum('period_type', ['weekly', 'monthly', 'quarterly']);
            $table->date('period_start');
            $table->date('period_end');

            // Aggregated Stats
            $table->decimal('average_mood_score', 3, 2);
            $table->decimal('average_energy_level', 3, 2)->nullable();
            $table->decimal('average_stress_level', 3, 2)->nullable();
            $table->decimal('average_anxiety_level', 3, 2)->nullable();
            $table->decimal('average_sleep_hours', 3, 2)->nullable();

            // Patterns
            $table->json('most_common_emotions')->nullable();
            $table->json('most_common_triggers')->nullable();
            $table->json('best_activities')->nullable(); // Activities associated with good mood
            $table->string('best_day_of_week')->nullable();
            $table->string('worst_day_of_week')->nullable();

            // Trends
            $table->enum('mood_trend', ['improving', 'stable', 'declining'])->nullable();
            $table->text('insights')->nullable(); // AI-generated insights

            // Metadata
            $table->integer('total_entries');
            $table->integer('tracking_consistency_percentage'); // How many days tracked

            $table->timestamps();

            $table->index(['user_id', 'period_type', 'period_start']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('mood_patterns');
        Schema::dropIfExists('mood_trackers');
    }
};
