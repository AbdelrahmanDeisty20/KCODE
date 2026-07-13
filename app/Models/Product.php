<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

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
        'stock',
        'short_name_ar',
        'short_name_en',
        'image',
        'sku',
        'ingredients_ar',
        'ingredients_en',
        'how_to_use_ar',
        'how_to_use_en',
        'status',
        
        // Product Details
        'texture_ar',
        'texture_en',
        'why_kcode_ar',
        'why_kcode_en',
        'usage_frequency_ar',
        'active_strength_level',
        'safety_notes_ar',
        'safety_notes_en',
        'ar_key_benefits',
        'en_key_benefits',

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

    public function getImageAttribute($value)
    {
        if ($value) {
            return asset('uploads/products/' . $value);
        }
        return asset('uploads/products/default.jpg');
    }

    public function getNameAttribute($value)
    {
        return app()->getLocale() == 'ar' ? $this->name_ar : $this->name_en;
    }

    public function getDescriptionAttribute($value)
    {
        return app()->getLocale() == 'ar' ? $this->description_ar : $this->description_en;
    }

    public function sizes()
    {
        return $this->hasMany(ProductSize::class);
    }

    public function images()
    {
        return $this->hasMany(ProductImage::class);
    }

    public function concerns()
    {
        return $this->hasMany(ProductConcern::class);
    }

    public function skinTypes()
    {
        return $this->hasMany(ProductSkinType::class);
    }

    public function goals()
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
}
