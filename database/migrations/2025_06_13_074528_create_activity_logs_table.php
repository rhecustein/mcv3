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
        Schema::create('activity_logs', function (Blueprint $table) {
            $table->id();

            // Aktor aktivitas
            $table->foreignId('user_id')->nullable()->constrained('users')->onDelete('set null');
            $table->string('role_type')->nullable(); // contoh: admin, doctor, patient, company_admin

            // Aktivitas
            $table->string('event');                 // contoh: "login", "create_result", "export_pdf"
            $table->text('description')->nullable(); // detail penjelasan aktivitas

            // Konteks objek yang diubah (opsional)
            $table->string('subject_type')->nullable(); // Model class, e.g. "App\Models\Result"
            $table->unsignedBigInteger('subject_id')->nullable(); // ID dari model tersebut

            // Metadata
            $table->ipAddress('ip_address')->nullable();
            $table->string('user_agent')->nullable();   // Browser/device info
            $table->string('location')->nullable();     // Optional, bisa integrasi GeoIP

            $table->timestamps();
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('activity_logs');
    }
};
