<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RoutineGoal extends Model
{
    protected $fillable = [
        'name_ar',
        'name_en',
        'image',
    ];
    public function getNameAttribute($value)
    {
        return app()->getLocale() == 'ar' ? $this->name_ar : $this->name_en;
    }
    public function getImagePathAttribute()
    {
        $value = $this->image;
        if (!$value) return null;
        if (filter_var($value, FILTER_VALIDATE_URL)) return $value;
        $base = is_link(public_path('storage')) ? 'storage/' : 'storage/app/public/';
        return asset($base . 'routine-goals/' . $value);
    }
    public function products()
    {
        return $this->hasMany(ProductGoal::class);
    }  
    public function assessment_goals()
    {
        return $this->hasMany(AssessmentGoal::class);
    }
}
