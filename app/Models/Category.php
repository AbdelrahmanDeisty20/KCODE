<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    protected $fillable = [
        'name_en',
        'name_ar',
        'image',
    ];
    public function getNameAttribute()
    {
        return app()->getLocale() == "ar" ? $this->name_ar : $this->name_en;
    }
    public function getImageAttribute($value)
    {
        if (!$value) return null;
        if (filter_var($value, FILTER_VALIDATE_URL)) return $value;
        $base = is_link(public_path('storage')) ? 'storage/' : 'storage/app/public/';
        return asset($base . 'categories/' . $value);
    }
}
