<?php

namespace Database\Seeders\Support;

use App\Models\Product;
use Illuminate\Support\Facades\File;

/**
 * Shared helper for generating clean English product & PDP fields from R479 metadata
 * without ever overwriting existing valid English entries or touching Arabic fields.
 */
class R479EnglishBuilder
{
    private const CATEGORY_EN_MAP = [
        'غسول' => 'Cleanser',
        'تونر' => 'Toner',
        'إسنس' => 'Essence',
        'سيروم' => 'Serum',
        'سيروم وأمبول' => 'Serum',
        'مرطب' => 'Moisturizer',
        'واقي شمس' => 'Sunscreen',
        'ماسك' => 'Mask',
        'ماسكات' => 'Mask',
        'قناع' => 'Mask',
        'مقشر' => 'Exfoliator',
        'مقشّر' => 'Exfoliator',
        'عناية بالعين' => 'Eye Care',
        'العناية بالعين' => 'Eye Care',
        'عناية بالجسم' => 'Body Care',
        'العناية بالجسم' => 'Body Care',
        'منتج مكمل' => 'Complementary Product',
    ];

    private const SUBTITLE_EN_MAP = [
        'اختياري' => 'Optional',
        'هذا المنتج' => 'This Product',
        'بحسب الحاجة' => 'As needed',
        'حسب الحاجة' => 'As needed',
        'صباحاً' => 'Morning',
        'صباحًا' => 'Morning',
        'مساءً' => 'Evening',
        'صباحاً ومساءً' => 'Morning & Evening',
        'صباحًا ومساءً' => 'Morning & Evening',
        'صباحًا، بعد إزالة الماسك' => 'Morning, after mask removal',
        'صباحاً، بعد إزالة الماسك' => 'Morning, after mask removal',
        'بعد إزالة الماسك' => 'After mask removal',
    ];

    private const STEP_TITLE_EN_MAP = [
        1 => 'Cleanser',
        2 => 'Toner or Essence',
        3 => 'Serum',
        4 => 'Moisturizer',
        5 => 'Sunscreen',
    ];

    public static function containsArabic(?string $text): bool
    {
        return !empty($text) && preg_match('/\p{Arabic}/u', $text) === 1;
    }

    public static function needsEnglishText(?string $value): bool
    {
        return $value === null || trim($value) === '' || self::containsArabic($value);
    }

    public static function needsEnglishBenefits(mixed $value): bool
    {
        if (is_string($value)) {
            $decoded = json_decode($value, true);
            $value = (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) ? $decoded : [];
        }

        if (!is_array($value) || empty($value)) {
            return true;
        }

        foreach ($value as $item) {
            if (!is_array($item)) {
                continue;
            }
            $title = trim((string) ($item['title_en'] ?? ($item['title'] ?? '')));
            $text = trim((string) ($item['text_en'] ?? ($item['text'] ?? '')));
            if ($title !== '' && $text !== '' && !self::containsArabic($title) && !self::containsArabic($text)) {
                return false;
            }
        }

        return true;
    }

    /**
     * Load snapshot and kcode_products metadata indexed by SKU.
     *
     * @return array{snapshot: array<string, array<string, mixed>>, kcode: array<string, array<string, mixed>>}
     */
    public static function loadMetadataMaps(): array
    {
        static $cached = null;
        if ($cached !== null) {
            return $cached;
        }

        $snapshotBySku = [];
        $snapPath = database_path('seeders/data/r479_master_snapshot.json');
        if (File::exists($snapPath)) {
            $snap = json_decode(File::get($snapPath), true) ?? [];
            foreach ($snap as $row) {
                if (!empty($row['SKU'])) {
                    $snapshotBySku[(string) $row['SKU']] = $row;
                }
            }
        }

        $kcodeBySku = [];
        foreach ([base_path('exicel/kcode_products.json'), base_path('kcode_products.json')] as $path) {
            if (File::exists($path)) {
                $content = json_decode(File::get($path), true) ?? [];
                foreach ($content['products'] ?? [] as $p) {
                    if (!empty($p['sku'])) {
                        $kcodeBySku[(string) $p['sku']] = $p;
                    }
                }
                break;
            }
        }

        return $cached = [
            'snapshot' => $snapshotBySku,
            'kcode' => $kcodeBySku,
        ];
    }

