<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class Product extends Model
{
    protected $fillable = [
        'name_ar',
        'name_en',
        'description_ar',
        'description_en',
        'category_id',
        'sub_category_id',
        'brand_id',
        'price',
        'size',
        'stock',
        'is_best_seller',
        'sales_count',
        'short_name_ar',
        'short_name_en',
        'brief_insight_ar',
        'image',
        'sku',
        'barcode',
        'country_of_origin_ar',
        'ingredients_ar',
        'ingredients_en',
        'how_to_use_ar',
        'how_to_use_en',
        'status',
        'sensitive_eligible',
        // Product Details
        'texture_ar',
        'texture_en',
        'why_kcode_ar',
        'why_kcode_en',
        'role_ar',
        'usage_frequency_ar',
        'active_strength_level',
        'safety_notes_ar',
        'safety_notes_en',
        'limitations_notes_ar',
        'ar_key_benefits',
        'en_key_benefits',
        // Dynamic PDP Fields
        'routine_step_number',
        'routine_step_title_ar',
        'routine_steps_json',
        'usage_instructions_json',
        'complementary_routine_json',
        'product_faqs_json',
        // SEO Fields
        'ar_product_title_seo',
        'en_product_title_seo',
        'en_short_hook',
        'seo_meta_title_ar',
        'meta_description_en',
        'meta_description_ar',
        'primary_keyword_en',
        'primary_keyword_ar',
        'secondary_keywords_en',
        'secondary_keywords_ar',
        'final_url_slug',
        'image_alt_en',
        'image_alt_ar',
        'og_title_ar',
        'og_description_en',
        'og_description_ar',
        'pdp_headline_en',
        'above_fold_hook_en',
        'keywords',
    ];

    protected $casts = [
        'sensitive_eligible' => 'boolean',
        'is_best_seller' => 'boolean',
        'price' => 'float',
        'stock' => 'integer',
        'routine_step_number' => 'integer',
        'routine_steps_json' => 'array',
        'usage_instructions_json' => 'array',
        'complementary_routine_json' => 'array',
        'product_faqs_json' => 'array',
        'ar_key_benefits' => 'array',
        'en_key_benefits' => 'array',
    ];

    public function marketingDetail()
    {
        return $this->hasOne(ProductMarketingDetail::class);
    }

    public function recommendationRule()
    {
        return $this->hasOne(ProductRecommendationRule::class);
    }

    public function audit()
    {
        return $this->hasOne(ProductAudit::class);
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function subCategory()
    {
        return $this->belongsTo(SubCategory::class, 'sub_category_id');
    }

    public function brand()
    {
        return $this->belongsTo(Brand::class);
    }

    public function getImagePathAttribute()
    {
        if (!$this->image)
            return null;
        if (filter_var($this->image, FILTER_VALIDATE_URL))
            return $this->image;

        $path = ltrim(preg_replace('/^(storage\/)?(app\/public\/)?/', '', $this->image), '/');
        $path = ltrim(preg_replace('/^products\//i', '', $path), '/');
        $path = 'products/' . $path;

        return \Illuminate\Support\Facades\Storage::disk('public')->url($path);
    }

    public function getNameAttribute($value)
    {
        if (app()->getLocale() == 'ar') {
            return $this->name_ar ?: $this->name_en;
        }
        return $this->name_en ?: $this->name_ar;
    }

    public function getDescriptionAttribute($value)
    {
        if (app()->getLocale() == 'ar') {
            return $this->description_ar ?: $this->description_en;
        }
        return $this->description_en ?: $this->description_ar;
    }

    public function getShortNameAttribute()
    {
        if (app()->getLocale() == 'ar') {
            return $this->short_name_ar ?: $this->short_name_en;
        }
        return $this->short_name_en ?: $this->short_name_ar;
    }

    public function getIngredientsAttribute()
    {
        if (app()->getLocale() == 'ar') {
            return $this->ingredients_ar ?: $this->ingredients_en;
        }
        return $this->ingredients_en ?: $this->ingredients_ar;
    }

    public function getHowToUseAttribute()
    {
        if (app()->getLocale() == 'ar') {
            return $this->how_to_use_ar ?: $this->how_to_use_en;
        }
        return $this->how_to_use_en ?: $this->how_to_use_ar;
    }

    public function getTextureAttribute()
    {
        return app()->getLocale() == 'ar' ? $this->texture_ar : $this->texture_en;
    }

    public function getWhyKcodeAttribute()
    {
        return app()->getLocale() == 'ar' ? $this->why_kcode_ar : $this->why_kcode_en;
    }

    public function getSafetyNotesAttribute()
    {
        return app()->getLocale() == 'ar' ? $this->safety_notes_ar : $this->safety_notes_en;
    }

    public function getSensitiveStatusAttribute(): string
    {
        if (!empty($this->usage_instructions_json['verdict']['sensitive_status'])) {
            return (string) $this->usage_instructions_json['verdict']['sensitive_status'];
        }
        return $this->sensitive_eligible ? 'مناسب للبشرة الحساسة' : 'غير مناسب للبشرة الحساسة';
    }

    public function getSensitiveGuidanceAttribute(): ?string
    {
        if (!empty($this->usage_instructions_json['verdict']['sensitive_guidance'])) {
            return (string) $this->usage_instructions_json['verdict']['sensitive_guidance'];
        }
        return null;
    }

    public function getFragranceAttribute(): ?string
    {
        if (!empty($this->usage_instructions_json['verdict']['fragrance'])) {
            return (string) $this->usage_instructions_json['verdict']['fragrance'];
        }
        return 'دون عطر مضاف';
    }

    public function getTrustBadgesAttribute(): array
    {
        return app(\App\Services\SettingService::class)->getAssurances()['data'] ?? [];
    }

    public function getPromoCouponAttribute(): ?array
    {
        // 1. Check if an active general coupon is configured in Dashboard > Coupons
        $coupon = \App\Models\Coupon::where('is_active', true)
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
        $code = \App\Models\Setting::get('promo_coupon_code');
        if (!empty($code)) {
            $text = \App\Models\Setting::get('promo_coupon_text', "خصم 10% على طلبك الأول {$code}");
            return [
                'code' => $code,
                'text' => $text,
            ];
        }

        // 3. Check if explicitly disabled in Dashboard
        $enabled = \App\Models\Setting::get('promo_coupon_enabled', '1');
        if ($enabled === '0' || $enabled === false) {
            return null;
        }

        // 4. Default R479 fallback
        return [
            'code' => 'KCODE10',
            'text' => app()->getLocale() == 'ar' ? 'خصم 10% على طلبك الأول KCODE10' : '10% off your first order with KCODE10',
        ];
    }

    public function getSpecificationsAttribute(): array
    {
        return [
            'brand' => $this->brand?->name ?? '',
            'full_name' => $this->name_en ?: $this->name,
            'barcode' => $this->barcode,
            'type' => $this->role_ar ?: ($this->role ?: ($this->subCategory?->name ?? ($this->category?->name ?? ''))),
            'size' => $this->size,
            'texture' => (app()->getLocale() == 'ar' ? ($this->texture_ar ?: $this->texture) : ($this->texture_en ?: $this->texture)),
            'fragrance' => $this->fragrance,
            'ingredients' => $this->ingredients,
        ];
    }

    public function getKeyBenefitsAttribute()
    {
        $val = app()->getLocale() == 'ar' ? $this->ar_key_benefits : $this->en_key_benefits;
        if (is_string($val)) {
            $decoded = json_decode($val, true);
            if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
                return $decoded;
            }
        }
        if (empty($val) && !empty($this->usage_instructions_json['benefits'])) {
            return $this->usage_instructions_json['benefits'];
        }
        return $val;
    }

    public function getProductTitleSeoAttribute()
    {
        return app()->getLocale() == 'ar' ? $this->ar_product_title_seo : $this->en_product_title_seo;
    }

    public function getMetaDescriptionAttribute()
    {
        return app()->getLocale() == 'ar' ? $this->meta_description_ar : $this->meta_description_en;
    }

    public function getPrimaryKeywordAttribute()
    {
        return app()->getLocale() == 'ar' ? $this->primary_keyword_ar : $this->primary_keyword_en;
    }

    public function getSecondaryKeywordsAttribute()
    {
        return app()->getLocale() == 'ar' ? $this->secondary_keywords_ar : $this->secondary_keywords_en;
    }

    public function getImageAltAttribute()
    {
        return app()->getLocale() == 'ar' ? $this->image_alt_ar : $this->image_alt_en;
    }

    public function getOgDescriptionAttribute()
    {
        return app()->getLocale() == 'ar' ? $this->og_description_ar : $this->og_description_en;
    }

    public function getBriefInsightAttribute()
    {
        if (app()->getLocale() == 'ar') {
            return $this->brief_insight_ar;
        }
        if ($this->brief_insight_ar && !preg_match('/\p{Arabic}/u', $this->brief_insight_ar)) {
            return $this->brief_insight_ar;
        }
        return $this->en_key_benefits ?: ($this->description_en ?: 'Authentic Korean skincare formula designed for optimal skin compatibility.');
    }

    public function getCountryOfOriginAttribute()
    {
        return app()->getLocale() == 'ar' ? ($this->country_of_origin_ar ?: 'كوريا الجنوبية') : 'South Korea';
    }

    public function getRoleAttribute()
    {
        if (app()->getLocale() == 'ar') {
            return $this->role_ar ?: ($this->routine_step_title_ar ?: ($this->category?->name_ar ?? 'العناية بالبشرة'));
        }
        if ($this->role_ar && !preg_match('/\p{Arabic}/u', $this->role_ar)) {
            return $this->role_ar;
        }
        $catName = $this->category?->name_en ?: $this->subCategory?->name_en;
        if ($catName) {
            return "Targeted {$catName} Care";
        }
        return match ($this->detectRoutineStepNumber()) {
            1 => 'Gentle Facial Cleanser',
            2 => 'Hydrating & Balancing Toner',
            3 => 'Targeted Treatment Serum',
            4 => 'Barrier-Supporting Moisturizer',
            5 => 'Daily Broad-Spectrum Sunscreen',
            default => 'Daily Skincare Treatment',
        };
    }

    public function getLimitationsNotesAttribute()
    {
        if (app()->getLocale() == 'ar') {
            return $this->limitations_notes_ar;
        }
        if ($this->limitations_notes_ar && !preg_match('/\p{Arabic}/u', $this->limitations_notes_ar)) {
            return $this->limitations_notes_ar;
        }
        return 'Store in a cool, dry place away from direct sunlight. A patch test on a small area is recommended before full use.';
    }

    public function getUsageFrequencyAttribute()
    {
        $freq = $this->usage_frequency_ar ?: ($this->usage_instructions_json['timing'] ?? null);
        if (app()->getLocale() == 'ar') {
            return $freq ?: 'استخدام يومي';
        }
        if (!$freq) {
            return 'Daily use (Morning & Evening)';
        }
        if (!preg_match('/\p{Arabic}/u', $freq)) {
            return $freq;
        }
        if (str_contains($freq, 'أسبوع')) {
            return 'Weekly use (1-2 times per week)';
        }
        if (str_contains($freq, 'عند الحاجة') || str_contains($freq, 'موضع')) {
            return 'As needed on targeted areas';
        }
        if (str_contains($freq, 'مساء')) {
            return str_contains($freq, 'صباح') ? 'Daily use (Morning & Evening)' : 'Evening use only';
        }
        if (str_contains($freq, 'صباح')) {
            return 'Morning use';
        }
        return 'Daily use (Morning & Evening)';
    }

    public function getSeoMetaTitleAttribute()
    {
        return app()->getLocale() == 'ar'
            ? $this->seo_meta_title_ar
            : (($this->en_product_title_seo ?: $this->name_en) . ' | KCODE');
    }

    public function getOgTitleAttribute()
    {
        return app()->getLocale() == 'ar'
            ? $this->og_title_ar
            : ($this->en_product_title_seo ?: $this->name_en);
    }

    public function detectRoutineStepNumber(): int
    {
        if ($this->category_id) {
            return match ((int) $this->category_id) {
                1 => 1,
                2, 3 => 2,
                4 => 3,
                5 => 4,
                6 => 5,
                default => 3,
            };
        }

        $dbRoutineStep = $this->routines()->first()?->routineStep;
        if ($dbRoutineStep) {
            $stepName = strtolower($dbRoutineStep->name_en);
            if (str_contains($stepName, 'cleans')) return 1;
            if (str_contains($stepName, 'toner') || str_contains($stepName, 'essence')) return 2;
            if (str_contains($stepName, 'serum') || str_contains($stepName, 'ampoule')) return 3;
            if (str_contains($stepName, 'moistur') || str_contains($stepName, 'cream')) return 4;
            if (str_contains($stepName, 'sun')) return 5;
        }

        return 3;
    }

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

    public function getUsageInstructionsAttribute(): ?array
    {
        if (!empty($this->usage_instructions_json) && is_array($this->usage_instructions_json)) {
            $raw = $this->usage_instructions_json;
            $tiles = $raw['tiles'] ?? [];

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
                'timing_label' => 'Timing',
                'timing' => !empty($raw['timing_en']) ? $raw['timing_en'] : $this->usage_frequency,
                'timing_note' => !empty($raw['timing_note_en']) ? $raw['timing_note_en'] : 'After gradually introducing into your routine.',
                'amount_label' => 'Amount',
                'amount' => !empty($raw['amount_en']) ? $raw['amount_en'] : 'A few drops / Moderate amount',
                'amount_note' => !empty($raw['amount_note_en']) ? $raw['amount_note_en'] : 'On targeted areas or entire face.',
                'application_label' => 'Application',
                'application_method' => !empty($raw['application_method_en']) ? $raw['application_method_en'] : 'Gently pat',
                'application_note' => !empty($raw['application_note_en']) ? $raw['application_note_en'] : 'Until fully absorbed, before moisturizer.',
                'gradual_start_label' => 'Introduction',
                'gradual_start' => !empty($raw['gradual_start_en']) ? $raw['gradual_start_en'] : 'Start gradually',
                'gradual_start_note' => !empty($raw['gradual_start_note_en']) ? $raw['gradual_start_note_en'] : 'Increase frequency according to your skin tolerance.',
                'caution' => $this->limitations_notes,
                'tiles' => $raw['tiles'] ?? null,
                'benefits' => $raw['benefits'] ?? null,
                'verdict' => $raw['verdict'] ?? null,
                'raw_how_to_use' => $this->how_to_use,
            ];
        }

        return null;
    }

    public function getComplementaryRoutineAttribute(): array
    {
        $isAr = app()->getLocale() == 'ar';
        $items = $this->complementary_routine_json;
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

        $reasonEnMap = [
            'تنظيف لطيف قبل السيروم' => 'Gentle cleansing before serum',
            'تنظيف لطيف قبل استخدام المنتج' => 'Gentle cleansing before applying the product',
            'ترطيب خفيف قبل السيروم' => 'Light hydration before serum',
            'ترطيب خفيف وتهيئة البشرة' => 'Light hydration and skin prep',
            'ترطيب بعد السيروم' => 'Moisturizing after serum',
            'حبس الرطوبة وتدعيم الحاجز الجلدي' => 'Locks in moisture and supports the skin barrier',
            'حماية صباحية لإكمال الروتين' => 'Morning UV protection to complete the routine',
            'علاج مركز بعد التنظيف' => 'Targeted treatment after cleansing',
            'علاج مركز بعد التونر' => 'Targeted treatment after toner',
            'علاج مركز قبل المرطب' => 'Targeted treatment before moisturizer',
            'علاج مركز قبل واقي الشمس' => 'Targeted treatment before sunscreen',
        ];

        if (!empty($items) && is_array($items)) {
            foreach ($items as $item) {
                $dbProduct = null;
                if (!empty($item['product_id'])) {
                    $dbProduct = self::find($item['product_id']);
                } elseif (!empty($item['sku'])) {
                    $dbProduct = self::where('sku', $item['sku'])->first();
                }

                if ($dbProduct && $dbProduct->id === $this->id) {
                    continue;
                }

                $rawCat = $item['category'] ?? ($dbProduct?->subCategory?->name_ar ?? 'منتج مكمل');
                $rawReason = $item['reason'] ?? '';

                $finalCat = $isAr
                    ? $rawCat
                    : (!empty($item['category_en']) ? $item['category_en'] : ($categoryEnMap[$rawCat] ?? ($dbProduct?->category?->name_en ?? $rawCat)));
                $finalReason = $isAr
                    ? $rawReason
                    : (!empty($item['reason_en']) ? $item['reason_en'] : ($reasonEnMap[$rawReason] ?? $rawReason));

                if ($dbProduct) {
                    $result[] = [
                        'id' => $dbProduct->id,
                        'sku' => $dbProduct->sku,
                        'brand' => $dbProduct->brand?->name ?? ($item['brand'] ?? ''),
                        'name' => $dbProduct->name,
                        'size' => $dbProduct->size ?: ($item['size'] ?? ''),
                        'category' => $finalCat,
                        'reason' => $finalReason,
                        'optional' => (bool) ($item['optional'] ?? false),
                        'price' => (float) $dbProduct->price,
                        'image' => $dbProduct->image_path,
                    ];
                } else {
                    $result[] = [
                        'id' => $item['product_id'] ?? null,
                        'sku' => $item['sku'] ?? '',
                        'brand' => $item['brand'] ?? '',
                        'name' => $item['name'] ?? '',
                        'size' => $item['size'] ?? '',
                        'category' => $finalCat,
                        'reason' => $finalReason,
                        'optional' => (bool) ($item['optional'] ?? false),
                        'price' => (float) ($item['price'] ?? 0),
                        'image' => $item['image'] ?? null,
                    ];
                }
            }
        }

        if (!empty($result)) {
            return $result;
        }

        $stepsToFetch = [
            ['cat_ids' => [1], 'keyword' => 'cleans', 'label' => 'غسول', 'reason' => 'تنظيف لطيف قبل استخدام المنتج', 'optional' => false],
            ['cat_ids' => [2, 3], 'keyword' => 'toner', 'label' => 'تونر', 'reason' => 'ترطيب خفيف وتهيئة البشرة', 'optional' => true],
            ['cat_ids' => [5], 'keyword' => 'moistur', 'label' => 'مرطب', 'reason' => 'حبس الرطوبة وتدعيم الحاجز الجلدي', 'optional' => false],
            ['cat_ids' => [6], 'keyword' => 'sun', 'label' => 'واقي شمس', 'reason' => 'حماية صباحية لإكمال الروتين', 'optional' => false],
        ];

        $currentStep = $this->detectRoutineStepNumber();
        if ($currentStep === 1) {
            $stepsToFetch[0] = ['cat_ids' => [4], 'keyword' => 'serum', 'label' => 'سيروم', 'reason' => 'علاج مركز بعد التنظيف', 'optional' => false];
        } elseif ($currentStep === 2) {
            $stepsToFetch[1] = ['cat_ids' => [4], 'keyword' => 'serum', 'label' => 'سيروم', 'reason' => 'علاج مركز بعد التونر', 'optional' => false];
        } elseif ($currentStep === 4) {
            $stepsToFetch[2] = ['cat_ids' => [4], 'keyword' => 'serum', 'label' => 'سيروم', 'reason' => 'علاج مركز قبل المرطب', 'optional' => false];
        } elseif ($currentStep === 5) {
            $stepsToFetch[3] = ['cat_ids' => [4], 'keyword' => 'serum', 'label' => 'سيروم', 'reason' => 'علاج مركز قبل واقي الشمس', 'optional' => false];
        }

        foreach ($stepsToFetch as $config) {
            $compProduct = null;
            if ($this->brand_id) {
                $compProduct = self::where('id', '!=', $this->id)
                    ->where('brand_id', $this->brand_id)
                    ->where(function ($q) use ($config) {
                        $q->whereIn('category_id', $config['cat_ids'])
                          ->orWhereHas('subCategory', function ($sub) use ($config) {
                              $sub->where('name_en', 'LIKE', '%' . $config['keyword'] . '%');
                          });
                    })->first();
            }

            if (!$compProduct) {
                $compProduct = self::where('id', '!=', $this->id)
                    ->where(function ($q) use ($config) {
                        $q->whereIn('category_id', $config['cat_ids'])
                          ->orWhereHas('subCategory', function ($sub) use ($config) {
                              $sub->where('name_en', 'LIKE', '%' . $config['keyword'] . '%');
                          });
                    })->first();
            }

            if ($compProduct) {
                $result[] = [
                    'id' => $compProduct->id,
                    'sku' => $compProduct->sku,
                    'brand' => $compProduct->brand?->name ?? '',
                    'name' => $compProduct->name,
                    'size' => $compProduct->size ?: '',
                    'category' => $isAr ? $config['label'] : ($categoryEnMap[$config['label']] ?? $config['label']),
                    'reason' => $isAr ? $config['reason'] : ($reasonEnMap[$config['reason']] ?? $config['reason']),
                    'optional' => $config['optional'],
                    'price' => (float) $compProduct->price,
                    'image' => $compProduct->image_path,
                ];
            }
        }

        return $result;
    }

    public function getProductFaqsAttribute(): ?array
    {
        if (!empty($this->product_faqs_json) && is_array($this->product_faqs_json)) {
            $isAr = app()->getLocale() == 'ar';

            $enFaqsMap = [
                'target' => [
                    'question' => 'Does it target post-acne marks or active breakouts?',
                    'answer' => 'We primarily evaluate it for dark spots and uneven skin tone, including post-blemish marks. Active breakouts require targeted blemish care, as addressing marks alone does not treat the root cause of breakouts.',
                ],
                'pairing' => [
                    'question' => 'Is it suitable if I am already using another brightening product?',
                    'answer' => 'It depends on the other product’s active ingredients, concentration, and your skin’s tolerance. Sharing the same goal is not enough to combine two products safely, as layering similar actives may duplicate the role or increase irritation.',
                ],
                'moisturizer' => [
                    'question' => 'Does it replace a moisturizer?',
                    'answer' => 'Hydration here plays a supportive role alongside targeted care. Follow with a moisturizer suited to your skin’s needs once the product has absorbed.',
                ],
                'results' => [
                    'question' => 'When can I expect visible improvement?',
                    'answer' => 'Improvement is gradual and varies depending on concern depth and skin response. Track progress with consistent use and daily sun protection; there is no single timeline that applies to everyone.',
                ],
            ];

            return array_map(function ($faq) use ($isAr, $enFaqsMap) {
                $faqId = $faq['id'] ?? 'custom';
                $qAr = $faq['question'] ?? '';
                $aAr = $faq['answer'] ?? '';

                if ($isAr) {
                    return [
                        'id' => $faqId,
                        'question' => $qAr,
                        'answer' => $aAr,
                    ];
                }

                $qEn = !empty($faq['question_en']) ? $faq['question_en'] : ($enFaqsMap[$faqId]['question'] ?? $qAr);
                $aEn = !empty($faq['answer_en']) ? $faq['answer_en'] : ($enFaqsMap[$faqId]['answer'] ?? $aAr);

                return [
                    'id' => $faqId,
                    'question' => $qEn,
                    'answer' => $aEn,
                ];
            }, $this->product_faqs_json);
        }

        return null;
    }

    public function getFormulaEvaluationInfoAttribute(): array
    {
        return [
            'title_ar' => Setting::get('formula_eval_title', 'كيف نقيّم التركيبة في KCODE؟'),
            'subtitle_ar' => Setting::get('formula_eval_subtitle', 'وراء كل خلاصة، مراجعة تبدأ من تركيبة المنتج نفسه.'),
            'steps' => [
                [
                    'title_ar' => Setting::get('formula_eval_step1_title', 'نبدأ من العبوة'),
                    'text_ar'  => Setting::get('formula_eval_step1_desc', 'نراجع قائمة المكونات المدوّنة على المنتج، ونقرأها كاملة لفهم ما تحتويه التركيبة فعلًا، بدل الاكتفاء بالمكوّن الذي يبرزه الإعلان.'),
                ],
                [
                    'title_ar' => Setting::get('formula_eval_step2_title', 'وجود المكوّن وحده لا يكفي'),
                    'text_ar'  => Setting::get('formula_eval_step2_desc', 'ندرس وظيفة المكوّن وترتيبه في القائمة، ونأخذ النسب المعلنة في الحسبان. ثم نقيّم دوره ضمن التركيبة: هل يدعم الهدف الأساسي للمنتج، أم يؤدي دورًا مساندًا؟'),
                ],
                [
                    'title_ar' => Setting::get('formula_eval_step3_title', 'نقرأ التركيبة ككل'),
                    'text_ar'  => Setting::get('formula_eval_step3_desc', 'نربط المكونات ببعضها وباحتياجات البشرة، ونوازن بين ما تدعمه التركيبة وما يستدعي الانتباه في ملاءمتها وتحمّلها. ثم نحوّل هذه القراءة إلى خلاصة واضحة تساعدك على فهم المنتج.'),
                ],
            ],
            'footer_note_ar' => Setting::get('formula_eval_footer_note', 'نقيّم كل منتج بمعاييرنا نفسها؛ شهرته ووجوده في المتجر لا يمنحانه توصية تلقائية.'),
        ];
    }

    public function images()
    {
        return $this->hasMany(ProductImage::class);
    }

    public function favorites()
    {
        return $this->hasMany(Favourite::class);
    }

    public function concerns()
    {
        return $this->belongsToMany(Concern::class, 'product_concerns');
    }

    public function skinTypes()
    {
        return $this->belongsToMany(SkinType::class, 'product_skin_types');
    }

    public function goals()
    {
        return $this->belongsToMany(RoutineGoal::class, 'product_goals', 'product_id', 'goal_id');
    }

    public function productSkinTypes()
    {
        return $this->hasMany(ProductSkinType::class, 'product_id');
    }

    public function productConcerns()
    {
        return $this->hasMany(ProductConcern::class);
    }

    public function productGoals()
    {
        return $this->hasMany(ProductGoal::class);
    }

    public function routines()
    {
        return $this->hasMany(ProductRoutine::class);
    }

    public function alternatives()
    {
        return $this->hasMany(ProductAlternative::class);
    }

    public function reviews()
    {
        return $this->hasMany(Review::class);
    }

    public function getAverageRatingAttribute()
    {
        return round($this->reviews()->avg('rating'), 1) ?? 0.0;
    }

    public function getNumReviewsAttribute()
    {
        return $this->reviews()->count();
    }

    public function scopeBestSeller($query)
    {
        return $query->where(function ($q) {
            $q->where('sales_count', '>=', 100)
              ->orWhere('is_best_seller', true);
        });
    }

    public function offers()
    {
        return $this->hasMany(Offer::class);
    }
}