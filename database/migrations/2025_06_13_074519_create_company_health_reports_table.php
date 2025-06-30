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
        Schema::create('company_health_reports', function (Blueprint $table) {
            $table->id();

            // Relasi ke perusahaan
            $table->foreignId('company_id')->constrained('companies')->onDelete('cascade');

            // Periode laporan
            $table->date('report_date');                            // Tanggal dibuatnya rekap
            $table->string('report_period')->nullable();            // Contoh: "2025-06", "Q2-2025"

            // Ringkasan data kesehatan
            $table->integer('total_patients')->default(0);          // Jumlah pasien terdaftar
            $table->integer('total_appointments')->default(0);      // Jumlah konsultasi
            $table->integer('total_sick_letters')->default(0);      // Jumlah surat sakit
            $table->integer('total_health_letters')->default(0);    // Jumlah surat sehat

            // Data penyakit dominan
            $table->string('top_diagnosis')->nullable();            // Nama diagnosis terbanyak
            $table->integer('top_diagnosis_count')->nullable();     // Jumlah kasus diagnosis terbanyak

            // Disimpan sebagai file / JSON opsional
            $table->string('report_file')->nullable();              // Path PDF jika hasil report disimpan
            $table->json('data_json')->nullable();                  // Backup raw statistik dalam JSON

            $table->timestamps();
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('company_health_reports');
    }
};
