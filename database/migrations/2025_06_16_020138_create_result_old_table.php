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
        Schema::create('result_old', function (Blueprint $table) {
            $table->id();
            $table->string('outlet')->nullable();
            $table->string('patient')->nullable();
            $table->string('company')->nullable();
            $table->string('doctor')->nullable();
            $table->string('unique_code');
            $table->string('qrcode')->unique();
            $table->string('no_letters');
            $table->enum('type', ['mc', 'skb']);
            $table->date('start_date')->nullable();
            $table->date('date')->nullable();
            $table->time('time')->nullable();
            $table->text('document_name')->nullable();
            $table->integer('duration')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('result_old');
    }
};
