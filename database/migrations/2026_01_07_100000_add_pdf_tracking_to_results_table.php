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
        Schema::table('results', function (Blueprint $table) {
            // PDF tracking columns
            $table->string('pdf_path')->nullable()->after('qrcode');
            $table->timestamp('pdf_generated_at')->nullable();
            $table->timestamp('pdf_deleted_at')->nullable();
            $table->boolean('pdf_archived')->default(false);
            $table->string('pdf_archive_path')->nullable();
            $table->boolean('pdf_generation_failed')->default(false);
            $table->text('pdf_error')->nullable();
            $table->bigInteger('pdf_size_bytes')->nullable();

            // Indexes for performance
            $table->index('pdf_generated_at');
            $table->index('pdf_deleted_at');
            $table->index(['tenant_id', 'pdf_generated_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('results', function (Blueprint $table) {
            $table->dropIndex(['pdf_generated_at']);
            $table->dropIndex(['pdf_deleted_at']);
            $table->dropIndex(['tenant_id', 'pdf_generated_at']);

            $table->dropColumn([
                'pdf_path',
                'pdf_generated_at',
                'pdf_deleted_at',
                'pdf_archived',
                'pdf_archive_path',
                'pdf_generation_failed',
                'pdf_error',
                'pdf_size_bytes',
            ]);
        });
    }
};
