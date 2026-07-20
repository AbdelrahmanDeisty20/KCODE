<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ReturnPolicySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('return_policies')->truncate();

        DB::table('return_policies')->insert([
            'title_ar'   => 'سياسة الاسترجاع والاستبدال',
            'title_en'   => 'Return & Exchange Policy',
            'content_ar' => 'سيتم إضافة تفاصيل سياسة الاسترجاع والاستبدال قريباً.',
            'content_en' => 'Return and exchange policy details will be added soon.',
            'is_active'  => true,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }
}
