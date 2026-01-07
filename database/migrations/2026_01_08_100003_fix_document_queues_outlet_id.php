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
     * ISSUE: document_queues.outlet_id is defined as integer without foreign key constraint
     * Should have proper foreign key constraint to outlets table
     *
     * RISK: LOW - Type is compatible (integer → unsignedBigInteger), just adding constraint
     */
    public function up(): void
    {
        // Step 1: Check data integrity before adding constraint
        $invalidReferences = DB::table('document_queues as dq')
            ->whereNotNull('dq.outlet_id')
            ->whereNotExists(function ($query) {
                $query->select(DB::raw(1))
                    ->from('outlets')
                    ->whereColumn('outlets.id', 'dq.outlet_id');
            })
            ->count();

        if ($invalidReferences > 0) {
            \Log::warning('document_queues.outlet_id has invalid references', [
                'count' => $invalidReferences
            ]);

            // Set invalid references to null
            DB::table('document_queues')
                ->whereNotNull('outlet_id')
                ->whereNotExists(function ($query) {
                    $query->select(DB::raw(1))
                        ->from('outlets')
                        ->whereColumn('outlets.id', 'document_queues.outlet_id');
                })
                ->update(['outlet_id' => null]);

            \Log::info('Invalid outlet_id references set to null', [
                'count' => $invalidReferences
            ]);
        }

        // Step 2: Change column type and add foreign key constraint
        Schema::table('document_queues', function (Blueprint $table) {
            // Change from integer to unsignedBigInteger (foreignId compatible)
            $table->unsignedBigInteger('outlet_id')->nullable()->change();
        });

        // Step 3: Add foreign key constraint
        Schema::table('document_queues', function (Blueprint $table) {
            $table->foreign('outlet_id')
                ->references('id')
                ->on('outlets')
                ->onDelete('set null');
        });

        // Log the result
        \Log::info('document_queues.outlet_id foreign key added', [
            'invalid_references_cleaned' => $invalidReferences,
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Drop foreign key constraint
        Schema::table('document_queues', function (Blueprint $table) {
            $table->dropForeign(['outlet_id']);
        });

        // Change back to integer
        Schema::table('document_queues', function (Blueprint $table) {
            $table->integer('outlet_id')->nullable()->change();
        });

        \Log::info('document_queues.outlet_id foreign key removed');
    }
};
