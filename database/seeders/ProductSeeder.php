<?php

namespace Database\Seeders;

use App\Models\Brand;
use App\Models\Category;
use App\Models\SubCategory;
use App\Models\Product;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categoryCleanser = Category::where('name_en', 'Cleanser')->first();
        $categoryMoisturizer = Category::where('name_en', 'Moisturizer')->first();
        $categorySunscreen = Category::where('name_en', 'Sunscreen')->first();

        $brandLaRoche = Brand::where('name_en', 'La Roche-Posay')->first();
        $brandCeraVe = Brand::where('name_en', 'CeraVe')->first();
        $brandOrdinary = Brand::where('name_en', 'The Ordinary')->first();

        // Default categories/brands fallback if not found
        $categoryId1 = $categoryCleanser ? $categoryCleanser->id : 1;
        $categoryId2 = $categoryMoisturizer ? $categoryMoisturizer->id : 1;
        $categoryId3 = $categorySunscreen ? $categorySunscreen->id : 1;

        $brandId1 = $brandLaRoche ? $brandLaRoche->id : 1;
        $brandId2 = $brandCeraVe ? $brandCeraVe->id : 1;
        $brandId3 = $brandOrdinary ? $brandOrdinary->id : 1;

        // Seed Subcategories
        $subCategoryDryCleanse = SubCategory::updateOrCreate(
            ['name_en' => 'Hydrating Cleanse', 'category_id' => $categoryId1],
            ['name_ar' => 'منظف مرطب']
        );
        $subCategoryAcneTreatment = SubCategory::updateOrCreate(
            ['name_en' => 'Acne Treatment', 'category_id' => $categoryId2],
            ['name_ar' => 'علاج حب الشباب']
        );

        $products = [
            [
                'name_en' => 'CeraVe Hydrating Facial Cleanser',
                'name_ar' => 'سيرافي غسول الوجه المرطب',
                'short_name_en' => 'CeraVe Cleanser',
                'short_name_ar' => 'غسول سيرافي',
                'description_en' => 'A unique formula with three essential ceramides that cleanses, hydrates and helps restore the protective skin barrier.',
                'description_ar' => 'تركيبة فريدة تحتوي على ثلاثة سيراميدات أساسية تنظف وترطب وتساعد على استعادة الحاجز الواقي للبشرة.',
                'category_id' => $categoryId1,
                'sub_category_id' => $subCategoryDryCleanse->id,
                'brand_id' => $brandId2,
                'price' => 15.00,
                'stock' => 50,
                'sku' => 'CERAVE-CLEANSER-001',
                'ingredients_en' => 'Purified Water, Glycerin, Cetearyl Alcohol, Ceramide 3, Ceramide 6-II, Ceramide 1, Hyaluronic Acid, Cholesterol.',
                'ingredients_ar' => 'مياه نقية، جليسرين، كحول السيتياريل، سيراميد 3، سيراميد 6-II، سيراميد 1، حمض الهيالورونيك، كوليسترول.',
                'how_to_use_en' => 'Wet skin with lukewarm water. Massage cleanser into skin in a gentle, circular motion. Rinse.',
                'how_to_use_ar' => 'بللي البشرة بالماء الفاتر. دلكي الغسول على البشرة بحركة دائرية لطيفة. اشطفيه بالماء.',
                'image' => 'cerave_cleanser.jpg',
                'status' => 'active',
                
                // Product details
                'texture_ar' => 'رغوي لطيف',
                'texture_en' => 'Gentle creamy gel',
                'why_kcode_ar' => 'لأنه يحتوي على السيراميد ولا يسبب جفاف البشرة',
                'why_kcode_en' => 'Contains essential ceramides and does not strip the skin barrier',
                'usage_frequency_ar' => 'يومياً صباحاً ومساءً',
                'active_strength_level' => 'Low',
                'safety_notes_ar' => 'تجنب ملامسة العينين مباشرة',
                'safety_notes_en' => 'Avoid direct contact with eyes',
                'ar_key_benefits' => 'تنظيف لطيف، استعادة الحاجز الواقي، ترطيب مستمر',
                'en_key_benefits' => 'Gentle cleansing, restores skin barrier, long-lasting hydration',

                // SEO fields
                'ar_product_title_seo' => 'سيرافي غسول البشرة الجافة والمرطبة',
                'en_product_title_seo' => 'CeraVe Hydrating Facial Cleanser - Skin Barrier Restoration',
                'en_short_hook' => 'Cleanses and hydrates without disrupting the skin barrier.',
                'seo_meta_title_ar' => 'غسول سيرافي المرطب للبشرة الجافة والعادية',
                'meta_description_en' => 'Shop CeraVe Hydrating Facial Cleanser with hyaluronic acid and ceramides. Cleanses and restores dry skin.',
                'meta_description_ar' => 'اشتر غسول سيرافي المرطب بحمض الهيالورونيك والسيراميدات لترطيب وتنظيف البشرة الجافة.',
                'primary_keyword_en' => 'CeraVe Hydrating Cleanser',
                'primary_keyword_ar' => 'غسول سيرافي المرطب',
                'secondary_keywords_en' => 'dry skin cleanser, ceramide face wash',
                'secondary_keywords_ar' => 'غسول للبشرة الجافة، غسول السيراميد',
                'final_url_slug' => 'cerave-hydrating-facial-cleanser',
                'image_alt_en' => 'CeraVe Hydrating Facial Cleanser bottle',
                'image_alt_ar' => 'عبوة غسول سيرافي المرطب للوجه',
                'og_title_ar' => 'غسول سيرافي للوجه - الاختيار المثالي للبشرة الجافة',
                'og_description_en' => 'Hydrate and cleanse your skin with CeraVe\'s dermatologically tested formula.',
                'og_description_ar' => 'رطب ونظف بشرتك بتركيبة سيرافي المختبرة من قبل أطباء الجلد.',
                'pdp_headline_en' => 'Cleanse and Hydrate with Ceramides',
                'above_fold_hook_en' => 'Moisturize as you cleanse',
                'keywords' => 'cerave, cleanser, hydrating, ceramides, dry skin',
            ],
            [
                'name_en' => 'La Roche-Posay Effaclar Duo+',
                'name_ar' => 'لاروش بوزيه إيفاكلار ثنائي+',
                'short_name_en' => 'Effaclar Duo+',
                'short_name_ar' => 'إيفاكلار ثنائي+',
                'description_en' => 'Corrective unclogging care, anti-imperfections, anti-marks and anti-recurrence for oily and acne-prone skin.',
                'description_ar' => 'علاج مصحح لانسداد المسام، مضاد للشوائب والآثار ومضاد لظهور حب الشباب للبشرة الدهنية والمعرضة لحب الشباب.',
                'category_id' => $categoryId2,
                'sub_category_id' => $subCategoryAcneTreatment->id,
                'brand_id' => $brandId1,
                'price' => 22.50,
                'stock' => 35,
                'sku' => 'LAROCHE-EFFACLAR-002',
                'ingredients_en' => 'Aqua/Water, Glycerin, Dimethicone, Isocetyl Stearate, Niacinamide, Isopropyl Lauroyl Sarcosinate, Silica, Ammonium Polyacryloyldimethyl Taurate.',
                'ingredients_ar' => 'مياه، جليسرين، ثنائي الميثيكون، إيزوسيتيل ستيرات، نياسيناميد، إيزوبروبيل لوريل ساركوسينات، سيليكا.',
                'how_to_use_en' => 'Apply to entire face morning and/or evening after cleansing skin.',
                'how_to_use_ar' => 'يوضع على كامل الوجه صباحاً و/أو مساءً بعد تنظيف البشرة.',
                'image' => 'laroche_effaclar.jpg',
                'status' => 'active',

                // Product details
                'texture_ar' => 'جل كريمي خفيف',
                'texture_en' => 'Lightweight gel cream',
                'why_kcode_ar' => 'فعال جداً في علاج حب الشباب وآثار الحبوب بفضل النياسيناميد والـ LHA',
                'why_kcode_en' => 'Highly effective against acne blemishes and marks with Niacinamide and LHA',
                'usage_frequency_ar' => 'مرة واحدة يومياً مساءً',
                'active_strength_level' => 'Medium',
                'safety_notes_ar' => 'قد يسبب وخزاً بسيطاً في البداية، استخدم واقي شمس صباحاً',
                'safety_notes_en' => 'May cause mild tingling initially, apply sunscreen in the morning',
                'ar_key_benefits' => 'تقليل الحبوب، إزالة الآثار، فتح المسام المسدودة',
                'en_key_benefits' => 'Reduces blemishes, clears marks, unclogs pores',

                // SEO fields
                'ar_product_title_seo' => 'لاروش بوزيه إيفاكلار ثنائي بلس لعلاج الحبوب',
                'en_product_title_seo' => 'La Roche-Posay Effaclar Duo+ Anti-Acne Treatment',
                'en_short_hook' => 'Corrective treatment for oily and acne-prone skin.',
                'seo_meta_title_ar' => 'لاروش بوزيه ايفاكلار ديو بلس للحبوب والآثار',
                'meta_description_en' => 'Discover La Roche-Posay Effaclar Duo+, the best corrective treatment for oily, acne-prone skin to reduce marks and spots.',
                'meta_description_ar' => 'اكتشف إيفاكلار ثنائي بلس من لاروش بوزيه، العلاج المصحح للبشرة الدهنية والمعرضة لحب الشباب.',
                'primary_keyword_en' => 'Effaclar Duo',
                'primary_keyword_ar' => 'إيفاكلار ديو',
                'secondary_keywords_en' => 'acne treatment, oily skin cream',
                'secondary_keywords_ar' => 'علاج حب الشباب، كريم البشرة الدهنية',
                'final_url_slug' => 'laroche-posay-effaclar-duo-plus',
                'image_alt_en' => 'La Roche-Posay Effaclar Duo+ tube',
                'image_alt_ar' => 'أنبوب لاروش بوزيه إيفاكلار ثنائي بلس',
                'og_title_ar' => 'علاج حب الشباب المتكامل من لاروش بوزيه',
                'og_description_en' => 'Clear breakouts and reduce dark spots with Effaclar Duo+.',
                'og_description_ar' => 'تخلص من الحبوب والبقع الداكنة مع إيفاكلار ديو بلس.',
                'pdp_headline_en' => 'Say Goodbye to Acne Marks',
                'above_fold_hook_en' => 'Visible results in just 12 hours',
                'keywords' => 'laroche, effaclar, acne, blemishes, oily skin',
            ],
            [
                'name_en' => 'The Ordinary Niacinamide 10% + Zinc 1%',
                'name_ar' => 'ذا أورديناري نياسيناميد 10% + زنك 1%',
                'short_name_en' => 'Niacinamide 10% + Zinc 1%',
                'short_name_ar' => 'نياسيناميد 10% + زنك 1%',
                'description_en' => 'High-strength vitamin and mineral blemish formula to reduce the appearance of skin blemishes and congestion.',
                'description_ar' => 'تركيبة فيتامينات ومعادن عالية القوة لتقليل ظهور شوائب البشرة واحتقانها.',
                'category_id' => $categoryId2,
                'sub_category_id' => $subCategoryAcneTreatment->id,
                'brand_id' => $brandId3,
                'price' => 10.00,
                'stock' => 100,
                'sku' => 'ORDINARY-NIACINAMIDE-003',
                'ingredients_en' => 'Aqua (Water), Niacinamide, Pentylene Glycol, Zinc PCA, Dimethyl Isosorbide, Tamarindus Indica Seed Gum, Xanthan Gum.',
                'ingredients_ar' => 'مياه، نياسيناميد، بنتيلين جليكول، زنك PCA، ثنائي ميثيل إيزوسوربيد، صمغ بذور التمر الهندي.',
                'how_to_use_en' => 'Apply to entire face morning and evening before heavier creams.',
                'how_to_use_ar' => 'يوضع على كامل الوجه صباحاً ومساءً قبل الكريمات الأثقل.',
                'image' => 'ordinary_niacinamide.jpg',
                'status' => 'active',

                // Product details
                'texture_ar' => 'سيروم مائي خفيف',
                'texture_en' => 'Light water-based serum',
                'why_kcode_ar' => 'لتنظيم إفراز الدهون وتفتيح البشرة وتقليل حجم المسام',
                'why_kcode_en' => 'Regulates sebum production, brightens skin tone and minimizes pores',
                'usage_frequency_ar' => 'يومياً صباحاً ومساءً قبل الكريمات',
                'active_strength_level' => 'High',
                'safety_notes_ar' => 'لا تستخدمه في نفس الروتين مع فيتامين سي النقي',
                'safety_notes_en' => 'Do not use in the same routine with pure Vitamin C',
                'ar_key_benefits' => 'تنظيم إفراز الزيوت، تفتيح البشرة، تصغير المسام',
                'en_key_benefits' => 'Regulates sebum, brightens skin tone, reduces pore appearance',

                // SEO fields
                'ar_product_title_seo' => 'ذا أورديناري نياسيناميد 10% + زنك 1% للمسام والزيوت',
                'en_product_title_seo' => 'The Ordinary Niacinamide 10% + Zinc 1% Blemish Serum',
                'en_short_hook' => 'High-strength blemish serum with niacinamide and zinc.',
                'seo_meta_title_ar' => 'سيروم نياسيناميد ذا اورديناري لتنظيم الدهون والمسام',
                'meta_description_en' => 'Shop The Ordinary Niacinamide 10% + Zinc 1% to balance visible sebum activity and target congestion.',
                'meta_description_ar' => 'تسوق سيروم نياسيناميد 10% + زنك 1% من ذا أورديناري لتنظيم إفراز الدهون والمسام.',
                'primary_keyword_en' => 'The Ordinary Niacinamide',
                'primary_keyword_ar' => 'سيروم نياسيناميد اورديناري',
                'secondary_keywords_en' => 'niacinamide zinc serum, pore minimizer',
                'secondary_keywords_ar' => 'سيروم المسام، سيروم تنظيم الدهون',
                'final_url_slug' => 'the-ordinary-niacinamide-10-zinc-1',
                'image_alt_en' => 'The Ordinary Niacinamide dropper bottle',
                'image_alt_ar' => 'عبوة سيروم نياسيناميد ذا أورديناري',
                'og_title_ar' => 'سيروم النياسيناميد الأشهر عالمياً من ذا أورديناري',
                'og_description_en' => 'Target skin blemishes and congestion with 10% pure Niacinamide.',
                'og_description_ar' => 'استهدف شوائب البشرة والدهون الزائدة بـ 10% نياسيناميد نقي.',
                'pdp_headline_en' => 'Clear, Balanced and Radiant Skin',
                'above_fold_hook_en' => 'Balance your skin oil and pores',
                'keywords' => 'ordinary, niacinamide, zinc, serum, oily skin, pores',
            ],
        ];

        foreach ($products as $product) {
            Product::updateOrCreate(
                ['sku' => $product['sku']],
                $product
            );
        }
    }
}
