<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * ISSUE: results.deleted_at is currently defined as string, but the Result model uses SoftDeletes trait
     * which expects it to be a timestamp column.
     *
     * RISK: HIGH - Need to parse existing string dates to timestamps
     *
     * This migration:
     * 1. Backs up existing deleted_at string values
     * 2. Parses them to Carbon timestamps
     * 3. Changes column type to timestamp
     * 4. Restores parsed timestamps
     */
    public function up(): void
    {
        // Step 1: Check if there's existing data in deleted_at
        $hasDeletedRecords = DB::table('results')
            ->whereNotNull('deleted_at')
            ->exists();

        $backupData = [];

        if ($hasDeletedRecords) {
            // Backup existing deleted_at values
            $deletedResults = DB::table('results')
                ->whereNotNull('deleted_at')
                ->select('id', 'deleted_at')
                ->get();

            \Log::info('Backing up results.deleted_at string values', [
                'count' => $deletedResults->count()
            ]);

            foreach ($deletedResults as $result) {
                $backupData[$result->id] = $result->deleted_at;
            }
        }

        // Step 2: Temporarily set deleted_at to NULL to avoid type conflicts
        if ($hasDeletedRecords) {
            DB::table('results')->whereNotNull('deleted_at')->update(['deleted_at' => null]);
        }

        // Step 3: Change column type from string to timestamp
        Schema::table('results', function (Blueprint $table) {
            // Drop the string column and recreate as timestamp
            $table->dropColumn('deleted_at');
        });

        Schema::table('results', function (Blueprint $table) {
            // Add deleted_at as timestamp (for SoftDeletes)
            $table->timestamp('deleted_at')->nullable()->after('created_longitude');
        });

        // Step 4: Restore and parse backed up values
        if (!empty($backupData)) {
            $parsed = 0;
            $failed = 0;

            foreach ($backupData as $id => $deletedAtString) {
                try {
                    // Try multiple date formats
                    $timestamp = $this->parseDeletedAt($deletedAtString);

                    if ($timestamp) {
                        DB::table('results')
                            ->where('id', $id)
                            ->update(['deleted_at' => $timestamp]);
                        $parsed++;
                    } else {
                        // Use current timestamp as fallback for unparseable values
                        DB::table('results')
                            ->where('id', $id)
                            ->update(['deleted_at' => now()]);
                        $failed++;

                        \Log::warning('Could not parse deleted_at value', [
                            'result_id' => $id,
                            'original_value' => $deletedAtString,
                            'fallback' => 'current timestamp'
                        ]);
                    }
                } catch (\Exception $e) {
                    $failed++;
                    // Set to current timestamp as fallback
                    DB::table('results')
                        ->where('id', $id)
                        ->update(['deleted_at' => now()]);

                    \Log::error('Error parsing deleted_at', [
                        'result_id' => $id,
                        'error' => $e->getMessage(),
                        'original_value' => $deletedAtString
                    ]);
                }
            }

            \Log::info('results.deleted_at migration completed', [
                'total' => count($backupData),
                'parsed' => $parsed,
                'failed_fallback_to_now' => $failed,
            ]);

            echo "\n=== deleted_at Migration Results ===\n";
            echo "Total records: " . count($backupData) . "\n";
            echo "Successfully parsed: {$parsed}\n";
            echo "Failed (set to current time): {$failed}\n";
        }

        \Log::info('results.deleted_at column type changed from string to timestamp');
    }

    /**
     * Parse deleted_at string to Carbon timestamp
     * Tries multiple formats to maximize compatibility
     */
    private function parseDeletedAt($value): ?Carbon
    {
        if (empty($value) || $value === 'NULL' || $value === 'null') {
            return null;
        }

        // List of common date formats to try
        $formats = [
            'Y-m-d H:i:s',      // MySQL datetime format
            'Y-m-d',             // Date only
            'd/m/Y H:i:s',       // Indonesian format with time
            'd/m/Y',             // Indonesian format date only
            'd-m-Y H:i:s',       // Dash format with time
            'd-m-Y',             // Dash format date only
            'Y-m-d\TH:i:s',      // ISO 8601
            'Y-m-d\TH:i:s.u\Z',  // ISO 8601 with microseconds
        ];

        foreach ($formats as $format) {
            try {
                $date = Carbon::createFromFormat($format, $value);
                if ($date) {
                    return $date;
                }
            } catch (\Exception $e) {
                // Continue to next format
                continue;
            }
        }

        // Last resort: try Carbon's flexible parse
        try {
            return Carbon::parse($value);
        } catch (\Exception $e) {
            return null;
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Step 1: Backup current timestamp values
        $deletedResults = DB::table('results')
            ->whereNotNull('deleted_at')
            ->select('id', 'deleted_at')
            ->get();

        $backupData = [];
        foreach ($deletedResults as $result) {
            // Convert timestamp to string format
            $backupData[$result->id] = Carbon::parse($result->deleted_at)->format('Y-m-d H:i:s');
        }

        // Step 2: Drop timestamp column
        Schema::table('results', function (Blueprint $table) {
            $table->dropColumn('deleted_at');
        });

        // Step 3: Recreate as string
        Schema::table('results', function (Blueprint $table) {
            $table->string('deleted_at')->nullable()->after('created_longitude');
        });

        // Step 4: Restore string values
        foreach ($backupData as $id => $deletedAtString) {
            DB::table('results')
                ->where('id', $id)
                ->update(['deleted_at' => $deletedAtString]);
        }

        \Log::warning('results.deleted_at rolled back to string type (SoftDeletes will not work properly)');
    }
};
