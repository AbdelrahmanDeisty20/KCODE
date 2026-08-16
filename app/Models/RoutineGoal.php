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
    public function getImageAttribute($value)
    {
        if (!$value) return null;
        if (filter_var($value, FILTER_VALIDATE_URL)) return $value;

        $path = ltrim(preg_replace('/^storage\//', '', $value), '/');
        if (!str_starts_with($path, 'routine-goals/')) {
            $path = 'routine-goals/' . $path;
        }

        if (request() && request()->is('admin*')) {
            return $path;
        }

        return asset('storage/' . $path);
    }

    public function getImagePathAttribute()
    {
        return $this->image;
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
