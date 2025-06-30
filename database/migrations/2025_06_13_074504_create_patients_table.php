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
        Schema::create('patients', function (Blueprint $table) {
            $table->id();

            // Relasi ke user (akun login)
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('outlet_id')->nullable()->constrained('outlets')->onDelete('set null');
            $table->foreignId('admin_id')->nullable()->constrained('admins')->onDelete('set null');
            // Relasi opsional ke perusahaan (admin perusahaan)
            $table->foreignId('company_id')->nullable()->constrained('companies')->onDelete('set null');

            // Informasi identitas tambahan
            $table->string('full_name');                  // Nama lengkap
            $table->string('nik')->nullable();            // Nomor Induk Kependudukan
            $table->string('identity')->nullable(); // Nomor identitas lain (KTP, SIM, dsb.)
            $table->string('gender')->nullable();         // Male / Female / Other
            $table->date('birth_date')->nullable();
            $table->string('birth_place')->nullable();    // Tempat lahir
            $table->string('blood_type')->nullable();     // A, B, AB, O
            $table->string('phone')->nullable();
            $table->text('address')->nullable();
            $table->string('emergency_contact_name')->nullable(); // Nama kontak darurat
            //martial status
            $table->enum('marital_status', ['single', 'married', 'divorced', 'widowed'])->default('single');
            //job
            $table->string('job')->nullable();            // Pekerjaan saat ini
            $table->string('company_name')->nullable();   // Nama perusahaan tempat bekerja
            //religion
            $table->string('religion')->nullable();       // Agama

            // Informasi medis ringan
            $table->text('medical_notes')->nullable();    // catatan penting (riwayat, alergi, dsb.)

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('patients');
    }
};
