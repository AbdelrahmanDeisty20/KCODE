<?php

namespace Database\Seeders;

use App\Models\Setting;
use Illuminate\Database\Seeder;

class SettingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $settings = [
            [
                'key_ar' => 'رقم الواتساب للاستشارة',
                'key_en' => 'whatsapp_number',
                'value_ar' => '966500000000',
                'value_en' => '966500000000',
            ],
            [
                'key_ar' => 'رقم الدعم الفني',
                'key_en' => 'support_phone',
                'value_ar' => '966500000000',
                'value_en' => '966500000000',
            ],
        ];

        foreach ($settings as $setting) {
            Setting::updateOrCreate(
                ['key_en' => $setting['key_en']],
                [
                    'key_ar' => $setting['key_ar'],
                    'value_ar' => $setting['value_ar'],
                    'value_en' => $setting['value_en'],
                ]
            );
        }
    }
}