    /**
     * Normalize metadata context from product model + optional JSON rows.
     *
     * @return array<string, mixed>
     */
    public static function buildContext(
        Product $product,
        ?array $snap = null,
        ?array $kp = null
    ): array {
        $brandName = trim((string) ($snap['Brand'] ?? ($kp['brand'] ?? ($product->brand?->name_en ?? ''))));
        $rawName = trim((string) ($product->name_en ?: ($snap['Product'] ?? ($kp['product'] ?? 'Skincare Product'))));
        $size = trim((string) ($product->size ?: ($snap['Size'] ?? ($kp['size'] ?? ''))));

        $category = trim((string) ($snap['Category'] ?? ($kp['category'] ?? ($product->category?->name_en ?? 'Serum'))));
        $subCategory = trim((string) ($snap['Sub Category'] ?? ($kp['sub_category'] ?? ($product->subCategory?->name_en ?: $category))));

        if (strtolower($category) === 'body') {
            $category = 'Body Care';
        } elseif (strtolower($category) === 'lip') {
            $category = 'Mask';
        }

        $texture = self::resolveCleanEnglish(
            $snap['Texture'] ?? null,
            $kp['texture'] ?? null,
            !self::needsEnglishText($product->texture_en) ? $product->texture_en : null
        ) ?? 'Lightweight';

        $actives = trim((string) ($snap['Customer Actives'] ?? ($kp['customer_actives'] ?? ($snap['Key Actives'] ?? ($kp['key_actives'] ?? '')))));
        if (self::containsArabic($actives)) {
            $actives = '';
        }

        $frequency = trim((string) ($snap['Frequency'] ?? ($kp['frequency'] ?? '')));
        if (self::containsArabic($frequency)) {
            $frequency = '';
        }

        $routineRole = trim((string) ($snap['Routine Role'] ?? ($kp['routine_role'] ?? '')));
        $zone = strtoupper(trim((string) ($snap['Zone'] ?? ($kp['zone'] ?? 'AM-PM'))));
        $fragrance = trim((string) ($snap['Fragrance'] ?? ($kp['fragrance'] ?? 'None')));
        $sensitivityCode = strtolower(trim((string) ($snap['Sensitivity Class Code'] ?? ($kp['sensitivity_class'] ?? ''))));
        $sensitivityLabelAr = trim((string) (
            $snap['Sensitive Classification']
            ?? ($kp['sensitive_status_ar'] ?? ($kp['sensitivity_label'] ?? ($product->usage_instructions_json['verdict']['sensitive_status'] ?? '')))
        ));

        $sensitiveEligible = $sensitivityCode !== ''
            ? $sensitivityCode === 'suitable'
            : (bool) $product->sensitive_eligible;

        $stepNumber = (int) ($product->routine_step_number ?: self::inferRoutineStepNumber($category, $subCategory));

        return [
            'sku' => $product->sku,
            'brand' => $brandName,
            'name' => $rawName,
            'short_name' => trim((string) ($product->short_name_en ?: ($snap['Product'] ?? ($kp['product'] ?? $rawName)))),
            'size' => $size,
            'category' => $category,
            'sub_category' => $subCategory,
            'texture' => $texture,
            'actives' => $actives,
            'routine_role' => $routineRole,
            'frequency' => $frequency,
            'zone' => $zone,
            'fragrance' => $fragrance,
            'sensitivity_code' => $sensitivityCode,
            'sensitivity_label_ar' => $sensitivityLabelAr,
            'sensitive_eligible' => $sensitiveEligible,
            'step_number' => $stepNumber,
            'hydrating_score' => (int) ($snap['Hydration'] ?? ($kp['goals']['hydration'] ?? 0)),
            'barrier_score' => (int) ($snap['Barrier'] ?? ($kp['goals']['barrier'] ?? 0)),
            'soothing_score' => (int) ($snap['Redness PIE'] ?? ($kp['goals']['redness_pie'] ?? 0)),
            'brightening_score' => (int) ($snap['Pigmentation PIH'] ?? ($kp['goals']['pigmentation_pih'] ?? 0)),
            'acne_score' => (int) ($snap['Acne'] ?? ($kp['goals']['acne'] ?? 0)),
            'lines_score' => (int) ($snap['Lines'] ?? ($kp['goals']['lines'] ?? 0)),
        ];
    }

