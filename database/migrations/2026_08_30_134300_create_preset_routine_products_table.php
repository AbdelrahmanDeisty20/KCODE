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
        Schema::create('preset_routine_products', function (Blueprint $table) {
            $table->id();
            $table->foreignId('preset_routine_id')->constrained('preset_routines')->cascadeOnDelete();
            $table->foreignId('product_id')->constrained('products')->cascadeOnDelete();
            $table->integer('display_order')->default(1);
            $table->string('step_name_ar')->nullable();
            $table->string('step_name_en')->nullable();
            $table->boolean('morning')->default(true);
            $table->boolean('night')->default(true);
            $table->string('use_time_ar')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('preset_routine_products');
    }
};
