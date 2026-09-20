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
        Schema::table('products', function (Blueprint $table) {
            $table->unsignedTinyInteger('routine_step_number')->nullable()->after('role_ar');
            $table->string('routine_step_title_ar')->nullable()->after('routine_step_number');
            $table->json('routine_steps_json')->nullable()->after('routine_step_title_ar');
            $table->json('usage_instructions_json')->nullable()->after('usage_frequency_ar');
            $table->json('complementary_routine_json')->nullable()->after('usage_instructions_json');
            $table->json('product_faqs_json')->nullable()->after('complementary_routine_json');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn([
                'routine_step_number',
                'routine_step_title_ar',
                'routine_steps_json',
                'usage_instructions_json',
                'complementary_routine_json',
                'product_faqs_json',
            ]);
        });
    }
};
