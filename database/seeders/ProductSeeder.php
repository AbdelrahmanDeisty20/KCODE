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
use App\Models\ProductMarketingDetail;
use App\Models\ProductRecommendationRule;
use App\Models\ProductAudit;
use App\Models\ProductRoutine;
use App\Models\RoutineStep;
use App\Models\RoutineGoal;
use App\Models\ProductGoal;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Schema;

class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. Clear existing relations & products
        Schema::disableForeignKeyConstraints();
        ProductConcern::truncate();
        ProductSkinType::truncate();
        ProductGoal::truncate();
        ProductMarketingDetail::truncate();
        ProductRecommendationRule::truncate();
        ProductAudit::truncate();
        ProductRoutine::truncate();
        Product::truncate();
        Schema::enableForeignKeyConstraints();

        $allGoals = RoutineGoal::all();

        $stepMapping = [
            'Cleansing Balm' => ['ar' => 'غسول بلسم زيتي', 'en' => 'Cleansing Balm', 'order' => 1],
            'Oil Cleanser' => ['ar' => 'غسول زيتي', 'en' => 'Oil Cleanser', 'order' => 2],
            'Cleanser' => ['ar' => 'غسول', 'en' => 'Cleanser', 'order' => 3],
            'Toner Pads' => ['ar' => 'تونر بادز', 'en' => 'Toner Pads', 'order' => 4],
            'Toner' => ['ar' => 'تونر', 'en' => 'Toner', 'order' => 5],
            'Mist' => ['ar' => 'بخاخ مرطب', 'en' => 'Mist', 'order' => 6],
            'Essence' => ['ar' => 'إيسنس', 'en' => 'Essence', 'order' => 7],
            'Ampoule' => ['ar' => 'أمبول', 'en' => 'Ampoule', 'order' => 8],
            'Serum' => ['ar' => 'سيروم', 'en' => 'Serum', 'order' => 9],
            'Booster Serum' => ['ar' => 'سيروم معزز', 'en' => 'Booster Serum', 'order' => 10],
            'Treatment' => ['ar' => 'علاج للبشرة', 'en' => 'Treatment', 'order' => 11],
            'Eye Serum' => ['ar' => 'سيروم للعين', 'en' => 'Eye Serum', 'order' => 12],
            'Eye Cream' => ['ar' => 'كريم للعين', 'en' => 'Eye Cream', 'order' => 13],
            'Eye Patch' => ['ar' => 'لصقات العين', 'en' => 'Eye Patch', 'order' => 14],
            'Moisturizer' => ['ar' => 'مرطب', 'en' => 'Moisturizer', 'order' => 15],
            'Balm' => ['ar' => 'بلسم مرطب', 'en' => 'Balm', 'order' => 16],
            'Spot Treatment' => ['ar' => 'علاج موضعي', 'en' => 'Spot Treatment', 'order' => 17],
            'Mask' => ['ar' => 'ماسك', 'en' => 'Mask', 'order' => 18],
            'Sunscreen' => ['ar' => 'واقي شمس', 'en' => 'Sunscreen', 'order' => 19],
            'Sunscreen Serum' => ['ar' => 'سيروم واقي شمس', 'en' => 'Sunscreen Serum', 'order' => 20],
            'Sunscreen Stick' => ['ar' => 'واقي شمس ستيك', 'en' => 'Sunscreen Stick', 'order' => 21],
            'Routine Set' => ['ar' => 'مجموعة روتين', 'en' => 'Routine Set', 'order' => 22],
            'Body Treatment Spray' => ['ar' => 'بخاخ علاج الجسم', 'en' => 'Body Treatment Spray', 'order' => 23],
        ];

        // 2. Open CSV file
        $filePath = base_path('exicel/KCODE_FINAL_10_10_POLISHED - Product_Master.csv');
        if (!file_exists($filePath)) {
            $this->command->error("CSV file not found at: {$filePath}");
            return;
        }

        $file = fopen($filePath, 'r');
        $headers = fgetcsv($file);

        // Standard translations for Category
        $categoryTranslations = [
            'Cleanser' => 'غسول',
            'Toner' => 'تونر',
            'Serum' => 'سيروم',
            'Moisturizer' => 'مرطب',
            'Sunscreen' => 'واقي شمس',
            'Micellar Water' => 'ماء ميسيلار',
            'Essence' => 'إيسنس للبشرة',
            'Lip Balm' => 'مرطب شفاه',
            'Eye Care' => 'كريم للعين',
            'Mist' => 'رذاذ للبشرة',
            'Mask' => 'ماسك للبشرة',
            'Set' => 'مجموعة العناية',
            'Treatment' => 'علاج للبشرة',
            'Body Care' => 'العناية بالجسم',
        ];

        // Concern lookup mapping to Concern DB model
        $concernNameMapping = [
            'Pores' => 'Enlarged Pores',
            'Oil Control' => 'Acne',
            'Smooth Texture' => 'Dullness',
            'Dark Spots' => 'Pigmentation',
            'Glow' => 'Dullness',
            'Brightening' => 'Pigmentation',
            'UV Protection' => 'Dryness',
            'Daily Protection' => 'Dryness',
            'Hydration' => 'Dryness',
            'Cleansing' => 'Acne',
            'Balance' => 'Sensitivity',
            'Fresh Skin' => 'Dullness',
            'Barrier Support' => 'Dryness',
            'Plump Skin' => 'Fine Lines',
            'Fine Lines' => 'Fine Lines',
            'Firming' => 'Fine Lines',
            'Anti-Aging' => 'Fine Lines',
            'Healthy Skin' => 'Dullness',
            'Redness' => 'Sensitivity',
            'Sensitivity' => 'Sensitivity',
            'Calm Skin' => 'Sensitivity',
            'Acne' => 'Acne',
            'Inflammation' => 'Acne',
            'Clear Skin' => 'Acne',
            'Elasticity' => 'Fine Lines',
        ];

        $rowCount = 0;
        while (($row = fgetcsv($file)) !== false) {
            $data = array_combine($headers, $row);
            $productId = trim($data['product_id']);


            $primaryBadgeEn = '';
            $primaryBadgeAr = '';
            $resultPromiseEn = '';
            $resultPromiseAr = '';
            $objectionAnswerEn = '';
            $objectionAnswerAr = '';
            $routineReasonEn = '';
            $routineReasonAr = '';
            $bundleCtaEn = '';
            $bundleCtaAr = '';
            $addToCartMicrocopyEn = '';
            $addToCartMicrocopyAr = '';

            $defaultPriorityScore = 0;
            $sameStepChoiceGroup = '';
            $amDefault = false;
            $pmDefault = false;
            $selectionRuleAr = '';
            $maxDefaultProductsPerStep = 1;
            $selectionWeightFormulaNote = '';
            $selectionPriorityTieBreaker = '';
            $exclusionRule = '';
            $conflictRuleStrictness = 'Mild';
            $pairingRule = '';
            $alternativeButtonRule = '';
            $addOnDisplayRule = '';
            $routineBuilderNote = '';
            $fallbackProductRule = '';
            $routineRole = '';

            $avoidPairingSameRoutine = '';
            $developerOutputRule = '';
            $showAlternativesButton = true;
            $removeIfCustomerHasIt = false;
            $sourceUrl = '';
            $dataConfidence = 'High';
            $needsManualCheck = false;

            $productStep = '';
            $layerOrder = 0;
            $routineTime = '';
            $isCoreRoutine = false;
            $isAddOnRoutine = false;

            // Correct shifts for KCODE-P121 and KCODE-P122
            if ($productId === 'KCODE-P121') {
                $brandEn = 'Aestura';
                $brandAr = 'أستورا';
                $nameEn = 'Aestura Atobarrier 365 Cream (80ml)';
                $nameAr = 'كريم إصلاح حاجز البشرة Aestura Atobarrier 365 (80مل)';
                $shortNameAr = 'كريم أستورا للحاجز';
                $shortNameEn = 'Aestura Barrier Cream';
                $categoryEn = 'Moisturizer';
                $subCategoryEn = 'Barrier Cream';
                $textureAr = 'كريمي مغذي';
                $textureEn = 'Cream';
                $whyKcodeAr = 'تم اختياره لأنه يعالج ضعف الحاجز ويرطب البشرة الجافة بعمق';
                $whyKcodeEn = 'Selected for restoring skin barrier and deep hydration';
                $howToUseAr = 'يوضع بالتساوي بعد السيروم كخطوة الترطيب';
                $howToUseEn = 'Apply after serum as the moisturizing step';
                $usageFrequencyAr = 'يومياً';
                $activeStrength = 'Low';
                $safetyNotesAr = 'لا توجد تعارضات';
                $safetyNotesEn = 'No conflicts';
                $arKeyBenefits = 'يرطب ويقوي الحاجز';
                $enKeyBenefits = 'Hydrates and repairs barrier';
                $arProductTitleSeo = 'كريم إستورا لتجديد حاجز البشرة';
                $enProductTitleSeo = 'Aestura Atobarrier 365 Cream';
                $enShortHook = 'Restores barrier fast';
                $seoMetaTitleAr = 'كريم إستورا أتوبرير 365 لإصلاح حاجز البشرة';
                $metaDescriptionEn = 'Aestura Atobarrier 365 Cream repairs damaged skin barrier and provides long-lasting hydration.';
                $metaDescriptionAr = 'كريم أستورا أتوبرير 365 لإصلاح حاجز البشرة المتضرر وترطيب البشرة الجافة.';
                $primaryKeywordEn = 'aestura barrier cream';
                $primaryKeywordAr = 'كريم استورا للحاجز';
                $secondaryKeywordsEn = 'aestura cream, barrier repair cream';
                $secondaryKeywordsAr = 'كريم سيراميد كوري، كريم استورا';
                $finalUrlSlug = 'aestura-atobarrier-cream';
                $imageAltEn = 'Aestura Atobarrier 365 Cream';
                $imageAltAr = 'كريم إستورا أتوبرير 365';
                $ogTitleAr = 'كريم إستورا أتوبرير 365 لإصلاح حاجز البشرة';
                $ogDescriptionEn = 'Aestura Atobarrier 365 Cream repairs damaged skin barrier.';
                $ogDescriptionAr = 'كريم إستورا أتوبرير 365 لإصلاح حاجز البشرة وترطيبه.';
                $pdpHeadlineEn = 'Restores Skin Barrier and Hydration';
                $aboveFoldHookEn = 'Moisture and repair cream';
                $keywords = 'aestura, atobarrier, barrier cream, ceramides';
                $skinTypeFit = 'Dry, Sensitive';
                $concernsList = ['Barrier Support', 'Hydration', 'Calm Skin'];
            } elseif ($productId === 'KCODE-P122') {
                $brandEn = 'Illiyoon';
                $brandAr = 'إليون';
                $nameEn = 'Illiyoon Ceramide Ato Concentrate Cream (200ml)';
                $nameAr = 'كريم سيراميد Illiyoon لإصلاح الحاجز (200مل)';
                $shortNameAr = 'كريم إليون سيراميد';
                $shortNameEn = 'Illiyoon Ceramide Cream';
                $categoryEn = 'Moisturizer';
                $subCategoryEn = 'Barrier Cream';
                $textureAr = 'كريمي غني';
                $textureEn = 'Rich cream';
                $whyKcodeAr = 'خيار اقتصادي ممتاز ذو فاعلية عالية لدعم حاجز البشرة';
                $whyKcodeEn = 'Excellent affordable option for barrier support';
                $howToUseAr = 'يوضع بعد السيروم كخطوة الترطيب';
                $howToUseEn = 'Apply after serum as the moisturizing step';
                $usageFrequencyAr = 'يومياً';
                $activeStrength = 'Low';
                $safetyNotesAr = 'لا توجد تعارضات';
                $safetyNotesEn = 'No conflicts';
                $arKeyBenefits = 'يرطب ويدعم الحاجز';
                $enKeyBenefits = 'Hydrates and supports barrier';
                $arProductTitleSeo = 'كريم إليون سيراميد لترطيب البشرة';
                $enProductTitleSeo = 'Illiyoon Ceramide Ato Concentrate Cream';
                $enShortHook = 'Rich cream';
                $seoMetaTitleAr = 'كريم إليون سيراميد أتو لترطيب البشرة الجافة';
                $metaDescriptionEn = 'Illiyoon Ceramide Ato Concentrate Cream offers deep hydration and skin barrier support.';
                $metaDescriptionAr = 'كريم إليون سيراميد أتو بمفعول مركز لترطيب وتلطيف البشرة الجافة والحساسة.';
                $primaryKeywordEn = 'illiyoon ceramide cream';
                $primaryKeywordAr = 'كريم اليون سيراميد';
                $secondaryKeywordsEn = 'ceramide cream, barrier cream';
                $secondaryKeywordsAr = 'كريم مرطب كوري، كريم اليون';
                $finalUrlSlug = 'illiyoon-ceramide-cream';
                $imageAltEn = 'Illiyoon Ceramide Ato Concentrate Cream';
                $imageAltAr = 'كريم إليون سيراميد أتو';
                $ogTitleAr = 'كريم إليون سيراميد لترطيب البشرة ودعم حاجزها';
                $ogDescriptionEn = 'Illiyoon Ceramide Ato Concentrate Cream offers deep hydration.';
                $ogDescriptionAr = 'كريم إليون سيراميد لترطيب البشرة ودعم حاجزها الطبيعي.';
                $pdpHeadlineEn = 'Deep Hydration and Barrier Support';
                $aboveFoldHookEn = 'Affordable barrier cream';
                $keywords = 'illiyoon, ceramide, barrier cream, dry skin';
                $skinTypeFit = 'Dry, Sensitive';
                $concernsList = ['Barrier Support', 'Hydration', 'Calm Skin'];
            } else {
                // Normal row
                $brandEn = trim($data['brand_en']);
                $brandAr = trim($data['brand_ar']);
                $nameEn = trim($data['display_en_name']);
                $nameAr = trim($data['display_ar_name']);
                $shortNameAr = trim($data['short_ar_name']);
                $shortNameEn = $nameEn; // Fallback
                $categoryEn = trim($data['category']);
                $subCategoryEn = trim($data['sub_category']);
                
                $textureAr = trim($data['texture_ar']);
                $textureEn = trim($data['texture_en']);
                $whyKcodeAr = trim($data['why_kcode_ar']);
                $whyKcodeEn = trim($data['why_kcode_en']);
                $howToUseAr = trim($data['how_to_use_ar']);
                $howToUseEn = trim($data['how_to_use_en']);
                $usageFrequencyAr = trim($data['usage_frequency_ar']);
                
                // Parse active strength
                $activeStrengthRaw = trim($data['active_strength_level Low / Medium / High']);
                if (stripos($activeStrengthRaw, 'Low') !== false) {
                    $activeStrength = 'Low';
                } elseif (stripos($activeStrengthRaw, 'Medium') !== false) {
                    $activeStrength = 'Medium';
                } elseif (stripos($activeStrengthRaw, 'High') !== false) {
                    $activeStrength = 'High';
                } else {
                    $activeStrength = 'Low'; // Fallback default
                }

                $safetyNotesAr = trim($data['safety_notes_ar']);
                $safetyNotesEn = trim($data['safety_notes_en']);
                $arKeyBenefits = trim($data['ar_key_benefits']);
                $enKeyBenefits = trim($data['en_key_benefits']);
                
                $arProductTitleSeo = trim($data['ar_product_title_seo']);
                $enProductTitleSeo = trim($data['en_product_title_seo']);
                $enShortHook = trim($data['en_short_hook']);
                $seoMetaTitleAr = trim($data['seo_meta_title_ar']);
                $metaDescriptionEn = trim($data['meta_description_en']);
                $metaDescriptionAr = trim($data['meta_description_ar']);
                $primaryKeywordEn = trim($data['primary_keyword_en']);
                $primaryKeywordAr = trim($data['primary_keyword_ar']);
                $secondaryKeywordsEn = trim($data['secondary_keywords_en']);
                $secondaryKeywordsAr = trim($data['secondary_keywords_ar']);
                $finalUrlSlug = trim($data['final_url_slug']);
                $imageAltEn = trim($data['image_alt_en']);
                $imageAltAr = trim($data['image_alt_ar']);
                $ogTitleAr = trim($data['og_title_ar']);
                $ogDescriptionEn = trim($data['og_description_en']);
                $ogDescriptionAr = trim($data['og_description_ar']);
                $pdpHeadlineEn = trim($data['pdp_headline_en']);
                $aboveFoldHookEn = trim($data['above_fold_hook_en']);
                $keywords = trim($data['Keywords']);
                $skinTypeFit = trim($data['skin_type_fit']);


                // Marketing
                $primaryBadgeEn = trim($data['primary_badge_en'] ?? '');
                $primaryBadgeAr = trim($data['primary_badge_ar'] ?? '');
                $resultPromiseEn = trim($data['result_promise_en'] ?? '');
                $resultPromiseAr = trim($data['result_promise_ar'] ?? '');
                $objectionAnswerEn = trim($data['objection_answer_en'] ?? '');
                $objectionAnswerAr = trim($data['objection_answer_ar'] ?? '');
                $routineReasonEn = trim($data['routine_reason_en'] ?? '');
                $routineReasonAr = trim($data['routine_reason_ar'] ?? '');
                $bundleCtaEn = trim($data['bundle_cta_en'] ?? '');
                $bundleCtaAr = trim($data['bundle_cta_ar'] ?? '');
                $addToCartMicrocopyEn = trim($data['add_to_cart_microcopy_en'] ?? '');
                $addToCartMicrocopyAr = trim($data['add_to_cart_microcopy_ar'] ?? '');

                // Rules
                $defaultPriorityScore = intval(trim($data['default_priority_score'] ?? '0'));
                $sameStepChoiceGroup = trim($data['same_step_choice_group'] ?? '');
                $amDefault = (trim($data['am_default'] ?? '') === 'Yes' || trim($data['am_default'] ?? '') === '1');
                $pmDefault = (trim($data['pm_default'] ?? '') === 'Yes' || trim($data['pm_default'] ?? '') === '1');
                $selectionRuleAr = trim($data['selection_rule_ar'] ?? '');
                $maxDefaultProductsPerStep = intval(trim($data['max_default_products_per_step'] ?? '1'));
                $selectionWeightFormulaNote = trim($data['selection_weight_formula_note'] ?? '');
                $selectionPriorityTieBreaker = trim($data['selection_priority_tie_breaker'] ?? '');
                $exclusionRule = trim($data['exclusion_rule'] ?? '');
                $conflictRuleStrictness = trim($data['conflict_rule_strictness'] ?? 'Mild');
                $pairingRule = trim($data['pairing_rule'] ?? '');
                $alternativeButtonRule = trim($data['alternative_button_rule'] ?? '');
                $addOnDisplayRule = trim($data['add_on_display_rule'] ?? '');
                $routineBuilderNote = trim($data['routine_builder_note'] ?? '');
                $fallbackProductRule = trim($data['fallback_product_rule'] ?? '');
                $routineRole = trim($data['Routine Role'] ?? '');

                // Audits
                $avoidPairingSameRoutine = trim($data['avoid_pairing_same_routine'] ?? '');
                $developerOutputRule = trim($data['developer_output_rule'] ?? '');
                $showAlternativesButton = (trim($data['show_alternatives_button'] ?? '') === 'No') ? false : true;
                $removeIfCustomerHasIt = (trim($data['remove_if_customer_has_it'] ?? '') === 'Yes' || trim($data['remove_if_customer_has_it'] ?? '') === '1');
                $sourceUrl = trim($data['source_url'] ?? '');
                $dataConfidence = trim($data['data_confidence'] ?? 'High');
                $needsManualCheck = (trim($data['needs_manual_check'] ?? '') === 'Yes' || trim($data['needs_manual_check'] ?? '') === '1');

                // Routine seeding values
                $productStep = trim($data['product_step'] ?? '');
                $layerOrder = intval(trim($data['layer_order'] ?? '0'));
                $routineTime = trim($data['routine_time'] ?? '');
                $isCoreRoutine = (trim($data['is_core_routine_step'] ?? '') === 'Yes' || trim($data['is_core_routine_step'] ?? '') === '1');
                $isAddOnRoutine = (trim($data['is_add_on'] ?? '') === 'Yes' || trim($data['is_add_on'] ?? '') === '1');

                $concernsList = [];
                foreach (['primary_concern', 'secondary_concern', 'tertiary_concern'] as $f) {
                    $c = trim($data[$f] ?? '');
                    if ($c && $c !== '-') {
                        $concernsList[] = $c;
                    }
                }
            }

            // 3. Resolve Category
            $category = null;
            if ($categoryEn) {
                $categoryAr = $categoryTranslations[$categoryEn] ?? $categoryEn;
                $category = Category::updateOrCreate(
                    ['name_en' => $categoryEn],
                    [
                        'name_ar' => $categoryAr,
                        'image' => 'https://images.unsplash.com/photo-1608248597279-f99d160bfcbc?auto=format&fit=crop&q=80&w=400'
                    ]
                );
            }

            // 4. Resolve SubCategory
            $subCategory = null;
            if ($subCategoryEn && $category) {
                // Map of high quality skincare images for subcategories
                $subCategoryImageUrls = [
                    'Cleansing Balm' => 'https://images.unsplash.com/photo-1556228720-195a672e8a03?auto=format&fit=crop&q=80&w=400',
                    'Oil Cleanser' => 'https://images.unsplash.com/photo-1556228720-195a672e8a03?auto=format&fit=crop&q=80&w=400',
                    'Cleanser' => 'https://images.unsplash.com/photo-1556228720-195a672e8a03?auto=format&fit=crop&q=80&w=400',
                    'Toner Pads' => 'https://images.unsplash.com/photo-1608248597279-f99d160bfcbc?auto=format&fit=crop&q=80&w=400',
                    'Toner' => 'https://images.unsplash.com/photo-1608248597279-f99d160bfcbc?auto=format&fit=crop&q=80&w=400',
                    'Mist' => 'https://images.unsplash.com/photo-1608248597279-f99d160bfcbc?auto=format&fit=crop&q=80&w=400',
                    'Essence' => 'https://images.unsplash.com/photo-1620916566398-39f1143ab7be?auto=format&fit=crop&q=80&w=400',
                    'Ampoule' => 'https://images.unsplash.com/photo-1620916566398-39f1143ab7be?auto=format&fit=crop&q=80&w=400',
                    'Serum' => 'https://images.unsplash.com/photo-1620916566398-39f1143ab7be?auto=format&fit=crop&q=80&w=400',
                    'Booster Serum' => 'https://images.unsplash.com/photo-1620916566398-39f1143ab7be?auto=format&fit=crop&q=80&w=400',
                    'Treatment' => 'https://images.unsplash.com/photo-1556228578-0d85b1a4d571?auto=format&fit=crop&q=80&w=400',
                    'Eye Serum' => 'https://images.unsplash.com/photo-1608248597279-f99d160bfcbc?auto=format&fit=crop&q=80&w=400',
                    'Eye Cream' => 'https://images.unsplash.com/photo-1608248597279-f99d160bfcbc?auto=format&fit=crop&q=80&w=400',
                    'Eye Patch' => 'https://images.unsplash.com/photo-1608248597279-f99d160bfcbc?auto=format&fit=crop&q=80&w=400',
                    'Moisturizer' => 'https://images.unsplash.com/photo-1601049676099-e7ed07d825b0?auto=format&fit=crop&q=80&w=400',
                    'Balm' => 'https://images.unsplash.com/photo-1617897903246-719242758050?auto=format&fit=crop&q=80&w=400',
                    'Spot Treatment' => 'https://images.unsplash.com/photo-1556228720-195a672e8a03?auto=format&fit=crop&q=80&w=400',
                    'Mask' => 'https://images.unsplash.com/photo-1596755094514-f87e34085b2c?auto=format&fit=crop&q=80&w=400',
                    'Sunscreen' => 'https://images.unsplash.com/photo-1598440947619-2c35fc9aa908?auto=format&fit=crop&q=80&w=400',
                    'Sunscreen Serum' => 'https://images.unsplash.com/photo-1598440947619-2c35fc9aa908?auto=format&fit=crop&q=80&w=400',
                    'Sunscreen Stick' => 'https://images.unsplash.com/photo-1598440947619-2c35fc9aa908?auto=format&fit=crop&q=80&w=400',
                    'Routine Set' => 'https://images.unsplash.com/photo-1612817288484-6f916006741a?auto=format&fit=crop&q=80&w=400',
                    'Body Treatment Spray' => 'https://images.unsplash.com/photo-1556228578-0d85b1a4d571?auto=format&fit=crop&q=80&w=400',
                ];

                $subCatImageUrl = $subCategoryImageUrls[$subCategoryEn] ?? 'https://images.unsplash.com/photo-1608248597279-f99d160bfcbc?auto=format&fit=crop&q=80&w=400';
                $subCatFilename = \Illuminate\Support\Str::slug($subCategoryEn) . '.jpg';
                
                ImageDownloader::downloadAndSave($subCatImageUrl, 'sub_categories', $subCatFilename);

                $subCategory = SubCategory::updateOrCreate(
                    ['name_en' => $subCategoryEn, 'category_id' => $category->id],
                    [
                        'name_ar' => $subCategoryEn,
                        'image' => $subCatFilename
                    ]
                );
            }

            // 5. Resolve Brand
            $brand = null;
            if ($brandEn) {
                $brand = Brand::updateOrCreate(
                    ['name_en' => $brandEn],
                    ['name_ar' => $brandAr ?: $brandEn]
                );
            }

            // 6. Create Product
            // Create nice random prices & stock to keep PDP functional
            $price = rand(150, 450) / 10;
            $stock = rand(30, 100);
            $isBestSeller = (rand(1, 100) > 80);
            $salesCount = $isBestSeller ? rand(100, 500) : rand(0, 99);
            
            // Build the image filename using slug
            $imageFilename = $data['sku_slug'] . '.jpg';

            // Define Unsplash Skincare image URLs mapping by Category
            $categoryImageUrls = [
                'Cleanser' => 'https://images.unsplash.com/photo-1556228720-195a672e8a03?auto=format&fit=crop&q=80&w=400',
                'Toner' => 'https://images.unsplash.com/photo-1608248597279-f99d160bfcbc?auto=format&fit=crop&q=80&w=400',
                'Serum' => 'https://images.unsplash.com/photo-1620916566398-39f1143ab7be?auto=format&fit=crop&q=80&w=400',
                'Ampoule' => 'https://images.unsplash.com/photo-1620916566398-39f1143ab7be?auto=format&fit=crop&q=80&w=400',
                'Moisturizer' => 'https://images.unsplash.com/photo-1601049676099-e7ed07d825b0?auto=format&fit=crop&q=80&w=400',
                'Sunscreen' => 'https://images.unsplash.com/photo-1598440947619-2c35fc9aa908?auto=format&fit=crop&q=80&w=400',
                'Face Mask' => 'https://images.unsplash.com/photo-1596755094514-f87e34085b2c?auto=format&fit=crop&q=80&w=400',
                'Eye Cream' => 'https://images.unsplash.com/photo-1608248597279-f99d160bfcbc?auto=format&fit=crop&q=80&w=400',
                'Exfoliator' => 'https://images.unsplash.com/photo-1556228578-0d85b1a4d571?auto=format&fit=crop&q=80&w=400',
                'Lip Balm' => 'https://images.unsplash.com/photo-1617897903246-719242758050?auto=format&fit=crop&q=80&w=400',
                'Face Oil' => 'https://images.unsplash.com/photo-1608571423902-eed4a5ad8108?auto=format&fit=crop&q=80&w=400',
                'Makeup Remover' => 'https://images.unsplash.com/photo-1556228578-0d85b1a4d571?auto=format&fit=crop&q=80&w=400',
                'Essence' => 'https://images.unsplash.com/photo-1620916566398-39f1143ab7be?auto=format&fit=crop&q=80&w=400',
                'Night Cream' => 'https://images.unsplash.com/photo-1601049676099-e7ed07d825b0?auto=format&fit=crop&q=80&w=400',
                'Micellar Water' => 'https://images.unsplash.com/photo-1617897903246-719242758050?auto=format&fit=crop&q=80&w=400',
                'Acne Treatment' => 'https://images.unsplash.com/photo-1556228720-195a672e8a03?auto=format&fit=crop&q=80&w=400',
                'Body Lotion' => 'https://images.unsplash.com/photo-1556228578-0d85b1a4d571?auto=format&fit=crop&q=80&w=400',
            ];

            $downloadUrl = $categoryImageUrls[$categoryEn] ?? 'https://images.unsplash.com/photo-1608248597279-f99d160bfcbc?auto=format&fit=crop&q=80&w=400';
            ImageDownloader::downloadAndSave($downloadUrl, 'products', $imageFilename);

            $product = Product::updateOrCreate(
                ['sku' => $productId],
                [
                    'name_en' => $nameEn,
                    'name_ar' => $nameAr,
                    'description_en' => trim($data['en_long_description'] ?? ''),
                    'description_ar' => trim($data['ar_long_description'] ?? ''),
                    'category_id' => $category ? $category->id : null,
                    'sub_category_id' => $subCategory ? $subCategory->id : null,
                    'brand_id' => $brand ? $brand->id : null,
                    'price' => $price,
                    'stock' => $stock,
                    'is_best_seller' => $isBestSeller,
                    'sales_count' => $salesCount,
                    'short_name_ar' => $shortNameAr,
                    'short_name_en' => $shortNameEn,
                    'image' => $imageFilename,
                    'ingredients_en' => '-',
                    'ingredients_ar' => '-',
                    'how_to_use_en' => $howToUseEn,
                    'how_to_use_ar' => $howToUseAr,
                    'status' => 'active',
                    'texture_ar' => $textureAr,
                    'texture_en' => $textureEn,
                    'why_kcode_ar' => $whyKcodeAr,
                    'why_kcode_en' => $whyKcodeEn,
                    'usage_frequency_ar' => $usageFrequencyAr,
                    'active_strength_level' => $activeStrength,
                    'safety_notes_ar' => $safetyNotesAr,
                    'safety_notes_en' => $safetyNotesEn,
                    'ar_key_benefits' => $arKeyBenefits,
                    'en_key_benefits' => $enKeyBenefits,
                    'ar_product_title_seo' => $arProductTitleSeo,
                    'en_product_title_seo' => $enProductTitleSeo,
                    'en_short_hook' => $enShortHook,
                    'seo_meta_title_ar' => $seoMetaTitleAr,
                    'meta_description_en' => $metaDescriptionEn,
                    'meta_description_ar' => $metaDescriptionAr,
                    'primary_keyword_en' => $primaryKeywordEn,
                    'primary_keyword_ar' => $primaryKeywordAr,
                    'secondary_keywords_en' => $secondaryKeywordsEn,
                    'secondary_keywords_ar' => $secondaryKeywordsAr,
                    'final_url_slug' => $finalUrlSlug,
                    'image_alt_en' => $imageAltEn,
                    'image_alt_ar' => $imageAltAr,
                    'og_title_ar' => $ogTitleAr,
                    'og_description_en' => $ogDescriptionEn,
                    'og_description_ar' => $ogDescriptionAr,
                    'pdp_headline_en' => $pdpHeadlineEn,
                    'above_fold_hook_en' => $aboveFoldHookEn,
                    'keywords' => $keywords,

                ]
            );

            // 7. Seed concerns
            $priority = 1;
            foreach ($concernsList as $concernName) {
                // Find standard concern name from mapping
                $mappedName = $concernNameMapping[$concernName] ?? null;
                if ($mappedName) {
                    $concern = Concern::where('name_en', $mappedName)->first();
                    if ($concern) {
                        ProductConcern::updateOrCreate([
                            'product_id' => $product->id,
                            'concern_id' => $concern->id,
                        ], [
                            'priority' => $priority++,
                        ]);
                    }
                }
            }

            // 8. Seed skin type fits
            if ($skinTypeFit) {
                $isAll = (stripos($skinTypeFit, 'All') !== false);
                $typesToLink = [];
                if ($isAll) {
                    $typesToLink = ['Oily', 'Dry', 'Combination', 'Sensitive'];
                } else {
                    if (stripos($skinTypeFit, 'Oily') !== false) {
                        $typesToLink[] = 'Oily';
                    }
                    if (stripos($skinTypeFit, 'Dry') !== false) {
                        $typesToLink[] = 'Dry';
                    }
                    if (stripos($skinTypeFit, 'Combination') !== false) {
                        $typesToLink[] = 'Combination';
                    }
                    if (stripos($skinTypeFit, 'Sensitive') !== false) {
                        $typesToLink[] = 'Sensitive';
                    }
                }

                foreach ($typesToLink as $typeName) {
                    $st = SkinType::where('name_en', $typeName)->first();
                    if ($st) {
                        ProductSkinType::updateOrCreate([
                            'product_id' => $product->id,
                            'skin_type_id' => $st->id,
                        ]);
                    }
                }
            }

            // 8.5 Seed random goals (1 or 2 per product)
            if ($allGoals->isNotEmpty()) {
                $randomGoals = $allGoals->random(rand(1, 2));
                $goalPriority = 1;
                foreach ($randomGoals as $goal) {
                    ProductGoal::updateOrCreate([
                        'product_id' => $product->id,
                        'goal_id' => $goal->id,
                    ], [
                        'priority' => $goalPriority++,
                    ]);
                }
            }

            // 9. Seed Marketing details
            ProductMarketingDetail::updateOrCreate(
                ['product_id' => $product->id],
                [
                    'primary_badge_en' => $primaryBadgeEn,
                    'primary_badge_ar' => $primaryBadgeAr,
                    'result_promise_en' => $resultPromiseEn,
                    'result_promise_ar' => $resultPromiseAr,
                    'objection_answer_en' => $objectionAnswerEn,
                    'objection_answer_ar' => $objectionAnswerAr,
                    'routine_reason_en' => $routineReasonEn,
                    'routine_reason_ar' => $routineReasonAr,
                    'bundle_cta_en' => $bundleCtaEn,
                    'bundle_cta_ar' => $bundleCtaAr,
                    'add_to_cart_microcopy_en' => $addToCartMicrocopyEn,
                    'add_to_cart_microcopy_ar' => $addToCartMicrocopyAr,
                ]
            );

            // 10. Seed Recommendation rules
            ProductRecommendationRule::updateOrCreate(
                ['product_id' => $product->id],
                [
                    'default_priority_score' => $defaultPriorityScore,
                    'same_step_choice_group' => $sameStepChoiceGroup,
                    'am_default' => $amDefault,
                    'pm_default' => $pmDefault,
                    'selection_rule_ar' => $selectionRuleAr,
                    'max_default_products_per_step' => $maxDefaultProductsPerStep,
                    'selection_weight_formula_note' => $selectionWeightFormulaNote,
                    'selection_priority_tie_breaker' => $selectionPriorityTieBreaker,
                    'exclusion_rule' => $exclusionRule,
                    'conflict_rule_strictness' => $conflictRuleStrictness,
                    'pairing_rule' => $pairingRule,
                    'alternative_button_rule' => $alternativeButtonRule,
                    'add_on_display_rule' => $addOnDisplayRule,
                    'routine_builder_note' => $routineBuilderNote,
                    'fallback_product_rule' => $fallbackProductRule,
                    'routine_role' => $routineRole,
                ]
            );

            // 11. Seed Audit details
            ProductAudit::updateOrCreate(
                ['product_id' => $product->id],
                [
                    'avoid_pairing_same_routine' => $avoidPairingSameRoutine,
                    'developer_output_rule' => $developerOutputRule,
                    'show_alternatives_button' => $showAlternativesButton,
                    'remove_if_customer_has_it' => $removeIfCustomerHasIt,
                    'source_url' => $sourceUrl,
                    'data_confidence' => $dataConfidence,
                    'needs_manual_check' => $needsManualCheck,
                ]
            );

            // 12. Seed Product Routine association
            if ($productStep) {
                // Find or create RoutineStep matching the CSV step name
                $mapping = $stepMapping[$productStep] ?? ['ar' => $productStep, 'en' => $productStep, 'order' => 99];
                $routineStep = RoutineStep::updateOrCreate(
                    ['name_en' => $productStep],
                    [
                        'name_ar' => $mapping['ar'],
                        'order'   => $mapping['order']
                    ]
                );

                $morning = (stripos($routineTime, 'AM') !== false);
                $night = (stripos($routineTime, 'PM') !== false);

                ProductRoutine::updateOrCreate([
                    'product_id' => $product->id,
                    'routine_step_id' => $routineStep->id,
                ], [
                    'morning' => $morning,
                    'night' => $night,
                    'layer_order' => $layerOrder,
                    'is_core' => $isCoreRoutine,
                    'is_addon' => $isAddOnRoutine,
                ]);
            }

            $rowCount++;
        }
        fclose($file);

        $this->command->info("Successfully seeded {$rowCount} products from CSV.");
    }
}
