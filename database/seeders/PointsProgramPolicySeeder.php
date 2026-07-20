<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PointsProgramPolicySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('points_program_policies')->truncate();

        DB::table('points_program_policies')->insert([
            'title_ar'   => 'برنامج النقاط والمكافآت',
            'title_en'   => 'Points & Rewards Program Policy',
            'content_ar' => 'سيتم إضافة تفاصيل برنامج النقاط والمكافآت قريباً.',
            'content_en' => 'Points and rewards program policy details will be added soon.',
            'is_active'  => true,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }
}
