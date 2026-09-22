<?php

namespace Database\Seeders;

use App\Models\Product;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class KcodePdpFieldsSeeder extends Seeder
{
    /**
     * Run the database seeds for KCODE PDP and QuickView fields using real JSON data when available.
     */
    public function run(): void
    {
        $jsonPaths = [
            base_path('exicel/kcode_products.json'),
            base_path('kcode_products.json'),
            'C:/Users/Dell/Downloads/KCODE_Developer_Pack_v16.5 (2)/kcode_modern_v16_5/kcode_products.json',
        ];

        $jsonProductsBySku = [];
        foreach ($jsonPaths as $p) {
            if (file_exists($p)) {
                $content = json_decode(file_get_contents($p), true);
                if (!empty($content['products'])) {
                    foreach ($content['products'] as $pData) {
                        if (!empty($pData['sku'])) {
                            $jsonProductsBySku[$pData['sku']] = $pData;
                        }
                    }
                }
                break;
            }
        }

        $products = Product::all();

        $textureMap = [
            'Lightweight' => 'خفيف وقابل للامتصاص السريع',
            'Medium'      => 'قوام متوسط التغذية',
            'Cream'       => 'كريمي غني ومغذي',
            'Gel'         => 'جل منعش وخفيف',
            'Oil'         => 'زيتي لطيف على البشرة',
        ];

        $frequencyMap = [
            'Daily'    => 'استخدام يومي (صباحاً ومساءً)',
            'Weekly'   => 'استخدام أسبوعي (1-2 مرة أسبوعياً)',
            'Spot'     => 'عند الحاجة على موضع المتاعب',
        ];

        foreach ($products as $product) {
            $jsonData = $jsonProductsBySku[$product->sku] ?? null;

            $textureVal = $jsonData['texture'] ?? 'Lightweight';
            $freqVal = $jsonData['frequency'] ?? 'Daily';
            $actives = $jsonData['customer_actives'] ?? ($jsonData['key_actives'] ?? null);
            $toleranceNum = (int) ($jsonData['tolerance'] ?? 75);

            $strengthLevel = 'Medium';
            if ($toleranceNum >= 70) {
                $strengthLevel = 'High';
            } elseif ($toleranceNum <= 40) {
                $strengthLevel = 'Low';
            }

            $subCatName = strtolower($product->subCategory?->name_en ?? $product->category?->name_en ?? '');
            $roleStr = strtolower($product->role_ar ?: ($jsonData['routine_role'] ?? ''));

            $stepNumber = 3;
            if (str_contains($subCatName, 'cleans') || str_contains($roleStr, 'cleans')) {
                $stepNumber = 1;
            } elseif (str_contains($subCatName, 'toner') || str_contains($subCatName, 'essence') || str_contains($roleStr, 'toner')) {
                $stepNumber = 2;
            } elseif (str_contains($subCatName, 'serum') || str_contains($subCatName, 'ampoule') || str_contains($roleStr, 'treatment') || str_contains($roleStr, 'serum')) {
                $stepNumber = 3;
            } elseif (str_contains($subCatName, 'moistur') || str_contains($subCatName, 'cream') || str_contains($subCatName, 'lotion') || str_contains($roleStr, 'moistur')) {
                $stepNumber = 4;
            } elseif (str_contains($subCatName, 'sun') || str_contains($roleStr, 'sun')) {
                $stepNumber = 5;
            }

            $stepTitle = match ($stepNumber) {
                1 => 'غسول',
                2 => 'تونر أو إسنس',
                3 => 'السيروم / العلاج المركز',
                4 => 'مرطب',
                5 => 'واقي شمس',
                default => 'العناية بالبشرة',
            };

            $routineStepsJson = [
                ['step' => 1, 'title' => 'غسول', 'subtitle' => null, 'is_current' => $stepNumber === 1],
                ['step' => 2, 'title' => 'تونر أو إسنس', 'subtitle' => 'اختياري', 'is_current' => $stepNumber === 2],
                ['step' => 3, 'title' => 'السيروم', 'subtitle' => $stepNumber === 3 ? 'هذا المنتج' : null, 'is_current' => $stepNumber === 3],
                ['step' => 4, 'title' => 'مرطب', 'subtitle' => null, 'is_current' => $stepNumber === 4],
                ['step' => 5, 'title' => 'واقي شمس', 'subtitle' => 'صباحاً', 'is_current' => $stepNumber === 5],
            ];

            $usageInstructionsJson = [
                'timing' => $frequencyMap[$freqVal] ?? 'استخدام يومي (صباحاً ومساءً)',
                'timing_note' => 'بعد التدرّج في الاستخدام.',
                'amount' => 'بضع قطرات',
                'amount_note' => 'على المناطق المستهدفة.',
                'application_method' => 'ربّت بلطف',
                'application_note' => 'حتى الامتصاص، قبل المرطب.',
                'gradual_start' => 'ابدأ تدريجيًا',
                'gradual_start_note' => 'وزِد التكرار حسب تحمّل بشرتك.',
            ];

            $complementaryRoutineJson = [
                ['sku' => 'KC0140', 'category' => 'غسول', 'reason' => 'تنظيف لطيف قبل السيروم', 'optional' => false],
                ['sku' => 'KC0016', 'category' => 'تونر', 'reason' => 'ترطيب خفيف قبل السيروم', 'optional' => true],
                ['sku' => 'KC0223', 'category' => 'مرطب', 'reason' => 'ترطيب بعد السيروم', 'optional' => false],
                ['sku' => 'KC0077', 'category' => 'واقي شمس', 'reason' => 'حماية صباحية لإكمال الروتين', 'optional' => false],
            ];

            $productFaqsJson = [
                [
                    'id' => 'target',
                    'question' => 'هل يستهدف آثار الحبوب أم الحبوب نفسها؟',
                    'answer' => 'نقيّمه أساسًا للعناية بالبقع الداكنة وتفاوت اللون، بما فيها الآثار اللونية التي تبقى بعد الحبوب. أمّا الحبوب النشطة فتحتاج عناية تستهدفها أيضًا؛ العناية بآثارها وحدها لا تعالج سبب ظهورها.',
                ],
                [
                    'id' => 'pairing',
                    'question' => 'هل يناسبني إذا كنت أستخدم منتجًا آخر للبقع؟',
                    'answer' => 'يعتمد ذلك على مكونات المنتج الآخر وتركيزه ومدى تحمّل بشرتك. تشابه الهدف لا يكفي للحكم على ملاءمة الجمع؛ نراجع التركيبتين معًا، لأن إضافة منتج آخر قد تكرر الدور نفسه أو تزيد التهيّج.',
                ],
                [
                    'id' => 'moisturizer',
                    'question' => 'هل يغني عن المرطب؟',
                    'answer' => 'الترطيب فيه دور مساند للعناية بالبقع. استخدم بعده مرطبًا مناسبًا لاحتياج بشرتك، بعد امتصاص السيروم.',
                ],
                [
                    'id' => 'results',
                    'question' => 'متى يمكن ملاحظة تحسّن؟',
                    'answer' => 'تحسّن مظهر البقع تدريجي، وتختلف مدته بحسب نوع البقع وعمقها واستجابة البشرة. تابع التغيّر مع الاستخدام المنتظم والحماية اليومية من الشمس؛ لا توجد مدة واحدة تنطبق على الجميع.',
                ],
            ];

            $realInciList = ($jsonData['inci_ingredients'] ?? null) ?: ($jsonData['full_inci'] ?? null);
            if (!$realInciList) {
                if ($product->id == 1 || str_contains(strtolower($product->name_en), 'niacinamide')) {
                    $realInciList = 'Water, Glycerin, Niacinamide, Tranexamic Acid, Butylene Glycol, Diethoxyethyl Succinate, 1,2-Hexanediol, Arbutin, Sodium Hyaluronate, Alpha-Arbutin, Coccinia Indica Fruit Extract, Eclipta Prostrata Extract, Macadamia Integrifolia Seed Oil, Olea Europaea (Olive) Fruit Oil, Simmondsia Chinensis (Jojoba) Seed Oil, Vitis Vinifera (Grape) Seed Oil, Theobroma Cacao (Cocoa) Extract, Hydrolyzed Hyaluronic Acid, Chamaecyparis Obtusa Leaf Extract, Prunus Persica (Peach) Flower Extract, Camellia Sinensis Seed Oil, Yeast Ferment Extract, Centella Asiatica Extract, Artemisia Princeps Leaf Extract, Candida Bombicola/Glucose/Methyl Rapeseedate Ferment, Hyaluronic Acid, Pentylene Glycol, Betaine Salicylate, Sucrose Palmitate, Hydrogenated Lecithin, Gellan Gum, Sodium Phytate, Cellulose, Caprylic/Capric Triglyceride, Panthenol, Cyanocobalamin, Polyglutamic Acid, 3-O-Ethyl Ascorbic Acid, Ceramide NP, Dextrin, Asiaticoside, Madecassic Acid, Asiatic Acid, Dimethylsilanol Hyaluronate, Hydrolyzed Sodium Hyaluronate, Potassium Hyaluronate, Hydroxypropyltrimonium Hyaluronate, Sodium Hyaluronate Crosspolymer, Sodium Hyaluronate Dimethylsilanol, Sodium Acetylated Hyaluronate, Xanthan Gum';
                } else {
                    $realInciList = 'Water, Glycerin, Butylene Glycol, 1,2-Hexanediol, Niacinamide, Centella Asiatica Extract, Sodium Hyaluronate, Panthenol, Allantoin, Carbomer, Arginine, Ethylhexylglycerin, Disodium EDTA, Adenosine, Caprylic/Capric Triglyceride, Hydrogenated Lecithin, Ceramide NP, Tocopherol, Xanthan Gum';
                }
            }

            $product->update([
                'ingredients_en'        => $realInciList,
                'ingredients_ar'        => $realInciList,
                'size'                  => ($jsonData['size'] ?? null) ?: ($product->size ?: '30ml'),
                'barcode'               => ($jsonData['barcode'] ?? null) ?: ($product->barcode ?: ('880967077' . str_pad($product->id, 4, '0', STR_PAD_LEFT))),
                'sensitive_eligible'    => true,
                'brief_insight_ar'      => $product->brief_insight_ar ?: 'سيروم مهدئ يعزز مرونة البشرة ويقلل الاحمرار والتهيج',
                'country_of_origin_ar'  => $product->country_of_origin_ar ?: 'كوريا الجنوبية',
                'role_ar'               => ($jsonData['routine_role'] ?? null) ?: ($product->role_ar ?: 'Daily Treatment'),
                'limitations_notes_ar'  => $product->limitations_notes_ar ?: 'يُحفظ في مكان بارد وجاف بعيداً عن أشعة الشمس المباشرة. يُنصح بإجراء اختبار حساسية على جزء صغير قبل الاستخدام الكامل.',
                
                // Detailed Product Fields
                'texture_ar'            => $textureMap[$textureVal] ?? $textureVal,
                'texture_en'            => $textureVal,
                'why_kcode_ar'          => $product->why_kcode_ar ?: 'تركيبة كورية أصلية معتمدة تم تقييم أمانها وملاءمتها للبشرة في مختبرات KCODE.',
                'why_kcode_en'          => $product->why_kcode_en ?: 'Authentic Korean formula safety-tested for skin compatibility by KCODE Labs.',
                'usage_frequency_ar'    => $frequencyMap[$freqVal] ?? $freqVal,
                'active_strength_level' => $strengthLevel,
                'safety_notes_ar'       => $product->safety_notes_ar ?: 'خالي من العطور الاصطناعية والمواد المهيجة مناسب للبشرة الحساسة.',
                'safety_notes_en'       => $product->safety_notes_en ?: 'Fragrance-free and low irritation risk for sensitive skin.',
                'ar_key_benefits'       => $actives ? "المكونات الفعالة الرئيسية: {$actives}" : 'تعزيز صحة البشرة والنضارة وتحسين مرونة الجلد',
                'en_key_benefits'       => $actives ? "Key Active Ingredients: {$actives}" : 'Enhance skin health, radiance and barrier strength',

                // SEO Fields
                'ar_product_title_seo'  => $product->ar_product_title_seo ?: $product->name_ar,
                'en_product_title_seo'  => $product->en_product_title_seo ?: $product->name_en,
                'en_short_hook'         => $product->en_short_hook ?: 'Authentic K-Beauty Skincare',
                'seo_meta_title_ar'     => $product->seo_meta_title_ar ?: ($product->name_ar . ' | KCODE'),
                'meta_description_ar'   => $product->meta_description_ar ?: ($product->description_ar ?: 'منتج كوري أصلي للعناية بالبشرة متوفر في متجر KCODE.'),
                'meta_description_en'   => $product->meta_description_en ?: ($product->description_en ?: 'Authentic Korean skincare product available on KCODE.'),
                'primary_keyword_ar'    => $product->primary_keyword_ar ?: 'عناية بالبشرة',
                'primary_keyword_en'    => $product->primary_keyword_en ?: 'K-Beauty',
                'final_url_slug'        => $product->final_url_slug ?: Str::slug($product->name_en),

                // Dynamic PDP & Routine Fields
                'routine_step_number'        => $product->routine_step_number ?: $stepNumber,
                'routine_step_title_ar'      => $product->routine_step_title_ar ?: $stepTitle,
                'routine_steps_json'         => $product->routine_steps_json ?: $routineStepsJson,
                'usage_instructions_json'    => $product->usage_instructions_json ?: $usageInstructionsJson,
                'complementary_routine_json' => $product->complementary_routine_json ?: $complementaryRoutineJson,
                'product_faqs_json'          => $product->product_faqs_json ?: $productFaqsJson,
            ]);
        }
    }
}
