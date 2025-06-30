<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('icd_masters', function (Blueprint $table) {
            $table->id();
            $table->string('code')->unique(); // ICD code e.g., A00
            $table->string('title');          // Short title
            $table->text('description')->nullable(); // Longer description
            $table->string('chapter')->nullable();   // ICD chapter/category (optional)
            $table->string('version')->default('ICD-10'); // ICD version
            $table->string('parent_code')->nullable();     // For ICD-11 hierarchical link
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('icd_masters');
    }
};
