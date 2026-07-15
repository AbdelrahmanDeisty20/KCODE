<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AssessmentAnswer extends Model
{
    protected $fillable = [
        'assessment_id',
        'question_id',
        'answer_id',
    ];

    public function assessment()
    {
        return $this->belongsTo(Assessment::class);
    }

    public function question()
    {
        return $this->belongsTo(QuizQuestion::class, 'question_id');
    }

    public function answer()
    {
        return $this->belongsTo(QuizOption::class, 'answer_id');
    }
}
