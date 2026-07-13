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
        Schema::table('sub_categories', function (Blueprint $table) {
            if (!Schema::hasColumn('sub_categories', 'name_en')) {
                $table->string('name_en')->nullable()->after('id');
            }
            if (!Schema::hasColumn('sub_categories', 'name_ar')) {
                $table->string('name_ar')->nullable()->after('name_en');
            }
            if (!Schema::hasColumn('sub_categories', 'category_id')) {
                $table->foreignId('category_id')->nullable()->after('name_ar')->constrained('categories')->cascadeOnDelete();
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('sub_categories', function (Blueprint $table) {
            if (Schema::hasColumn('sub_categories', 'category_id')) {
                $table->dropForeign(['category_id']);
                $table->dropColumn('category_id');
            }
            if (Schema::hasColumn('sub_categories', 'name_en')) {
                $table->dropColumn('name_en');
            }
            if (Schema::hasColumn('sub_categories', 'name_ar')) {
                $table->dropColumn('name_ar');
            }
        });
    }
};
