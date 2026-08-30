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
        Schema::create('preset_routines', function (Blueprint $table) {
            $table->id();
            $table->string('title_ar');
            $table->string('title_en');
            $table->text('description_ar')->nullable();
            $table->text('description_en')->nullable();
            $table->string('badge_ar')->nullable();
            $table->string('badge_en')->nullable();
            $table->string('skin_type_ar')->nullable();
            $table->string('skin_type_en')->nullable();
            $table->string('goal_ar')->nullable();
            $table->string('goal_en')->nullable();
            $table->foreignId('skin_type_id')->nullable()->constrained('skin_types')->nullOnDelete();
            $table->foreignId('goal_id')->nullable()->constrained('routine_goals')->nullOnDelete();
            $table->string('status')->default('active');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('preset_routines');
    }
};
