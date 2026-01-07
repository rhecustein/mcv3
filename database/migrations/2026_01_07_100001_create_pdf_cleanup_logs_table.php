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
        Schema::create('pdf_cleanup_logs', function (Blueprint $table) {
            $table->id();
            $table->string('tenant_id')->nullable(); // null = all tenants
            $table->integer('days_old'); // PDFs older than X days
            $table->integer('archived_count')->default(0);
            $table->integer('deleted_count')->default(0);
            $table->integer('error_count')->default(0);
            $table->bigInteger('freed_bytes')->default(0); // Storage freed
            $table->boolean('archive_enabled')->default(true);
            $table->string('triggered_by')->nullable(); // 'schedule', 'manual', 'user_id'
            $table->text('notes')->nullable();
            $table->timestamp('executed_at');
            $table->timestamps();

            $table->index('executed_at');
            $table->index(['tenant_id', 'executed_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pdf_cleanup_logs');
    }
};
