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
            // Compression tracking
            $table->boolean('pdf_compressed')->default(false)->after('pdf_size_bytes');
            $table->timestamp('pdf_compressed_at')->nullable();
            $table->bigInteger('pdf_original_size_bytes')->nullable();
            $table->integer('pdf_compression_ratio')->nullable(); // Percentage (e.g., 70 = 70% reduction)
            $table->string('pdf_compression_method')->nullable(); // ghostscript, imagemagick, etc.

            // Index for finding PDFs to compress
            $table->index(['pdf_compressed', 'pdf_generated_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('results', function (Blueprint $table) {
            $table->dropIndex(['pdf_compressed', 'pdf_generated_at']);

            $table->dropColumn([
                'pdf_compressed',
                'pdf_compressed_at',
                'pdf_original_size_bytes',
                'pdf_compression_ratio',
                'pdf_compression_method',
            ]);
        });
    }
};
