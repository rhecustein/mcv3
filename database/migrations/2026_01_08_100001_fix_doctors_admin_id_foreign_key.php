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
     * ISSUE: doctors.admin_id currently has foreign key to 'admins' table,
     * but the Doctor model's admin() relationship expects it to reference 'users' table.
     *
     * RISK: HIGH - Need to migrate data if admin_id stores admins.id instead of users.id
     */
    public function up(): void
    {
        // Step 1: Check if we need to migrate data
        // If admin_id currently stores admins.id, we need to convert to admins.user_id
        $needsDataMigration = $this->checkIfDataMigrationNeeded();

        // Step 2: Drop existing foreign key constraint
        Schema::table('doctors', function (Blueprint $table) {
            // Drop the foreign key constraint to admins table
            $table->dropForeign(['admin_id']);
        });

        // Step 3: Migrate data if needed
        if ($needsDataMigration) {
            $this->migrateAdminIdData();
        }

        // Step 4: Add new foreign key constraint to users table
        Schema::table('doctors', function (Blueprint $table) {
            // Add foreign key to users table (admin_id should reference users.id)
            $table->foreign('admin_id')
                ->references('id')
                ->on('users')
                ->onDelete('set null');
        });

        // Log the result
        \Log::info('doctors.admin_id foreign key fixed', [
            'data_migration_needed' => $needsDataMigration,
            'new_constraint' => 'users.id',
        ]);
    }

    /**
     * Check if data migration is needed
     * Returns true if admin_id values reference admins.id (need conversion)
     * Returns false if admin_id values already reference users.id (no conversion needed)
     */
    private function checkIfDataMigrationNeeded(): bool
    {
        // Get a sample of doctors with admin_id set
        $doctors = DB::table('doctors')
            ->whereNotNull('admin_id')
            ->limit(10)
            ->get();

        if ($doctors->isEmpty()) {
            // No data to check
            return false;
        }

        $referencesAdmins = 0;
        $referencesUsers = 0;

        foreach ($doctors as $doctor) {
            $adminId = $doctor->admin_id;

            // Check if it references admins table
            $adminExists = DB::table('admins')->where('id', $adminId)->exists();
            if ($adminExists) {
                $referencesAdmins++;
            }

            // Check if it references users table
            $userExists = DB::table('users')->where('id', $adminId)->exists();
            if ($userExists) {
                $referencesUsers++;
            }
        }

        // If more records reference admins than users, we need migration
        return $referencesAdmins > $referencesUsers;
    }

    /**
     * Migrate admin_id from admins.id to admins.user_id
     */
    private function migrateAdminIdData(): void
    {
        // Get all doctors with admin_id that references admins table
        $doctors = DB::table('doctors')
            ->whereNotNull('admin_id')
            ->get();

        $migrated = 0;
        $skipped = 0;
        $errors = 0;

        foreach ($doctors as $doctor) {
            $adminId = $doctor->admin_id;

            // Check if this admin_id references admins table
            $admin = DB::table('admins')->where('id', $adminId)->first();

            if ($admin && $admin->user_id) {
                // Migrate: Replace admins.id with admins.user_id
                DB::table('doctors')
                    ->where('id', $doctor->id)
                    ->update(['admin_id' => $admin->user_id]);
                $migrated++;
            } elseif (DB::table('users')->where('id', $adminId)->exists()) {
                // Already references users table, skip
                $skipped++;
            } else {
                // Orphaned reference, set to null
                DB::table('doctors')
                    ->where('id', $doctor->id)
                    ->update(['admin_id' => null]);
                $errors++;
            }
        }

        \Log::info('doctors.admin_id data migration completed', [
            'migrated' => $migrated,
            'skipped' => $skipped,
            'set_to_null' => $errors,
        ]);

        echo "\n=== Data Migration Results ===\n";
        echo "Migrated: {$migrated}\n";
        echo "Skipped (already correct): {$skipped}\n";
        echo "Set to null (orphaned): {$errors}\n";
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Step 1: Drop foreign key to users
        Schema::table('doctors', function (Blueprint $table) {
            $table->dropForeign(['admin_id']);
        });

        // Step 2: Restore foreign key to admins
        // NOTE: Data may be inconsistent after rollback if migration was performed
        Schema::table('doctors', function (Blueprint $table) {
            $table->foreign('admin_id')
                ->references('id')
                ->on('admins')
                ->onDelete('set null');
        });

        \Log::warning('doctors.admin_id foreign key rolled back to admins table');
    }
};
