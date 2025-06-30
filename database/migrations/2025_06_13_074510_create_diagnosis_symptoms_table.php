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
        Schema::create('diagnosis_symptoms', function (Blueprint $table) {
            $table->id();

            // Relasi ke diagnosis
            $table->foreignId('medical_diagnosis_id')->constrained('medical_diagnoses')->onDelete('cascade');

            // Nama gejala
            $table->string('symptom');

            // Optional severity or duration
            $table->enum('severity', ['mild', 'moderate', 'severe'])->nullable();
            $table->string('duration')->nullable(); // contoh: "2 days", "1 week"

            $table->timestamps();
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('diagnosis_symptoms');
    }
};
