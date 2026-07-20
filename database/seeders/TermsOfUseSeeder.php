<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TermsOfUseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('terms_of_use')->truncate();

        DB::table('terms_of_use')->insert([
            'title_ar'   => 'شروط الاستخدام',
            'title_en'   => 'Terms of Use',
            'content_ar' => 'سيتم إضافة تفاصيل شروط الاستخدام قريباً.',
            'content_en' => 'Terms of use details will be added soon.',
            'is_active'  => true,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }
}
