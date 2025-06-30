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
        Schema::create('doctors', function (Blueprint $table) {
            $table->id();

            // Relasi ke akun user
            $table->foreignId('user_id')->constrained()->onDelete('cascade');

            // Relasi ke admin (pengelola wilayah)
            $table->foreignId('admin_id')->nullable()->constrained('admins')->onDelete('set null');

            // Relasi ke outlet tempat praktik
            $table->foreignId('outlet_id')->nullable()->constrained('outlets')->onDelete('set null');

            // Informasi Profesional
            $table->string('license_number')->nullable(); // STR / SIP
            $table->string('specialization')->nullable(); // contoh: "General Practitioner"
            $table->string('specialist')->nullable();
            $table->string('practice_days')->nullable();  // misal: "Mon-Wed-Fri"
            $table->string('education')->nullable();      // contoh: "Universitas Indonesia"

            // Kontak langsung
            $table->string('phone')->nullable();
            $table->enum('gender', ['male', 'female'])->nullable();
            $table->date('birth_date')->nullable();

            $table->text('signature_image')->nullable();         // bisa simpan base64 atau path file
            $table->string('signature_qrcode')->nullable();      // simpan QR/URL/token unik
            $table->timestamp('signed_at')->nullable();          // waktu tanda tangan dilakukan
            $table->string('signature_token')->nullable();       // untuk validasi QR jika diperlukan
            $table->boolean('is_signature_verified')->default(false); // status verifikasi manual/admin

            $table->timestamps();
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('doctors');
    }
};
