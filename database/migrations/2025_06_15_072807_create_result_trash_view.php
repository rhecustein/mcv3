<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('result_trash_view', function (Blueprint $table) {
            $table->id();

            // Tanggal & waktu penghapusan
            $table->timestamp('deleted_at')->nullable();

            // Informasi tracking
            $table->string('deleted_ip')->nullable();
            $table->string('deleted_location')->nullable();

            // Relasi pengguna & outlet
            $table->foreignId('deleted_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('deleted_outlet_id')->nullable()->constrained('outlets')->nullOnDelete();

            // Untuk tracking atau fitur tambahan
            $table->timestamps(); // created_at & updated_at
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('result_trash_view');
    }
};
