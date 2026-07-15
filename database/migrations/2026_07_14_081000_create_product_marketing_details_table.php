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
        Schema::create('product_marketing_details', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained('products')->cascadeOnDelete();
            $table->string('primary_badge_en')->nullable();
            $table->string('primary_badge_ar')->nullable();
            $table->text('result_promise_en')->nullable();
            $table->text('result_promise_ar')->nullable();
            $table->text('objection_answer_en')->nullable();
            $table->text('objection_answer_ar')->nullable();
            $table->text('routine_reason_en')->nullable();
            $table->text('routine_reason_ar')->nullable();
            $table->string('bundle_cta_en')->nullable();
            $table->string('bundle_cta_ar')->nullable();
            $table->string('add_to_cart_microcopy_en')->nullable();
            $table->string('add_to_cart_microcopy_ar')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('product_marketing_details');
    }
};
