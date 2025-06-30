<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('package_transactions', function (Blueprint $table) {
            $table->id();

            // Relasi ke pembeli
            $table->foreignId('company_id')->nullable()->constrained('companies')->onDelete('set null');
            $table->foreignId('outlet_id')->nullable()->constrained('outlets')->onDelete('set null');
            $table->foreignId('created_by')->nullable()->constrained('users')->onDelete('set null');

            // Relasi ke paket
            $table->foreignId('package_id')->constrained('packages')->onDelete('cascade');

            // Info transaksi
            $table->string('transaction_code')->unique();
            $table->enum('status', ['pending', 'paid', 'failed', 'expired'])->default('pending');
            $table->string('payment_method')->nullable();
            $table->string('payment_reference')->nullable();
            $table->enum('renewal_type', ['manual', 'auto', 'renewal'])->default('manual');
            $table->decimal('amount', 12, 2);
            $table->timestamp('paid_at')->nullable();

            // Masa aktif paket
            $table->date('start_date')->nullable();
            $table->date('end_date')->nullable();

            // Tambahan
            $table->string('invoice_number')->nullable();
            $table->json('metadata')->nullable();

            $table->timestamps();

            // Index tambahan
            $table->index(['company_id', 'status']);
            $table->index(['start_date', 'end_date']);
            $table->index('transaction_code');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('package_transactions');
    }
};
