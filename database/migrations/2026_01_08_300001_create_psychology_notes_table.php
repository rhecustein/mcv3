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
        Schema::create('psychology_notes', function (Blueprint $table) {
            $table->id();

            // Foreign keys
            $table->foreignId('session_id')->nullable()->constrained('psychology_sessions')->onDelete('cascade');
            $table->foreignId('psychologist_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('patient_id')->constrained('patients')->onDelete('cascade');
            $table->foreignId('created_by')->nullable()->constrained('users')->onDelete('set null');

            // Note details
            $table->string('note_type')->default('session_note'); // session_note, assessment, observation, recommendation
            $table->text('content');
            $table->boolean('is_private')->default(false);
            $table->json('attachments')->nullable(); // Array of file paths/URLs

            // Timestamps
            $table->timestamps();

            // Indexes for performance
            $table->index('session_id');
            $table->index('psychologist_id');
            $table->index('patient_id');
            $table->index('note_type');
            $table->index('is_private');
            $table->index('created_at');

            // Composite indexes
            $table->index(['patient_id', 'created_at']);
            $table->index(['psychologist_id', 'created_at']);
            $table->index(['session_id', 'note_type']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('psychology_notes');
    }
};
