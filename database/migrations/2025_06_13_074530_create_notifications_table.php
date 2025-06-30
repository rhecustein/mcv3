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
        Schema::create('notifications', function (Blueprint $table) {
            $table->id();

            // Target notifikasi
            $table->foreignId('user_id')->nullable()->constrained('users')->onDelete('cascade'); // notifikasi untuk siapa

            // Sumber notifikasi (opsional)
            $table->string('title');
            $table->text('message')->nullable();
            $table->string('type')->nullable();                    // contoh: "info", "warning", "system", "alert"

            // Media pengiriman
            $table->boolean('via_email')->default(false);
            $table->boolean('via_whatsapp')->default(false);
            $table->boolean('via_in_app')->default(true);

            // Status & waktu baca
            $table->boolean('is_read')->default(false);
            $table->timestamp('read_at')->nullable();

            // Routing ke halaman tertentu (opsional)
            $table->string('action_url')->nullable();
            $table->string('action_text')->nullable();             // contoh: "View Letter"

            // Siapa yang mengirim (opsional)
            $table->foreignId('sent_by')->nullable()->constrained('users')->onDelete('set null');

            $table->timestamps();
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('notifications');
    }
};
