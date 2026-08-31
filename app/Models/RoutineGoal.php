<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

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
        if (!$value)
            return null;
        if (filter_var($value, FILTER_VALIDATE_URL))
            return $value;

        $path = ltrim(preg_replace('/^(storage\/)?(app\/public\/)?/', '', $value), '/');
        $path = ltrim(preg_replace('/^routine-goals\//i', '', $path), '/');
        $path = 'routine-goals/' . $path;

        return asset('storage/app/public/' . $path);
    }

    public function products()
    {
        return $this->hasMany(ProductGoal::class, 'goal_id');
    }

    public function assessment_goals()
    {
        return $this->hasMany(AssessmentGoal::class, 'goal_id');
    }
}
