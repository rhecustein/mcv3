<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * This migration audits foreign key data to ensure safe migration in subsequent steps.
     */
    public function up(): void
    {
        $auditResults = [];

        // === AUDIT #1: doctors.admin_id ===
        // Check if admin_id references admins.id or users.id
        if (Schema::hasTable('doctors') && Schema::hasColumn('doctors', 'admin_id')) {
            $doctors = DB::table('doctors')
                ->whereNotNull('admin_id')
                ->select('admin_id')
                ->distinct()
                ->get();

            $adminIdReferencesAdmins = 0;
            $adminIdReferencesUsers = 0;
            $adminIdOrphaned = 0;

            foreach ($doctors as $doctor) {
                $adminId = $doctor->admin_id;

                // Check if it references admins table
                $adminExists = DB::table('admins')->where('id', $adminId)->exists();
                if ($adminExists) {
                    $adminIdReferencesAdmins++;
                }

                // Check if it references users table
                $userExists = DB::table('users')->where('id', $adminId)->exists();
                if ($userExists) {
                    $adminIdReferencesUsers++;
                }

                // Check if orphaned
                if (!$adminExists && !$userExists) {
                    $adminIdOrphaned++;
                }
            }

            $auditResults['doctors.admin_id'] = [
                'total_records' => $doctors->count(),
                'references_admins' => $adminIdReferencesAdmins,
                'references_users' => $adminIdReferencesUsers,
                'orphaned' => $adminIdOrphaned,
            ];
        }

        // === AUDIT #2: company_patient_feedback.followed_up_by ===
        if (Schema::hasTable('company_patient_feedback') && Schema::hasColumn('company_patient_feedback', 'followed_up_by')) {
            $feedbacks = DB::table('company_patient_feedback')
                ->whereNotNull('followed_up_by')
                ->select('followed_up_by')
                ->distinct()
                ->get();

            $referencesCompanies = 0;
            $referencesUsers = 0;
            $orphaned = 0;

            foreach ($feedbacks as $feedback) {
                $followedUpBy = $feedback->followed_up_by;

                // Check if references companies
                $companyExists = DB::table('companies')->where('id', $followedUpBy)->exists();
                if ($companyExists) {
                    $referencesCompanies++;
                }

                // Check if references users
                $userExists = DB::table('users')->where('id', $followedUpBy)->exists();
                if ($userExists) {
                    $referencesUsers++;
                }

                if (!$companyExists && !$userExists) {
                    $orphaned++;
                }
            }

            $auditResults['company_patient_feedback.followed_up_by'] = [
                'total_records' => $feedbacks->count(),
                'references_companies' => $referencesCompanies,
                'references_users' => $referencesUsers,
                'orphaned' => $orphaned,
            ];
        }

        // === AUDIT #3: results.deleted_at ===
        if (Schema::hasTable('results') && Schema::hasColumn('results', 'deleted_at')) {
            $columnType = DB::select("SHOW COLUMNS FROM results WHERE Field = 'deleted_at'");
            $type = $columnType[0]->Type ?? 'unknown';

            $totalDeletedAt = DB::table('results')
                ->whereNotNull('deleted_at')
                ->count();

            // Sample some deleted_at values
            $samples = DB::table('results')
                ->whereNotNull('deleted_at')
                ->limit(5)
                ->pluck('deleted_at');

            $auditResults['results.deleted_at'] = [
                'column_type' => $type,
                'total_non_null' => $totalDeletedAt,
                'sample_values' => $samples->toArray(),
            ];
        }

        // === AUDIT #4: document_queues.outlet_id ===
        if (Schema::hasTable('document_queues') && Schema::hasColumn('document_queues', 'outlet_id')) {
            $columnType = DB::select("SHOW COLUMNS FROM document_queues WHERE Field = 'outlet_id'");
            $type = $columnType[0]->Type ?? 'unknown';

            $totalRecords = DB::table('document_queues')->count();
            $outletIdNull = DB::table('document_queues')->whereNull('outlet_id')->count();
            $outletIdValid = DB::table('document_queues')
                ->whereNotNull('outlet_id')
                ->whereExists(function ($query) {
                    $query->select(DB::raw(1))
                        ->from('outlets')
                        ->whereColumn('outlets.id', 'document_queues.outlet_id');
                })
                ->count();

            $auditResults['document_queues.outlet_id'] = [
                'column_type' => $type,
                'total_records' => $totalRecords,
                'null_count' => $outletIdNull,
                'valid_references' => $outletIdValid,
                'invalid_references' => $totalRecords - $outletIdNull - $outletIdValid,
            ];
        }

        // === AUDIT #5: pdf_cleanup_logs.tenant_id ===
        if (Schema::hasTable('pdf_cleanup_logs') && Schema::hasColumn('pdf_cleanup_logs', 'tenant_id')) {
            $columnType = DB::select("SHOW COLUMNS FROM pdf_cleanup_logs WHERE Field = 'tenant_id'");
            $type = $columnType[0]->Type ?? 'unknown';

            $totalRecords = DB::table('pdf_cleanup_logs')->count();

            $auditResults['pdf_cleanup_logs.tenant_id'] = [
                'column_type' => $type,
                'total_records' => $totalRecords,
            ];
        }

        // === LOG AUDIT RESULTS ===
        Log::channel('single')->info('=== FOREIGN KEY AUDIT RESULTS ===', $auditResults);

        // Also output to console
        echo "\n=== FOREIGN KEY AUDIT RESULTS ===\n";
        echo json_encode($auditResults, JSON_PRETTY_PRINT);
        echo "\n\n";

        // Store audit results in a temporary table for reference
        if (!Schema::hasTable('_audit_results_temp')) {
            Schema::create('_audit_results_temp', function ($table) {
                $table->id();
                $table->text('audit_data');
                $table->timestamp('created_at');
            });
        }

        DB::table('_audit_results_temp')->insert([
            'audit_data' => json_encode($auditResults),
            'created_at' => now(),
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Drop temporary audit table
        Schema::dropIfExists('_audit_results_temp');
    }
};
