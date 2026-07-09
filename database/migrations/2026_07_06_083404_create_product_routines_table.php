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
        Schema::create('product_routines', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained('products')->cascadeOnDelete();
            $table->foreignId('routine_step_id')->constrained('routine_steps')->cascadeOnDelete();
            $table->boolean('morning')->default(false);
            $table->boolean('night')->default(false);
            $table->integer('layer_order')->default(0);
            $table->boolean('is_core')->default(false);
            $table->boolean('is_addon')->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('product_routines');
    }
};
