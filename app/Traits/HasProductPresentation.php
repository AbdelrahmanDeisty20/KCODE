<?php

namespace App\Traits;

use App\Models\Coupon;
use App\Models\Product;
use App\Models\Setting;
use App\Services\SettingService;
use App\Support\StaticCache;

/**
 * Trait HasProductPresentation
 *
 * Encapsulates all UI presentation formatting and frontend display structures
 * for the Product model (QuickView Modal & Full PDP pages).
 *
 * Keeps the main Product Eloquent Model lean, standard, and focused on database
 * relations and business scopes.
 */
trait HasProductPresentation
{
    /**
     * Get 4 trust badges from system settings (Authenticity, Fast Delivery, Safe Payment, 7-Day Return).
     */
    public function getTrustBadgesAttribute(): array
    {
        return app(SettingService::class)->getAssurances()['data'] ?? [];
    }

    /**
     * Get active promotional coupon (e.g. KCODE10) dynamically from Dashboard.
     */
    public function getPromoCouponAttribute(): ?array
    {
        $isAr = $this->isArabicLocale();

        // 1. Check if an active general coupon is configured in Dashboard > Coupons (cached)
        $coupon = StaticCache::remember('coupons', 'active_general_' . now()->format('Y-m-d'), function () {
            return Coupon::where('is_active', true)
                ->where('is_general', true)
                ->where(function ($q) {
                    $q->whereNull('start_date')->orWhere('start_date', '<=', now());
                })
                ->where(function ($q) {
                    $q->whereNull('end_date')->orWhere('end_date', '>=', now());
                })
                ->first();
        }, 300);

        if ($coupon) {
            $defaultText = $coupon->discount_type === 'percentage'
                ? ($isAr ? 'خصم ' . (int) $coupon->discount_value . "% على طلبك الأول {$coupon->code}" : 'Get ' . (int) $coupon->discount_value . "% off your first order {$coupon->code}")
                : ($isAr ? "خصم {$coupon->discount_value} ر.ع على طلبك {$coupon->code}" : "Get {$coupon->discount_value} OMR off with {$coupon->code}");

            return [
                'code' => $coupon->code,
                'text' => $coupon->title ?: $defaultText,
                'discount_type' => $coupon->discount_type,
                'discount_value' => (float) $coupon->discount_value,
            ];
        }

        // 2. Check Dashboard > Settings (key: promo_coupon_code)
        $code = Setting::get('promo_coupon_code');
        if (!empty($code)) {
            $defaultSettingText = $isAr
                ? "خصم 10% على طلبك الأول {$code}"
                : "10% off your first order with {$code}";
            $text = Setting::get('promo_coupon_text', $defaultSettingText);

            return [
                'code' => $code,
                'text' => $text,
            ];
        }

        // 3. Check if explicitly disabled in Dashboard
        $enabled = Setting::get('promo_coupon_enabled', '1');
        if ($enabled === '0' || $enabled === false) {
            return null;
        }

        // 4. Default fallback
        return [
            'code' => 'KCODE10',
            'text' => $isAr ? 'خصم 10% على طلبك الأول KCODE10' : '10% off your first order with KCODE10',
        ];
    }

    /**
     * Build unified specifications table (Brand, Type, Size, Texture, Fragrance, INCI).
     */
    public function getSpecificationsAttribute(): array
    {
        $isAr = $this->isArabicLocale();

        if ($isAr) {
            $type = $this->role_ar ?: ($this->role ?: ($this->subCategory?->name ?? ($this->category?->name ?? '')));
            $typeMap = [
                'sunscreen' => 'واقي شمس',
                'serum' => 'سيروم',
                'toner' => 'تونر',
                'cleanser' => 'غسول',
                'moisturizer' => 'مرطب',
                'cream' => 'كريم مرطب',
                'mask' => 'قناع / ماسك',
                'cleansing balm' => 'بلسم تنظيف',
                'eye care' => 'عناية بالعين',
                'exfoliator' => 'مقشّر',
                'mist' => 'ميست مرطب',
            ];
            $lower = strtolower(trim((string) $type));
            if (isset($typeMap[$lower])) {
                $type = $typeMap[$lower];
            }
        } else {
            $type = $this->english_role;
        }

        $fragrance = $this->fragrance;
        if (empty($fragrance) || strtolower((string) $fragrance) === 'none') {
            $fragrance = $isAr ? 'دون عطر مضاف أو زيوت عطرية' : 'Fragrance & essential-oil free';
        }

        $texture = $this->texture;
        if (empty($texture)) {
            $texture = $isAr ? 'خفيف وسريع الامتصاص' : 'Lightweight and fast-absorbing';
        }

        return [
            'brand' => $this->brand?->name ?? '',
            'full_name' => $this->fixedFullName(),
            'barcode' => $this->barcode,
            'type' => $type,
            'size' => $this->size,
            'texture' => $texture,
            'fragrance' => $fragrance,
            'ingredients' => $this->ingredients,
        ];
    }

