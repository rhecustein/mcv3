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
        Schema::create('packages', function (Blueprint $table) {
            $table->id();

            // Identitas paket
            $table->string('name');                           // Nama paket, contoh: "Basic", "Pro", "Enterprise"
            $table->string('code')->unique();                 // Kode paket, contoh: "BASIC01"

            // Fitur paket
            $table->integer('duration_in_days');              // Durasi paket dalam hari (contoh: 30, 90)
            $table->integer('max_letters')->nullable();       // Batas maksimal surat per bulan (nullable = unlimited)
            $table->integer('max_patients')->nullable();      // Maksimal jumlah pasien yang bisa didaftarkan

            // Deskripsi & manfaat
            $table->text('description')->nullable();          // Penjelasan fitur-fitur
            $table->json('features')->nullable();             // Simpan fitur dalam bentuk JSON (opsional)

            // Harga & status
            $table->decimal('price', 12, 2);                  // Harga paket
            $table->boolean('is_active')->default(true);      // Paket tersedia untuk dibeli atau tidak
            $table->boolean('is_recommended')->default(false); // Tampilkan sebagai highlight di UI

            $table->timestamps();
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('packages');
    }
};