    /**
     * Build complete clean English scalar columns from normalized context.
     *
     * @return array<string, mixed>
     */
    public static function buildScalarEnglishFields(array $ctx): array
    {
        $brandPrefix = $ctx['brand'] !== '' ? "{$ctx['brand']} " : '';
        $nameWithSize = $ctx['name'] . ($ctx['size'] !== '' && !str_contains($ctx['name'], $ctx['size']) ? " ({$ctx['size']})" : '');
        $subCat = $ctx['sub_category'] ?: $ctx['category'];
        $actives = $ctx['actives'];

        $descriptionEn = "Authentic {$brandPrefix}{$nameWithSize} — {$ctx['texture']} {$subCat}"
            . ($actives !== '' ? " formulated with {$actives}." : ' formulated for targeted skincare support.');

        $briefInsightEn = $actives !== ''
            ? "{$subCat} formulated with {$actives}"
            : "Authentic {$brandPrefix}{$subCat} for targeted skincare support";

        $whyKcodeEn = "Authentic {$brandPrefix}{$nameWithSize}"
            . ($actives !== '' ? " formulated with {$actives}." : " selected for balanced skin compatibility.");

        $roleEn = $subCat !== '' ? $subCat : ($ctx['routine_role'] ?: "Targeted {$ctx['category']} Care");
        $usageFreqEn = self::resolveEnglishFrequency($ctx['frequency'], $ctx['zone'], $ctx['category']);
        $fragranceEn = self::translateFragranceCode($ctx['fragrance']);
        $sensStatusEn = self::resolveSensitiveStatusEn($ctx['sensitivity_label_ar'], $ctx['sensitivity_code'], $ctx['sensitive_eligible']);
        $sensGuidanceEn = self::resolveSensitiveGuidanceEn($sensStatusEn, $fragranceEn);

        $limitationsEn = 'Store in a cool, dry place away from direct sunlight. A patch test on a small area is recommended before full use.';
        $safetyNotesEn = "{$sensStatusEn} — {$sensGuidanceEn}";

        $stepTitleEn = self::resolveHeaderStepTitleEn($ctx['step_number'], $ctx['category']);
        $usageTiles = self::resolveEnglishUsageTileCopy($ctx, $usageFreqEn, $sensStatusEn);

        $howToUseEn = implode("\n", [
            "{$usageTiles['timing_label_en']} — {$usageTiles['timing_en']} {$usageTiles['timing_note_en']}",
            "{$usageTiles['amount_label_en']} — {$usageTiles['amount_en']} {$usageTiles['amount_note_en']}",
            "{$usageTiles['application_label_en']} — {$usageTiles['application_method_en']} {$usageTiles['application_note_en']}",
            "{$usageTiles['gradual_start_label_en']} — {$usageTiles['gradual_start_en']} {$usageTiles['gradual_start_note_en']}",
        ]);

        $seoTitle = trim("{$brandPrefix}{$ctx['short_name']}") . ' | KCODE';

        return [
            'description_en' => $descriptionEn,
            'brief_insight_en' => $briefInsightEn,
            'why_kcode_en' => $whyKcodeEn,
            'country_of_origin_en' => 'South Korea',
            'role_en' => $roleEn,
            'texture_en' => $ctx['texture'],
            'usage_frequency_en' => $usageFreqEn,
            'limitations_notes_en' => $limitationsEn,
            'safety_notes_en' => $safetyNotesEn,
            'how_to_use_en' => $howToUseEn,
            'routine_step_title_en' => $stepTitleEn,
            'en_key_benefits' => self::buildEnglishKeyBenefits($ctx, $fragranceEn, $sensStatusEn),
            'seo_meta_title_en' => $seoTitle,
            'og_title_en' => $seoTitle,
            'en_product_title_seo' => $seoTitle,
            'meta_description_en' => $descriptionEn,
            'og_description_en' => $briefInsightEn,
            'en_short_hook' => $briefInsightEn,
            'primary_keyword_en' => $actives !== '' ? $actives : $subCat,
            'image_alt_en' => trim("{$brandPrefix}{$ctx['short_name']}" . ($ctx['size'] !== '' ? ", {$ctx['size']}" : '')),
        ];
    }

