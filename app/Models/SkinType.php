<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SkinType extends Model
{
    protected $fillable = [
        'name_ar',
        'name_en',
        'description_ar',
        'description_en',
        'image',
        'status',
    ];
    public function getNameAttribute($value)
    {
        return app()->getLocale() == 'ar' ? $this->name_ar : $this->name_en;
    }
    public function getDescriptionAttribute()
    {
        return app()->getLocale() == 'ar' ? $this->description_ar : $this->description_en;
    }
    public function getImagePathAttribute()
    {
        $value = $this->image;
        if (!$value) return null;
        if (filter_var($value, FILTER_VALIDATE_URL)) return $value;
        $base = is_link(public_path('storage')) ? 'storage/' : 'storage/app/public/';
        return asset($base . 'skin_types/' . $value);
    }
    public function assessments()
    {
        return $this->hasMany(Assessment::class);
    }
    public function users()
    {
        return $this->hasMany(User::class);
    }
    public function products()
    {
        return $this->belongsToMany(Product::class, 'product_skin_types');
    }
}
