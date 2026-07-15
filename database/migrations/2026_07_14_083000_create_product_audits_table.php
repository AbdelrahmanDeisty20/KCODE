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
        Schema::create('product_audits', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained('products')->cascadeOnDelete();
            $table->text('avoid_pairing_same_routine')->nullable();
            $table->text('developer_output_rule')->nullable();
            $table->boolean('show_alternatives_button')->default(true);
            $table->boolean('remove_if_customer_has_it')->default(false);
            $table->string('source_url')->nullable();
            $table->string('data_confidence')->default('High');
            $table->boolean('needs_manual_check')->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('product_audits');
    }
};
