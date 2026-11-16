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
        // 1. Highest priority: Custom value saved by admin in Filament Dashboard
        if (!empty($this->usage_instructions_json['verdict']['sensitive_status'])) {
            return (string) $this->usage_instructions_json['verdict']['sensitive_status'];
        }

        // 2. Direct database column if set
        if (!empty($this->attributes['sensitive_status'])) {
            return (string) $this->attributes['sensitive_status'];
        }

        // 3. Official R479 canonical mapping fallback (if not yet customized in dashboard)
        $canonicalMap = [
            'KC0011' => 'مناسب للبشرة الحساسة',
            'KC0023' => 'مناسب للبشرة الحساسة',
            'KC0063' => 'مناسب للبشرة الحساسة',
            'KC0068' => 'مناسب للبشرة الحساسة',
            'KC0084' => 'مناسب للبشرة الحساسة',
            'KC0089' => 'مناسب للبشرة الحساسة',
            'KC0127' => 'مناسب للبشرة الحساسة',
            'KC0129' => 'مناسب للبشرة الحساسة',
            'KC0145' => 'مناسب للبشرة الحساسة',
            'KC0153' => 'مناسب للبشرة الحساسة',
            'KC0158' => 'مناسب للبشرة الحساسة',
            'KC0178' => 'مناسب للبشرة الحساسة',
            'KC0054' => 'مناسب بشرط',
            'KC0122' => 'غير مناسب كخطوة أولى للبشرة الحساسة',
            'KC0124' => 'غير مناسب كخطوة أولى للبشرة الحساسة',
        ];

        if ($this->sku && isset($canonicalMap[$this->sku])) {
            return $canonicalMap[$this->sku];
        }

        return $this->sensitive_eligible ? 'مناسب للبشرة الحساسة' : 'مناسب بشرط';
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
        $alt = app()->getLocale() == 'ar' ? $this->image_alt_ar : $this->image_alt_en;
        if (empty($alt)) {
            $alt = $this->name_ar ?: ($this->name_en ?: $this->name);
        }
        return $alt;
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