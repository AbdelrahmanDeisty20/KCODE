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
            [
                'key_ar' => 'الحد الأدنى للشحن المجاني',
                'key_en' => 'free_shipping_min_amount',
                'value_ar' => '25',
                'value_en' => '25',
            ],
            [
                'key_ar' => 'رمز العملة',
                'key_en' => 'currency_symbol',
                'value_ar' => 'ر.ع',
                'value_en' => 'OMR',
            ],
            [
                'key_ar' => 'نص الإعلان العلوي',
                'key_en' => 'announcement_text',
                'value_ar' => 'شحن مجاني للطلبات فوق 25 ر.ع',
                'value_en' => 'Free shipping on orders over 25 OMR',
            ],
            [
                'key_ar' => 'كود خصم الإعلان العلوي',
                'key_en' => 'announcement_code',
                'value_ar' => 'KCODE10',
                'value_en' => 'KCODE10',
            ],

            // KCODE Philosophy / فلسفتنا
            [
                'key_ar' => 'شارة فلسفتنا',
                'key_en' => 'philosophy_badge',
                'value_ar' => 'فلسفتنا',
                'value_en' => 'Our Philosophy',
            ],
            [
                'key_ar' => 'عنوان فلسفتنا',
                'key_en' => 'philosophy_title',
                'value_ar' => 'كل اختيار له سبب',
                'value_en' => 'Every Choice Has a Reason',
            ],
            [
                'key_ar' => 'الوصف الفرعي لفلسفتنا',
                'key_en' => 'philosophy_subtitle',
                'value_ar' => 'في كود، لا نتبع الترندات العابرة أو العلامات التجارية الشهيرة لمجرد شهرتها. نحن نحلل التركيبة العلمية لكل منتج قبل ترشيحه لك.',
                'value_en' => 'At KCODE, we do not follow passing trends or famous brands just for their popularity. We analyze the scientific formulation of every product before recommending it to you.',
            ],
            [
                'key_ar' => 'مقولة فلسفتنا',
                'key_en' => 'philosophy_quote',
                'value_ar' => 'العناية بالبشرة ليست عشوائية، بل هي كود علمي متناغم يستحق الفهم والدقة.',
                'value_en' => 'Skincare is not random; it is a harmonious scientific code that deserves understanding and precision.',
            ],
            // Feature 1
            [
                'key_ar' => 'عنوان الفاعلية المثبتة',
                'key_en' => 'philosophy_feature_1_title',
                'value_ar' => 'الفاعلية المثبتة',
                'value_en' => 'Proven Efficacy',
            ],
            [
                'key_ar' => 'وصف الفاعلية المثبتة',
                'key_en' => 'philosophy_feature_1_desc',
                'value_ar' => 'نختار فقط المنتجات التي تحتوي على نسب مدروسة ومثبتة علمياً من المكونات النشطة والفعالة لبشرتك.',
                'value_en' => 'We select only products that contain scientifically proven and studied percentages of active ingredients for your skin.',
            ],
            // Feature 2
            [
                'key_ar' => 'عنوان سلامة التركيبة',
                'key_en' => 'philosophy_feature_2_title',
                'value_ar' => 'سلامة التركيبة',
                'value_en' => 'Formulation Safety',
            ],
            [
                'key_ar' => 'وصف سلامة التركيبة',
                'key_en' => 'philosophy_feature_2_desc',
                'value_ar' => 'نستبعد تماماً أي منتجات تحتوي على مكونات ضارة أو مهيجة قد تضر بحاجز البشرة الطبيعي.',
                'value_en' => 'We completely exclude any products containing harmful or irritating ingredients that may damage your natural skin barrier.',
            ],
            // Feature 3
            [
                'key_ar' => 'عنوان ملائمة الاحتياج',
                'key_en' => 'philosophy_feature_3_title',
                'value_ar' => 'ملائمة الاحتياج',
                'value_en' => 'Suitability to Need',
            ],
            [
                'key_ar' => 'وصف ملائمة الاحتياج',
                'key_en' => 'philosophy_feature_3_desc',
                'value_ar' => 'كل منتج يوضع في متجرنا يتم اختياره ليحل مشكلة حقيقية ومحددة لبشرتك بناءً على تركيبته.',
                'value_en' => 'Every product placed in our store is chosen to solve a real and specific skin problem based on its formulation.',
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
