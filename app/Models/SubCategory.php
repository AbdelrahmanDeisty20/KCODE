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

    public function getImageAttribute($value)
    {
        if (!$value) return null;
        if (filter_var($value, FILTER_VALIDATE_URL)) return $value;

        $path = ltrim(preg_replace('/^storage\//', '', $value), '/');
        if (!str_starts_with($path, 'sub_categories/')) {
            $path = 'sub_categories/' . $path;
        }

        if (request() && request()->is('admin*')) {
            return $path;
        }

        return asset('storage/' . $path);
    }

    public function getImagePathAttribute()
    {
        return $this->image;
    }

    public function getProductsCountAttribute()
    {
        return $this->products()->count();
    }
}
