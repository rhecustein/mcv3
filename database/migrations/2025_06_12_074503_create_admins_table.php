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
        Schema::create('admins', function (Blueprint $table) {
            $table->id();

            // Relasi ke tabel users (akun login)
            $table->foreignId('user_id')->constrained()->onDelete('cascade');

            // Informasi wilayah atau area kerja admin
            $table->string('region_name');      // contoh: "Jakarta Selatan"
            $table->string('province')->nullable(); // opsional jika provinsi digunakan

            // Informasi tambahan admin
            $table->string('position_title')->nullable();  // contoh: "Kepala Regional"
            $table->string('contact_number')->nullable();  // no telp langsung admin

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('admins');
    }
};
