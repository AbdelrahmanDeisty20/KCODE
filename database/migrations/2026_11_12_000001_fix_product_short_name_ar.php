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
        // Align short_name_ar with short_name_en for products where short_name_ar was populated with marketing text/brief
        DB::table('products')
            ->whereNotNull('short_name_en')
            ->where('short_name_en', '!=', '')
            ->where(function ($query) {
                $query->whereColumn('short_name_ar', 'brief_insight_ar')
                      ->orWhereColumn('short_name_ar', 'description_ar')
                      ->orWhereRaw('CHAR_LENGTH(short_name_ar) > 60');
            })
            ->update([
                'short_name_ar' => DB::raw('short_name_en')
            ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // No-op
    }
};