    /**
     * Merge English keys into usage_instructions_json without overwriting valid custom English values.
     *
     * @return array<string, mixed>
     */
    public static function mergeUsageInstructionsEnglish(array $existingJson, array $ctx): array
    {
        $scalars = self::buildScalarEnglishFields($ctx);
        $fragranceEn = self::translateFragranceCode($ctx['fragrance']);
        $sensStatusEn = self::resolveSensitiveStatusEn($ctx['sensitivity_label_ar'], $ctx['sensitivity_code'], $ctx['sensitive_eligible']);
        $usageCopy = self::resolveEnglishUsageTileCopy($ctx, $scalars['usage_frequency_en'], $sensStatusEn);

        foreach ($usageCopy as $key => $val) {
            if (self::needsEnglishText($existingJson[$key] ?? null)) {
                $existingJson[$key] = $val;
            }
        }

        if (self::needsEnglishText($existingJson['caution_en'] ?? null)) {
            $existingJson['caution_en'] = $scalars['limitations_notes_en'];
        }

        $verdict = is_array($existingJson['verdict'] ?? null) ? $existingJson['verdict'] : [];
        $verdictDefaults = [
            'note_en' => $scalars['description_en'],
            'skin_fit_value_en' => 'Oily · Combination · Normal · Dry',
            'sensitive_status_en' => $sensStatusEn,
            'sensitive_guidance_en' => self::resolveSensitiveGuidanceEn($sensStatusEn, $fragranceEn),
            'fragrance_en' => $fragranceEn,
            'actives_en' => $ctx['actives'] !== '' ? $ctx['actives'] : ($ctx['short_name'] ?: $ctx['category']),
        ];

        foreach ($verdictDefaults as $key => $val) {
            if (self::needsEnglishText($verdict[$key] ?? null)) {
                $verdict[$key] = $val;
            }
        }

        $existingJson['verdict'] = $verdict;

        return $existingJson;
    }

    /**
     * Populate missing title_en and subtitle_en on each routine_steps_json entry.
     *
     * @return array<int, array<string, mixed>>
     */
    public static function mergeRoutineStepsEnglish(array $steps): array
    {
        foreach ($steps as $idx => $step) {
            if (!is_array($step)) {
                continue;
            }

            $stepNum = (int) ($step['step'] ?? ($idx + 1));
            $isCurrent = (bool) ($step['is_current'] ?? false);

            if (self::needsEnglishText($step['title_en'] ?? null)) {
                $rawTitle = trim((string) ($step['title'] ?? ''));
                $step['title_en'] = !self::needsEnglishText($rawTitle)
                    ? $rawTitle
                    : (self::STEP_TITLE_EN_MAP[$stepNum] ?? 'Skincare Step');
            }

            if (self::needsEnglishText($step['subtitle_en'] ?? null)) {
                $rawSub = trim((string) ($step['subtitle'] ?? ''));
                if ($rawSub !== '') {
                    $step['subtitle_en'] = self::translateSubtitleEn($rawSub, $isCurrent);
                } elseif ($isCurrent) {
                    $step['subtitle_en'] = 'This Product';
                }
            }

            $steps[$idx] = $step;
        }

        return $steps;
    }

