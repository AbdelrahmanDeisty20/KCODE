<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Concern extends Model
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

    public function getDescriptionAttribute($value)
    {
        return app()->getLocale() == 'ar' ? $this->description_ar : $this->description_en;
    }

    public function getImagePathAttribute()
    {
        if (!$this->image)
            return null;
        if (filter_var($this->image, FILTER_VALIDATE_URL))
            return $this->image;

        $path = ltrim(preg_replace('/^(storage\/)?(app\/public\/)?/', '', $this->image), '/');
        $path = ltrim(preg_replace('/^concerns\//i', '', $path), '/');
        $path = 'concerns/' . $path;

        return Storage::disk('public')->url($path);
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
