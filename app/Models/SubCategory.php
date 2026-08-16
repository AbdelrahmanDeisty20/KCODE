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
        return app()->getLocale() == 'ar' ? $this->name_ar : $this->name_en;
    }

    public function getImagePathAttribute()
    {
        $value = $this->image;
        if (!$value)
            return null;
        if (filter_var($value, FILTER_VALIDATE_URL))
            return $value;

        $path = ltrim(preg_replace('/^(storage\/)?(app\/public\/)?/', '', $value), '/');
        $path = ltrim(preg_replace('/^sub_categories\//i', '', $path), '/');
        $path = 'sub_categories/' . $path;

        return Storage::disk('public')->url($path);
    }

    public function getProductsCountAttribute()
    {
        return $this->products()->count();
    }
}
