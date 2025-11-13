<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('mcu_packages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('provider_id')->constrained('mcu_providers')->onDelete('cascade');
            $table->string('name');
            $table->string('slug');
            $table->string('code')->unique();
            $table->text('description');
            $table->json('inclusions'); // List of tests/procedures included
            $table->json('exclusions')->nullable(); // What's not included
            $table->json('preparation_instructions')->nullable();
            $table->decimal('price', 12, 2); // Base price
            $table->decimal('discounted_price', 12, 2)->nullable();
            $table->integer('discount_percentage')->default(0);
            $table->enum('category', ['basic', 'standard', 'premium', 'executive', 'specialized'])->default('standard');
            $table->enum('gender_target', ['all', 'male', 'female'])->default('all');
            $table->integer('min_age')->default(0);
            $table->integer('max_age')->default(100);
            $table->integer('duration_minutes')->default(120); // Estimated duration
            $table->integer('validity_days')->default(30); // Result validity
            $table->string('image')->nullable();
            $table->json('images')->nullable(); // Multiple images
            $table->boolean('is_featured')->default(false);
            $table->boolean('is_active')->default(true);
            $table->integer('booking_count')->default(0);
            $table->integer('stock')->nullable(); // Daily capacity
            $table->timestamps();

            $table->index('provider_id');
            $table->index(['category', 'is_active']);
            $table->index('is_featured');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('mcu_packages');
    }
};
