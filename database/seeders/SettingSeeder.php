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
            [
                'key_ar' => 'البريد الإلكتروني للدعم',
                'key_en' => 'support_email',
                'value_ar' => 'care@kcodeskin.com',
                'value_en' => 'care@kcodeskin.com',
            ],
            [
                'key_ar' => 'رابط إنستغرام',
                'key_en' => 'instagram_url',
                'value_ar' => 'https://instagram.com/kcodeskin',
                'value_en' => 'https://instagram.com/kcodeskin',
            ],
            [
                'key_ar' => 'رابط تيك توك',
                'key_en' => 'tiktok_url',
                'value_ar' => 'https://tiktok.com/@kcodeskin',
                'value_en' => 'https://tiktok.com/@kcodeskin',
            ],
            [
                'key_ar' => 'نص ضمان الأصالة',
                'key_en' => 'authenticity_info_ar',
                'value_ar' => 'نورد منتجاتنا مباشرةً من العلامات التجارية أو موزعيها المعتمدين، ثم نفحص كل منتج وعبوته قبل اعتماده للبيع.',
                'value_en' => 'We source our products directly from brands or authorized distributors, inspecting each item before sale.',
            ],
            [
                'key_ar' => 'نص معلومات الشحن والتوصيل',
                'key_en' => 'delivery_info_ar',
                'value_ar' => 'نفس اليوم داخل مسقط للطلبات المؤكَّدة قبل الساعة الواحدة ظهرًا. ومن 24 إلى 48 ساعة لباقي عُمان.',
                'value_en' => 'Same-day delivery in Muscat for orders before 1 PM. 24-48 hours for the rest of Oman.',
            ],
            [
                'key_ar' => 'نص سياسة الإرجاع',
                'key_en' => 'return_policy_info_ar',
                'value_ar' => 'يمكنك إرجاع المنتج خلال 7 أيام من استلامه، بشرط أن يكون غير مستخدم وغير مفتوح وفي عبوته الأصلية.',
                'value_en' => 'Returns accepted within 7 days of delivery, provided the item is unused and in original packaging.',
            ],
            [
                'key_ar' => 'حقوق النشر في أسفل الموقع',
                'key_en' => 'footer_copyright_ar',
                'value_ar' => 'جميع الحقوق محفوظة © KCODE 2026',
                'value_en' => 'All Rights Reserved © KCODE 2026',
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

            // KCODE Formula Evaluation Policy / كيف نقيّم التركيبة في KCODE
            [
                'key_ar' => 'عنوان منهجية تقييم التركيبة',
                'key_en' => 'formula_eval_title',
                'value_ar' => 'كيف نقيّم التركيبة في KCODE؟',
                'value_en' => 'How We Evaluate Formulations at KCODE',
            ],
            [
                'key_ar' => 'مقدمة منهجية تقييم التركيبة',
                'key_en' => 'formula_eval_subtitle',
                'value_ar' => 'وراء كل خلاصة، مراجعة تبدأ من تركيبة المنتج نفسه.',
                'value_en' => 'Behind every conclusion, a review that begins with the product formula itself.',
            ],
            [
                'key_ar' => 'تقييم التركيبة - الخطوة 1 (العنوان)',
                'key_en' => 'formula_eval_step1_title',
                'value_ar' => 'نبدأ من العبوة',
                'value_en' => 'We start from the packaging',
            ],
            [
                'key_ar' => 'تقييم التركيبة - الخطوة 1 (الشرح)',
                'key_en' => 'formula_eval_step1_desc',
                'value_ar' => 'نراجع قائمة المكونات المدوّنة على المنتج، ونقرأها كاملة لفهم ما تحتويه التركيبة فعلًا، بدل الاكتفاء بالمكوّن الذي يبرزه الإعلان.',
                'value_en' => 'We review the full ingredient list printed on the product to understand what the formula actually contains, rather than relying solely on advertised key ingredients.',
            ],
            [
                'key_ar' => 'تقييم التركيبة - الخطوة 2 (العنوان)',
                'key_en' => 'formula_eval_step2_title',
                'value_ar' => 'وجود المكوّن وحده لا يكفي',
                'value_en' => 'Ingredient presence alone is not enough',
            ],
            [
                'key_ar' => 'تقييم التركيبة - الخطوة 2 (الشرح)',
                'key_en' => 'formula_eval_step2_desc',
                'value_ar' => 'ندرس وظيفة المكوّن وترتيبه في القائمة، ونأخذ النسب المعلنة في الحسبان. ثم نقيّم دوره ضمن التركيبة: هل يدعم الهدف الأساسي للمنتج، أم يؤدي دورًا مساندًا؟',
                'value_en' => 'We evaluate ingredient position, function, and concentration to judge whether it supports the primary goal or plays a secondary role.',
            ],
            [
                'key_ar' => 'تقييم التركيبة - الخطوة 3 (العنوان)',
                'key_en' => 'formula_eval_step3_title',
                'value_ar' => 'نقرأ التركيبة ككل',
                'value_en' => 'We read the formula as a whole',
            ],
            [
                'key_ar' => 'تقييم التركيبة - الخطوة 3 (الشرح)',
                'key_en' => 'formula_eval_step3_desc',
                'value_ar' => 'نربط المكونات ببعضها وباحتياجات البشرة، ونوازن بين ما تدعمه التركيبة وما يستدعي الانتباه في ملاءمتها وتحمّلها. ثم نحوّل هذه القراءة إلى خلاصة واضحة تساعدك على فهم المنتج.',
                'value_en' => 'We connect ingredients with skin needs, balancing efficacy and tolerance into a clear insight to help you understand the product.',
            ],
            [
                'key_ar' => 'ملاحظة تقييم التركيبة الختامية',
                'key_en' => 'formula_eval_footer_note',
                'value_ar' => 'نقيّم كل منتج بمعاييرنا نفسها؛ شهرته ووجوده في المتجر لا يمنحانه توصية تلقائية.',
                'value_en' => 'Every product is evaluated against the same criteria; fame or availability in store does not grant an automatic recommendation.',
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
