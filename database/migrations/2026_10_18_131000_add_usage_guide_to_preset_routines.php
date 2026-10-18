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
            $table->json('usage_guide_json')->nullable()->after('what_it_offers_json');
        });

        Schema::table('preset_routine_products', function (Blueprint $table) {
            $table->text('application_ar')->nullable()->after('reason_en');
            $table->text('starter_guide_ar')->nullable()->after('application_ar');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('preset_routines', function (Blueprint $table) {
            $table->dropColumn('usage_guide_json');
        });

        Schema::table('preset_routine_products', function (Blueprint $table) {
            $table->dropColumn(['application_ar', 'starter_guide_ar']);
        });
    }
};
