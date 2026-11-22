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
            if (!Schema::hasColumn('products', 'brief_insight_en')) {
                $table->text('brief_insight_en')->nullable()->after('brief_insight_ar');
            }
            if (!Schema::hasColumn('products', 'country_of_origin_en')) {
                $table->string('country_of_origin_en')->nullable()->after('country_of_origin_ar');
            }
            if (!Schema::hasColumn('products', 'role_en')) {
                $table->string('role_en')->nullable()->after('role_ar');
            }
            if (!Schema::hasColumn('products', 'usage_frequency_en')) {
                $table->string('usage_frequency_en')->nullable()->after('usage_frequency_ar');
            }
            if (!Schema::hasColumn('products', 'limitations_notes_en')) {
                $table->text('limitations_notes_en')->nullable()->after('limitations_notes_ar');
            }
            if (!Schema::hasColumn('products', 'routine_step_title_en')) {
                $table->string('routine_step_title_en')->nullable()->after('routine_step_title_ar');
            }
            if (!Schema::hasColumn('products', 'seo_meta_title_en')) {
                $table->string('seo_meta_title_en')->nullable()->after('seo_meta_title_ar');
            }
            if (!Schema::hasColumn('products', 'og_title_en')) {
                $table->string('og_title_en')->nullable()->after('og_title_ar');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $columns = array_values(array_filter([
                'brief_insight_en',
                'country_of_origin_en',
                'role_en',
                'usage_frequency_en',
                'limitations_notes_en',
                'routine_step_title_en',
                'seo_meta_title_en',
                'og_title_en',
            ], fn (string $col) => Schema::hasColumn('products', $col)));

            if (!empty($columns)) {
                $table->dropColumn($columns);
            }
        });
    }
};
