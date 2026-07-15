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
        Schema::create('product_recommendation_rules', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained('products')->cascadeOnDelete();
            $table->integer('default_priority_score')->default(0);
            $table->string('same_step_choice_group')->nullable();
            $table->boolean('am_default')->default(false);
            $table->boolean('pm_default')->default(false);
            $table->text('selection_rule_ar')->nullable();
            $table->integer('max_default_products_per_step')->default(1);
            $table->text('selection_weight_formula_note')->nullable();
            $table->text('selection_priority_tie_breaker')->nullable();
            $table->text('exclusion_rule')->nullable();
            $table->string('conflict_rule_strictness')->default('Mild');
            $table->text('pairing_rule')->nullable();
            $table->text('alternative_button_rule')->nullable();
            $table->text('add_on_display_rule')->nullable();
            $table->text('routine_builder_note')->nullable();
            $table->text('fallback_product_rule')->nullable();
            $table->string('routine_role')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('product_recommendation_rules');
    }
};
