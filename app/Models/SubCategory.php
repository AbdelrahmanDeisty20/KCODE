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

    public function getImagePathAttribute()
    {
        $value = $this->image;
        if (!$value) return null;
        if (filter_var($value, FILTER_VALIDATE_URL)) return $value;
        $base = is_link(public_path('storage')) ? 'storage/' : 'storage/app/public/';
        return asset($base . 'sub_categories/' . $value);
    }

    public function getProductsCountAttribute()
    {
        return $this->products()->count();
    }
}
