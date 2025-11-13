<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained('tenants')->onDelete('cascade');
            $table->string('payment_number')->unique();
            $table->morphs('payable'); // Can be booking, invoice, etc.

            $table->decimal('amount', 12, 2);
            $table->string('currency', 3)->default('IDR');

            $table->enum('payment_method', [
                'bank_transfer',
                'credit_card',
                'debit_card',
                'e_wallet',
                'qris',
                'va',
                'cash',
                'other'
            ]);

            $table->enum('payment_gateway', [
                'midtrans',
                'xendit',
                'doku',
                'manual',
                'other'
            ])->nullable();

            $table->string('gateway_transaction_id')->nullable();
            $table->string('gateway_payment_url')->nullable();
            $table->json('gateway_response')->nullable();

            $table->enum('status', [
                'pending',
                'processing',
                'success',
                'failed',
                'expired',
                'refunded'
            ])->default('pending');

            $table->timestamp('paid_at')->nullable();
            $table->timestamp('expired_at')->nullable();
            $table->timestamp('refunded_at')->nullable();

            $table->text('notes')->nullable();
            $table->string('receipt_url')->nullable();

            $table->timestamps();

            $table->index(['tenant_id', 'status']);
            $table->index('payment_number');
            $table->index('gateway_transaction_id');
            $table->index(['payable_type', 'payable_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};
