<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Assessment extends Model
{
    protected $fillable = [
        'user_id',
        'skin_type_id',
    ];
    public function user()
    {
        return $this->belongsTo(User::class);
    }
    public function skinType()
    {
        return $this->belongsTo(SkinType::class);
    }
    public function concerns()
    {
        return $this->hasMany(AssessmentConcern::class);
    }
    public function assessment_goals()
    {
        return $this->hasMany(AssessmentGoal::class);
    }
    public function routines()
    {
        return $this->hasMany(Routine::class);
    }
    public function answers()
    {
        return $this->hasMany(AssessmentAnswer::class);
    }
}