    /**
     * Preserve existing English FAQ fields when re-syncing FAQs from Arabic HTML sources.
     *
     * @return array<int, array<string, mixed>>
     */
    public static function mergeFaqTranslations(array $newFaqs, ?array $existingFaqs): array
    {
        if (empty($existingFaqs)) {
            return $newFaqs;
        }

        $existingById = [];
        foreach ($existingFaqs as $index => $faq) {
            if (!is_array($faq)) {
                continue;
            }
            $id = (string) ($faq['id'] ?? "idx_{$index}");
            $existingById[$id] = $faq;
        }

        foreach ($newFaqs as $index => $faq) {
            $id = (string) ($faq['id'] ?? "idx_{$index}");
            $match = $existingById[$id] ?? ($existingFaqs[$index] ?? null);
            if (!is_array($match)) {
                continue;
            }

            if (empty($faq['question_en']) && !empty($match['question_en'])) {
                $faq['question_en'] = $match['question_en'];
            }
            if (empty($faq['answer_en']) && !empty($match['answer_en'])) {
                $faq['answer_en'] = $match['answer_en'];
            }
            $newFaqs[$index] = $faq;
        }

        return $newFaqs;
    }

    /**
     * Populate missing category_en and reason_en inside complementary_routine_json items.
     *
     * @param  array<string, array<string, mixed>>  $snapshotBySku
     * @return array<int, array<string, mixed>>
     */
    public static function mergeComplementaryRoutineEnglish(array $items, array $snapshotBySku = []): array
    {
        foreach ($items as $idx => $item) {
            if (!is_array($item)) {
                continue;
            }

            $sku = (string) ($item['sku'] ?? '');
            $snap = $sku !== '' ? ($snapshotBySku[$sku] ?? null) : null;
            $rawCat = trim((string) ($item['category'] ?? ''));

            if (self::needsEnglishText($item['category_en'] ?? null)) {
                $item['category_en'] = self::CATEGORY_EN_MAP[$rawCat]
                    ?? ($snap['Category'] ?? (!self::needsEnglishText($rawCat) ? $rawCat : 'Skincare Step'));
            }

            if (self::needsEnglishText($item['reason_en'] ?? null)) {
                $catEn = strtolower((string) $item['category_en']);
                $subCat = $snap['Sub Category'] ?? ($snap['Category'] ?? $item['category_en']);
                $actives = trim((string) ($snap['Customer Actives'] ?? ''));

                if ($actives !== '') {
                    $item['reason_en'] = "{$subCat} featuring {$actives} to support your routine.";
                } elseif (str_contains($catEn, 'cleans')) {
                    $item['reason_en'] = 'Gentle cleansing step to prep skin without over-drying.';
                } elseif (str_contains($catEn, 'toner') || str_contains($catEn, 'essence')) {
                    $item['reason_en'] = 'Prepares skin and replenishes hydration before active steps.';
                } elseif (str_contains($catEn, 'moistur')) {
                    $item['reason_en'] = 'Supports the skin barrier and locks in moisture.';
                } elseif (str_contains($catEn, 'sun')) {
                    $item['reason_en'] = 'Daily broad-spectrum UV protection as your final morning step.';
                } else {
                    $item['reason_en'] = 'Complements your daily skincare routine.';
                }
            }

            $items[$idx] = $item;
        }

        return $items;
    }

