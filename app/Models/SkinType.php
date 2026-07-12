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
    public function getImagePathAttribute($value)
    {
        if($value){
            // If it's already a full URL, return it as is
            if (filter_var($value, FILTER_VALIDATE_URL)) {
                return $value;
            }
            return asset('uploads/skin_types/' . $value);
        }
        return asset('uploads/skin_types/default.jpg');
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
        return $this->hasMany(Product::class);
    }
}
