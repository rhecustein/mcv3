<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('psychology_notes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained('tenants')->onDelete('cascade');
            $table->foreignId('session_id')->constrained('psychology_sessions')->onDelete('cascade');
            $table->foreignId('psychologist_id')->constrained('psychologists')->onDelete('cascade');
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade'); // Client

            // Note Content (ENCRYPTED)
            $table->text('presenting_problem')->nullable(); // Keluhan utama
            $table->text('session_summary')->nullable(); // Ringkasan sesi
            $table->text('observations')->nullable(); // Observasi psikolog
            $table->text('assessment')->nullable(); // Penilaian/diagnosis
            $table->text('intervention')->nullable(); // Intervensi yang dilakukan
            $table->text('treatment_plan')->nullable(); // Rencana treatment
            $table->text('homework')->nullable(); // PR untuk klien
            $table->text('progress_notes')->nullable(); // Catatan perkembangan
            $table->text('risk_assessment')->nullable(); // Penilaian risiko (suicide, harm)

            // Clinical Information
            $table->json('symptoms')->nullable(); // Gejala yang diamati
            $table->enum('mood', ['very_poor', 'poor', 'neutral', 'good', 'excellent'])->nullable();
            $table->enum('affect', ['flat', 'blunted', 'restricted', 'appropriate', 'labile'])->nullable();
            $table->enum('risk_level', ['low', 'moderate', 'high', 'critical'])->nullable();

            // Follow-up
            $table->text('recommendations')->nullable();
            $table->date('next_session_recommended')->nullable();
            $table->boolean('requires_referral')->default(false);
            $table->text('referral_notes')->nullable();

            // Metadata
            $table->boolean('is_encrypted')->default(true);
            $table->string('encryption_key_id')->nullable(); // For key rotation
            $table->timestamp('last_accessed_at')->nullable();

            $table->timestamps();
            $table->softDeletes();

            // Indexes
            $table->index(['tenant_id', 'psychologist_id']);
            $table->index(['tenant_id', 'user_id']);
            $table->index('session_id');
        });

        // Audit log for notes access (compliance)
        Schema::create('psychology_notes_access_log', function (Blueprint $table) {
            $table->id();
            $table->foreignId('note_id')->constrained('psychology_notes')->onDelete('cascade');
            $table->foreignId('accessed_by')->constrained('users')->onDelete('cascade');
            $table->string('access_type'); // view, edit, export
            $table->string('ip_address')->nullable();
            $table->text('user_agent')->nullable();
            $table->timestamp('accessed_at');

            $table->index(['note_id', 'accessed_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('psychology_notes_access_log');
        Schema::dropIfExists('psychology_notes');
    }
};
