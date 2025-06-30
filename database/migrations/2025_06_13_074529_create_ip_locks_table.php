<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('ip_locks', function (Blueprint $table) {
            $table->id();

            // IP Address yang diblokir
            $table->ipAddress('ip_address')->index();

            // Tipe blokir: sementara atau permanen
            $table->enum('lock_type', ['temporary', 'permanent'])->default('temporary');

            // Informasi terkait blokir
            $table->string('reason')->nullable();           // contoh: "Terlalu banyak percobaan login"
            $table->timestamp('locked_at')->nullable();     // kapan mulai diblok
            $table->timestamp('unlocked_at')->nullable();   // kapan di-unblock (untuk temporary)

            // User yang mengunci (opsional)
            $table->foreignId('locked_by')->nullable()->constrained('users')->onDelete('set null');

            $table->string('city')->nullable();
            $table->string('province')->nullable();
            $table->boolean('success')->default(false);
            $table->timestamp('logged_in_at');

            // Status kunci aktif atau tidak
            $table->boolean('is_active')->default(true);

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ip_locks');
    }
};
