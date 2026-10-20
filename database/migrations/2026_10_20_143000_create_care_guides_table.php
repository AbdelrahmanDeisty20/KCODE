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
        Schema::create('care_guides', function (Blueprint $table) {
            $table->id();
            $table->string('title_ar');
            $table->string('title_en')->nullable();
            $table->text('description_ar');
            $table->text('description_en')->nullable();
            $table->text('note_ar')->nullable();
            $table->text('note_en')->nullable();
            $table->string('button_quiz_text_ar')->nullable();
            $table->string('button_quiz_text_en')->nullable();
            $table->string('button_quiz_action')->nullable()->default('quiz');
            $table->string('button_routines_text_ar')->nullable();
            $table->string('button_routines_text_en')->nullable();
            $table->string('button_routines_action')->nullable()->default('routines');
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('care_guides');
    }
};
