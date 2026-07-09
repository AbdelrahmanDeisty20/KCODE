<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AssessmentGoal extends Model
{
    protected $fillable = [
        'assessment_id',
        'goal_id',
    ];
    public function assessment()
    {
        return $this->belongsTo(Assessment::class);
    }
    public function goal()
    {
        return $this->belongsTo(RoutineGoal::class);
    }
}
