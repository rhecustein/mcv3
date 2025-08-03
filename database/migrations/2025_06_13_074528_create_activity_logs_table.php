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
            
            // Kolom 'user_id' ini mungkin tidak diperlukan lagi jika Anda menggunakan 'causer'.
            // Anda bisa menghapusnya jika 'causer' sudah cukup untuk menyimpan data pengguna.
            // $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();

            // === PERBAIKAN ===
            // Menambahkan kolom 'causer_id' dan 'causer_type' (polymorphic relationship).
            // Ini adalah cara standar untuk melacak model mana (misal: User) yang menyebabkan log.
            // Metode ini juga secara otomatis menambahkan index untuk kedua kolom tersebut.
            $table->nullableMorphs('causer');

            $table->string('action'); // login, logout, create_certificate, etc.
            
            // Kolom ini untuk melacak model yang menjadi objek dari log (misal: Certificate)
            $table->string('model_type')->nullable(); 
            $table->unsignedBigInteger('model_id')->nullable(); 
            
            $table->text('description');
            $table->json('properties')->nullable(); // Additional data
            
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->string('session_id')->nullable();
            
            $table->timestamps();
            
            // === INDEXES ===
            // Index untuk 'user_id' bisa dihapus jika kolomnya tidak dipakai.
            // $table->index(['user_id', 'created_at']);
            
            $table->index(['model_type', 'model_id']); // Model-specific logs
            $table->index('action'); // Action-based queries
            $table->index('created_at'); // Time-based queries
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