    /**
     * Build structured 5-step routine position for PDP bar.
     */
    public function getRoutinePositionAttribute(): ?array
    {
        $dbSteps = $this->routine_steps_json;
        if (empty($dbSteps) || !is_array($dbSteps)) {
            return null;
        }

        $isAr = $this->isArabicLocale();
        $stepNumber = $this->routine_step_number ?: $this->detectRoutineStepNumber();

        $stepTitleEnMap = [
            1 => 'Cleanser',
            2 => 'Toner or Essence',
            3 => 'Serum',
            4 => 'Moisturizer',
            5 => 'Sunscreen',
        ];

        $subtitleEnMap = [
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

        $formattedSteps = array_map(function ($s) use ($stepNumber, $isAr, $stepTitleEnMap, $subtitleEnMap) {
            $stepIdx = (int) ($s['step'] ?? 1);
            $isCurrent = $stepIdx === (int) $stepNumber;
            $rawTitle = (string) ($s['title'] ?? '');
            $rawSubtitle = $isCurrent ? ($s['subtitle'] ?? 'هذا المنتج') : ($s['subtitle'] ?? null);

            $enTitle = $this->firstNonArabic([$s['title_en'] ?? null, $rawTitle])
                ?: ($stepTitleEnMap[$stepIdx] ?? 'Skincare Step');

            $enSubtitle = $this->resolveEnglishRoutineSubtitle(
                $s['subtitle_en'] ?? null,
                $rawSubtitle,
                $subtitleEnMap,
                $isCurrent
            );

            return [
                'step' => $stepIdx,
                'title' => $isAr ? $rawTitle : $enTitle,
                'subtitle' => $isAr ? $rawSubtitle : $enSubtitle,
                'is_current' => $isCurrent,
            ];
        }, $dbSteps);

        usort($formattedSteps, fn ($a, $b) => $a['step'] <=> $b['step']);

        $headerStepTitleEn = match ((int) $stepNumber) {
            1 => 'Cleanser',
            2 => 'Toner or Essence',
            3 => 'Serum / Targeted Treatment',
            4 => 'Moisturizer',
            5 => 'Sunscreen',
            default => $this->category?->name_en ?? 'Skincare Routine',
        };

        return [
            'step_number' => (int) $stepNumber,
            'total_steps' => count($formattedSteps),
            'step_title' => $isAr
                ? ($this->routine_step_title_ar ?: ($this->category?->name_ar ?? 'الروتين'))
                : ($this->firstNonArabic([$this->routine_step_title_en ?? null, $this->routine_step_title_ar ?? null]) ?: $headerStepTitleEn),
            'steps' => $formattedSteps,
        ];
    }

    protected function resolveEnglishRoutineSubtitle(?string $explicitEn, ?string $rawSubtitle, array $map, bool $isCurrent): ?string
    {
        $direct = $this->firstNonArabic([$explicitEn]);
        if ($direct !== null) {
            return $direct;
        }

        if (empty($rawSubtitle)) {
            return $isCurrent ? 'This Product' : null;
        }

        $trimmed = trim($rawSubtitle);
        if (!$this->containsArabic($trimmed)) {
            return $trimmed;
        }

        if (isset($map[$trimmed])) {
            return $map[$trimmed];
        }

        if (str_contains($trimmed, 'هذا المنتج')) {
            return 'This Product';
        }
        if (str_contains($trimmed, 'اختيار')) {
            return 'Optional';
        }
        if (str_contains($trimmed, 'حاجة')) {
            return 'As needed';
        }
        if (str_contains($trimmed, 'ماسك')) {
            return 'After mask removal';
        }
        if (str_contains($trimmed, 'صباح') && str_contains($trimmed, 'مساء')) {
            return 'Morning & Evening';
        }
        if (str_contains($trimmed, 'صباح')) {
            return 'Morning';
        }
        if (str_contains($trimmed, 'مساء')) {
            return 'Evening';
        }

        return $isCurrent ? 'This Product' : null;
    }

    /**
     * Detect routine step number from category or role if not explicitly set.
     */
    public function detectRoutineStepNumber(): int
    {
        $cat = $this->category;
        if ($cat) {
            $name = strtolower($cat->name_en ?? '');
            if (str_contains($name, 'cleans')) return 1;
            if (str_contains($name, 'toner') || str_contains($name, 'essence')) return 2;
            if (str_contains($name, 'serum') || str_contains($name, 'ampoule')) return 3;
            if (str_contains($name, 'moistur') || str_contains($name, 'cream')) return 4;
            if (str_contains($name, 'sun')) return 5;
        }

        $role = strtolower($this->role_ar ?? ($this->role_en ?? ''));
        if (str_contains($role, 'غسول') || str_contains($role, 'تنظيف')) return 1;
        if (str_contains($role, 'تونر') || str_contains($role, 'إسنس')) return 2;
        if (str_contains($role, 'سيروم') || str_contains($role, 'علاج') || str_contains($role, 'أمبول')) return 3;
        if (str_contains($role, 'مرطب') || str_contains($role, 'كريم')) return 4;
        if (str_contains($role, 'واقي') || str_contains($role, 'شمس')) return 5;

        return 3;
    }

    /**
     * Resolve raw usage_instructions_json merged with catalog seed tiles if empty.
     *
     * @return array{raw: array<string, mixed>, tiles: array<int, array<string, mixed>>}
     */
    protected function resolveRawUsageSource(): array
    {
        $raw = is_array($this->usage_instructions_json) ? $this->usage_instructions_json : [];
        $tiles = is_array($raw['tiles'] ?? null) ? $raw['tiles'] : [];

        return ['raw' => $raw, 'tiles' => $tiles];
    }

    protected function translateUsageTileLabelEn(?string $rawLabel, string $fallback): string
    {
        $direct = $this->firstNonArabic([$rawLabel]);
        if ($direct !== null) {
            return $direct;
        }

        $label = trim((string) $rawLabel);
        $map = [
            'التوقيت والترتيب' => 'Timing & Order',
            'التوقيت والتكرار' => 'Timing & Frequency',
            'التوقيت' => 'Timing',
            'الكمية والتوزيع' => 'Amount & Application',
            'الكمية حسب البشرة' => 'Amount by Skin Type',
            'الكمية' => 'Amount',
            'طريقة التطبيق' => 'Application Method',
            'طريقة الوضع' => 'Application',
            'التوزيع والامتصاص' => 'Distribution & Absorption',
            'الترتيب والترطيب' => 'Routine Order & Moisture',
            'التطبيق' => 'Application',
            'المدة وإكمال الروتين' => 'Duration & Routine Completion',
            'احتياطات الاستخدام' => 'Precautions',
            'الحماية والتثبيت' => 'Sun Protection',
            'التدرّج' => 'Gradual Start',
            'التدرج' => 'Gradual Start',
            'بعد الكريم' => 'Next Step',
            'صباحًا' => 'Morning',
            'صباحاً' => 'Morning',
            'العناية المصاحبة' => 'Complementary Care',
        ];

        return $map[$label] ?? $fallback;
    }

    /**
     * Build the 4 structured usage tile definitions for either Arabic or English.
     *
     * @return array<string, mixed>
     */
    protected function buildLocalizedUsageFields(array $raw, array $tiles, bool $isAr): array
    {
        $specs = [
            [
                'label_field' => 'timing_label',
                'text_field' => 'timing',
                'note_field' => 'timing_note',
                'ar_defaults' => ['التوقيت', $this->usage_frequency_ar ?: 'استخدام يومي', 'بعد التدرّج في الاستخدام.'],
                'en_defaults' => ['Timing', $this->english_usage_frequency, 'After gradually introducing into your routine.'],
            ],
            [
                'label_field' => 'amount_label',
                'text_field' => 'amount',
                'note_field' => 'amount_note',
                'ar_defaults' => ['الكمية', 'بضع قطرات', 'على المناطق المستهدفة.'],
                'en_defaults' => ['Amount', 'Moderate amount / A few drops', 'On targeted areas or entire face.'],
            ],
            [
                'label_field' => 'application_label',
                'text_field' => 'application_method',
                'note_field' => 'application_note',
                'ar_defaults' => ['طريقة الوضع', 'ربّت بلطف', 'حتى الامتصاص، قبل المرطب.'],
                'en_defaults' => ['Application', 'Gently pat onto skin', 'Until fully absorbed, before moisturizer.'],
            ],
            [
                'label_field' => 'gradual_start_label',
                'text_field' => 'gradual_start',
                'note_field' => 'gradual_start_note',
                'ar_defaults' => ['التدرّج', 'ابدأ تدريجيًا', 'وزِد التكرار حسب تحمّل بشرتك.'],
                'en_defaults' => ['Introduction', 'Start gradually', 'Increase frequency according to your skin tolerance.'],
            ],
        ];

        $output = [];
        $localizedTiles = [];

        foreach ($specs as $idx => $spec) {
            $tile = is_array($tiles[$idx] ?? null) ? $tiles[$idx] : [];
            $labelField = $spec['label_field'];
            $textField = $spec['text_field'];
            $noteField = $spec['note_field'];

            if ($isAr) {
                $label = !empty($raw[$labelField]) ? $raw[$labelField] : ($tile['label'] ?? $spec['ar_defaults'][0]);
                $text = !empty($raw[$textField]) ? $raw[$textField] : ($tile['text'] ?? ($tile['value'] ?? $spec['ar_defaults'][1]));
                $note = !empty($raw[$noteField]) ? $raw[$noteField] : ($tile['note'] ?? $spec['ar_defaults'][2]);
            } else {
                $label = $this->firstNonArabic([
                    $raw["{$labelField}_en"] ?? null,
                    $tile['label_en'] ?? null,
                ]) ?: $this->translateUsageTileLabelEn($tile['label'] ?? ($raw[$labelField] ?? null), $spec['en_defaults'][0]);

                $text = $this->firstNonArabic([
                    $raw["{$textField}_en"] ?? null,
                    $tile['text_en'] ?? null,
                    $tile['value_en'] ?? null,
                    $tile['text'] ?? null,
                    $tile['value'] ?? null,
                    $raw[$textField] ?? null,
                ]) ?: $spec['en_defaults'][1];

                $note = $this->firstNonArabic([
                    $raw["{$noteField}_en"] ?? null,
                    $tile['note_en'] ?? null,
                    $tile['note'] ?? null,
                    $raw[$noteField] ?? null,
                ]) ?: $spec['en_defaults'][2];
            }

            $output[$labelField] = $label;
            $output[$textField] = $text;
            $output[$noteField] = $note;

            $localizedTiles[] = [
                'label' => $label,
                'text' => $text,
                'note' => $note,
            ];
        }

        $output['localized_tiles'] = $isAr && !empty($tiles) ? $tiles : $localizedTiles;

        return $output;
    }

    /**
     * Format clean English multi-line usage instructions when how_to_use_en is unavailable.
     */
    public function formatEnglishHowToUseLines(): ?string
    {
        ['raw' => $raw, 'tiles' => $tiles] = $this->resolveRawUsageSource();
        if (empty($raw) && empty($tiles)) {
            return null;
        }

        $fields = $this->buildLocalizedUsageFields($raw, $tiles, false);
        $lines = [];
        foreach ($fields['localized_tiles'] as $tile) {
            if (!empty($tile['label']) && !empty($tile['text'])) {
                $lines[] = "{$tile['label']} — {$tile['text']}";
            }
        }

        return !empty($lines) ? implode("\n", $lines) : null;
    }

    protected function resolveEnglishSkinFitValue(): string
    {
        if ($this->relationLoaded('skinTypes') || $this->skinTypes()->exists()) {
            $names = $this->skinTypes
                ->map(fn ($skinType) => $this->firstNonArabic([$skinType->name_en ?? null, $skinType->name ?? null]))
                ->filter()
                ->values()
                ->all();

            if (!empty($names)) {
                return implode(' · ', $names);
            }
        }

        return 'Oily · Combination · Normal · Dry';
    }

    protected function localizeVerdict(?array $verdict, bool $isAr): ?array
    {
        if ($verdict === null && $isAr) {
            return null;
        }

        if ($isAr) {
            return $verdict;
        }

        if (empty($verdict)) {
            return null;
        }

        $note = $this->firstNonArabic([
            $verdict['note_en'] ?? null,
            $verdict['note'] ?? null,
        ]) ?: $this->english_description;

        $skinFitValue = $this->firstNonArabic([
            $verdict['skin_fit_value_en'] ?? null,
            $verdict['skin_fit_value'] ?? null,
        ]) ?: $this->resolveEnglishSkinFitValue();

        $skinFitGuidance = $this->firstNonArabic([
            $verdict['skin_fit_guidance_en'] ?? null,
            $verdict['skin_fit_guidance'] ?? null,
        ]) ?: '';

        $sensitiveStatus = $this->english_sensitive_status;
        $sensitiveGuidance = $this->english_sensitive_guidance;
        $fragrance = $this->english_fragrance;
        $actives = $this->firstNonArabic([
            $verdict['actives_en'] ?? null,
            $verdict['actives'] ?? null,
            $this->primary_keyword_en,
        ]) ?: $this->fixedShortName();

        $summaryHtml = $this->firstNonArabic([$verdict['summary_html_en'] ?? null]);
        if ($summaryHtml === null && !empty($verdict['summary_html'])) {
            $guidanceHtml = $sensitiveGuidance
                ? '<p class="fit-guidance">' . e($sensitiveGuidance) . '</p>'
                : '';

            $summaryHtml = '<article class="kcode-verdict" aria-labelledby="verdict-title">'
                . '<div class="verdict-top"><h2 id="verdict-title" class="verdict-label"><bdi>KCODE</bdi> Summary</h2></div>'
                . '<div class="verdict-body"><p class="verdict-note master-copy">' . e($note) . '</p>'
                . '<div class="verdict-audience" aria-label="Skin compatibility">'
                . '<div class="fit-item"><span class="fit-label">Suitable Skin Types</span><strong class="fit-value">' . e($skinFitValue) . '</strong></div>'
                . '<section class="fit-item fit-sensitive" aria-label="For Sensitive Skin"><div class="sensitive-head"><span class="fit-label">For Sensitive Skin</span><strong class="fit-value fit-status">' . e($sensitiveStatus) . '</strong></div>' . $guidanceHtml . '</section>'
                . '</div></div></article>';
        }

        return [
            'note' => $note,
            'skin_fit_value' => $skinFitValue,
            'skin_fit_guidance' => $skinFitGuidance,
            'sensitive_status' => $sensitiveStatus,
            'sensitive_guidance' => $sensitiveGuidance,
            'fragrance' => $fragrance,
            'actives' => $actives,
            'summary_html' => $summaryHtml,
        ];
    }

    /**
     * Get structured 4-tile usage instructions (Timing, Amount, Method, Gradual Introduction).
     */
    public function getUsageInstructionsAttribute(): ?array
    {
        ['raw' => $raw, 'tiles' => $tiles] = $this->resolveRawUsageSource();
        if (empty($raw) && empty($tiles)) {
            return null;
        }

        $isAr = $this->isArabicLocale();
        $fields = $this->buildLocalizedUsageFields($raw, $tiles, $isAr);
        $localizedTiles = $fields['localized_tiles'];
        unset($fields['localized_tiles']);

        $caution = $isAr
            ? ($raw['caution'] ?? $this->limitations_notes)
            : ($this->firstNonArabic([$raw['caution_en'] ?? null, $raw['caution'] ?? null]) ?: $this->english_limitations_notes);

        return array_merge($fields, [
            'caution' => $caution,
            'tiles' => $localizedTiles,
            'benefits' => $this->key_benefits,
            'verdict' => $this->localizeVerdict($raw['verdict'] ?? null, $isAr),
            'raw_how_to_use' => $this->how_to_use,
        ]);
    }

    /**
     * Get complementary routine products with batch database lookup (prevents N+1 loop).
     */
    public function getComplementaryRoutineAttribute(): array
    {
        $isAr = $this->isArabicLocale();
        $items = $this->complementary_routine_json;
        if (empty($items) || !is_array($items)) {
            return [];
        }

        // Batch fetch all items at once in a single SQL query instead of looping Product::find
        $skus = array_filter(array_column($items, 'sku'));
        $ids = array_filter(array_column($items, 'product_id'));

        $fetchedProducts = Product::with(['brand', 'subCategory', 'category'])
            ->where(function ($q) use ($ids, $skus) {
                if (!empty($ids)) $q->whereIn('id', $ids);
                if (!empty($skus)) $q->orWhereIn('sku', $skus);
            })
            ->get();

        $productsById = $fetchedProducts->keyBy('id');
        $productsBySku = $fetchedProducts->keyBy('sku');

        $result = [];
        $categoryEnMap = [
            'غسول' => 'Cleanser',
            'تونر' => 'Toner',
            'إسنس' => 'Essence',
            'سيروم' => 'Serum',
            'سيروم وأمبول' => 'Serum',
            'مرطب' => 'Moisturizer',
            'واقي شمس' => 'Sunscreen',
            'ماسك' => 'Mask',
            'قناع' => 'Mask',
            'مقشر' => 'Exfoliator',
            'مقشّر' => 'Exfoliator',
            'عناية بالعين' => 'Eye Care',
            'عناية بالجسم' => 'Body Care',
            'منتج مكمل' => 'Complementary Product',
        ];

        foreach ($items as $item) {
            $dbProduct = null;
            if (!empty($item['product_id']) && isset($productsById[$item['product_id']])) {
                $dbProduct = $productsById[$item['product_id']];
            } elseif (!empty($item['sku']) && isset($productsBySku[$item['sku']])) {
                $dbProduct = $productsBySku[$item['sku']];
            }

            if (!$dbProduct || $dbProduct->id === $this->id) {
                continue;
            }

            $rawCat = (string) ($item['category'] ?? ($dbProduct->subCategory?->name_ar ?? 'منتج مكمل'));
            $finalCat = $isAr
                ? $rawCat
                : ($this->firstNonArabic([
                    $item['category_en'] ?? null,
                    $categoryEnMap[trim($rawCat)] ?? null,
                    $dbProduct->subCategory?->name_en,
                    $dbProduct->category?->name_en,
                    $rawCat,
                ]) ?: 'Skincare Step');

            $rawReasonAr = (string) ($item['reason'] ?? ($item['why'] ?? ''));
            $rawReasonEn = (string) ($item['reason_en'] ?? ($item['why_en'] ?? ''));
            $finalReason = $isAr
                ? ($rawReasonAr ?: $rawReasonEn)
                : ($this->firstNonArabic([$rawReasonEn, $rawReasonAr]) ?: 'Complements your daily skincare routine.');

            $result[] = [
                'id' => $dbProduct->id,
                'sku' => $dbProduct->sku,
                'brand' => $dbProduct->brand?->name ?? ($item['brand'] ?? ''),
                'name' => $dbProduct->fixedFullName(),
                'size' => $dbProduct->size ?: ($item['size'] ?? ''),
                'category' => $finalCat,
                'reason' => $finalReason,
                'optional' => (bool) ($item['optional'] ?? false),
                'price' => (float) $dbProduct->price,
                'image' => $dbProduct->image_path,
            ];
        }

        return $result;
    }

    /**
     * Get bilingual product FAQs from JSON.
     */
    public function getProductFaqsAttribute(): ?array
    {
        if (empty($this->product_faqs_json) || !is_array($this->product_faqs_json)) {
            return null;
        }

        $isAr = $this->isArabicLocale();
        $formatted = [];

        foreach ($this->product_faqs_json as $faq) {
            if (!is_array($faq)) {
                continue;
            }

            if ($isAr) {
                $question = trim((string) ($faq['question'] ?? ''));
                $answer = trim((string) ($faq['answer'] ?? ''));
            } else {
                $question = $this->firstNonArabic([$faq['question_en'] ?? null, $faq['question'] ?? null]) ?? '';
                $answer = $this->firstNonArabic([$faq['answer_en'] ?? null, $faq['answer'] ?? null]) ?? '';
            }

            if ($question !== '' && $answer !== '') {
                $formatted[] = [
                    'id' => $faq['id'] ?? 'custom',
                    'question' => $question,
                    'answer' => $answer,
                ];
            }
        }

        return $formatted;
    }

    /**
     * Get KCODE Formula Evaluation criteria modal content.
     */
    public function getFormulaEvaluationInfoAttribute(): array
    {
        $isAr = $this->isArabicLocale();

        $badge = Setting::get(
            'formula_eval_badge',
            $isAr ? 'معايير KCODE' : 'KCODE Standards'
        );
        $title = Setting::get(
            'formula_eval_title',
            $isAr ? 'كيف نقيّم التركيبة في KCODE؟' : 'How We Evaluate Formulations at KCODE'
        );
        $description = Setting::get(
            'formula_eval_subtitle',
            Setting::get(
                'formula_eval_desc',
                $isAr
                    ? 'وراء كل خلاصة، مراجعة تبدأ من تركيبة المنتج نفسه.'
                    : 'Behind every conclusion, a review that begins with the product formula itself.'
            )
        );
        $footerNote = Setting::get(
            'formula_eval_footer_note',
            $isAr
                ? 'نقيّم كل منتج بمعاييرنا نفسها؛ شهرته ووجوده في المتجر لا يمنحانه توصية تلقائية.'
                : 'Every product is evaluated against the same criteria; fame or availability in store does not grant an automatic recommendation.'
        );

        $pillars = [
            [
                'id' => 1,
                'title_key' => 'formula_eval_step1_title',
                'desc_key' => 'formula_eval_step1_desc',
                'ar_title' => 'نبدأ من العبوة',
                'en_title' => 'We start from the packaging',
                'ar_desc' => 'نراجع قائمة المكونات المدوّنة على المنتج، ونقرأها كاملة لفهم ما تحتويه التركيبة فعلًا.',
                'en_desc' => 'We review the full ingredient list printed on the product to understand what the formula actually contains.',
                'icon' => 'sparkles',
            ],
            [
                'id' => 2,
                'title_key' => 'formula_eval_step2_title',
                'desc_key' => 'formula_eval_step2_desc',
                'ar_title' => 'وجود المكوّن وحده لا يكفي',
                'en_title' => 'Ingredient presence alone is not enough',
                'ar_desc' => 'ندرس وظيفة المكوّن وترتيبه في القائمة، ونأخذ النسب المعلنة في الحسبان لتحديد دوره الفعلي.',
                'en_desc' => 'We evaluate ingredient position, function, and concentration to judge whether it supports the primary goal.',
                'icon' => 'shield-check',
            ],
            [
                'id' => 3,
                'title_key' => 'formula_eval_step3_title',
                'desc_key' => 'formula_eval_step3_desc',
                'ar_title' => 'نقرأ التركيبة ككل',
                'en_title' => 'We read the formula as a whole',
                'ar_desc' => 'نربط المكونات ببعضها وباحتياجات البشرة، ونوازن بين ما تدعمه التركيبة وما يستدعي الانتباه.',
                'en_desc' => 'We connect ingredients with skin needs, balancing efficacy and tolerance into a clear insight.',
                'icon' => 'droplet',
            ],
        ];

        $items = array_map(function (array $pillar) use ($isAr) {
            return [
                'id' => $pillar['id'],
                'title' => Setting::get($pillar['title_key'], $isAr ? $pillar['ar_title'] : $pillar['en_title']),
                'description' => Setting::get($pillar['desc_key'], $isAr ? $pillar['ar_desc'] : $pillar['en_desc']),
                'icon' => $pillar['icon'],
            ];
        }, $pillars);

        return [
            'badge' => $badge,
            'title' => $title,
            'description' => $description,
            'items' => $items,
            'footer_note' => $footerNote,
            'footer_note_ar' => $footerNote,
        ];
    }
}
