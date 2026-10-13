<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('categories', function (Blueprint $table) {
            if (!Schema::hasColumn('categories', 'description_ar')) {
                $table->text('description_ar')->nullable()->after('name_en');
            }
            if (!Schema::hasColumn('categories', 'description_en')) {
                $table->text('description_en')->nullable()->after('description_ar');
            }
        });

        // Backfill the 10 official KCODE categories (only where empty)
        foreach (\Database\Seeders\CategorySeeder::descriptions() as $nameEn => $desc) {
            DB::table('categories')
                ->where('name_en', $nameEn)
                ->whereNull('description_en')
                ->update([
                    'description_ar' => $desc['ar'],
                    'description_en' => $desc['en'],
                ]);
        }
    }

    public function down(): void
    {
        Schema::table('categories', function (Blueprint $table) {
            $table->dropColumn(['description_ar', 'description_en']);
        });
    }
};
