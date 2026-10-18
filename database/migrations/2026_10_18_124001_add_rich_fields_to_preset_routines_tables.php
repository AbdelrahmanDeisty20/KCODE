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
        Schema::table('preset_routines', function (Blueprint $table) {
            $table->json('benefits_json')->nullable()->after('goal_en'); // 4 bullets shown on the card
            $table->json('what_it_offers_json')->nullable()->after('benefits_json'); // 6 detailed benefits in the detail grid
            $table->text('how_to_use_ar')->nullable()->after('what_it_offers_json'); // "طريقة الاستخدام" tab
            $table->text('how_to_use_en')->nullable()->after('how_to_use_ar');
            $table->json('complementary_products_json')->nullable()->after('how_to_use_en'); // "منتجات مكملة" tab
        });

        Schema::table('preset_routine_products', function (Blueprint $table) {
            $table->text('reason_ar')->nullable()->after('step_name_en'); // "لماذا اخترناه لك؟"
            $table->text('reason_en')->nullable()->after('reason_ar');
            $table->string('frequency_ar')->nullable()->after('reason_en'); // "التكرار يومياً"
            $table->string('frequency_en')->nullable()->after('frequency_ar');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('preset_routines', function (Blueprint $table) {
            $table->dropColumn([
                'benefits_json',
                'what_it_offers_json',
                'how_to_use_ar',
                'how_to_use_en',
                'complementary_products_json',
            ]);
        });

        Schema::table('preset_routine_products', function (Blueprint $table) {
            $table->dropColumn([
                'reason_ar',
                'reason_en',
                'frequency_ar',
                'frequency_en',
            ]);
        });
    }
};
