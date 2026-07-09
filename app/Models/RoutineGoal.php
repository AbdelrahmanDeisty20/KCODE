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
    public function getImagePathAttribute($value)
    {
        if($value){
            return asset('uploads/routine-goals/' . $value);
        }
        return asset('uploads/routine-goals/default.jpg');
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
