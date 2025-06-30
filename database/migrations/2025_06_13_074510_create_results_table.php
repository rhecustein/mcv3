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
        Schema::create('results', function (Blueprint $table) {
            $table->id();

            // Relasi utama
            $table->foreignId('outlet_id')->constrained('outlets')->onDelete('cascade');
            $table->foreignId('patient_id')->constrained('patients')->onDelete('cascade');
            $table->foreignId('doctor_id')->constrained('doctors')->onDelete('cascade');
            //company_id
            $table->foreignId('company_id')->nullable()->constrained('companies')->onDelete('cascade');
            $table->foreignId('medical_diagnosis_id')->nullable()->constrained('medical_diagnoses')->onDelete('cascade');

            // Informasi dokumen dan identifikasi
            $table->string('unique_code')->unique();
            $table->string('qrcode')->unique();                     // token QR
            $table->string('no_letters')->nullable();               // nomor surat
            $table->string('document_name')->nullable();            // nama file PDF, jika ada

            //employee idcard
            $table->string('employee_idcard')->nullable();          // ID Kartu Pegawai, jika ada

            // Tipe dan tanda tangan
            $table->enum('type', ['mc', 'skb'])->default('skb');    // surat sakit / surat keterangan sehat
            $table->enum('sign_type', ['qrcode', 'sign_virtual'])->nullable();
            $table->string('sign_value')->nullable();

            // Tanggal-tanggal penting
            $table->date('verification_date')->nullable();
            $table->date('print_date')->nullable();

            //mc
            $table->integer('duration')->nullable();   
            $table->date('start_date')->nullable();
            $table->date('end_date')->nullable();
            //skb
            $table->date('date')->nullable();
            $table->time('time')->nullable();

            // Durasi sakit
                        // hari

            // Notifikasi (status dan trigger)
            $table->boolean('send_notif_wa')->default(false);
            $table->boolean('send_notif_email')->default(false);
            $table->boolean('notif_wa')->default(false);
            $table->boolean('notif_email')->default(false);

            // edit 
            $table->enum('edit', ['yes', 'no'])->default('no'); // apakah sudah di edit
            $table->string('created_ip')->nullable(); // IP address saat dibuat
            $table->string('created_city')->nullable(); // kota saat dibuat
            $table->decimal('created_latitude', 10, 7)->nullable();
            $table->decimal('created_longitude', 10, 7)->nullable();
              
            //delete_at
            $table->string('deleted_at')->nullable(); // tanggal penghapusan
            $table->timestamps();
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('results');
    }
};
