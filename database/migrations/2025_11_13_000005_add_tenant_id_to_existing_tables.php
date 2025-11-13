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
        // Add tenant_id to all existing tables
        $tables = [
            'users',
            'outlets',
            'patients',
            'doctors',
            'results',
            'companies',
            'company_health_reports',
            'document_queues',
        ];

        foreach ($tables as $tableName) {
            if (Schema::hasTable($tableName)) {
                Schema::table($tableName, function (Blueprint $table) {
                    if (!Schema::hasColumn($table->getTable(), 'tenant_id')) {
                        $table->foreignId('tenant_id')->nullable()->after('id')->constrained('tenants')->onDelete('cascade');
                        $table->index('tenant_id');
                    }
                });
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $tables = [
            'users',
            'outlets',
            'patients',
            'doctors',
            'results',
            'companies',
            'company_health_reports',
            'document_queues',
        ];

        foreach ($tables as $tableName) {
            if (Schema::hasTable($tableName)) {
                Schema::table($tableName, function (Blueprint $table) {
                    if (Schema::hasColumn($table->getTable(), 'tenant_id')) {
                        $table->dropForeign(['tenant_id']);
                        $table->dropColumn('tenant_id');
                    }
                });
            }
        }
    }
};
