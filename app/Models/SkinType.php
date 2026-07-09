<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SkinType extends Model
{
    protected $fillable = [
        'name_ar',
        'name_en',
        'image',
        'status',
    ];
    public function getNameAttribute($value)
    {
        return app()->getLocale() == 'ar' ? $this->name_ar : $this->name_en;
    }
    public function getImagePath($value)
    {
        if($value){
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
}
