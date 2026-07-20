<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PrivacyPolicySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('privacy_policies')->truncate();

        DB::table('privacy_policies')->insert([
            'title_ar'   => 'سياسة الخصوصية',
            'title_en'   => 'Privacy Policy',
            'content_ar' => 'سيتم إضافة تفاصيل سياسة الخصوصية قريباً.',
            'content_en' => 'Privacy policy details will be added soon.',
            'is_active'  => true,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }
}
