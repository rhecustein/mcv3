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
        Schema::create('patient_company_relations', function (Blueprint $table) {
            $table->id();

            // Relasi ke pasien dan perusahaan
            $table->foreignId('patient_id')->constrained('patients')->onDelete('cascade');
            $table->foreignId('company_id')->constrained('companies')->onDelete('cascade');

            // Detail hubungan kerja
            $table->string('employee_id')->nullable();           // NIK karyawan internal
            $table->string('position')->nullable();              // Posisi/jabatan
            $table->string('department')->nullable();            // Departemen/bagian
            $table->enum('status', ['active', 'inactive'])->default('active'); // status hubungan

            // Riwayat waktu kerja
            $table->date('joined_at')->nullable();
            $table->date('left_at')->nullable();

            // Untuk audit
            $table->boolean('is_verified')->default(false);
            $table->timestamp('verified_at')->nullable();

            $table->timestamps();
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('patient_company_relations');
    }
};
