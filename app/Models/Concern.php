<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Concern extends Model
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
    public function getImagePathAttribute($value)
    {
        if($value){
            return asset('uploads/concerns/' . $value);
        }
        return asset('uploads/concerns/default.jpg');
    }
    public function products()
    {
        return $this->hasMany(ProductConcern::class);
    }  
    public function AssessmentConcern()
    {
        return $this->hasMany(AssessmentConcern::class);
    }
}