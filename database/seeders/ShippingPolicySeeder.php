<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ShippingPolicySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('shipping_policies')->truncate();

        DB::table('shipping_policies')->insert([
            'title_ar'   => 'سياسة الشحن والتوصيل',
            'title_en'   => 'Shipping & Delivery Policy',
            'content_ar' => 'سيتم إضافة تفاصيل سياسة الشحن والتوصيل قريباً.',
            'content_en' => 'Shipping and delivery policy details will be added soon.',
            'is_active'  => true,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }
}
