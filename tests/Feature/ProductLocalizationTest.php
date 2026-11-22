<?php

namespace Tests\Feature;

use App\Models\Brand;
use App\Models\Category;
use App\Models\Concern;
use App\Models\Product;
use App\Models\Setting;
use App\Models\SkinType;
use App\Models\SubCategory;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class ProductLocalizationTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        Schema::dropAllTables();

        $migrations = glob(database_path('migrations/*.php'));
        sort($migrations);

        $skipMigrations = [
            '2026_07_26_154500_change_order_status_to_enum_in_orders_table.php',
            '2026_11_12_000001_fix_product_short_name_ar.php',
            '2026_11_18_115000_prefix_categories_and_brands_image_paths.php',
        ];

        foreach ($migrations as $file) {
            if (in_array(basename($file), $skipMigrations, true)) {
                continue;
            }

            $migration = require $file;
            if (is_object($migration) && method_exists($migration, 'up')) {
                $migration->up();
            }
        }

        \App\Support\StaticCache::flushAll();
    }

    private function seedFormulaEvaluationSettings(): void
    {
        $rows = [
            ['formula_eval_title', 'كيف نقيّم التركيبة في KCODE؟', 'How We Evaluate Formulations at KCODE'],
            ['formula_eval_subtitle', 'وراء كل خلاصة، مراجعة تبدأ من تركيبة المنتج نفسه.', 'Behind every conclusion, a review that begins with the product formula itself.'],
            ['formula_eval_step1_title', 'نبدأ من العبوة', 'We start from the packaging'],
            ['formula_eval_step1_desc', 'نراجع قائمة المكونات الكاملة.', 'We review the full ingredient list printed on the product.'],
            ['formula_eval_step2_title', 'وجود المكوّن وحده لا يكفي', 'Ingredient presence alone is not enough'],
            ['formula_eval_step2_desc', 'ندرس وظيفة المكوّن وترتيبه.', 'We evaluate ingredient position and concentration.'],
            ['formula_eval_step3_title', 'نقرأ التركيبة ككل', 'We read the formula as a whole'],
            ['formula_eval_step3_desc', 'نربط المكونات باحتياجات البشرة.', 'We connect ingredients with skin needs cleanly.'],
            ['formula_eval_footer_note', 'نقيّم كل منتج بمعاييرنا نفسها.', 'Every product is evaluated against the same criteria.'],
        ];

        foreach ($rows as [$keyEn, $valAr, $valEn]) {
            Setting::create([
                'key_ar' => $keyEn,
                'key_en' => $keyEn,
                'value_ar' => $valAr,
                'value_en' => $valEn,
            ]);
        }
    }

    private function createCatalogFixture(): array
    {
        $brand = Brand::create([
            'name_en' => 'BIODANCE',
            'name_ar' => 'بيو دانس',
        ]);

        $category = Category::create([
            'name_en' => 'Mask',
            'name_ar' => 'ماسك',
            'description_en' => 'Concentrated weekly treatment',
            'description_ar' => 'عناية مركزة أسبوعية',
        ]);

        $subCategory = SubCategory::create([
            'name_en' => 'Sheet/Hydrogel Mask',
            'name_ar' => 'ماسك هيدروجيل',
            'category_id' => $category->id,
        ]);

        $skinType = SkinType::create([
            'name_en' => 'Sensitive',
            'name_ar' => 'حساسة',
            'description_en' => 'Delicate skin',
            'description_ar' => 'بشرة حساسة',
        ]);

        $concern = Concern::create([
            'name_en' => 'Dryness & Hydration',
            'name_ar' => 'الجفاف والترطيب',
            'description_en' => 'Deep hydration',
            'description_ar' => 'ترطيب عميق',
        ]);

        $complementary = Product::create([
            'sku' => 'KC0137',
            'name_en' => 'DIVE IN Hyaluronic Acid Serum',
            'name_ar' => 'سيروم الهيالورونيك',
            'short_name_en' => 'DIVE IN Serum',
            'short_name_ar' => 'سيروم ترطيب',
            'description_en' => 'Hydrating serum with low molecular hyaluronic acid.',
            'description_ar' => 'سيروم مرطب بحمض الهيالورونيك.',
            'ingredients_en' => 'Water, Glycerin, Sodium Hyaluronate',
            'ingredients_ar' => 'Water, Glycerin, Sodium Hyaluronate',
            'how_to_use_en' => 'Apply 2-3 drops before mask.',
            'how_to_use_ar' => 'ضع بضع قطرات قبل الماسك.',
            'category_id' => $category->id,
            'sub_category_id' => $subCategory->id,
            'brand_id' => $brand->id,
            'price' => 7.5,
            'stock' => 25,
            'size' => '50ml',
            'status' => 'active',
        ]);

        return compact('brand', 'category', 'subCategory', 'skinType', 'concern', 'complementary');
    }

    public function test_bilingual_product_details_switch_cleanly_while_title_stays_fixed_english(): void
    {
        $this->seedFormulaEvaluationSettings();
        $fx = $this->createCatalogFixture();

        $product = Product::create([
            'sku' => 'KC0185',
            'name_en' => 'Bio Collagen Real Deep Mask',
            'name_ar' => 'Bio Collagen Real Deep Mask',
            'short_name_en' => 'Bio Collagen Real Deep Mask',
            'short_name_ar' => 'Bio Collagen Real Deep Mask',
            'description_en' => 'Gel mask delivering intense hydration and smoothing fine lines.',
            'description_ar' => 'ماسك بقوام جل لترطيب عميق وتحسين الخطوط الدقيقة.',
            'brief_insight_en' => 'Hydrogel mask for deep hydration and smoothing dullness.',
            'brief_insight_ar' => 'ماسك هيدروجيل لترطيب البشرة وتحسين مظهر البهتان.',
            'country_of_origin_en' => 'Republic of Korea',
            'country_of_origin_ar' => 'كوريا الجنوبية',
            'role_en' => 'Overnight Hydrating Hydrogel Mask',
            'role_ar' => 'ماسك هيدروجيل مرطّب',
            'usage_frequency_en' => '1-2 times per week',
            'usage_frequency_ar' => 'مرة إلى مرتين أسبوعيًا',
            'limitations_notes_en' => 'Store in a cool dry place. Patch test recommended.',
            'limitations_notes_ar' => 'يُحفظ في مكان بارد وجاف. يُنصح باختبار الحساسية.',
            'routine_step_number' => 4,
            'routine_step_title_en' => 'Hydrogel Mask Step',
            'routine_step_title_ar' => 'خطوة الماسك',
            'texture_en' => 'Hydrogel',
            'texture_ar' => 'جل',
            'why_kcode_en' => 'Selected by KCODE for niacinamide and probiotic ferments.',
            'why_kcode_ar' => 'اختاره KCODE لاحتوائه على النياسيناميد والمخمرات.',
            'safety_notes_en' => 'Avoid direct contact with eyes.',
            'safety_notes_ar' => 'تجنّب ملامسة العينين.',
            'ingredients_en' => 'Water, Glycerin, Niacinamide, Collagen Extract',
            'ingredients_ar' => 'Water, Glycerin, Niacinamide, Collagen Extract',
            'how_to_use_en' => 'Apply after serum for 3-4 hours or overnight.',
            'how_to_use_ar' => 'يوضع بعد السيروم لمدة 3-4 ساعات أو طوال الليل.',
            'ar_key_benefits' => [
                ['title' => 'ترطيب عميق', 'text' => 'يدعم ترطيب سطح البشرة.'],
            ],
            'en_key_benefits' => [
                ['title' => 'Deep Hydration', 'text' => 'Supports surface moisture retention.'],
            ],
            'routine_steps_json' => [
                ['step' => 1, 'title' => 'غسول', 'title_en' => 'Cleanser', 'subtitle' => null, 'subtitle_en' => null, 'is_current' => false],
                ['step' => 4, 'title' => 'ماسك هيدروجيل', 'title_en' => 'Hydrogel Mask', 'subtitle' => 'هذا المنتج', 'subtitle_en' => 'This Product', 'is_current' => true],
            ],
            'usage_instructions_json' => [
                'timing_label' => 'التوقيت',
                'timing_label_en' => 'Timing',
                'timing' => 'مرة إلى مرتين أسبوعيًا',
                'timing_en' => '1-2 times per week',
                'timing_note' => 'بعد السيروم.',
                'timing_note_en' => 'After serum.',
                'amount_label' => 'الكمية',
                'amount_label_en' => 'Amount',
                'amount' => 'شريحة كاملة',
                'amount_en' => 'One full two-piece sheet',
                'amount_note' => 'تثبت برفق.',
                'amount_note_en' => 'Smooth gently onto face.',
                'application_label' => 'التطبيق',
                'application_label_en' => 'Method',
                'application_method' => 'يُترك حتى يصبح شفافًا',
                'application_method_en' => 'Leave until transparent',
                'application_note' => '3-4 ساعات.',
                'application_note_en' => 'For 3-4 hours.',
                'gradual_start_label' => 'ملاحظات',
                'gradual_start_label_en' => 'Notes',
                'gradual_start' => 'أزله عند التهيج',
                'gradual_start_en' => 'Remove if discomfort occurs',
                'gradual_start_note' => 'اتبع بواقي شمس صباحًا.',
                'gradual_start_note_en' => 'Follow with SPF in the morning.',
                'caution' => 'يحفظ بعيدًا عن الحرارة.',
                'caution_en' => 'Keep away from direct heat.',
                'verdict' => [
                    'note' => 'خلاصة عربية للمنتج.',
                    'note_en' => 'Hydrating overnight gel mask with niacinamide.',
                    'sensitive_status' => 'مناسب للبشرة الحساسة',
                    'sensitive_status_en' => 'Suitable for sensitive skin',
                    'sensitive_guidance' => 'تجنبه عند التحسس المعروف لأي مكون.',
                    'sensitive_guidance_en' => 'Avoid if you have a known allergy to any ingredient.',
                    'fragrance' => 'دون عطر مضاف',
                    'fragrance_en' => 'Fragrance-free',
                    'actives_en' => 'Niacinamide · Probiotic Ferments · Collagen Extract',
                ],
            ],
            'complementary_routine_json' => [
                [
                    'product_id' => $fx['complementary']->id,
                    'sku' => 'KC0137',
                    'category' => 'سيروم',
                    'category_en' => 'Serum',
                    'reason' => 'يعزز الترطيب قبل الماسك.',
                    'reason_en' => 'Boosts baseline hydration underneath the mask.',
                    'optional' => false,
                ],
            ],
            'product_faqs_json' => [
                [
                    'id' => 'faq_1',
                    'question' => 'هل يناسب البشرة الحساسة؟',
                    'question_en' => 'Is it suitable for sensitive skin?',
                    'answer' => 'نعم، خالٍ من العطر المضاف.',
                    'answer_en' => 'Yes, it is formulated without added fragrance.',
                ],
            ],
            'seo_meta_title_ar' => 'ماسك بايو كولاجين | KCODE',
            'seo_meta_title_en' => 'Bio Collagen Real Deep Mask | KCODE EN',
            'og_title_ar' => 'بايو كولاجين ماسك',
            'og_title_en' => 'Bio Collagen Real Deep Mask OG',
            'meta_description_ar' => 'وصف سيو عربي',
            'meta_description_en' => 'English SEO meta description.',
            'category_id' => $fx['category']->id,
            'sub_category_id' => $fx['subCategory']->id,
            'brand_id' => $fx['brand']->id,
            'price' => 8.8,
            'stock' => 50,
            'size' => '34g (4EA)',
            'barcode' => '8809937361060',
            'sensitive_eligible' => true,
            'status' => 'active',
        ]);

        $product->skinTypes()->attach($fx['skinType']->id);
        $product->concerns()->attach($fx['concern']->id);

        // 1. Arabic response verification
        $arResponse = $this->withHeaders(['Accept-Language' => 'ar'])
            ->getJson("/api/products/{$product->id}");

        $arResponse->assertOk();
        $ar = $arResponse->json('data');

        $this->assertSame('Bio Collagen Real Deep Mask', $ar['name']);
        $this->assertSame('Bio Collagen Real Deep Mask', $ar['short_name']);
        $this->assertSame('Bio Collagen Real Deep Mask', $ar['specifications']['full_name']);
        $this->assertSame('DIVE IN Hyaluronic Acid Serum', $ar['complementary_routine'][0]['name']);
        $this->assertSame('ماسك هيدروجيل لترطيب البشرة وتحسين مظهر البهتان.', $ar['brief_insight']);
        $this->assertSame('كوريا الجنوبية', $ar['country_of_origin']);
        $this->assertSame('ماسك هيدروجيل مرطّب', $ar['role']);
        $this->assertSame('مناسب للبشرة الحساسة', $ar['sensitive_status']);
        $this->assertSame('دون عطر مضاف', $ar['fragrance']);
        $this->assertSame('كيف نقيّم التركيبة في KCODE؟', $ar['formula_evaluation_info']['title']);

        // 2. English response verification
        $enResponse = $this->withHeaders(['Accept-Language' => 'en'])
            ->getJson("/api/products/{$product->id}");

        $enResponse->assertOk();
        $en = $enResponse->json('data');

        // Title stays fixed in English
        $this->assertSame('Bio Collagen Real Deep Mask', $en['name']);
        $this->assertSame('Bio Collagen Real Deep Mask', $en['short_name']);
        $this->assertSame('Bio Collagen Real Deep Mask', $en['specifications']['full_name']);
        $this->assertSame('DIVE IN Hyaluronic Acid Serum', $en['complementary_routine'][0]['name']);

        // All detail fields switch cleanly to English
        $this->assertSame('Gel mask delivering intense hydration and smoothing fine lines.', $en['description']);
        $this->assertSame('Hydrogel mask for deep hydration and smoothing dullness.', $en['brief_insight']);
        $this->assertSame('Hydrogel mask for deep hydration and smoothing dullness.', $en['brief_insight_en']);
        $this->assertSame('Hydrogel mask for deep hydration and smoothing dullness.', $en['brief_insight_ar']);
        $this->assertSame('Hydrogel mask for deep hydration and smoothing dullness.', $en['short_description_en']);
        $this->assertSame('Republic of Korea', $en['country_of_origin']);
        $this->assertSame('Overnight Hydrating Hydrogel Mask', $en['role']);
        $this->assertSame('Overnight Hydrating Hydrogel Mask', $en['role_en']);
        $this->assertSame('Store in a cool dry place. Patch test recommended.', $en['limitations_notes']);
        $this->assertSame('1-2 times per week', $en['usage_frequency']);
        $this->assertSame('Suitable for sensitive skin', $en['sensitive_status']);
        $this->assertSame('Avoid if you have a known allergy to any ingredient.', $en['sensitive_guidance']);
        $this->assertSame('Fragrance-free', $en['fragrance']);
        $this->assertSame('Hydrogel', $en['texture']);
        $this->assertSame('Overnight Hydrating Hydrogel Mask', $en['specifications']['type']);
        $this->assertSame('Fragrance-free', $en['specifications']['fragrance']);
        $this->assertSame('Deep Hydration', $en['key_benefits'][0]['title']);
        $this->assertSame('Hydrogel Mask Step', $en['routine_position']['step_title']);
        $this->assertSame('1-2 times per week', $en['usage_instructions']['timing']);
        $this->assertSame('Method', $en['usage_instructions']['application_label']);
        $this->assertSame('Suitable for sensitive skin', $en['usage_instructions']['verdict']['sensitive_status']);
        $this->assertSame('Serum', $en['complementary_routine'][0]['category']);
        $this->assertSame('Boosts baseline hydration underneath the mask.', $en['complementary_routine'][0]['reason']);
        $this->assertSame('Is it suitable for sensitive skin?', $en['product_faqs'][0]['question']);
        $this->assertSame('How We Evaluate Formulations at KCODE', $en['formula_evaluation_info']['title']);
        $this->assertSame('We start from the packaging', $en['formula_evaluation_info']['items'][0]['title']);
        $this->assertSame('Bio Collagen Real Deep Mask | KCODE EN', $en['seo']['seo_meta_title']);
        $this->assertSame('Bio Collagen Real Deep Mask OG', $en['seo']['og_title']);
        $this->assertSame('Sensitive', $en['skin_types'][0]['name']);
        $this->assertArrayNotHasKey('name_ar', $en['skin_types'][0]);

        // 3. List endpoint verification for ProductCard in English mode
        $listResponse = $this->withHeaders(['Accept-Language' => 'en'])
            ->getJson('/api/products');

        $listResponse->assertOk();
        $firstItem = collect($listResponse->json('data'))->firstWhere('id', $product->id);
        $this->assertNotNull($firstItem);
        $this->assertSame('Bio Collagen Real Deep Mask', $firstItem['name']);
        $this->assertSame('Hydrogel mask for deep hydration and smoothing dullness.', $firstItem['short_description_en']);
        $this->assertSame('Gel mask delivering intense hydration and smoothing fine lines.', $firstItem['description_en']);
        $this->assertSame('Overnight Hydrating Hydrogel Mask', $firstItem['role_en']);
        $this->assertSame('Suitable for sensitive skin', $firstItem['sensitive_status_en']);
        $this->assertSame('Fragrance-free', $firstItem['fragrance_en']);
    }

    public function test_legacy_arabic_seeded_product_never_leaks_arabic_in_english_mode(): void
    {
        $this->seedFormulaEvaluationSettings();
        $fx = $this->createCatalogFixture();

        // Mirrors live R479-seeded DB state where description_en / how_to_use_en held Arabic
        // and *_en PDP columns + tiles *_en were not yet populated.
        $legacy = Product::create([
            'sku' => 'KC0185_LEGACY',
            'name_en' => 'Bio Collagen Real Deep Mask',
            'name_ar' => 'Bio Collagen Real Deep Mask',
            'short_name_en' => 'Bio Collagen Real Deep Mask',
            'short_name_ar' => 'Bio Collagen Real Deep Mask',
            'description_en' => 'ماسك بقوام جل يساعد على ترطيب البشرة.',
            'description_ar' => 'ماسك بقوام جل يساعد على ترطيب البشرة.',
            'brief_insight_ar' => 'ماسك هيدروجيل لترطيب البشرة.',
            'country_of_origin_ar' => 'كوريا الجنوبية',
            'role_ar' => 'ماسك هيدروجيل مرطّب',
            'usage_frequency_ar' => 'مرة إلى مرتين أسبوعيًا',
            'limitations_notes_ar' => 'يحفظ في مكان بارد وجاف.',
            'routine_step_number' => 4,
            'routine_step_title_ar' => 'ماسك هيدروجيل',
            'texture_en' => 'Gel',
            'texture_ar' => 'جل',
            'why_kcode_en' => 'Authentic BIODANCE formula with Niacinamide and Adenosine.',
            'why_kcode_ar' => 'تركيبة أصلية مع النياسيناميد.',
            'safety_notes_en' => 'تجنب ملامسة العينين من الداخل.',
            'safety_notes_ar' => 'تجنب ملامسة العينين من الداخل.',
            'ingredients_en' => 'Water, Glycerin, Niacinamide',
            'ingredients_ar' => 'Water, Glycerin, Niacinamide',
            'how_to_use_en' => 'التوقيت والترتيب — استخدمه مرة إلى مرتين أسبوعيًا.',
            'how_to_use_ar' => 'التوقيت والترتيب — استخدمه مرة إلى مرتين أسبوعيًا.',
            'ar_key_benefits' => [
                ['title' => 'ترطيب وملمس أكثر نعومة', 'text' => 'يساعد الغليسرين على الترطيب.'],
            ],
            'en_key_benefits' => null,
            'routine_steps_json' => [
                ['step' => 3, 'title' => 'سيروم', 'subtitle' => 'بحسب الحاجة', 'is_current' => false],
                ['step' => 4, 'title' => 'ماسك هيدروجيل', 'subtitle' => 'هذا المنتج', 'is_current' => true],
                ['step' => 5, 'title' => 'واقي شمس', 'subtitle' => 'صباحًا، بعد إزالة الماسك', 'is_current' => false],
            ],
            'usage_instructions_json' => [
                'tiles' => [
                    ['label' => 'التوقيت والترتيب', 'text' => 'استخدمه مرة إلى مرتين أسبوعيًا.'],
                    ['label' => 'طريقة التطبيق', 'text' => 'ثبت جزأي الماسك برفق.'],
                    ['label' => 'المدة وإكمال الروتين', 'text' => 'اتركه 3 إلى 4 ساعات.'],
                    ['label' => 'احتياطات الاستخدام', 'text' => 'أزله عند حدوث تهيّج.'],
                ],
                'benefits' => [
                    ['title' => 'ترطيب وملمس أكثر نعومة', 'text' => 'يساعد الغليسرين على الترطيب.'],
                ],
                'verdict' => [
                    'note' => 'ماسك بقوام جل.',
                    'sensitive_status' => 'مناسب بشرط',
                    'sensitive_guidance' => 'مناسب بشرط عدم وجود تهيّج نشط في البشرة.',
                    'fragrance' => 'دون عطر مضاف',
                    'summary_html' => '<article class="kcode-verdict"><p>ملخص عربي</p></article>',
                ],
            ],
            'complementary_routine_json' => [
                [
                    'product_id' => $fx['complementary']->id,
                    'sku' => 'KC0137',
                    'category' => 'ماسك',
                    'reason' => 'يُستخدم لترطيب إضافي.',
                    'optional' => false,
                ],
            ],
            'product_faqs_json' => [
                [
                    'id' => 'faq_1',
                    'question' => 'كم مرة أستخدمه؟',
                    'answer' => 'مرة إلى مرتين أسبوعيًا.',
                ],
            ],
            'category_id' => $fx['category']->id,
            'sub_category_id' => $fx['subCategory']->id,
            'brand_id' => $fx['brand']->id,
            'price' => 8.8,
            'stock' => 20,
            'size' => '34g (4EA)',
            'sensitive_eligible' => true,
            'status' => 'active',
        ]);

        $enResponse = $this->withHeaders(['Accept-Language' => 'en'])
            ->getJson("/api/products/{$legacy->id}");

        $enResponse->assertOk();
        $en = $enResponse->json('data');

        $noArabicPaths = [
            'name',
            'short_name',
            'description',
            'description_en',
            'short_description',
            'short_description_en',
            'brief_insight',
            'brief_insight_en',
            'brief_insight_ar',
            'how_to_use',
            'safety_notes',
            'why_kcode',
            'country_of_origin',
            'country_of_origin_ar',
            'role',
            'role_en',
            'role_ar',
            'limitations_notes',
            'limitations_notes_ar',
            'usage_frequency',
            'usage_frequency_ar',
            'sensitive_status',
            'sensitive_guidance',
            'fragrance',
            'specifications.type',
            'specifications.texture',
            'specifications.fragrance',
            'quick_view.role',
            'quick_view.timing',
            'quick_view.fragrance',
            'quick_view.sensitive_status',
            'quick_view.sensitive_guidance',
            'quick_view.note',
            'quick_view.summary_html',
            'routine_position.step_title',
            'usage_instructions.timing_label',
            'usage_instructions.timing',
            'usage_instructions.amount_label',
            'usage_instructions.application_label',
            'usage_instructions.gradual_start_label',
            'usage_instructions.caution',
            'usage_instructions.raw_how_to_use',
            'formula_evaluation_info.title',
            'formula_evaluation_info.description',
            'formula_evaluation_info.footer_note',
        ];

        foreach ($noArabicPaths as $dotPath) {
            $value = data_get($en, $dotPath);
            $this->assertIsString($value, "Expected string at {$dotPath}");
            $this->assertDoesNotMatchRegularExpression('/\p{Arabic}/u', $value, "Arabic leaked at {$dotPath}: {$value}");
        }

        foreach ($en['routine_position']['steps'] as $step) {
            $this->assertDoesNotMatchRegularExpression('/\p{Arabic}/u', $step['title']);
            if ($step['subtitle'] !== null) {
                $this->assertDoesNotMatchRegularExpression('/\p{Arabic}/u', $step['subtitle']);
            }
        }

        foreach ($en['usage_instructions']['tiles'] as $tile) {
            $this->assertDoesNotMatchRegularExpression('/\p{Arabic}/u', $tile['label']);
            $this->assertDoesNotMatchRegularExpression('/\p{Arabic}/u', $tile['text']);
        }

        foreach ($en['formula_evaluation_info']['items'] as $evalItem) {
            $this->assertDoesNotMatchRegularExpression('/\p{Arabic}/u', $evalItem['title']);
            $this->assertDoesNotMatchRegularExpression('/\p{Arabic}/u', $evalItem['description']);
        }

        $this->assertSame([], $en['key_benefits']);
        $this->assertSame([], $en['formula_insights']);
        $this->assertSame([], $en['product_faqs']);
        $this->assertSame('Mask', $en['complementary_routine'][0]['category']);
        $this->assertSame('Complements your daily skincare routine.', $en['complementary_routine'][0]['reason']);
        $this->assertSame('Conditionally suitable', $en['sensitive_status']);
        $this->assertSame('Fragrance-free', $en['fragrance']);
        $this->assertSame('Hydrating Hydrogel Mask', $en['specifications']['type']);
    }

    public function test_filament_en_fields_override_seeded_arabic_tiles_and_canonical_not_suitable_status(): void
    {
        $fx = $this->createCatalogFixture();

        $product = Product::create([
            'sku' => 'KC0122',
            'name_en' => 'AHA 7 Whitehead Power Liquid',
            'name_ar' => 'AHA 7 Whitehead Power Liquid',
            'short_name_en' => 'AHA 7 Whitehead Power Liquid',
            'short_name_ar' => 'AHA 7 Whitehead Power Liquid',
            'description_en' => 'Exfoliating treatment with glycolic acid.',
            'description_ar' => 'مقشر بحمض الجليكوليك.',
            'ingredients_en' => 'Pyrus Malus (Apple) Fruit Water, Butylene Glycol, Glycolic Acid',
            'ingredients_ar' => 'Pyrus Malus (Apple) Fruit Water, Butylene Glycol, Glycolic Acid',
            'how_to_use_en' => 'Use 2-3 evenings weekly after cleansing.',
            'how_to_use_ar' => 'يستخدم 2-3 ليالٍ أسبوعيًا.',
            'usage_instructions_json' => [
                'timing_en' => '2-3 evenings per week only',
                'timing_label_en' => 'Evening Frequency',
                'tiles' => [
                    ['label' => 'التوقيت', 'text' => 'مرتين أسبوعيًا'],
                    ['label' => 'الكمية', 'text' => 'بضع قطرات'],
                    ['label' => 'التطبيق', 'text' => 'على قطنة'],
                    ['label' => 'ملاحظة', 'text' => 'واقي شمس صباحًا'],
                ],
                'verdict' => [
                    'sensitive_status' => 'غير مناسب كخطوة أولى للبشرة الحساسة',
                    'sensitive_guidance' => 'يحتوي على أحماض مقشرة.',
                ],
            ],
            'category_id' => $fx['category']->id,
            'sub_category_id' => $fx['subCategory']->id,
            'brand_id' => $fx['brand']->id,
            'price' => 9.5,
            'stock' => 15,
            'sensitive_eligible' => false,
            'status' => 'active',
        ]);

        $en = $this->withHeaders(['Accept-Language' => 'en'])
            ->getJson("/api/products/{$product->id}")
            ->assertOk()
            ->json('data');

        $this->assertSame('Not suitable as a first step for sensitive skin', $en['sensitive_status']);
        $this->assertSame('Evening Frequency', $en['usage_instructions']['timing_label']);
        $this->assertSame('2-3 evenings per week only', $en['usage_instructions']['timing']);
        $this->assertSame('Evening Frequency', $en['usage_instructions']['tiles'][0]['label']);
        $this->assertSame('2-3 evenings per week only', $en['usage_instructions']['tiles'][0]['text']);
        $this->assertStringContainsString('exfoliating', $en['sensitive_guidance']);
    }

    public function test_backfill_seeder_fills_english_pdp_without_overwriting_custom_english_or_arabic(): void
    {
        $fx = $this->createCatalogFixture();

        // Product 1: KC0185 seeded with Arabic in description_en / how_to_use_en and empty *_en PDP fields
        $kc0185 = Product::create([
            'sku' => 'KC0185',
            'name_en' => 'Bio Collagen Real Deep Mask',
            'name_ar' => 'Bio Collagen Real Deep Mask',
            'short_name_en' => 'Bio Collagen Real Deep Mask',
            'short_name_ar' => 'Bio Collagen Real Deep Mask',
            'description_en' => 'ماسك بقوام جل يساعد على ترطيب البشرة.',
            'description_ar' => 'ماسك بقوام جل يساعد على ترطيب البشرة.',
            'brief_insight_ar' => 'ماسك هيدروجيل لترطيب البشرة.',
            'ingredients_en' => 'Water, Glycerin, Niacinamide',
            'ingredients_ar' => 'Water, Glycerin, Niacinamide',
            'how_to_use_en' => 'التوقيت — استخدمه مرة أسبوعيًا.',
            'how_to_use_ar' => 'التوقيت — استخدمه مرة أسبوعيًا.',
            'safety_notes_en' => 'تجنب العين.',
            'safety_notes_ar' => 'تجنب العين.',
            'routine_steps_json' => [
                ['step' => 4, 'title' => 'ماسك', 'subtitle' => 'هذا المنتج', 'is_current' => true],
            ],
            'usage_instructions_json' => [
                'timing' => 'مرة أسبوعيًا',
                'verdict' => [
                    'sensitive_status' => 'مناسب للبشرة الحساسة',
                    'fragrance' => 'دون عطر مضاف',
                ],
            ],
            'complementary_routine_json' => [
                ['sku' => 'KC0137', 'category' => 'سيروم', 'reason' => 'سبب عربي', 'optional' => false],
            ],
            'category_id' => $fx['category']->id,
            'sub_category_id' => $fx['subCategory']->id,
            'brand_id' => $fx['brand']->id,
            'price' => 8.8,
            'stock' => 97,
            'size' => '34g (4EA)',
            'sensitive_eligible' => true,
            'status' => 'active',
        ]);

        // Product 2: Custom product whose English fields were already customized in Filament Dashboard
        $custom = Product::create([
            'sku' => 'KC9999_CUSTOM',
            'name_en' => 'Custom Serum',
            'name_ar' => 'Custom Serum',
            'short_name_en' => 'Custom Serum',
            'short_name_ar' => 'Custom Serum',
            'description_en' => 'Custom manual EN description.',
            'description_ar' => 'وصف عربي مخصص.',
            'brief_insight_en' => 'Custom manual brief EN.',
            'brief_insight_ar' => 'نبذة عربية مخصصة.',
            'role_en' => 'Custom Serum Role EN',
            'country_of_origin_en' => 'Japan',
            'usage_frequency_en' => 'Custom frequency EN',
            'limitations_notes_en' => 'Custom limitation EN',
            'routine_step_title_en' => 'Custom Step Title EN',
            'how_to_use_en' => 'Custom manual how to use EN.',
            'how_to_use_ar' => 'طريقة استخدام عربية.',
            'ingredients_en' => 'Water, Glycerin',
            'ingredients_ar' => 'Water, Glycerin',
            'why_kcode_en' => 'Custom why KCODE EN.',
            'safety_notes_en' => 'Custom safety EN.',
            'en_key_benefits' => [
                ['title' => 'Custom Card', 'text' => 'Custom benefit text.'],
            ],
            'usage_instructions_json' => [
                'timing_en' => 'Custom timing EN',
                'verdict' => [
                    'sensitive_status_en' => 'Conditionally suitable',
                    'fragrance_en' => 'Lightly fragranced',
                ],
            ],
            'category_id' => $fx['category']->id,
            'sub_category_id' => $fx['subCategory']->id,
            'brand_id' => $fx['brand']->id,
            'price' => 14.5,
            'stock' => 12,
            'status' => 'active',
        ]);

        $this->seed(\Database\Seeders\BackfillEnglishProductPdpSeeder::class);

        $kc0185->refresh();
        $this->assertSame('ماسك بقوام جل يساعد على ترطيب البشرة.', $kc0185->description_ar);
        $this->assertSame('ماسك هيدروجيل لترطيب البشرة.', $kc0185->brief_insight_ar);
        $this->assertSame(8.8, (float) $kc0185->price);
        $this->assertSame(97, (int) $kc0185->stock);
        $this->assertDoesNotMatchRegularExpression('/\p{Arabic}/u', (string) $kc0185->description_en);
        $this->assertDoesNotMatchRegularExpression('/\p{Arabic}/u', (string) $kc0185->how_to_use_en);
        $this->assertDoesNotMatchRegularExpression('/\p{Arabic}/u', (string) $kc0185->safety_notes_en);
        $this->assertSame('South Korea', $kc0185->country_of_origin_en);
        $this->assertSame('Sheet/Hydrogel Mask', $kc0185->role_en);
        $this->assertNotEmpty($kc0185->brief_insight_en);
        $this->assertNotEmpty($kc0185->usage_frequency_en);
        $this->assertIsArray($kc0185->en_key_benefits);
        $this->assertCount(3, $kc0185->en_key_benefits);
        $this->assertSame('Moisturizer', $kc0185->routine_steps_json[0]['title_en']);
        $this->assertSame('This Product', $kc0185->routine_steps_json[0]['subtitle_en']);
        $this->assertSame('Suitable for sensitive skin', $kc0185->usage_instructions_json['verdict']['sensitive_status_en']);
        $this->assertSame('Fragrance-free', $kc0185->usage_instructions_json['verdict']['fragrance_en']);
        $this->assertSame('Serum', $kc0185->complementary_routine_json[0]['category_en']);
        $this->assertDoesNotMatchRegularExpression('/\p{Arabic}/u', $kc0185->complementary_routine_json[0]['reason_en']);

        // Assert custom manual English fields remained completely intact
        $custom->refresh();
        $this->assertSame('Custom manual EN description.', $custom->description_en);
        $this->assertSame('Custom manual brief EN.', $custom->brief_insight_en);
        $this->assertSame('Custom Serum Role EN', $custom->role_en);
        $this->assertSame('Japan', $custom->country_of_origin_en);
        $this->assertSame('Custom timing EN', $custom->usage_instructions_json['timing_en']);
        $this->assertSame('Lightly fragranced', $custom->usage_instructions_json['verdict']['fragrance_en']);
        $this->assertSame('Custom Card', $custom->en_key_benefits[0]['title']);
    }

    public function test_sync_r479_seeder_preserves_custom_english_faq_and_usage_while_filling_english(): void
    {
        $fx = $this->createCatalogFixture();

        $kc0185 = Product::create([
            'sku' => 'KC0185',
            'name_en' => 'Bio Collagen Real Deep Mask',
            'name_ar' => 'Bio Collagen Real Deep Mask',
            'short_name_en' => 'Bio Collagen Real Deep Mask',
            'short_name_ar' => 'Bio Collagen Real Deep Mask',
            'description_en' => 'Custom EN editorial description.',
            'description_ar' => 'قديم',
            'ingredients_en' => 'Water',
            'ingredients_ar' => 'Water',
            'how_to_use_en' => '',
            'how_to_use_ar' => '',
            'product_faqs_json' => [
                [
                    'id' => 'faq_1',
                    'question' => 'سؤال قديم',
                    'question_en' => 'Custom preserved EN question?',
                    'answer' => 'إجابة قديمة',
                    'answer_en' => 'Custom preserved EN answer.',
                ],
            ],
            'usage_instructions_json' => [
                'timing_en' => 'Custom preserved timing EN',
            ],
            'category_id' => $fx['category']->id,
            'sub_category_id' => $fx['subCategory']->id,
            'brand_id' => $fx['brand']->id,
            'price' => 8.8,
            'stock' => 97,
            'status' => 'active',
        ]);

        $this->seed(\Database\Seeders\SyncAllR479ProductsSeeder::class);

        $kc0185->refresh();
        $this->assertSame('Custom EN editorial description.', $kc0185->description_en);
        $this->assertDoesNotMatchRegularExpression('/\p{Arabic}/u', (string) $kc0185->how_to_use_en);
        $this->assertSame('Custom preserved EN question?', $kc0185->product_faqs_json[0]['question_en']);
        $this->assertSame('Custom preserved EN answer.', $kc0185->product_faqs_json[0]['answer_en']);
        $this->assertSame('Custom preserved timing EN', $kc0185->usage_instructions_json['timing_en']);
        $this->assertSame('Sheet/Hydrogel Mask', $kc0185->role_en);
    }

    public function test_other_store_apis_and_backfill_idempotency_remain_intact(): void
    {
        $this->seed(\Database\Seeders\SettingSeeder::class);
        $this->seedFormulaEvaluationSettings();
        $fx = $this->createCatalogFixture();

        $user = \App\Models\User::create([
            'name' => 'Test Customer',
            'email' => 'customer@example.com',
            'password' => bcrypt('password'),
        ]);

        $product = Product::create([
            'sku' => 'KC0185',
            'name_en' => 'Bio Collagen Real Deep Mask',
            'name_ar' => 'Bio Collagen Real Deep Mask',
            'short_name_en' => 'Bio Collagen Real Deep Mask',
            'short_name_ar' => 'Bio Collagen Real Deep Mask',
            'description_ar' => 'ماسك هيدروجيل.',
            'description_en' => 'ماسك هيدروجيل.',
            'ingredients_en' => 'Water, Glycerin',
            'ingredients_ar' => 'Water, Glycerin',
            'how_to_use_ar' => 'يوضع على البشرة.',
            'how_to_use_en' => 'يوضع على البشرة.',
            'category_id' => $fx['category']->id,
            'sub_category_id' => $fx['subCategory']->id,
            'brand_id' => $fx['brand']->id,
            'price' => 8.8,
            'stock' => 25,
            'status' => 'active',
        ]);

        \App\Models\Offer::create([
            'product_id' => $product->id,
            'discount_percentage' => 15,
            'start_date' => now()->subDay(),
            'end_date' => now()->addDay(),
            'is_active' => true,
        ]);

        \App\Models\Review::create([
            'product_id' => $product->id,
            'user_id' => $user->id,
            'rating' => 5,
            'comment' => 'ممتاز جداً',
        ]);

        \App\Models\Review::create([
            'product_id' => null,
            'user_id' => $user->id,
            'rating' => 5,
            'comment' => 'متجر رائع',
        ]);

        // Run backfill seeder twice to verify strict idempotency
        $this->seed(\Database\Seeders\BackfillEnglishProductPdpSeeder::class);
        $firstSnapshot = $product->fresh()->toArray();
        $this->seed(\Database\Seeders\BackfillEnglishProductPdpSeeder::class);
        $secondSnapshot = $product->fresh()->toArray();
        $this->assertSame($firstSnapshot['description_en'], $secondSnapshot['description_en']);
        $this->assertSame($firstSnapshot['en_key_benefits'], $secondSnapshot['en_key_benefits']);

        // Verify all key storefront endpoints continue working in both AR and EN
        foreach (['ar', 'en'] as $locale) {
            $headers = ['Accept-Language' => $locale];
            $this->withHeaders($headers)->getJson('/api/brands')->assertOk();
            $this->withHeaders($headers)->getJson('/api/categories')->assertOk();
            $this->withHeaders($headers)->getJson('/api/sub-categories')->assertOk();
            $this->withHeaders($headers)->getJson('/api/skin-types')->assertOk();
            $this->withHeaders($headers)->getJson('/api/concerns')->assertOk();
            $this->withHeaders($headers)->getJson('/api/products/filter')->assertOk();
            $this->withHeaders($headers)->getJson('/api/products/search?query=Bio')->assertOk();
            $this->withHeaders($headers)->getJson("/api/products/{$product->id}/alternatives")->assertOk();
            $this->withHeaders($headers)->getJson('/api/offers')->assertOk();
            $this->withHeaders($headers)->getJson('/api/assurances')->assertOk();
            $this->withHeaders($headers)->getJson('/api/settings')->assertOk();
            $this->withHeaders($headers)->getJson('/api/banner/announcement')->assertOk();
            $this->withHeaders($headers)->getJson('/api/reviews/general')->assertOk();
        }

        // Verify category & brand localization was unaffected
        $arCategory = $this->withHeaders(['Accept-Language' => 'ar'])->getJson('/api/categories')->json('data.0');
        $enCategory = $this->withHeaders(['Accept-Language' => 'en'])->getJson('/api/categories')->json('data.0');
        $this->assertSame('ماسك', $arCategory['name']);
        $this->assertSame('Mask', $enCategory['name']);

        $arBrand = $this->withHeaders(['Accept-Language' => 'ar'])->getJson('/api/brands')->json('data.0');
        $enBrand = $this->withHeaders(['Accept-Language' => 'en'])->getJson('/api/brands')->json('data.0');
        $this->assertSame('BIODANCE', $arBrand['name']);
        $this->assertSame('BIODANCE', $enBrand['name']);
    }
}