    /**
     * Non-destructively fill any missing or Arabic-contaminated English fields on an existing Product model.
     *
     * @param  array<string, array<string, mixed>>  $snapshotBySku
     * @param  array<string, array<string, mixed>>  $kcodeBySku
     */
    public static function fillMissingEnglishOnModel(
        Product $product,
        array $snapshotBySku = [],
        array $kcodeBySku = []
    ): bool {
        $snap = $product->sku ? ($snapshotBySku[$product->sku] ?? null) : null;
        $kp = $product->sku ? ($kcodeBySku[$product->sku] ?? null) : null;
        $ctx = self::buildContext($product, $snap, $kp);
        $scalarFields = self::buildScalarEnglishFields($ctx);

        foreach ($scalarFields as $column => $value) {
            if ($column === 'en_key_benefits') {
                if (self::needsEnglishBenefits($product->en_key_benefits)) {
                    $product->en_key_benefits = $value;
                }
                continue;
            }

            $rawColumnValue = $product->getAttributes()[$column] ?? null;
            if (self::needsEnglishText(is_string($rawColumnValue) ? $rawColumnValue : null)) {
                $product->{$column} = $value;
            }
        }

        if (empty($product->routine_step_number)) {
            $product->routine_step_number = $ctx['step_number'];
        }

        if (!empty($product->usage_instructions_json) && is_array($product->usage_instructions_json)) {
            $product->usage_instructions_json = self::mergeUsageInstructionsEnglish($product->usage_instructions_json, $ctx);
        } else {
            $product->usage_instructions_json = self::mergeUsageInstructionsEnglish([], $ctx);
        }

        if (!empty($product->routine_steps_json) && is_array($product->routine_steps_json)) {
            $product->routine_steps_json = self::mergeRoutineStepsEnglish($product->routine_steps_json);
        }

        if (!empty($product->complementary_routine_json) && is_array($product->complementary_routine_json)) {
            $product->complementary_routine_json = self::mergeComplementaryRoutineEnglish($product->complementary_routine_json, $snapshotBySku);
        }

        return $product->isDirty();
    }

    /**
     * Build 3 structured editorial English formulation cards based on declared metadata.
     *
     * @return array<int, array{title: string, text: string}>
     */
    private static function buildEnglishKeyBenefits(array $ctx, string $fragranceEn, string $sensStatusEn): array
    {
        $subCat = $ctx['sub_category'] ?: $ctx['category'];
        $actives = $ctx['actives'];

        $focusParts = [];
        if ($ctx['hydrating_score'] >= 70) $focusParts[] = 'hydration';
        if ($ctx['barrier_score'] >= 70) $focusParts[] = 'barrier support';
        if ($ctx['soothing_score'] >= 70) $focusParts[] = 'calmness and redness care';
        if ($ctx['brightening_score'] >= 70) $focusParts[] = 'tone clarity and radiance';
        if ($ctx['acne_score'] >= 70) $focusParts[] = 'blemish care';
        if ($ctx['lines_score'] >= 70) $focusParts[] = 'fine-line care';

        $focusSummary = !empty($focusParts) ? implode(', ', array_slice($focusParts, 0, 3)) : 'daily skin support';

        $card1Text = $actives !== ''
            ? "Formulated with {$actives} to support {$focusSummary} within a targeted {$subCat} formula."
            : "Designed as a {$subCat} to support {$focusSummary} based on its declared ingredient list.";

        $card2Text = "Features a {$ctx['texture']} texture crafted for step {$ctx['step_number']} ({$ctx['category']}) compatibility without heavy residue when layered appropriately.";

        $card3Text = "Classified as \"{$sensStatusEn}\" with a {$fragranceEn} profile; review the full INCI list if you have known ingredient sensitivities.";

        return [
            ['title' => 'Formulation Focus', 'text' => $card1Text],
            ['title' => 'Texture & Routine Role', 'text' => $card2Text],
            ['title' => 'Tolerance & Fragrance Profile', 'text' => $card3Text],
        ];
    }

