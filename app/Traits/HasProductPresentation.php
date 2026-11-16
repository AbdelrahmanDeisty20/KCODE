<?php

namespace App\Traits;

use App\Models\Coupon;
use App\Models\Product;
use App\Models\Setting;
use App\Services\SettingService;

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
        // 1. Check if an active general coupon is configured in Dashboard > Coupons
        $coupon = Coupon::where('is_active', true)
            ->where('is_general', true)
            ->where(function ($q) {
                $q->whereNull('start_date')->orWhere('start_date', '<=', now());
            })
            ->where(function ($q) {
                $q->whereNull('end_date')->orWhere('end_date', '>=', now());
            })
            ->first();

        if ($coupon) {
            $isAr = app()->getLocale() == 'ar';
            $defaultText = $coupon->discount_type === 'percentage'
                ? ($isAr ? "خصم " . (int)$coupon->discount_value . "% على طلبك الأول {$coupon->code}" : "Get " . (int)$coupon->discount_value . "% off your first order {$coupon->code}")
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
            $text = Setting::get('promo_coupon_text', "خصم 10% على طلبك الأول {$code}");
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
            'text' => app()->getLocale() == 'ar' ? 'خصم 10% على طلبك الأول KCODE10' : '10% off your first order with KCODE10',
        ];
    }

    /**
     * Build unified specifications table (Brand, Type, Size, Texture, Fragrance, INCI).
     */
    public function getSpecificationsAttribute(): array
    {
        $type = $this->role_ar ?: ($this->role ?: ($this->subCategory?->name ?? ($this->category?->name ?? '')));
        if (app()->getLocale() === 'ar' || request()->header('lang') === 'ar') {
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
        }

        $fragrance = $this->fragrance;
        if (empty($fragrance) || $fragrance === 'None' || $fragrance === 'none') {
            $fragrance = 'دون عطر مضاف أو زيوت عطرية';
        }

        $texture = (app()->getLocale() == 'ar' ? ($this->texture_ar ?: $this->texture) : ($this->texture_en ?: $this->texture));
        if (empty($texture)) {
            $texture = 'خفيف وسريع الامتصاص';
        }

        return [
            'brand' => $this->brand?->name ?? '',
            'full_name' => $this->name_en ?: $this->name,
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

        $isAr = app()->getLocale() == 'ar';
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
            'صباحاً' => 'Morning',
            'مساءً' => 'Evening',
        ];

        $formattedSteps = array_map(function ($s) use ($stepNumber, $isAr, $stepTitleEnMap, $subtitleEnMap) {
            $stepIdx = (int) ($s['step'] ?? 1);
            $isCurrent = $stepIdx === (int) $stepNumber;
            $rawTitle = $s['title'] ?? '';
            $rawSubtitle = $isCurrent ? ($s['subtitle'] ?? 'هذا المنتج') : ($s['subtitle'] ?? null);

            $enTitle = !empty($s['title_en']) ? $s['title_en'] : ($stepTitleEnMap[$stepIdx] ?? $rawTitle);
            $enSubtitle = !empty($s['subtitle_en'])
                ? $s['subtitle_en']
                : ($rawSubtitle ? ($subtitleEnMap[$rawSubtitle] ?? $rawSubtitle) : null);

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
                : $headerStepTitleEn,
            'steps' => $formattedSteps,
        ];
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

        $role = strtolower($this->role_ar ?? ($this->role ?? ''));
        if (str_contains($role, 'غسول') || str_contains($role, 'تنظيف')) return 1;
        if (str_contains($role, 'تونر') || str_contains($role, 'إسنس')) return 2;
        if (str_contains($role, 'سيروم') || str_contains($role, 'علاج') || str_contains($role, 'أمبول')) return 3;
        if (str_contains($role, 'مرطب') || str_contains($role, 'كريم')) return 4;
        if (str_contains($role, 'واقي') || str_contains($role, 'شمس')) return 5;

        return 3;
    }

    /**
     * Get structured 4-tile usage instructions (Timing, Amount, Method, Gradual Introduction).
     */
    public function getUsageInstructionsAttribute(): ?array
    {
        $raw = is_array($this->usage_instructions_json) ? $this->usage_instructions_json : [];
        $tiles = $raw['tiles'] ?? [];

        // Fallback to catalog data only if tiles are completely empty in DB
        if (empty($tiles) && !empty($this->sku)) {
            static $catalogProducts = null;
            if ($catalogProducts === null) {
                $path = database_path('seeders/data/r479_products.json');
                $catalogProducts = file_exists($path) ? json_decode(file_get_contents($path), true) : [];
            }
            if (!empty($catalogProducts[$this->sku]['usage_tiles'])) {
                $tiles = $catalogProducts[$this->sku]['usage_tiles'];
            }
        }

        if (!empty($raw) || !empty($tiles)) {
            $t0 = $tiles[0]['text'] ?? ($tiles[0]['value'] ?? ($raw['timing'] ?? $this->usage_frequency));
            $t1 = $tiles[1]['text'] ?? ($tiles[1]['value'] ?? ($raw['amount'] ?? 'بضع قطرات'));
            $t2 = $tiles[2]['text'] ?? ($tiles[2]['value'] ?? ($raw['application_method'] ?? 'ربّت بلطف'));
            $t3 = $tiles[3]['text'] ?? ($tiles[3]['value'] ?? ($raw['gradual_start'] ?? 'ابدأ تدريجيًا'));

            if (app()->getLocale() == 'ar') {
                return [
                    'timing_label' => $tiles[0]['label'] ?? ($raw['timing_label'] ?? 'التوقيت'),
                    'timing' => $t0,
                    'timing_note' => $tiles[0]['note'] ?? ($raw['timing_note'] ?? 'بعد التدرّج في الاستخدام.'),
                    'amount_label' => $tiles[1]['label'] ?? ($raw['amount_label'] ?? 'الكمية'),
                    'amount' => $t1,
                    'amount_note' => $tiles[1]['note'] ?? ($raw['amount_note'] ?? 'على المناطق المستهدفة.'),
                    'application_label' => $tiles[2]['label'] ?? ($raw['application_label'] ?? 'طريقة الوضع'),
                    'application_method' => $t2,
                    'application_note' => $tiles[2]['note'] ?? ($raw['application_note'] ?? 'حتى الامتصاص، قبل المرطب.'),
                    'gradual_start_label' => $tiles[3]['label'] ?? ($raw['gradual_start_label'] ?? 'التدرّج'),
                    'gradual_start' => $t3,
                    'gradual_start_note' => $tiles[3]['note'] ?? ($raw['gradual_start_note'] ?? 'وزِد التكرار حسب تحمّل بشرتك.'),
                    'caution' => $raw['caution'] ?? $this->limitations_notes,
                    'tiles' => $tiles,
                    'benefits' => $raw['benefits'] ?? null,
                    'verdict' => $raw['verdict'] ?? null,
                    'raw_how_to_use' => $this->how_to_use,
                ];
            }

            return [
                'timing_label' => !empty($tiles[0]['label']) ? $tiles[0]['label'] : (!empty($raw['timing_label_en']) ? $raw['timing_label_en'] : 'Timing'),
                'timing' => !empty($raw['timing_en']) ? $raw['timing_en'] : (!empty($tiles[0]['text']) ? $tiles[0]['text'] : $this->usage_frequency),
                'timing_note' => !empty($raw['timing_note_en']) ? $raw['timing_note_en'] : 'After gradually introducing into your routine.',
                'amount_label' => !empty($tiles[1]['label']) ? $tiles[1]['label'] : (!empty($raw['amount_label_en']) ? $raw['amount_label_en'] : 'Amount'),
                'amount' => !empty($raw['amount_en']) ? $raw['amount_en'] : (!empty($tiles[1]['text']) ? $tiles[1]['text'] : 'A few drops / Moderate amount'),
                'amount_note' => !empty($raw['amount_note_en']) ? $raw['amount_note_en'] : 'On targeted areas or entire face.',
                'application_label' => !empty($tiles[2]['label']) ? $tiles[2]['label'] : (!empty($raw['application_label_en']) ? $raw['application_label_en'] : 'Application'),
                'application_method' => !empty($raw['application_method_en']) ? $raw['application_method_en'] : (!empty($tiles[2]['text']) ? $tiles[2]['text'] : 'Gently pat'),
                'application_note' => !empty($raw['application_note_en']) ? $raw['application_note_en'] : 'Until fully absorbed, before moisturizer.',
                'gradual_start_label' => !empty($tiles[3]['label']) ? $tiles[3]['label'] : (!empty($raw['gradual_start_label_en']) ? $raw['gradual_start_label_en'] : 'Introduction'),
                'gradual_start' => !empty($raw['gradual_start_en']) ? $raw['gradual_start_en'] : (!empty($tiles[3]['text']) ? $tiles[3]['text'] : 'Start gradually'),
                'gradual_start_note' => !empty($raw['gradual_start_note_en']) ? $raw['gradual_start_note_en'] : 'Increase frequency according to your skin tolerance.',
                'caution' => $this->limitations_notes,
                'tiles' => $tiles,
                'benefits' => $raw['benefits'] ?? null,
                'verdict' => $raw['verdict'] ?? null,
                'raw_how_to_use' => $this->how_to_use,
            ];
        }

        return null;
    }

    /**
     * Get complementary routine products with batch database lookup (prevents N+1 loop).
     */
    public function getComplementaryRoutineAttribute(): array
    {
        $isAr = app()->getLocale() == 'ar';
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
            'منتج مكمل' => 'Complementary Product',
        ];

        foreach ($items as $item) {
            $dbProduct = null;
            if (!empty($item['product_id']) && isset($productsById[$item['product_id']])) {
                $dbProduct = $productsById[$item['product_id']];
            } elseif (!empty($item['sku']) && isset($productsBySku[$item['sku']])) {
                $dbProduct = $productsBySku[$item['sku']];
            }

            if ($dbProduct && $dbProduct->id === $this->id) {
                continue;
            }

            $rawCat = $item['category'] ?? ($dbProduct?->subCategory?->name_ar ?? 'منتج مكمل');
            $rawReason = $item['reason'] ?? ($item['why'] ?? '');

            $finalCat = $isAr
                ? $rawCat
                : (!empty($item['category_en']) ? $item['category_en'] : ($categoryEnMap[$rawCat] ?? ($dbProduct?->category?->name_en ?? $rawCat)));

            if ($dbProduct) {
                $result[] = [
                    'id' => $dbProduct->id,
                    'sku' => $dbProduct->sku,
                    'brand' => $dbProduct->brand?->name ?? ($item['brand'] ?? ''),
                    'name' => $dbProduct->name,
                    'size' => $dbProduct->size ?: ($item['size'] ?? ''),
                    'category' => $finalCat,
                    'reason' => $rawReason,
                    'optional' => (bool) ($item['optional'] ?? false),
                    'price' => (float) $dbProduct->price,
                    'image' => $dbProduct->image_path,
                ];
            }
        }

        return $result;
    }

    /**
     * Get bilingual product FAQs from JSON.
     */
    public function getProductFaqsAttribute(): ?array
    {
        if (!empty($this->product_faqs_json) && is_array($this->product_faqs_json)) {
            $isAr = app()->getLocale() == 'ar';
            return array_map(function ($faq) use ($isAr) {
                return [
                    'id' => $faq['id'] ?? 'custom',
                    'question' => $isAr ? ($faq['question'] ?? '') : ($faq['question_en'] ?? ($faq['question'] ?? '')),
                    'answer' => $isAr ? ($faq['answer'] ?? '') : ($faq['answer_en'] ?? ($faq['answer'] ?? '')),
                ];
            }, $this->product_faqs_json);
        }
        return null;
    }

    /**
     * Get KCODE Formula Evaluation criteria modal content.
     */
    public function getFormulaEvaluationInfoAttribute(): array
    {
        $isAr = app()->getLocale() == 'ar';
        return [
            'badge' => Setting::get('formula_eval_badge', 'معايير KCODE'),
            'title' => Setting::get('formula_eval_title', 'كيف نقيّم التركيبة في KCODE؟'),
            'description' => Setting::get('formula_eval_desc', 'فحص دقيق للمكونات وتركيزاتها وملاءمتها لاحتياجات البشرة الفعلية.'),
            'items' => [
                [
                    'id' => 1,
                    'title' => 'فاعلية المكونات النشطة',
                    'description' => 'التأكد من وجود المواد الفعالة بتركيزات مثبتة علمياً وقادرة على إحداث فارق حقيقي.',
                    'icon' => 'sparkles',
                ],
                [
                    'id' => 2,
                    'title' => 'توافق وسلامة التركيبة',
                    'description' => 'مراجعة خلو المستحضر من المواد المحسسة أو المهيجة للبشرة الحساسة.',
                    'icon' => 'shield-check',
                ],
                [
                    'id' => 3,
                    'title' => 'توازن الترطيب والحاجز الواقي',
                    'description' => 'ضمان احتواء التركيبة على عناصر تعزز حاجز البشرة وتحبس الترطيب.',
                    'icon' => 'droplet',
                ],
            ],
            'footer_note_ar' => Setting::get('formula_eval_footer_note', 'نقيّم كل منتج بمعاييرنا نفسها؛ شهرته ووجوده في المتجر لا يمنحانه توصية تلقائية.'),
        ];
    }
}
