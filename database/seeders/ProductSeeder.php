<?php

namespace Database\Seeders;

use App\Models\Brand;
use App\Models\Category;
use App\Models\SubCategory;
use App\Models\Product;
use App\Models\Concern;
use App\Models\SkinType;
use App\Models\ProductConcern;
use App\Models\ProductSkinType;
use App\Models\ProductRoutine;
use App\Models\RoutineStep;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

class ProductSeeder extends Seeder
{
    /**
     * Seed all 256 official KCODE R479 products from exicel/kcode_products.json.
     */
    public function run(): void
    {
        // 1. Clear existing products and relations
        Schema::disableForeignKeyConstraints();
        ProductConcern::truncate();
        ProductSkinType::truncate();
        \App\Models\ProductGoal::truncate();
        \App\Models\ProductMarketingDetail::truncate();
        \App\Models\ProductRecommendationRule::truncate();
        \App\Models\ProductAudit::truncate();
        ProductRoutine::truncate();
        \App\Models\ProductImage::truncate();
        Product::truncate();
        Schema::enableForeignKeyConstraints();

        // 2. Ensure storage folders exist and copy all R479 product & gallery images from database/seeders/assets/products
        $sourceAssetsDir = database_path('seeders/assets/products');
        $targetProductsDir = storage_path('app/public/products');
        $targetGalleryDir = storage_path('app/public/product_images');

        if (!File::exists($targetProductsDir)) {
            File::makeDirectory($targetProductsDir, 0755, true, true);
        }
        if (!File::exists($targetGalleryDir)) {
            File::makeDirectory($targetGalleryDir, 0755, true, true);
        }

        if (File::exists($sourceAssetsDir)) {
            foreach (File::files($sourceAssetsDir) as $file) {
                $filename = $file->getFilename();
                if ($filename === 'products_manifest.json') {
                    continue;
                }
                File::copy($file->getPathname(), $targetProductsDir . '/' . $filename);
                if (str_contains($filename, '_gallery_') || str_starts_with($filename, 'default')) {
                    File::copy($file->getPathname(), $targetGalleryDir . '/' . $filename);
                }
            }
        }

        $jsonPaths = [
            base_path('exicel/kcode_products.json'),
            base_path('kcode_products.json'),
        ];

        $jsonFile = null;
        foreach ($jsonPaths as $p) {
            if (file_exists($p)) {
                $jsonFile = $p;
                break;
            }
        }

        if (!$jsonFile) {
            $this->command->error("kcode_products.json file not found.");
            return;
        }

        $jsonContent = json_decode(file_get_contents($jsonFile), true);
        $jsonProducts = $jsonContent['products'] ?? [];

        $imagesMapFile = database_path('seeders/product_images_map.json');
        $imagesMap = file_exists($imagesMapFile) ? json_decode(file_get_contents($imagesMapFile), true) : [];

        // Normalization map to strictly map all R479 JSON categories to the 10 official Product Types
        $categoryNormalizer = [
            'Cleanser'       => 'Cleanser',
            'cleanser'       => 'Cleanser',
            'Toner'          => 'Toner',
            'toner'          => 'Toner',
            'Mist'           => 'Toner',
            'mist'           => 'Toner',
            'Essence'        => 'Essence',
            'essence'        => 'Essence',
            'Serum'          => 'Serum',
            'serum'          => 'Serum',
            'Spot Treatment' => 'Serum',
            'spot'           => 'Serum',
            'Moisturizer'    => 'Moisturizer',
            'moisturizer'    => 'Moisturizer',
            'Sunscreen'      => 'Sunscreen',
            'sunscreen'      => 'Sunscreen',
            'Eye Care'       => 'Eye Care',
            'eye'            => 'Eye Care',
            'Exfoliator'     => 'Exfoliator',
            'exfoliator'     => 'Exfoliator',
            'Mask'           => 'Mask',
            'mask'           => 'Mask',
            'lip'            => 'Mask',
            'Body Care'      => 'Body Care',
            'body'           => 'Body Care',
        ];

        $categoryTranslations = [
            'Cleanser'    => 'غسول',
            'Toner'       => 'تونر',
            'Essence'     => 'إسنس',
            'Serum'       => 'سيروم وأمبول',
            'Moisturizer' => 'مرطب',
            'Sunscreen'   => 'واقي شمس',
            'Eye Care'    => 'العناية بالعين',
            'Exfoliator'  => 'مقشر',
            'Mask'        => 'ماسكات',
            'Body Care'   => 'العناية بالجسم',
        ];

        $stepMapping = [
            'Cleanser'    => ['ar' => 'غسول', 'en' => 'Cleanser', 'order' => 1],
            'Toner'       => ['ar' => 'تونر', 'en' => 'Toner', 'order' => 2],
            'Essence'     => ['ar' => 'إسنس', 'en' => 'Essence', 'order' => 3],
            'Serum'       => ['ar' => 'سيروم وأمبول', 'en' => 'Serum', 'order' => 4],
            'Moisturizer' => ['ar' => 'مرطب', 'en' => 'Moisturizer', 'order' => 5],
            'Sunscreen'   => ['ar' => 'واقي شمس', 'en' => 'Sunscreen', 'order' => 6],
            'Eye Care'    => ['ar' => 'العناية بالعين', 'en' => 'Eye Care', 'order' => 7],
            'Exfoliator'  => ['ar' => 'مقشر', 'en' => 'Exfoliator', 'order' => 8],
            'Mask'        => ['ar' => 'ماسكات', 'en' => 'Mask', 'order' => 9],
            'Body Care'   => ['ar' => 'العناية بالجسم', 'en' => 'Body Care', 'order' => 10],
        ];

        if (SkinType::count() === 0) {
            (new SkinTypeSeeder())->run();
        }

        if (Concern::count() === 0) {
            (new ConcernSeeder())->run();
        }

        $allConcerns = Concern::all();
        $allRoutineGoals = \App\Models\RoutineGoal::all();

        $seededCount = 0;
        $complementaryPass = [];

        foreach ($jsonProducts as $pIndex => $pData) {
            $brandName = trim($pData['brand'] ?? '');
            $prodName = trim($pData['product'] ?? '');
            $sku = trim($pData['sku'] ?? '');
            $size = trim($pData['size'] ?? '');

            if (empty($brandName) || empty($prodName) || empty($sku)) {
                continue;
            }

            $fullEnName = "{$brandName} {$prodName}" . ($size ? " ({$size})" : "");
            $slug = Str::slug("{$sku}-{$brandName}-{$prodName}");

            // Match Brand case-insensitively
            $brand = Brand::whereRaw('LOWER(name_en) = ?', [strtolower($brandName)])->first();
            if (!$brand) {
                $brand = Brand::create([
                    'name_en' => $brandName,
                    'name_ar' => $brandName,
                    'image'   => Str::slug($brandName) . '.png',
                ]);
            }

            // Strictly normalize category to one of the 10 official Product Types
            $rawCategory = $pData['category'] ?? 'Serum';
            $rawSubCategory = $pData['sub_category'] ?? $rawCategory;
            if ($rawCategory === 'lip' && stripos($rawSubCategory, 'Serum') !== false) {
                $categoryName = 'Serum';
            } else {
                $categoryName = $categoryNormalizer[$rawCategory] ?? 'Serum';
            }

            $category = Category::where('name_en', $categoryName)->first();
            if (!$category) {
                $category = Category::create([
                    'name_en' => $categoryName,
                    'name_ar' => $categoryTranslations[$categoryName] ?? $categoryName,
                    'image'   => Str::slug($categoryName) . '.webp',
                ]);
            }

            $subCategoryNameEn = $rawSubCategory ?: $categoryName;
            $subCategoryNameAr = !empty($pData['sub_category_label_ar'])
                ? $pData['sub_category_label_ar']
                : ($categoryTranslations[$categoryName] ?? $subCategoryNameEn);

            $subCategory = SubCategory::updateOrCreate(
                ['name_en' => $subCategoryNameEn, 'category_id' => $category->id],
                ['name_ar' => $subCategoryNameAr, 'image' => Str::slug($subCategoryNameEn) . '.webp']
            );

            $descAr = $pData['description_ar'] ?? '';
            $briefAr = !empty($pData['brief_ar']) ? $pData['brief_ar'] : $descAr;
            $activesStr = !empty($pData['customer_actives']) ? $pData['customer_actives'] : ($pData['key_actives'] ?? '');
            $descEn = "Authentic {$brandName} {$prodName} ({$size})" . ($activesStr ? " formulated with {$activesStr}." : ".");
            $nameAr = "{$brandName} {$prodName}";

            $imgData = $imagesMap[$sku] ?? null;
            $mainImg = $imgData['main'] ?? "products/{$sku}.webp";
            $subsList = $imgData['subs'] ?? ["products/{$sku}_gallery_1.webp"];

            $inciText = !empty($pData['full_inci']) ? $pData['full_inci'] : '';
            $howToUseAr = !empty($pData['usage_text_ar']) ? $pData['usage_text_ar'] : $descAr;

            // Format benefits text from R479 formula reading cards
            $benefitsArr = $pData['benefits'] ?? [];
            $benefitsLinesAr = [];
            foreach ($benefitsArr as $bItem) {
                if (!empty($bItem['title']) && !empty($bItem['text'])) {
                    $benefitsLinesAr[] = "{$bItem['title']}: {$bItem['text']}";
                }
            }
            $arKeyBenefits = !empty($benefitsLinesAr)
                ? implode("\n\n", $benefitsLinesAr)
                : (!empty($pData['actives_ar']) ? "المكونات الفعالة: {$pData['actives_ar']}" : $briefAr);

            $enKeyBenefits = $activesStr ? "Key Active Ingredients: {$activesStr}" : $descEn;

            // Verdict & Safety Notes
            $verdictNoteAr = !empty($pData['verdict_note_ar']) ? $pData['verdict_note_ar'] : $descAr;
            $skinFitGuidanceAr = $pData['skin_fit_guidance_ar'] ?? '';
            $whyKcodeAr = $verdictNoteAr . ($skinFitGuidanceAr ? " {$skinFitGuidanceAr}" : '');

            $sensStatusAr = $pData['sensitive_status_ar'] ?? ($pData['sensitivity_label'] ?? '');
            $sensGuidanceAr = $pData['sensitive_guidance_ar'] ?? ($pData['sensitivity_condition'] ?? '');
            $safetyNotesAr = trim(($sensStatusAr ? "{$sensStatusAr} — " : '') . $sensGuidanceAr);
            if (empty($safetyNotesAr)) {
                $safetyNotesAr = !empty($pData['sensitive_eligible'])
                    ? 'مناسب للبشرة الحساسة؛ اتبع تعليمات الاستخدام.'
                    : 'غير مرشح للبشرة الحساسة؛ راجع قائمة المكونات قبل الاستخدام.';
            }

            $limitationsAr = !empty($pData['usage_caution_ar'])
                ? $pData['usage_caution_ar']
                : ($sensGuidanceAr ?: 'يُحفظ في مكان بارد وجاف بعيداً عن أشعة الشمس المباشرة. يُنصح بإجراء اختبار حساسية على جزء صغير قبل الاستخدام الكامل.');

            $toleranceNum = (float) ($pData['tolerance'] ?? 75);
            $strengthLevel = $toleranceNum >= 70 ? 'High' : ($toleranceNum <= 45 ? 'Low' : 'Medium');

            // Usage instructions JSON from the 4 R479 usage tiles
            $tiles = $pData['usage_tiles_detailed'] ?? [];
            $usageInstructionsJson = [
                'timing_label'       => $tiles[0]['label'] ?? 'التوقيت',
                'timing'             => $tiles[0]['value'] ?? (!empty($pData['timing_ar']) ? $pData['timing_ar'] : 'صباحًا ومساءً'),
                'timing_note'        => $tiles[0]['note'] ?? '',
                'amount_label'       => $tiles[1]['label'] ?? 'الكمية',
                'amount'             => $tiles[1]['value'] ?? '',
                'amount_note'        => $tiles[1]['note'] ?? '',
                'application_label'  => $tiles[2]['label'] ?? 'التطبيق',
                'application_method' => $tiles[2]['value'] ?? '',
                'application_note'   => $tiles[2]['note'] ?? '',
                'gradual_start_label'=> $tiles[3]['label'] ?? 'ملاحظة الروتين',
                'gradual_start'      => $tiles[3]['value'] ?? '',
                'gradual_start_note' => $tiles[3]['note'] ?? '',
                'caution'            => $pData['usage_caution_ar'] ?? '',
                'tiles'              => $tiles,
                'benefits'           => $benefitsArr,
                'verdict'            => [
                    'note'               => $verdictNoteAr,
                    'skin_fit_value'     => $pData['skin_fit_value_ar'] ?? 'الدهنية · المختلطة · العادية · الجافة',
                    'skin_fit_guidance'  => $skinFitGuidanceAr,
                    'sensitive_status'   => $sensStatusAr,
                    'sensitive_guidance' => $sensGuidanceAr,
                    'fragrance'          => $pData['fragrance_ar'] ?? '',
                    'actives'            => $pData['actives_ar'] ?? $activesStr,
                    'summary_html'       => $pData['summary_html'] ?? '',
                ],
            ];

            $sensitiveEligible = (bool) ($pData['sensitive_eligible'] ?? ($toleranceNum >= 60));

            $product = Product::updateOrCreate(
                ['sku' => $sku],
                [
                    'brand_id'              => $brand->id,
                    'category_id'           => $category->id,
                    'sub_category_id'       => $subCategory->id,
                    'name_en'               => $fullEnName,
                    'name_ar'               => $nameAr,
                    'short_name_en'         => $prodName,
                    'short_name_ar'         => $prodName,
                    'sku'                   => $sku,
                    'price'                 => (float) ($pData['price'] ?? 9.9),
                    'stock'                 => (int) ($pData['inventory'] ?? 80),
                    'image'                 => $mainImg,
                    'description_ar'        => $descAr,
                    'description_en'        => $descEn,
                    'ingredients_ar'        => $inciText,
                    'ingredients_en'        => $inciText,
                    'how_to_use_ar'         => $howToUseAr,
                    'how_to_use_en'         => $howToUseAr,
                    'status'                => 'active',
                    'is_best_seller'        => $pIndex < 40,
                    'sales_count'           => max(50, 500 - ($pIndex * 2)),
                    'size'                  => $size ?: '30ml',
                    'barcode'               => $pData['barcode'] ?? '',
                    'sensitive_eligible'    => $sensitiveEligible,
                    'brief_insight_ar'      => $briefAr,
                    'country_of_origin_ar'  => 'كوريا الجنوبية',
                    'role_ar'               => $briefAr ?: $subCategoryNameAr,
                    'limitations_notes_ar'  => $limitationsAr,
                    'texture_ar'            => !empty($pData['texture_ar']) ? $pData['texture_ar'] : ($pData['texture'] ?? 'خفيف'),
                    'texture_en'            => $pData['texture'] ?? 'Lightweight',
                    'why_kcode_ar'          => $whyKcodeAr,
                    'why_kcode_en'          => $descEn,
                    'usage_frequency_ar'    => !empty($pData['timing_ar']) ? $pData['timing_ar'] : ($tiles[0]['value'] ?? 'صباحًا ومساءً'),
                    'active_strength_level' => $strengthLevel,
                    'safety_notes_ar'       => $safetyNotesAr,
                    'safety_notes_en'       => $pData['sensitivity_reason'] ?? $safetyNotesAr,
                    'ar_key_benefits'       => $arKeyBenefits,
                    'en_key_benefits'       => $enKeyBenefits,
                    'ar_product_title_seo'  => "{$brandName} {$prodName} | KCODE",
                    'en_product_title_seo'  => "{$brandName} {$prodName} | KCODE",
                    'en_short_hook'         => $prodName,
                    'seo_meta_title_ar'     => "{$brandName} {$prodName} | KCODE",
                    'meta_description_ar'   => $descAr,
                    'meta_description_en'   => $descEn,
                    'primary_keyword_ar'    => !empty($pData['actives_ar']) ? $pData['actives_ar'] : $subCategoryNameAr,
                    'primary_keyword_en'    => $activesStr ?: $subCategoryNameEn,
                    'final_url_slug'        => $slug,
                    'image_alt_ar'          => $pData['image_alt_ar'] ?: "{$brandName} {$prodName}",
                    'image_alt_en'          => $pData['image_alt_en'] ?: "{$brandName} {$prodName}",
                    'og_title_ar'           => "{$brandName} {$prodName} | KCODE",
                    'og_description_ar'     => $briefAr,
                    'og_description_en'     => $descEn,
                    'routine_step_number'   => (int) ($pData['routine_step_number'] ?? 3),
                    'routine_step_title_ar' => $pData['routine_step_title_ar'] ?? $subCategoryNameAr,
                    'routine_steps_json'    => $pData['routine_steps'] ?? [],
                    'usage_instructions_json' => $usageInstructionsJson,
                    'product_faqs_json'     => $pData['faqs'] ?? [],
                    'complementary_routine_json' => $pData['complementary_routine'] ?? [],
                ]
            );

            $complementaryPass[$product->id] = $pData['complementary_routine'] ?? [];

            // Seed gallery images for ProductImage table
            \App\Models\ProductImage::where('product_id', $product->id)->delete();
            foreach ($subsList as $subImgUrl) {
                \App\Models\ProductImage::create([
                    'product_id' => $product->id,
                    'images'     => $subImgUrl,
                ]);
            }

            $skinFit = $pData['skin_fit'] ?? [];
            $goals = $pData['goals'] ?? [];

            // Map SkinTypes based on official R479 skin_fit dictionary scores (threshold >= 60)
            $skinFitKeyMap = [
                'Oily'        => 'oily',
                'Dry'         => 'dry',
                'Combination' => 'combination',
                'Normal'      => 'normal',
            ];

            $matchedSkinTypeIds = [];
            if (!empty($skinFit)) {
                foreach ($skinFitKeyMap as $dbSkinNameEn => $jsonKey) {
                    $fitScore = $skinFit[$jsonKey] ?? 0;
                    if ($fitScore >= 60 || in_array($jsonKey, $pData['skins'] ?? [], true)) {
                        $stModel = SkinType::where('name_en', $dbSkinNameEn)->first();
                        if ($stModel && !in_array($stModel->id, $matchedSkinTypeIds)) {
                            $matchedSkinTypeIds[] = $stModel->id;
                            ProductSkinType::firstOrCreate([
                                'product_id'   => $product->id,
                                'skin_type_id' => $stModel->id,
                            ]);
                        }
                    }
                }
            }

            if ($sensitiveEligible) {
                $sensitiveModel = SkinType::where('name_en', 'Sensitive')->first();
                if ($sensitiveModel) {
                    ProductSkinType::firstOrCreate([
                        'product_id'   => $product->id,
                        'skin_type_id' => $sensitiveModel->id,
                    ]);
                }
            }

            // Fallback: If no skin type met threshold 60, attach product to its highest scoring skin type
            if (empty($matchedSkinTypeIds) && !empty($skinFit)) {
                $bestFitKey = array_search(max($skinFit), $skinFit);
                $keyMap = ['oily' => 'Oily', 'dry' => 'Dry', 'combination' => 'Combination', 'normal' => 'Normal'];
                $bestName = $keyMap[$bestFitKey] ?? 'Normal';
                $stModel = SkinType::where('name_en', $bestName)->first();
                if ($stModel) {
                    ProductSkinType::firstOrCreate([
                        'product_id'   => $product->id,
                        'skin_type_id' => $stModel->id,
                    ]);
                }
            }

            // Map Concerns based on R479 goals dictionary scores
            $concernKeyMap = [
                'acne'             => 'Acne & Blemishes',
                'oil_control'      => 'Pores & Blackheads',
                'pores'            => 'Pores & Blackheads',
                'blackheads'       => 'Pores & Blackheads',
                'pigmentation_pih' => 'Pigmentation & Dark Spots',
                'redness_pie'      => 'Redness & Irritation',
                'barrier'          => 'Skin Barrier',
                'hydration'        => 'Dryness & Hydration',
                'radiance'         => 'Dullness & Lack of Glow',
                'lines'            => 'Wrinkles & Fine Lines',
                'eye_dark_circles' => 'Eye Contour Concerns',
                'eye_puffiness'    => 'Eye Contour Concerns',
            ];

            $matchedConcernIds = [];
            foreach ($concernKeyMap as $jsonKey => $dbConcernEn) {
                $score = $goals[$jsonKey] ?? 0;
                if ($score >= 50) {
                    $cModel = Concern::where('name_en', $dbConcernEn)->first();
                    if ($cModel && !in_array($cModel->id, $matchedConcernIds)) {
                        $matchedConcernIds[] = $cModel->id;
                        ProductConcern::firstOrCreate([
                            'product_id' => $product->id,
                            'concern_id' => $cModel->id,
                        ]);
                    }
                }
            }

            if (empty($matchedConcernIds)) {
                foreach ($allConcerns as $c) {
                    ProductConcern::firstOrCreate([
                        'product_id' => $product->id,
                        'concern_id' => $c->id,
                    ]);
                }
            }

            // Map Goals (ProductGoal) based on R479 goals dictionary scores
            $goalKeyMap = [
                'radiance'         => 'Radiance & Freshness',
                'acne'             => 'Acne & Scars',
                'oil_control'      => 'Pore Care',
                'pores'            => 'Pore Care',
                'blackheads'       => 'Pore Care',
                'pigmentation_pih' => 'Brightening & Evening Tone',
                'hydration'        => 'Hydration & Protection',
                'barrier'          => 'Hydration & Protection',
            ];

            $matchedGoalIds = [];
            foreach ($goalKeyMap as $jsonKey => $dbGoalEn) {
                $score = $goals[$jsonKey] ?? 0;
                if ($score >= 50) {
                    $gModel = \App\Models\RoutineGoal::where('name_en', $dbGoalEn)->first();
                    if ($gModel && !in_array($gModel->id, $matchedGoalIds)) {
                        $matchedGoalIds[] = $gModel->id;
                        \App\Models\ProductGoal::firstOrCreate([
                            'product_id' => $product->id,
                            'goal_id'    => $gModel->id,
                        ]);
                    }
                }
            }

            if (empty($matchedGoalIds)) {
                foreach ($allRoutineGoals as $rg) {
                    \App\Models\ProductGoal::firstOrCreate([
                        'product_id' => $product->id,
                        'goal_id'    => $rg->id,
                    ]);
                }
            }

            $stepName = $categoryName;
            $mapping = $stepMapping[$stepName] ?? ['ar' => $stepName, 'en' => $stepName, 'order' => 4];
            $routineStep = RoutineStep::firstOrCreate(
                ['name_en' => $stepName],
                ['name_ar' => $mapping['ar'], 'order' => $mapping['order']]
            );

            $zone = strtoupper(trim($pData['zone'] ?? 'AM-PM'));
            $isMorning = str_contains($zone, 'AM');
            $isNight = str_contains($zone, 'PM') || $zone === 'WEEKLY';

            ProductRoutine::firstOrCreate([
                'product_id'      => $product->id,
                'routine_step_id' => $routineStep->id,
            ], [
                'morning'     => $isMorning,
                'night'       => $isNight,
                'layer_order' => (int) ($pData['order_within_step'] ?? 3),
                'is_core'     => ($pData['routine_tier'] ?? 'core') === 'core',
                'is_addon'    => ($pData['routine_tier'] ?? 'core') !== 'core',
            ]);

            $seededCount++;
        }

        // Second pass: resolve product_id inside complementary_routine_json now that all 256 products exist
        $skuToId = Product::pluck('id', 'sku')->toArray();
        foreach ($complementaryPass as $prodId => $compList) {
            if (empty($compList)) {
                continue;
            }
            $resolvedList = [];
            foreach ($compList as $cItem) {
                $cSku = $cItem['sku'] ?? '';
                $cItem['product_id'] = $skuToId[$cSku] ?? null;
                $resolvedList[] = $cItem;
            }
            Product::where('id', $prodId)->update([
                'complementary_routine_json' => $resolvedList,
            ]);
        }

        $this->command->info("Successfully seeded {$seededCount} official KCODE R479 products with full PDP data and WebP images.");
    }
}
