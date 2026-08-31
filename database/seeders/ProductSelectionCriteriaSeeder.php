<?php

namespace Database\Seeders;

use App\Models\ProductSelectionCriteria;
use Illuminate\Database\Seeder;

class ProductSelectionCriteriaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $criterias = [
            // Modal Criteria (معايير الفحص والتقييم بالمودال)
            [
                'title_ar'       => 'الأصالة ونقاء المصدر',
                'title_en'       => 'Authenticity and Pure Sourcing',
                'description_ar' => 'نوفر جميع المنتجات مباشرة من المصنعين والعلامات التجارية الكورية أو قنوات التوريد الرسمية، دون وسائط أو أسواق موازية. نضمن تاريخ الإنتاج ونقل وتخزين المنتجات في بيئات مضبوطة حرارياً لضمان وصولها بأعلى فاعلية.',
                'description_en' => 'We source all products directly from manufacturers and official Korean brand channels without intermediaries. We guarantee production dates and temperature-controlled storage.',
                'icon'           => 'shield-check',
                'badge_text_ar'  => 'أصالة 100%',
                'badge_text_en'  => '100% Authentic',
                'type'           => 'modal_criteria',
                'sort_order'     => 1,
                'is_active'      => true,
            ],
            [
                'title_ar'       => 'قياس النسب الفعالة والتركيبة العلمية',
                'title_en'       => 'Measuring Active Ratios & Scientific Formulation',
                'description_ar' => 'نراجع قائمة المكونات الكاملة (INCI) لكل منتج قبل ترشيحه. نتحقق من أن المكونات النشطة (مثل: النياسيناميد، الريتينول، الببتيدات، والسيراميد) موجودة بنسب مدروسة علمياً وسريرياً لتحقيق النتائج دون أن تكون مجرد إضافات تسويقية.',
                'description_en' => 'We review the full INCI ingredient list for every product to ensure active ingredients like Niacinamide, Retinol, Peptides, and Ceramides are present in clinically proven percentages.',
                'icon'           => 'flask',
                'badge_text_ar'  => 'تركيبة علمية',
                'badge_text_en'  => 'Scientific Formula',
                'type'           => 'modal_criteria',
                'sort_order'     => 2,
                'is_active'      => true,
            ],
            [
                'title_ar'       => 'معايير الحساسية واستبعاد المهيجات',
                'title_en'       => 'Sensitivity Standards & Excluding Irritants',
                'description_ar' => 'حاجز البشرة (Skin Barrier) خط أحمر. نستبعد تماماً العطور الصناعية والزيوت العطرية القوية والكحول الجاف في مستحضرات البشرة الحساسة. لا نكتفي بكلمة «لطيف» المدونة على العبوة، بل ندقق في كل مركب كيميائي.',
                'description_en' => 'The skin barrier is a red line. We strictly exclude artificial fragrances, strong essential oils, and drying alcohol for sensitive skin products.',
                'icon'           => 'ban',
                'badge_text_ar'  => 'خالٍ من المهيجات',
                'badge_text_en'  => 'Irritant Free',
                'type'           => 'modal_criteria',
                'sort_order'     => 3,
                'is_active'      => true,
            ],

            // Accordion Items (بنود لماذا KCODE)
            [
                'title_ar'       => 'منتجات أصلية من مصدرها',
                'title_en'       => 'Original Products Direct from Source',
                'description_ar' => 'نوفرها من العلامات ومن قنوات توريد موثوقة، لا من أسواق موازية — لتصلك بالجودة التي صُممت لتقدمها.',
                'description_en' => 'Directly sourced from verified brand channels to ensure maximum quality and fresh shelf life.',
                'icon'           => 'check-circle',
                'badge_text_ar'  => null,
                'badge_text_en'  => null,
                'type'           => 'accordion_item',
                'sort_order'     => 1,
                'is_active'      => true,
            ],
            [
                'title_ar'       => 'نستبعد ولا نكتفي بالترشيح',
                'title_en'       => 'We Exclude, Not Just Recommend',
                'description_ar' => 'نراجع كل منتج قبل أن يدخل اختياراتنا، ونستبعد ما لا يناسبك بدل أن نتركك تجرب.',
                'description_en' => 'Every single product is vetted to eliminate anything that might harm your skin barrier.',
                'icon'           => 'x-circle',
                'badge_text_ar'  => null,
                'badge_text_en'  => null,
                'type'           => 'accordion_item',
                'sort_order'     => 2,
                'is_active'      => true,
            ],
            [
                'title_ar'       => 'نفصل ما لا يُجمع',
                'title_en'       => 'Separating Incompatible Actives',
                'description_ar' => 'نراعي التفاعلات الكيميائية بين المواد النشطة لضمان عدم تعارض المكونات في روتينك اليومي.',
                'description_en' => 'We account for chemical interactions to ensure your daily skincare routine has zero conflicts.',
                'icon'           => 'layers-intersect',
                'badge_text_ar'  => null,
                'badge_text_en'  => null,
                'type'           => 'accordion_item',
                'sort_order'     => 3,
                'is_active'      => true,
            ],
            [
                'title_ar'       => 'العطر ليس تفصيلاً',
                'title_en'       => 'Fragrance is Not a Detail',
                'description_ar' => 'ننتبه تماماً للمكونات العطرية والزيوت الأساسية لتجنيب البشرة التهيج والتحسس.',
                'description_en' => 'We pay strict attention to fragrance profiles and essential oils to keep skin safe.',
                'icon'           => 'sparkles',
                'badge_text_ar'  => null,
                'badge_text_en'  => null,
                'type'           => 'accordion_item',
                'sort_order'     => 4,
                'is_active'      => true,
            ],
        ];

        foreach ($criterias as $criteria) {
            ProductSelectionCriteria::updateOrCreate(
                [
                    'title_ar' => $criteria['title_ar'],
                    'type'     => $criteria['type'],
                ],
                $criteria
            );
        }
    }
}
