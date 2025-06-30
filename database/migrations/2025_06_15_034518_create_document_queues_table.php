<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('document_queues', function (Blueprint $table) {
            $table->id();

            // Relasi
            $table->foreignId('result_id')->constrained('results')->onDelete('cascade');

            // Snapshot data dari Result
            $table->string('no_letters')->nullable();         // nomor surat
            $table->string('patient_name')->nullable();       // nama pasien
            $table->enum('type_surat', ['skb', 'mc'])->nullable(); // jenis surat
            $table->string('outlet_name')->nullable();          // outlet tempat pemeriksaan
            $table->date('start_date')->nullable();           // tanggal mulai berlaku
            $table->date('expired_date')->nullable();         // tanggal selesai berlaku

            // Status queue
            $table->enum('status', ['pending', 'processing', 'success', 'failed'])->default('pending');
            $table->string('generated_file')->nullable();
            $table->text('error_log')->nullable();
            $table->unsignedInteger('retry_count')->default(0);
            $table->enum('triggered_by', ['auto', 'manual'])->default('auto');

            // Timestamps proses
            $table->timestamp('processed_at')->nullable();
            $table->timestamp('completed_at')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('document_queues');
    }
};
