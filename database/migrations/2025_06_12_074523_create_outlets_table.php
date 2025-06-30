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
        Schema::create('outlets', function (Blueprint $table) {
            $table->id();

            // Relasi ke admin yang membawahi outlet
            $table->foreignId('admin_id')->nullable()->constrained('admins')->onDelete('set null');
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            // Informasi utama outlet
            $table->string('name');
            $table->string('code')->unique()->nullable();      // Kode outlet unik (opsional)
            $table->string('phone')->nullable();
            $table->string('email')->nullable();

            // Alamat dan lokasi
            $table->string('address')->nullable();
            $table->string('city')->nullable();
            $table->string('province')->nullable();
            $table->string('postal_code')->nullable();
            //latitude dan longitude
            $table->text('latitude')->nullable();
            $table->text('longitude')->nullable();

            //image
            $table->string('logo')->nullable(); // Logo outlet
            $table->string('banner')->nullable(); // Banner outlet
            $table->string('qr_code')->nullable(); // Kode QR outlet

            // Operasional
            $table->boolean('is_active')->default(true);
            $table->string('timezone')->nullable();

            $table->timestamps();
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('outlets');
    }
};
