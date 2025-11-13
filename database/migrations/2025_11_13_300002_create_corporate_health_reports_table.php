<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('corporate_health_reports', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained('tenants')->onDelete('cascade');
            $table->foreignId('company_id')->constrained('companies')->onDelete('cascade');

            $table->string('report_number')->unique();
            $table->string('report_title');
            $table->enum('report_type', ['monthly', 'quarterly', 'yearly', 'custom'])->default('monthly');
            $table->date('period_start');
            $table->date('period_end');

            // Statistics
            $table->integer('total_employees');
            $table->integer('employees_examined');
            $table->integer('fit_count')->default(0);
            $table->integer('fit_with_notes_count')->default(0);
            $table->integer('unfit_count')->default(0);
            $table->integer('pending_count')->default(0);

            // Top Findings
            $table->json('common_findings')->nullable(); // Top medical findings
            $table->json('risk_categories')->nullable(); // Health risk breakdown

            // Costs
            $table->decimal('total_cost', 12, 2)->default(0);
            $table->decimal('average_cost_per_employee', 12, 2)->default(0);

            // Report Files
            $table->string('pdf_file')->nullable();
            $table->string('excel_file')->nullable();

            $table->enum('status', ['draft', 'published'])->default('draft');
            $table->timestamp('published_at')->nullable();
            $table->foreignId('generated_by')->nullable()->constrained('users')->onDelete('set null');

            $table->timestamps();

            $table->index(['tenant_id', 'company_id']);
            $table->index('report_number');
            $table->index(['period_start', 'period_end']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('corporate_health_reports');
    }
};
