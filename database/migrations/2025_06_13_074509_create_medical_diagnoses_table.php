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
        Schema::create('medical_diagnoses', function (Blueprint $table) {
            $table->id();

            // Relasi ke dokter (opsional, bisa ambil dari appointment)
            $table->foreignId('outlet_id')->constrained('outlets')->onDelete('cascade');
            $table->foreignId('patient_id')->constrained('patients')->onDelete('cascade');
            $table->foreignId('doctor_id')->nullable()->constrained('doctors')->onDelete('set null');
            $table->foreignId('icd_master_id')->nullable()->constrained('icd_masters')->onDelete('set null');

            // Diagnosis utama
            $table->string('diagnosis_name');         // contoh: "Demam Berdarah"
            $table->text('description')->nullable();  // keterangan tambahan

            // Tanggal diagnosis
            $table->date('diagnosed_at')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('medical_diagnoses');
    }
};
