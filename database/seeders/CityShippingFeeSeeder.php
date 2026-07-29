<?php

namespace Database\Seeders;

use App\Models\City;
use Illuminate\Database\Seeder;

class CityShippingFeeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $shippingFeesMap = [
            'مسقط'          => 1.50,
            'السيب'          => 1.50,
            'بوشر'          => 1.50,
            'مطرح'          => 2.00,
            'العامرات'       => 2.00,
            'صلالة'         => 3.50,
            'نزوى'          => 2.50,
            'صحار'          => 2.50,
            'الرستاق'       => 2.50,
            'بركاء'         => 2.00,
            'الرياض'        => 3.00,
            'جدة'           => 3.50,
            'مكة المكرمة'    => 3.50,
            'الدمام'        => 4.00,
            'أبوظبي'        => 3.00,
            'دبي'           => 3.00,
            'الشارقة'       => 3.50,
            'الدوحة'        => 4.00,
            'الكويت'        => 4.00,
            'المنامة'       => 3.50,
            'القاهرة'       => 2.50,
            'الجيزة'        => 2.50,
            'الإسكندرية'    => 3.00,
        ];

        $cities = City::all();
        $variedFees = [1.50, 2.00, 2.50, 3.00, 3.50, 4.00, 5.00];

        foreach ($cities as $index => $city) {
            $fee = $shippingFeesMap[$city->name_ar] ?? $shippingFeesMap[$city->name_en] ?? null;

            if (!$fee) {
                $fee = $variedFees[$index % count($variedFees)];
            }

            $city->update(['shipping_fee' => $fee]);
        }
    }
}
