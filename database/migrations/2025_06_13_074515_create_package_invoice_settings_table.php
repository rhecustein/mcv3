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
        Schema::create('package_invoice_settings', function (Blueprint $table) {
            $table->id();

            // Relasi ke perusahaan atau outlet (salah satu saja akan digunakan per record)
            $table->foreignId('company_id')->nullable()->constrained('companies')->onDelete('cascade');
            $table->foreignId('outlet_id')->nullable()->constrained('outlets')->onDelete('cascade');

            // Informasi invoice
            $table->string('invoice_title')->nullable();           // Judul/kop invoice
            $table->string('invoice_email')->nullable();           // Email tujuan invoice
            $table->string('billing_contact')->nullable();         // Nama penanggung jawab pembayaran
            $table->string('billing_phone')->nullable();           // No telepon finance/admin
            $table->text('billing_address')->nullable();           // Alamat penagihan
            $table->string('tax_id')->nullable();                  // NPWP atau ID pajak jika ada

            // Preferensi pengiriman invoice
            $table->enum('send_invoice_via', ['email', 'whatsapp', 'both'])->default('email');
            $table->boolean('auto_send_invoice')->default(true);   // Kirim invoice otomatis saat pembelian?

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('package_invoice_settings');
    }
};