    private static function resolveCleanEnglish(?string ...$candidates): ?string
    {
        foreach ($candidates as $candidate) {
            if (!self::needsEnglishText($candidate)) {
                return trim((string) $candidate);
            }
        }

        return null;
    }

    private static function inferRoutineStepNumber(string $category, string $subCategory): int
    {
        $combined = strtolower("{$category} {$subCategory}");
        if (str_contains($combined, 'cleans') || str_contains($combined, 'wash')) return 1;
        if (str_contains($combined, 'toner') || str_contains($combined, 'essence') || str_contains($combined, 'mist')) return 2;
        if (str_contains($combined, 'moistur') || str_contains($combined, 'cream') || str_contains($combined, 'lotion') || str_contains($combined, 'balm')) return 4;
        if (str_contains($combined, 'sun')) return 5;

        return 3;
    }

    private static function resolveHeaderStepTitleEn(int $stepNumber, string $category): string
    {
        return match ($stepNumber) {
            1 => 'Cleanser',
            2 => 'Toner or Essence',
            3 => 'Serum / Targeted Treatment',
            4 => 'Moisturizer',
            5 => 'Sunscreen',
            default => $category ?: 'Skincare Routine',
        };
    }

    private static function resolveEnglishFrequency(string $frequency, string $zone, string $category): string
    {
        if ($frequency !== '') {
            return $frequency;
        }
        if (strtolower($category) === 'sunscreen' || $zone === 'AM') {
            return 'Daily morning use';
        }
        if ($zone === 'PM') {
            return 'Evening use';
        }
        if (strtolower($category) === 'mask') {
            return 'Weekly use (1-2 times per week)';
        }

        return 'Daily use (Morning & Evening)';
    }

    private static function translateFragranceCode(string $raw): string
    {
        return match (trim($raw)) {
            '', 'None', 'none' => 'Fragrance-free',
            'Essential Oil' => 'Contains essential oils',
            'Essential Oil + Allergens' => 'Contains essential oils & fragrance allergens',
            'Fragrance' => 'Added fragrance',
            'Fragrance + Allergens' => 'Added fragrance & declared allergens',
            default => !self::containsArabic($raw) ? $raw : 'Fragrance-free',
        };
    }

    private static function resolveSensitiveStatusEn(string $labelAr, string $code, bool $eligible): string
    {
        if (str_contains($labelAr, 'غير مناسب')) {
            return str_contains($labelAr, 'خطوة أولى')
                ? 'Not suitable as a first step for sensitive skin'
                : 'Not suitable for sensitive skin';
        }
        if (str_contains($labelAr, 'بشرط') || str_contains($labelAr, 'تدرج') || $code === 'conditional') {
            return 'Conditionally suitable';
        }
        if ($code === 'unsuitable') {
            return 'Not suitable for sensitive skin';
        }
        if ($code === 'suitable' || str_contains($labelAr, 'مناسب للبشرة الحساسة') || $eligible) {
            return 'Suitable for sensitive skin';
        }

        return 'Conditionally suitable';
    }

    private static function resolveSensitiveGuidanceEn(string $statusEn, string $fragranceEn): string
    {
        $fragranceNote = $fragranceEn === 'Fragrance-free'
            ? 'without added fragrance'
            : "({$fragranceEn})";

        if (str_starts_with($statusEn, 'Not suitable')) {
            return "Formulated with active or aromatic ingredients {$fragranceNote} that may sensitize reactive skin. Patch test before full use and avoid combining with other strong actives.";
        }
        if ($statusEn === 'Conditionally suitable') {
            return "Conditionally suitable {$fragranceNote}: introduce gradually on intact, non-irritated skin and patch test first. Discontinue if redness or stinging occurs.";
        }

        return "Suitable for sensitive skin {$fragranceNote} based on ingredient screening. Avoid broken or irritated skin, and discontinue if discomfort occurs.";
    }

