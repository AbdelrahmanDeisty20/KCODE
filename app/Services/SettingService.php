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

    /**
     * Get assurances (Authenticity, Delivery, Return policies).
     */
    public function getAssurances(): array
    {
        $assurances = [
            [
                'title' => Setting::get('authenticity_info_title_ar', 'أصالة مضمونة'),
                'icon'  => 'shield',
                'text'  => Setting::get('authenticity_info_ar', 'نورد منتجاتنا مباشرةً من العلامات التجارية أو موزعيها المعتمدين، ثم نفحص كل منتج وعبوته قبل اعتماده للبيع.')
            ],
            [
                'title' => Setting::get('delivery_info_title_ar', 'التوصيل داخل عُمان'),
                'icon'  => 'truck',
                'text'  => Setting::get('delivery_info_ar', 'نفس اليوم داخل مسقط للطلبات المؤكَّدة قبل الساعة الواحدة ظهرًا. ومن 24 إلى 48 ساعة لباقي عُمان.')
            ],
            [
                'title' => Setting::get('return_policy_info_title_ar', 'إرجاع خلال 7 أيام'),
                'icon'  => 'return',
                'text'  => Setting::get('return_policy_info_ar', 'يمكنك إرجاع المنتج خلال 7 أيام من استلامه، بشرط أن يكون غير مستخدم وغير مفتوح وفي عبوته الأصلية.')
            ],
        ];

        return [
            'status'  => true,
            'message' => __('messages.page_retrieved_successfully'),
            'data'    => $assurances,
        ];
    }
}
