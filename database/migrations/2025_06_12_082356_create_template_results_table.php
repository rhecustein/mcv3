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
        Schema::create('template_results', function (Blueprint $table) {
            $table->id();

            // Informasi template
            $table->string('name');                    // Nama template
            $table->string('code')->unique();          // Kode unik, misal: "SSU01"
            $table->enum('type', ['mc', 'skb']);       // mc: surat sakit, skb: surat sehat
            $table->text('description')->nullable();   // Keterangan opsional

            // Konten HTML surat
            $table->longText('html_content');          // HTML body surat

            // Pengaturan
            $table->boolean('default')->default(false);    // Template default untuk jenis tertentu
            $table->boolean('is_active')->default(true);   // Aktif atau tidak

            // Pembuat template
            $table->foreignId('created_by')->nullable()->constrained('users')->onDelete('set null');

            $table->timestamps();
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('template_results');
    }
};
