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
        Schema::create('loyalty_earning_rules', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('target_type'); // e.g., global, category, brand, product
            $table->unsignedBigInteger('target_id')->nullable();
            $table->decimal('multiplier', 8, 2)->default(1.00);
            $table->integer('fixed_points')->nullable();
            $table->timestamp('starts_at')->nullable();
            $table->timestamp('ends_at')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('loyalty_earning_rules');
    }
};
