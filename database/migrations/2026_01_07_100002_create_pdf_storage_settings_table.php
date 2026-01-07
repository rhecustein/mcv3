<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('pdf_storage_settings', function (Blueprint $table) {
            $table->id();
            $table->string('tenant_id')->nullable(); // null = global default
            $table->integer('auto_delete_days')->default(90); // 3 months default
            $table->boolean('auto_delete_enabled')->default(true);
            $table->boolean('archive_before_delete')->default(true);
            $table->string('archive_storage')->default('s3'); // s3, local, glacier
            $table->integer('compression_days')->default(30); // Compress PDFs older than 30 days
            $table->boolean('compression_enabled')->default(false);
            $table->bigInteger('storage_quota_bytes')->nullable(); // Max storage per tenant
            $table->boolean('alert_enabled')->default(true);
            $table->integer('alert_threshold_percent')->default(80); // Alert at 80%
            $table->string('alert_email')->nullable();
            $table->timestamps();

            $table->index('tenant_id');
            $table->unique('tenant_id');
        });

        // Insert default global settings
        DB::table('pdf_storage_settings')->insert([
            'tenant_id' => null,
            'auto_delete_days' => 90,
            'auto_delete_enabled' => true,
            'archive_before_delete' => true,
            'archive_storage' => 's3',
            'compression_days' => 30,
            'compression_enabled' => false,
            'storage_quota_bytes' => null,
            'alert_enabled' => true,
            'alert_threshold_percent' => 80,
            'alert_email' => null,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pdf_storage_settings');
    }
};
