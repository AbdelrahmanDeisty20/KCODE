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
        'brand_id',
        'short_name_ar',
        'short_name_en',
        'image',
        'sku',
        'ingredients_ar',
        'ingredients_en',
        'how_to_use_ar',
        'how_to_use_en',
        'status',
    ];

    public function category()
    {
        return $this->belongsTo(Category::class);
    }
    public function brand()
    {
        return $this->belongsTo(Brand::class);
    }
    public function getImageAttribute($value)
    {
        if($value){
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
    public function getAverageRatingAttribute()
    {
        return round($this->reviews()->avg('rating'), 1) ?? 0.0;
    }
    public function getNumReviewsAttribute()
    {
        return $this->reviews()->count();
    }
}
