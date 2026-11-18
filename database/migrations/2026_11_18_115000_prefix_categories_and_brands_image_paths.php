<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 1. Prefix categories image paths if folder is missing
        DB::table('categories')
            ->whereNotNull('image')
            ->where('image', '!=', '')
            ->where('image', 'not like', 'http%')
            ->where('image', 'not like', 'categories/%')
            ->update([
                'image' => DB::raw("CONCAT('categories/', REPLACE(REPLACE(image, 'storage/', ''), 'app/public/', ''))")
            ]);

        // 2. Prefix brands image paths if folder is missing
        DB::table('brands')
            ->whereNotNull('image')
            ->where('image', '!=', '')
            ->where('image', 'not like', 'http%')
            ->where('image', 'not like', 'brands/%')
            ->update([
                'image' => DB::raw("CONCAT('brands/', REPLACE(REPLACE(image, 'storage/', ''), 'app/public/', ''))")
            ]);

        // 3. Prefix sub_categories image paths if folder is missing
        DB::table('sub_categories')
            ->whereNotNull('image')
            ->where('image', '!=', '')
            ->where('image', 'not like', 'http%')
            ->where('image', 'not like', 'sub_categories/%')
            ->update([
                'image' => DB::raw("CONCAT('sub_categories/', REPLACE(REPLACE(image, 'storage/', ''), 'app/public/', ''))")
            ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
    }
};