    /**
     * @return array<string, string>
     */
    private static function resolveEnglishUsageTileCopy(array $ctx, string $usageFreqEn, string $sensStatusEn): array
    {
        $cat = strtolower($ctx['category']);
        $subCat = strtolower($ctx['sub_category']);

        $amount = 'Moderate amount';
        $method = 'Gently pat onto skin';
        $methodNote = 'Until absorbed before the next step.';

        if (str_contains($cat, 'cleans') || str_contains($subCat, 'wash')) {
            $amount = 'Nickel-sized amount';
            $method = 'Massage gently over damp skin';
            $methodNote = 'Lather with water and rinse thoroughly with lukewarm water.';
        } elseif (str_contains($cat, 'sun')) {
            $amount = 'Two-finger length';
            $method = 'Apply evenly to face & neck';
            $methodNote = 'Use 15 minutes before sun exposure and reapply every 2 hours outdoors.';
        } elseif (str_contains($cat, 'mask') && str_contains($subCat, 'sheet')) {
            $amount = 'One sheet mask';
            $method = 'Align gently onto clean skin';
            $methodNote = 'Leave on as directed, then remove without rinsing unless instructed.';
        } elseif (str_contains($cat, 'serum') || str_contains($cat, 'exfoliator')) {
            $amount = 'A few drops';
            $method = 'Pat gently across targeted areas';
            $methodNote = 'Follow with moisturizer to seal in hydration.';
        } elseif (str_contains($cat, 'moistur')) {
            $amount = 'Pea-sized to moderate amount';
            $method = 'Smooth evenly over face & neck';
            $methodNote = 'Apply as the final moisturizing step before morning sunscreen.';
        }

        $precaution = str_starts_with($sensStatusEn, 'Suitable')
            ? 'For external use only'
            : 'Introduce gradually';
        $precautionNote = str_starts_with($sensStatusEn, 'Suitable')
            ? 'Avoid direct eye contact and discontinue if irritation persists.'
            : 'Start slowly, patch test first, and use daily morning sunscreen.';

        $timingNote = str_contains($ctx['zone'], 'AM') && !str_contains($ctx['zone'], 'PM')
            ? 'In your morning routine before sun exposure.'
            : (str_contains($ctx['zone'], 'PM') && !str_contains($ctx['zone'], 'AM')
                ? 'In your evening routine after cleansing.'
                : 'After cleansing, based on your skin tolerance.');

        return [
            'timing_label_en' => 'Timing',
            'timing_en' => $usageFreqEn,
            'timing_note_en' => $timingNote,
            'amount_label_en' => 'Amount',
            'amount_en' => $amount,
            'amount_note_en' => 'Adjust gently according to skin comfort.',
            'application_label_en' => 'Application',
            'application_method_en' => $method,
            'application_note_en' => $methodNote,
            'gradual_start_label_en' => 'Precautions',
            'gradual_start_en' => $precaution,
            'gradual_start_note_en' => $precautionNote,
        ];
    }

    private static function translateSubtitleEn(string $raw, bool $isCurrent): ?string
    {
        if (isset(self::SUBTITLE_EN_MAP[$raw])) {
            return self::SUBTITLE_EN_MAP[$raw];
        }
        if (!self::containsArabic($raw)) {
            return $raw;
        }
        if (str_contains($raw, 'هذا المنتج')) return 'This Product';
        if (str_contains($raw, 'اختيار')) return 'Optional';
        if (str_contains($raw, 'حاجة')) return 'As needed';
        if (str_contains($raw, 'ماسك')) return 'After mask removal';
        if (str_contains($raw, 'صباح') && str_contains($raw, 'مساء')) return 'Morning & Evening';
        if (str_contains($raw, 'صباح')) return 'Morning';
        if (str_contains($raw, 'مساء')) return 'Evening';

        return $isCurrent ? 'This Product' : null;
    }
}
