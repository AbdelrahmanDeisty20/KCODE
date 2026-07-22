<?php

namespace Database\Seeders;

use App\Models\Coupon;
use Illuminate\Database\Seeder;

class CouponSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $coupons = [
            [
                'code' => 'KCODE10',
                'title_ar' => 'كود خصم KCODE10 الشامل',
                'title_en' => 'KCODE10 General Discount Code',
                'discount_type' => 'percentage',
                'discount_value' => 10.00,
                'min_order_amount' => 25.00,
                'max_discount_amount' => null,
                'is_general' => true,
                'is_active' => true,
            ],
            [
                'code' => 'WELCOME15',
                'title_ar' => 'كود الترحيب للعملاء الجدد 15%',
                'title_en' => 'Welcome 15% Discount Code',
                'discount_type' => 'percentage',
                'discount_value' => 15.00,
                'min_order_amount' => 15.00,
                'max_discount_amount' => 50.00,
                'is_general' => false,
                'is_active' => true,
            ]
        ];

        foreach ($coupons as $coupon) {
            Coupon::updateOrCreate(
                ['code' => $coupon['code']],
                $coupon
            );
        }
    }
}
