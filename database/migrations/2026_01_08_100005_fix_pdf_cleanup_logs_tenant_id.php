<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * ISSUE: pdf_cleanup_logs.tenant_id is currently a string, but should be a foreignId to tenants table
     *
     * RISK: LOW - Recent table, likely no production data yet
     */
    public function up(): void
    {
        // Step 1: Check if tenants table exists
        if (!Schema::hasTable('tenants')) {
            \Log::warning('Tenants table does not exist, skipping pdf_cleanup_logs.tenant_id migration');
            echo "\n⚠️  Tenants table does not exist. Skipping migration.\n";
            return;
        }

        // Step 2: Check for existing data
        $recordsCount = DB::table('pdf_cleanup_logs')->count();

        if ($recordsCount > 0) {
            \Log::info('pdf_cleanup_logs has existing data', ['count' => $recordsCount]);

            // Check if any tenant_id values are invalid
            $invalidRefs = DB::table('pdf_cleanup_logs as pcl')
                ->whereNotNull('pcl.tenant_id')
                ->whereNotExists(function ($query) {
                    $query->select(DB::raw(1))
                        ->from('tenants')
                        ->whereColumn('tenants.id', 'pcl.tenant_id');
                })
                ->count();

            if ($invalidRefs > 0) {
                // Set invalid references to null
                DB::table('pdf_cleanup_logs')
                    ->whereNotNull('tenant_id')
                    ->whereNotExists(function ($query) {
                        $query->select(DB::raw(1))
                            ->from('tenants')
                            ->whereColumn('tenants.id', 'pdf_cleanup_logs.tenant_id');
                    })
                    ->update(['tenant_id' => null]);

                \Log::warning('Invalid tenant_id references set to null', [
                    'count' => $invalidRefs
                ]);
            }
        }

        // Step 3: Drop the index on tenant_id before modifying
        Schema::table('pdf_cleanup_logs', function (Blueprint $table) {
            $table->dropIndex(['tenant_id', 'executed_at']);
        });

        // Step 4: Change column type from string to unsignedBigInteger
        Schema::table('pdf_cleanup_logs', function (Blueprint $table) {
            $table->unsignedBigInteger('tenant_id')->nullable()->change();
        });

        // Step 5: Add foreign key constraint
        Schema::table('pdf_cleanup_logs', function (Blueprint $table) {
            $table->foreign('tenant_id')
                ->references('id')
                ->on('tenants')
                ->onDelete('cascade');
        });

        // Step 6: Recreate the composite index
        Schema::table('pdf_cleanup_logs', function (Blueprint $table) {
            $table->index(['tenant_id', 'executed_at']);
        });

        // Log the result
        \Log::info('pdf_cleanup_logs.tenant_id foreign key added', [
            'existing_records' => $recordsCount,
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Drop the composite index
        Schema::table('pdf_cleanup_logs', function (Blueprint $table) {
            $table->dropIndex(['tenant_id', 'executed_at']);
        });

        // Drop foreign key
        Schema::table('pdf_cleanup_logs', function (Blueprint $table) {
            $table->dropForeign(['tenant_id']);
        });

        // Change back to string
        Schema::table('pdf_cleanup_logs', function (Blueprint $table) {
            $table->string('tenant_id')->nullable()->change();
        });

        // Recreate the composite index
        Schema::table('pdf_cleanup_logs', function (Blueprint $table) {
            $table->index(['tenant_id', 'executed_at']);
        });

        \Log::info('pdf_cleanup_logs.tenant_id rolled back to string type');
    }
};
