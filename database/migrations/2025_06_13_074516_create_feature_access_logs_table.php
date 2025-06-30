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
        Schema::create('feature_access_logs', function (Blueprint $table) {
            $table->id();

            // Pengakses fitur
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');

            // Terkait outlet/perusahaan (jika berlaku)
            $table->foreignId('outlet_id')->nullable()->constrained('outlets')->onDelete('set null');
            $table->foreignId('company_id')->nullable()->constrained('companies')->onDelete('set null');

            // Nama fitur yang diakses
            $table->string('feature_name');                // contoh: "generate_pdf", "view_dashboard", "download_excel"
            $table->string('feature_category')->nullable(); // contoh: "report", "surat", "statistik"

            // Konteks tambahan
            $table->string('access_url')->nullable();      // URL atau route yang diakses
            $table->ipAddress('ip_address')->nullable();   // alamat IP pengakses
            $table->string('device')->nullable();          // contoh: "web", "mobile", "tablet"

            // Waktu akses otomatis dari timestamps
            $table->timestamps();
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('feature_access_logs');
    }
};
