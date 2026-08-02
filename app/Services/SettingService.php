<?php

namespace App\Services;

use App\Models\Setting;

class SettingService
{
    /**
     * Get structured KCODE Philosophy settings section.
     */
    public function getPhilosophy(): array
    {
        $badge = Setting::get('philosophy_badge', 'فلسفتنا');
        $title = Setting::get('philosophy_title', 'كل اختيار له سبب');
        $subtitle = Setting::get('philosophy_subtitle');
        $quote = Setting::get('philosophy_quote');

        $features = [
            [
                'id' => 1,
                'title' => Setting::get('philosophy_feature_1_title', 'الفاعلية المثبتة'),
                'description' => Setting::get('philosophy_feature_1_desc'),
                'icon' => 'ribbon',
            ],
            [
                'id' => 2,
                'title' => Setting::get('philosophy_feature_2_title', 'سلامة التركيبة'),
                'description' => Setting::get('philosophy_feature_2_desc'),
                'icon' => 'shield',
            ],
            [
                'id' => 3,
                'title' => Setting::get('philosophy_feature_3_title', 'ملائمة الاحتياج'),
                'description' => Setting::get('philosophy_feature_3_desc'),
                'icon' => 'compass',
            ],
        ];

        return [
            'status' => true,
            'message' => __('messages.page_retrieved_successfully'),
            'data' => [
                'badge' => $badge,
                'title' => $title,
                'subtitle' => $subtitle,
                'quote' => $quote,
                'features' => $features,
            ],
        ];
    }

    /**
     * Get all general store settings.
     */
    public function getAllSettings(): array
    {
        $settings = Setting::all()->mapWithKeys(function ($setting) {
            return [$setting->key_en => [
                'key_ar' => $setting->key_ar,
                'key_en' => $setting->key_en,
                'value' => $setting->value,
                'value_ar' => $setting->value_ar,
                'value_en' => $setting->value_en,
            ]];
        });

        return [
            'status' => true,
            'message' => __('messages.page_retrieved_successfully'),
            'data' => $settings,
        ];
    }
}
