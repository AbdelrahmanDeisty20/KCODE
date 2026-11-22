<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class Product extends Model
{
    use \App\Traits\HasProductPresentation;

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
        'brief_insight_en',
        'image',
        'sku',
        'barcode',
        'country_of_origin_ar',
        'country_of_origin_en',
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
        'role_en',
        'usage_frequency_ar',
        'usage_frequency_en',
        'active_strength_level',
        'safety_notes_ar',
        'safety_notes_en',
        'limitations_notes_ar',
        'limitations_notes_en',
        'ar_key_benefits',
        'en_key_benefits',
        // Dynamic PDP Fields
        'routine_step_number',
        'routine_step_title_ar',
        'routine_step_title_en',
        'routine_steps_json',
        'usage_instructions_json',
        'complementary_routine_json',
        'product_faqs_json',
        // SEO Fields
        'ar_product_title_seo',
        'en_product_title_seo',
        'en_short_hook',
        'seo_meta_title_ar',
        'seo_meta_title_en',
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
        'og_title_en',
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

    /**
     * Determine whether the active application locale is Arabic.
     */
    public function isArabicLocale(): bool
    {
        $locale = app()->getLocale() ?: (request()->header('lang') ?: 'ar');

        return strtolower(substr((string) $locale, 0, 2)) === 'ar';
    }

    /**
     * Check whether a string contains Arabic characters.
     */
    public function containsArabic(?string $text): bool
    {
        return !empty($text) && preg_match('/\p{Arabic}/u', $text) === 1;
    }

    /**
     * Return the first non-empty candidate string containing no Arabic characters.
     *
     * @param  array<int, mixed>  $candidates
     */
    protected function firstNonArabic(array $candidates): ?string
    {
        foreach ($candidates as $candidate) {
            if (!is_string($candidate)) {
                continue;
            }
            $trimmed = trim($candidate);
            if ($trimmed !== '' && !$this->containsArabic($trimmed)) {
                return $trimmed;
            }
        }

        return null;
    }

    /**
     * Keep displayed short product title fixed in English across all locales.
     */
    public function fixedShortName(): string
    {
        return !empty($this->short_name_en)
            ? $this->short_name_en
            : ($this->short_name ?: ($this->name_en ?: ($this->name ?: '')));
    }

    /**
     * Keep displayed full product title fixed in English across all locales.
     */
    public function fixedFullName(): string
    {
        return !empty($this->name_en)
            ? $this->name_en
            : ($this->name ?: $this->fixedShortName());
    }

    /**
     * Check if the authenticated Sanctum user has favorited this product (cached per HTTP request).
     */
    public function isFavoritedByCurrentUser(): bool
    {
        $userId = auth('sanctum')->id();
        if (!$userId) {
            return false;
        }

        if ($this->relationLoaded('favorites')) {
            return $this->favorites
                ->where('user_id', $userId)
                ->where('is_active', true)
                ->isNotEmpty();
        }

        $attrKey = "kcode_user_fav_ids_{$userId}";
        $request = request();
        $favMap = $request->attributes->get($attrKey);

        if (!is_array($favMap)) {
            $favMap = Favourite::where('user_id', $userId)
                ->where('is_active', true)
                ->pluck('product_id')
                ->flip()
                ->all();
            $request->attributes->set($attrKey, $favMap);
        }

        return isset($favMap[$this->id]);
    }

    public function getNameAttribute($value)
    {
        if ($this->isArabicLocale()) {
            return $this->name_ar ?: $this->name_en;
        }

        return $this->name_en ?: $this->name_ar;
    }

    public function getEnglishDescriptionAttribute(): string
    {
        $verdictNoteEn = is_array($this->usage_instructions_json)
            ? ($this->usage_instructions_json['verdict']['note_en'] ?? null)
            : null;

        return $this->firstNonArabic([
            $this->description_en,
            $this->brief_insight_en,
            $verdictNoteEn,
            $this->why_kcode_en,
            $this->pdp_headline_en,
            $this->en_short_hook,
        ]) ?: $this->english_brief_insight;
    }

    public function getDescriptionAttribute($value)
    {
        if ($this->isArabicLocale()) {
            return $this->description_ar ?: $this->description_en;
        }

        return $this->english_description;
    }

    public function getShortNameAttribute()
    {
        if (!empty($this->short_name_en)) {
            return $this->short_name_en;
        }

        if (!empty($this->short_name_ar)
            && $this->short_name_ar !== $this->brief_insight_ar
            && $this->short_name_ar !== $this->description_ar
            && mb_strlen($this->short_name_ar) < 60) {
            return $this->short_name_ar;
        }

        return $this->name_en ?: ($this->name_ar ?: '');
    }

    public function getIngredientsAttribute()
    {
        if ($this->isArabicLocale()) {
            return $this->ingredients_ar ?: $this->ingredients_en;
        }

        return $this->firstNonArabic([$this->ingredients_en, $this->ingredients_ar])
            ?: ($this->ingredients_en ?: $this->ingredients_ar);
    }

    public function getHowToUseAttribute()
    {
        if ($this->isArabicLocale()) {
            return $this->how_to_use_ar ?: $this->how_to_use_en;
        }

        $direct = $this->firstNonArabic([$this->how_to_use_en]);
        if ($direct !== null) {
            return $direct;
        }

        if (method_exists($this, 'formatEnglishHowToUseLines')) {
            $synthesized = $this->formatEnglishHowToUseLines();
            if ($synthesized !== null) {
                return $synthesized;
            }
        }

        return $this->how_to_use_en ?: $this->how_to_use_ar;
    }

    public function getEnglishTextureAttribute(): string
    {
        $direct = $this->firstNonArabic([$this->texture_en, $this->texture_ar]);
        if ($direct !== null) {
            return $direct;
        }

        $ar = trim((string) $this->texture_ar);
        $map = [
            'خفيف وسريع الامتصاص' => 'Lightweight & fast-absorbing',
            'جل' => 'Gel',
            'كريمي' => 'Creamy',
            'كريم' => 'Cream',
            'سائل' => 'Liquid',
            'خفيف' => 'Lightweight',
            'متوسط' => 'Medium',
            'مائي' => 'Watery',
            'لوشن' => 'Lotion',
            'سيروم' => 'Serum',
            'بلسم' => 'Balm',
            'زيت' => 'Oil',
            'رغوي' => 'Foaming',
            'ستيك' => 'Stick',
        ];

        foreach ($map as $keyword => $en) {
            if ($ar !== '' && str_contains($ar, $keyword)) {
                return $en;
            }
        }

        return 'Lightweight';
    }

    public function getTextureAttribute()
    {
        if ($this->isArabicLocale()) {
            return $this->texture_ar ?: $this->texture_en;
        }

        return $this->english_texture;
    }

    public function getEnglishWhyKcodeAttribute(): string
    {
        $direct = $this->firstNonArabic([
            $this->why_kcode_en,
            $this->description_en,
            $this->brief_insight_en,
        ]);

        if ($direct !== null) {
            return $direct;
        }

        $brand = $this->brand?->name_en ? "{$this->brand->name_en} " : '';
        $name = $this->fixedFullName();

        return "Authentic {$brand}{$name} selected by KCODE based on its ingredient profile and skin compatibility.";
    }

    public function getWhyKcodeAttribute()
    {
        if ($this->isArabicLocale()) {
            return $this->why_kcode_ar ?: ($this->description_ar ?: $this->why_kcode_en);
        }

        return $this->english_why_kcode;
    }

    public function getSafetyNotesAttribute()
    {
        if ($this->isArabicLocale()) {
            return $this->safety_notes_ar;
        }

        return $this->firstNonArabic([$this->safety_notes_en, $this->limitations_notes_en])
            ?: $this->english_limitations_notes;
    }

    protected function resolveRawArabicSensitiveStatus(): string
    {
        if (!empty($this->usage_instructions_json['verdict']['sensitive_status'])) {
            return (string) $this->usage_instructions_json['verdict']['sensitive_status'];
        }

        if (!empty($this->attributes['sensitive_status'])) {
            return (string) $this->attributes['sensitive_status'];
        }

        return $this->sensitive_eligible ? 'مناسب للبشرة الحساسة' : 'مناسب بشرط';
    }

    public function getEnglishSensitiveStatusAttribute(): string
    {
        $verdict = is_array($this->usage_instructions_json) ? ($this->usage_instructions_json['verdict'] ?? []) : [];

        $direct = $this->firstNonArabic([
            $verdict['sensitive_status_en'] ?? null,
            $verdict['sensitive_status'] ?? null,
        ]);

        if ($direct !== null) {
            return $direct;
        }

        $rawAr = $this->resolveRawArabicSensitiveStatus();
        if (str_contains($rawAr, 'غير مناسب')) {
            return str_contains($rawAr, 'خطوة أولى')
                ? 'Not suitable as a first step for sensitive skin'
                : 'Not suitable for sensitive skin';
        }
        if (str_contains($rawAr, 'بشرط')) {
            return 'Conditionally suitable';
        }

        return 'Suitable for sensitive skin';
    }

    public function getSensitiveStatusAttribute(): string
    {
        if ($this->isArabicLocale()) {
            return $this->resolveRawArabicSensitiveStatus();
        }

        return $this->english_sensitive_status;
    }

    public function getEnglishSensitiveGuidanceAttribute(): ?string
    {
        $verdict = is_array($this->usage_instructions_json) ? ($this->usage_instructions_json['verdict'] ?? []) : [];

        $direct = $this->firstNonArabic([
            $verdict['sensitive_guidance_en'] ?? null,
            $verdict['sensitive_guidance'] ?? null,
            $this->limitations_notes_en,
        ]);

        if ($direct !== null) {
            return $direct;
        }

        if (empty($verdict['sensitive_guidance'])) {
            return null;
        }

        $statusEn = $this->english_sensitive_status;
        if (str_starts_with($statusEn, 'Not suitable')) {
            return 'Contains potent active or exfoliating ingredients; avoid combining with strong actives and patch test before full use.';
        }
        if ($statusEn === 'Conditionally suitable') {
            return 'Conditionally suitable: introduce gradually on intact, non-irritated skin and patch test first. Stop use if irritation persists.';
        }

        return 'Formulated for gentle compatibility with sensitive skin. Avoid applying on broken or irritated skin, and discontinue if discomfort occurs.';
    }

    public function getSensitiveGuidanceAttribute(): ?string
    {
        if ($this->isArabicLocale()) {
            return !empty($this->usage_instructions_json['verdict']['sensitive_guidance'])
                ? (string) $this->usage_instructions_json['verdict']['sensitive_guidance']
                : null;
        }

        return $this->english_sensitive_guidance;
    }

    public function getEnglishFragranceAttribute(): string
    {
        $verdict = is_array($this->usage_instructions_json) ? ($this->usage_instructions_json['verdict'] ?? []) : [];

        $explicitEn = $this->firstNonArabic([$verdict['fragrance_en'] ?? null]);
        if ($explicitEn !== null) {
            return $explicitEn;
        }

        $raw = trim((string) ($verdict['fragrance'] ?? ''));
        if (strtolower($raw) === 'none') {
            return 'Fragrance & essential-oil free';
        }
        if ($raw !== '' && !$this->containsArabic($raw)) {
            return $raw;
        }
        if (str_contains($raw, 'زيوت عطرية')) {
            return 'Fragrance & essential-oil free';
        }
        if (str_contains($raw, 'دون عطر') || str_contains($raw, 'بدون عطر') || str_contains($raw, 'خال')) {
            return 'Fragrance-free';
        }
        if (str_contains($raw, 'شجرة الشاي') || str_contains($raw, 'زيت عطري')) {
            return 'Contains essential oils / botanical fragrance';
        }
        if (str_contains($raw, 'خفيف')) {
            return 'Lightly fragranced';
        }
        if (str_contains($raw, 'معطر') || str_contains($raw, 'يحتوي')) {
            return 'Fragranced';
        }

        return 'Fragrance-free';
    }

    public function getFragranceAttribute(): ?string
    {
        if ($this->isArabicLocale()) {
            if (!empty($this->usage_instructions_json['verdict']['fragrance'])) {
                return (string) $this->usage_instructions_json['verdict']['fragrance'];
            }

            return 'دون عطر مضاف';
        }

        return $this->english_fragrance;
    }

    /**
     * Decode and normalize a benefits list, optionally keeping only English items.
     *
     * @return array<int, array<string, string>>
     */
    protected function normalizeBenefitsList(mixed $raw, bool $englishOnly = false): array
    {
        if (is_string($raw)) {
            $decoded = json_decode($raw, true);
            $raw = (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) ? $decoded : [];
        }

        if (!is_array($raw)) {
            return [];
        }

        $result = [];
        foreach ($raw as $item) {
            if (!is_array($item)) {
                continue;
            }

            if ($englishOnly) {
                $title = $this->firstNonArabic([$item['title_en'] ?? null, $item['title'] ?? null]);
                $text = $this->firstNonArabic([$item['text_en'] ?? null, $item['text'] ?? null]);
                if ($title !== null && $text !== null) {
                    $result[] = ['title' => $title, 'text' => $text];
                }
            } else {
                $title = trim((string) ($item['title'] ?? ($item['title_en'] ?? '')));
                $text = trim((string) ($item['text'] ?? ($item['text_en'] ?? '')));
                if ($title !== '' || $text !== '') {
                    $result[] = ['title' => $title, 'text' => $text];
                }
            }
        }

        return $result;
    }

    public function getKeyBenefitsAttribute()
    {
        if ($this->isArabicLocale()) {
            $items = $this->normalizeBenefitsList($this->ar_key_benefits, false);
            if (!empty($items)) {
                return $items;
            }

            return $this->normalizeBenefitsList($this->usage_instructions_json['benefits'] ?? [], false);
        }

        $items = $this->normalizeBenefitsList($this->en_key_benefits, true);
        if (!empty($items)) {
            return $items;
        }

        return $this->normalizeBenefitsList($this->usage_instructions_json['benefits'] ?? [], true);
    }

    public function getProductTitleSeoAttribute()
    {
        return $this->isArabicLocale() ? $this->ar_product_title_seo : $this->en_product_title_seo;
    }

    public function getMetaDescriptionAttribute()
    {
        if ($this->isArabicLocale()) {
            return $this->meta_description_ar;
        }

        return $this->firstNonArabic([$this->meta_description_en]) ?: $this->english_brief_insight;
    }

    public function getPrimaryKeywordAttribute()
    {
        return $this->isArabicLocale() ? $this->primary_keyword_ar : $this->primary_keyword_en;
    }

    public function getSecondaryKeywordsAttribute()
    {
        return $this->isArabicLocale() ? $this->secondary_keywords_ar : $this->secondary_keywords_en;
    }

    public function getImageAltAttribute()
    {
        $isAr = $this->isArabicLocale();
        $alt = $isAr ? $this->image_alt_ar : $this->image_alt_en;
        if (empty($alt)) {
            $alt = $isAr
                ? ($this->name_ar ?: $this->fixedFullName())
                : $this->fixedFullName();
        }

        return $alt;
    }

    public function getOgDescriptionAttribute()
    {
        if ($this->isArabicLocale()) {
            return $this->og_description_ar;
        }

        return $this->firstNonArabic([$this->og_description_en]) ?: $this->english_brief_insight;
    }

    public function getEnglishBriefInsightAttribute(): string
    {
        $benefitTitle = null;
        $enBenefits = $this->normalizeBenefitsList($this->en_key_benefits, true);
        if (!empty($enBenefits[0]['title'])) {
            $benefitTitle = $enBenefits[0]['title'];
        }

        return $this->firstNonArabic([
            $this->brief_insight_en,
            $this->brief_insight_ar,
            $this->en_short_hook,
            $this->pdp_headline_en,
            $this->above_fold_hook_en,
            $this->description_en,
            $benefitTitle,
        ]) ?: 'Authentic Korean skincare formula designed for optimal skin compatibility.';
    }

    public function getBriefInsightAttribute()
    {
        if ($this->isArabicLocale()) {
            return $this->brief_insight_ar;
        }

        return $this->english_brief_insight;
    }

    public function getEnglishCountryOfOriginAttribute(): string
    {
        return $this->firstNonArabic([
            $this->country_of_origin_en,
            $this->country_of_origin_ar,
        ]) ?: 'South Korea';
    }

    public function getCountryOfOriginAttribute()
    {
        if ($this->isArabicLocale()) {
            return $this->country_of_origin_ar ?: 'كوريا الجنوبية';
        }

        return $this->english_country_of_origin;
    }

    public function getEnglishRoleAttribute(): string
    {
        $direct = $this->firstNonArabic([$this->role_en, $this->role_ar]);
        if ($direct !== null) {
            return $direct;
        }

        $rawRoleAr = mb_strtolower(trim((string) $this->role_ar));
        $keywordMap = [
            'واقي شمس' => 'Daily Sunscreen',
            'ماسك هيدروجيل' => 'Hydrating Hydrogel Mask',
            'ماسك' => 'Targeted Treatment Mask',
            'سيروم للعين' => 'Targeted Eye Care Serum',
            'محيط العين' => 'Targeted Eye Care',
            'سيروم' => 'Targeted Treatment Serum',
            'تونر' => 'Balancing Facial Toner',
            'غسول' => 'Gentle Facial Cleanser',
            'بلسم تنظيف' => 'Cleansing Balm',
            'زيت تنظيف' => 'Cleansing Oil',
            'مرطب' => 'Barrier-Supporting Moisturizer',
            'كريم' => 'Nourishing Cream',
            'مقشر' => 'Gentle Exfoliator',
            'مقشّر' => 'Gentle Exfoliator',
            'إسنس' => 'Hydrating Essence',
        ];
        foreach ($keywordMap as $arKey => $enLabel) {
            if ($rawRoleAr !== '' && str_contains($rawRoleAr, $arKey)) {
                return $enLabel;
            }
        }

        $catName = $this->subCategory?->name_en ?: $this->category?->name_en;
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

    public function getRoleAttribute()
    {
        if ($this->isArabicLocale()) {
            return $this->role_ar ?: ($this->routine_step_title_ar ?: ($this->category?->name_ar ?? 'العناية بالبشرة'));
        }

        return $this->english_role;
    }

    public function getEnglishLimitationsNotesAttribute(): string
    {
        return $this->firstNonArabic([
            $this->limitations_notes_en,
            $this->limitations_notes_ar,
        ]) ?: 'Store in a cool, dry place away from direct sunlight. A patch test on a small area is recommended before full use.';
    }

    public function getLimitationsNotesAttribute()
    {
        if ($this->isArabicLocale()) {
            return $this->limitations_notes_ar;
        }

        return $this->english_limitations_notes;
    }

    public function getEnglishUsageFrequencyAttribute(): string
    {
        $jsonTimingEn = is_array($this->usage_instructions_json)
            ? ($this->usage_instructions_json['timing_en'] ?? null)
            : null;

        $direct = $this->firstNonArabic([
            $this->usage_frequency_en,
            $jsonTimingEn,
            $this->usage_frequency_ar,
        ]);

        if ($direct !== null) {
            return $direct;
        }

        $freq = (string) ($this->usage_frequency_ar ?: ($this->usage_instructions_json['timing'] ?? ''));
        if ($freq === '') {
            return 'Daily use (Morning & Evening)';
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

    public function getUsageFrequencyAttribute()
    {
        if ($this->isArabicLocale()) {
            $freq = $this->usage_frequency_ar ?: ($this->usage_instructions_json['timing'] ?? null);

            return $freq ?: 'استخدام يومي';
        }

        return $this->english_usage_frequency;
    }

    public function getEnglishSeoMetaTitleAttribute(): string
    {
        return $this->firstNonArabic([$this->seo_meta_title_en])
            ?: (($this->en_product_title_seo ?: $this->fixedFullName()) . ' | KCODE');
    }

    public function getSeoMetaTitleAttribute()
    {
        if ($this->isArabicLocale()) {
            return $this->seo_meta_title_ar;
        }

        return $this->english_seo_meta_title;
    }

    public function getEnglishOgTitleAttribute(): string
    {
        return $this->firstNonArabic([$this->og_title_en])
            ?: ($this->en_product_title_seo ?: $this->fixedFullName());
    }

    public function getOgTitleAttribute()
    {
        if ($this->isArabicLocale()) {
            return $this->og_title_ar;
        }

        return $this->english_og_title;
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
        if (array_key_exists('reviews_avg_rating', $this->attributes) && $this->attributes['reviews_avg_rating'] !== null) {
            return round((float) $this->attributes['reviews_avg_rating'], 1);
        }

        if ($this->relationLoaded('reviews')) {
            $avg = $this->reviews->avg('rating');
            return $avg !== null ? round((float) $avg, 1) : 0.0;
        }

        return round((float) ($this->reviews()->avg('rating') ?? 0.0), 1);
    }

    public function getNumReviewsAttribute()
    {
        if (array_key_exists('reviews_count', $this->attributes)) {
            return (int) $this->attributes['reviews_count'];
        }

        if ($this->relationLoaded('reviews')) {
            return $this->reviews->count();
        }

        return (int) $this->reviews()->count();
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