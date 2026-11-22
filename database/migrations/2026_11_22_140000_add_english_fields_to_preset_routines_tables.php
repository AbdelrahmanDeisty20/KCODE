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
            if (!Schema::hasColumn('preset_routines', 'benefits_en_json')) {
                $table->json('benefits_en_json')->nullable()->after('benefits_json');
            }
            if (!Schema::hasColumn('preset_routines', 'what_it_offers_en_json')) {
                $table->json('what_it_offers_en_json')->nullable()->after('what_it_offers_json');
            }
        });

        Schema::table('preset_routine_products', function (Blueprint $table) {
            if (!Schema::hasColumn('preset_routine_products', 'use_time_en')) {
                $table->string('use_time_en')->nullable()->after('use_time_ar');
            }
            if (!Schema::hasColumn('preset_routine_products', 'application_en')) {
                $table->text('application_en')->nullable()->after('application_ar');
            }
            if (!Schema::hasColumn('preset_routine_products', 'starter_guide_en')) {
                $table->text('starter_guide_en')->nullable()->after('starter_guide_ar');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('preset_routines', function (Blueprint $table) {
            $columns = array_values(array_filter([
                'benefits_en_json',
                'what_it_offers_en_json',
            ], fn (string $col) => Schema::hasColumn('preset_routines', $col)));

            if (!empty($columns)) {
                $table->dropColumn($columns);
            }
        });

        Schema::table('preset_routine_products', function (Blueprint $table) {
            $columns = array_values(array_filter([
                'use_time_en',
                'application_en',
                'starter_guide_en',
            ], fn (string $col) => Schema::hasColumn('preset_routine_products', $col)));

            if (!empty($columns)) {
                $table->dropColumn($columns);
            }
        });
    }
};
