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
     * ISSUE: company_patient_feedback.followed_up_by currently has foreign key to 'companies' table,
     * but the CompanyPatientFeedback model's followedUpBy() relationship expects it to reference 'users' table.
     *
     * RISK: MEDIUM - Need to check if followed_up_by has data and migrate if necessary
     */
    public function up(): void
    {
        // Step 1: Check current data
        $hasData = DB::table('company_patient_feedback')
            ->whereNotNull('followed_up_by')
            ->exists();

        if ($hasData) {
            \Log::info('company_patient_feedback.followed_up_by has existing data', [
                'count' => DB::table('company_patient_feedback')->whereNotNull('followed_up_by')->count()
            ]);

            // Check if data needs migration
            $needsMigration = $this->checkIfDataMigrationNeeded();

            if ($needsMigration) {
                // Temporarily set followed_up_by to null for safety
                // Admin can re-assign after migration
                DB::table('company_patient_feedback')
                    ->whereNotNull('followed_up_by')
                    ->update(['followed_up_by' => null]);

                \Log::warning('company_patient_feedback.followed_up_by data cleared for safe migration', [
                    'reason' => 'References were to companies table, need user references'
                ]);
            }
        }

        // Step 2: Drop existing foreign key constraint
        Schema::table('company_patient_feedback', function (Blueprint $table) {
            // Drop the foreign key constraint to companies table
            $table->dropForeign(['followed_up_by']);
        });

        // Step 3: Add new foreign key constraint to users table
        Schema::table('company_patient_feedback', function (Blueprint $table) {
            // Add foreign key to users table (followed_up_by should reference users.id)
            $table->foreign('followed_up_by')
                ->references('id')
                ->on('users')
                ->onDelete('set null');
        });

        // Log the result
        \Log::info('company_patient_feedback.followed_up_by foreign key fixed', [
            'new_constraint' => 'users.id',
            'had_data' => $hasData,
        ]);
    }

    /**
     * Check if data migration is needed
     * Returns true if followed_up_by values reference companies.id (need to clear)
     * Returns false if followed_up_by values already reference users.id (safe to proceed)
     */
    private function checkIfDataMigrationNeeded(): bool
    {
        $feedbacks = DB::table('company_patient_feedback')
            ->whereNotNull('followed_up_by')
            ->limit(10)
            ->get();

        if ($feedbacks->isEmpty()) {
            return false;
        }

        $referencesCompanies = 0;
        $referencesUsers = 0;

        foreach ($feedbacks as $feedback) {
            $followedUpBy = $feedback->followed_up_by;

            // Check if it references companies table
            $companyExists = DB::table('companies')->where('id', $followedUpBy)->exists();
            if ($companyExists) {
                $referencesCompanies++;
            }

            // Check if it references users table
            $userExists = DB::table('users')->where('id', $followedUpBy)->exists();
            if ($userExists) {
                $referencesUsers++;
            }
        }

        // If more records reference companies than users, we need migration
        return $referencesCompanies > $referencesUsers;
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Step 1: Drop foreign key to users
        Schema::table('company_patient_feedback', function (Blueprint $table) {
            $table->dropForeign(['followed_up_by']);
        });

        // Step 2: Restore foreign key to companies
        // NOTE: Data will be inconsistent after rollback
        Schema::table('company_patient_feedback', function (Blueprint $table) {
            $table->foreign('followed_up_by')
                ->references('id')
                ->on('companies')
                ->onDelete('set null');
        });

        \Log::warning('company_patient_feedback.followed_up_by foreign key rolled back to companies table');
    }
};
