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
        Schema::create('companies', function (Blueprint $table) {
            $table->id();

            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete(); // tanpa after()
            $table->foreignId('package_id')->nullable()->constrained('packages')->nullOnDelete();

            // Tracking masa aktif langganan
            $table->date('package_start_date')->nullable();
            $table->date('package_end_date')->nullable();

            // Informasi tracking paket
            $table->integer('used_letters_this_month')->default(0);     // Surat yang sudah dibuat bulan ini
            $table->integer('used_patients_total')->default(0);         // Total pasien aktif yang didaftarkan

            // Informasi umum perusahaan
            $table->string('name');
            $table->string('code')->unique()->nullable();          // Kode unik perusahaan (untuk identifikasi/admin)
            $table->string('industry_type')->nullable();           // Jenis industri, contoh: "Manufaktur", "IT", "Kesehatan"
            $table->string('registration_number')->nullable();     // Nomor registrasi/legalitas (opsional)
            $table->string('email')->nullable();
            $table->string('phone')->nullable();

            // Alamat
            $table->text('address')->nullable();
            $table->string('city')->nullable();
            $table->string('province')->nullable();
            $table->string('postal_code')->nullable();

            // Status perusahaan
            $table->boolean('is_active')->default(true);

            $table->timestamps();
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('companies');
    }
};
