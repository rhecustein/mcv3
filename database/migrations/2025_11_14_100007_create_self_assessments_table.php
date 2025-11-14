<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Assessment Templates (PHQ-9, GAD-7, DASS-21, etc.)
        Schema::create('assessment_templates', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // PHQ-9, GAD-7, DASS-21, etc.
            $table->string('code')->unique(); // phq9, gad7, dass21
            $table->text('description')->nullable();
            $table->enum('category', ['depression', 'anxiety', 'stress', 'general', 'burnout', 'ptsd']);
            $table->json('questions'); // Array of questions with options
            $table->json('scoring_rules'); // How to calculate score
            $table->json('interpretation_ranges'); // Score ranges and meanings
            $table->integer('total_questions');
            $table->integer('max_score');
            $table->boolean('is_active')->default(true);
            $table->integer('recommended_frequency_days')->default(14); // How often to retake
            $table->timestamps();
        });

        // User Assessment Results
        Schema::create('self_assessments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained('tenants')->onDelete('cascade');
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('template_id')->constrained('assessment_templates')->onDelete('restrict');

            // Assessment Info
            $table->string('assessment_number')->unique();
            $table->dateTime('started_at');
            $table->dateTime('completed_at')->nullable();
            $table->enum('status', ['in_progress', 'completed', 'abandoned'])->default('in_progress');

            // Responses
            $table->json('responses'); // User answers to questions
            $table->integer('total_score')->nullable();
            $table->decimal('percentage_score', 5, 2)->nullable();

            // Interpretation
            $table->string('severity_level')->nullable(); // minimal, mild, moderate, severe, etc.
            $table->text('interpretation')->nullable();
            $table->json('recommendations')->nullable();
            $table->boolean('requires_professional_help')->default(false);
            $table->boolean('is_high_risk')->default(false); // Suicide/self-harm risk

            // Follow-up
            $table->boolean('psychologist_notified')->default(false);
            $table->foreignId('referred_to_psychologist')->nullable()->constrained('psychologists')->onDelete('set null');
            $table->foreignId('follow_up_session_id')->nullable()->constrained('psychology_sessions')->onDelete('set null');

            // Privacy
            $table->boolean('is_anonymous')->default(false);
            $table->boolean('shared_with_psychologist')->default(false);
            $table->boolean('shared_with_employer')->default(false); // For corporate, anonymous

            // Comparison
            $table->foreignId('previous_assessment_id')->nullable()->constrained('self_assessments')->onDelete('set null');
            $table->integer('score_change')->nullable(); // Difference from previous
            $table->enum('trend', ['improving', 'stable', 'worsening'])->nullable();

            $table->timestamps();
            $table->softDeletes();

            // Indexes
            $table->index(['tenant_id', 'user_id', 'template_id']);
            $table->index(['user_id', 'completed_at']);
            $table->index('is_high_risk');
        });

        // Crisis Alerts (when high-risk assessment detected)
        Schema::create('crisis_alerts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained('tenants')->onDelete('cascade');
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('assessment_id')->nullable()->constrained('self_assessments')->onDelete('set null');

            // Alert Information
            $table->enum('alert_type', ['suicide_risk', 'self_harm', 'severe_depression', 'panic', 'other']);
            $table->enum('severity', ['moderate', 'high', 'critical'])->default('high');
            $table->text('description');
            $table->json('indicators')->nullable(); // What triggered the alert

            // Response
            $table->enum('status', ['pending', 'acknowledged', 'contacted', 'resolved'])->default('pending');
            $table->foreignId('assigned_to_psychologist')->nullable()->constrained('psychologists')->onDelete('set null');
            $table->timestamp('acknowledged_at')->nullable();
            $table->timestamp('contacted_at')->nullable();
            $table->timestamp('resolved_at')->nullable();

            // Actions Taken
            $table->text('actions_taken')->nullable();
            $table->text('outcome_notes')->nullable();
            $table->foreignId('emergency_session_id')->nullable()->constrained('psychology_sessions')->onDelete('set null');

            // Escalation
            $table->boolean('escalated_to_emergency')->default(false);
            $table->text('escalation_notes')->nullable();

            $table->timestamps();

            // Indexes
            $table->index(['tenant_id', 'status', 'severity']);
            $table->index(['user_id', 'created_at']);
        });

        // Seeder untuk assessment templates populer
        DB::table('assessment_templates')->insert([
            [
                'name' => 'PHQ-9 (Patient Health Questionnaire)',
                'code' => 'phq9',
                'description' => 'Screening tool for depression severity',
                'category' => 'depression',
                'questions' => json_encode([
                    ['text' => 'Little interest or pleasure in doing things', 'options' => ['Not at all', 'Several days', 'More than half the days', 'Nearly every day']],
                    ['text' => 'Feeling down, depressed, or hopeless', 'options' => ['Not at all', 'Several days', 'More than half the days', 'Nearly every day']],
                    ['text' => 'Trouble falling or staying asleep, or sleeping too much', 'options' => ['Not at all', 'Several days', 'More than half the days', 'Nearly every day']],
                    ['text' => 'Feeling tired or having little energy', 'options' => ['Not at all', 'Several days', 'More than half the days', 'Nearly every day']],
                    ['text' => 'Poor appetite or overeating', 'options' => ['Not at all', 'Several days', 'More than half the days', 'Nearly every day']],
                    ['text' => 'Feeling bad about yourself or that you are a failure', 'options' => ['Not at all', 'Several days', 'More than half the days', 'Nearly every day']],
                    ['text' => 'Trouble concentrating on things', 'options' => ['Not at all', 'Several days', 'More than half the days', 'Nearly every day']],
                    ['text' => 'Moving or speaking slowly or being fidgety/restless', 'options' => ['Not at all', 'Several days', 'More than half the days', 'Nearly every day']],
                    ['text' => 'Thoughts that you would be better off dead or hurting yourself', 'options' => ['Not at all', 'Several days', 'More than half the days', 'Nearly every day']],
                ]),
                'scoring_rules' => json_encode(['min' => 0, 'max' => 27, 'calculation' => 'sum']),
                'interpretation_ranges' => json_encode([
                    ['min' => 0, 'max' => 4, 'level' => 'minimal', 'description' => 'Minimal or no depression'],
                    ['min' => 5, 'max' => 9, 'level' => 'mild', 'description' => 'Mild depression'],
                    ['min' => 10, 'max' => 14, 'level' => 'moderate', 'description' => 'Moderate depression'],
                    ['min' => 15, 'max' => 19, 'level' => 'moderately_severe', 'description' => 'Moderately severe depression'],
                    ['min' => 20, 'max' => 27, 'level' => 'severe', 'description' => 'Severe depression'],
                ]),
                'total_questions' => 9,
                'max_score' => 27,
                'recommended_frequency_days' => 14,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'GAD-7 (Generalized Anxiety Disorder)',
                'code' => 'gad7',
                'description' => 'Screening tool for anxiety severity',
                'category' => 'anxiety',
                'questions' => json_encode([
                    ['text' => 'Feeling nervous, anxious, or on edge', 'options' => ['Not at all', 'Several days', 'More than half the days', 'Nearly every day']],
                    ['text' => 'Not being able to stop or control worrying', 'options' => ['Not at all', 'Several days', 'More than half the days', 'Nearly every day']],
                    ['text' => 'Worrying too much about different things', 'options' => ['Not at all', 'Several days', 'More than half the days', 'Nearly every day']],
                    ['text' => 'Trouble relaxing', 'options' => ['Not at all', 'Several days', 'More than half the days', 'Nearly every day']],
                    ['text' => 'Being so restless that it is hard to sit still', 'options' => ['Not at all', 'Several days', 'More than half the days', 'Nearly every day']],
                    ['text' => 'Becoming easily annoyed or irritable', 'options' => ['Not at all', 'Several days', 'More than half the days', 'Nearly every day']],
                    ['text' => 'Feeling afraid as if something awful might happen', 'options' => ['Not at all', 'Several days', 'More than half the days', 'Nearly every day']],
                ]),
                'scoring_rules' => json_encode(['min' => 0, 'max' => 21, 'calculation' => 'sum']),
                'interpretation_ranges' => json_encode([
                    ['min' => 0, 'max' => 4, 'level' => 'minimal', 'description' => 'Minimal anxiety'],
                    ['min' => 5, 'max' => 9, 'level' => 'mild', 'description' => 'Mild anxiety'],
                    ['min' => 10, 'max' => 14, 'level' => 'moderate', 'description' => 'Moderate anxiety'],
                    ['min' => 15, 'max' => 21, 'level' => 'severe', 'description' => 'Severe anxiety'],
                ]),
                'total_questions' => 7,
                'max_score' => 21,
                'recommended_frequency_days' => 14,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('crisis_alerts');
        Schema::dropIfExists('self_assessments');
        Schema::dropIfExists('assessment_templates');
    }
};
