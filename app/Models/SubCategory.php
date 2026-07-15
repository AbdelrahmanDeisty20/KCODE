<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SubCategory extends Model
{
    protected $fillable = [
        'name_en',
        'name_ar',
        'category_id',
        'image',
    ];

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function products()
    {
        return $this->hasMany(Product::class);
    }

    public function getNameAttribute()
    {
        return app()->getLocale() == "ar" ? $this->name_ar : $this->name_en;
    }

    public function getImagePathAttribute($value)
    {
        if ($value) {
            if (filter_var($value, FILTER_VALIDATE_URL)) {
                return $value;
            }
            return asset('uploads/sub_categories/' . $value);
        }
        return asset('uploads/sub_categories/default.jpg');
    }

    public function getProductsCountAttribute()
    {
        return $this->products()->count();
    }
}
