<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AssessmentConcern extends Model
{
    protected $fillable = [
        'assessment_id',
        'concern_id',
    ];
    public function assessment()
    {
        return $this->belongsTo(Assessment::class);
    }
    public function concern()
    {
        return $this->belongsTo(Concern::class);
    }
}
