<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CouponPolicySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('coupon_policies')->truncate();

        DB::table('coupon_policies')->insert([
            'title_ar'   => 'سياسة الكوبونات والخصومات',
            'title_en'   => 'Coupons & Discounts Policy',
            'content_ar' => 'سيتم إضافة تفاصيل سياسة الكوبونات والخصومات قريباً.',
            'content_en' => 'Coupons and discounts policy details will be added soon.',
            'is_active'  => true,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }
}
