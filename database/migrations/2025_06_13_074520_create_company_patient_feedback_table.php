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
        Schema::create('company_patient_feedback', function (Blueprint $table) {
            $table->id();

            // Relasi utama
            $table->foreignId('company_id')->constrained('companies')->onDelete('cascade');
            $table->foreignId('patient_id')->constrained('patients')->onDelete('cascade');

            // Ulasan atau keluhan
            $table->text('feedback');                       // Isi masukan dari pasien
            $table->enum('type', ['complaint', 'suggestion', 'praise'])->default('suggestion');

            // Penilaian layanan (opsional)
            $table->tinyInteger('rating')->nullable();      // Skala 1–5

            // Status tindak lanjut
            $table->boolean('is_followed_up')->default(false);
            $table->timestamp('followed_up_at')->nullable();
            $table->foreignId('followed_up_by')->nullable()->constrained('companies')->onDelete('set null');

            $table->timestamps();
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('company_patient_feedback');
    }
};
