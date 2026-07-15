<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Routine extends Model
{
    protected $fillable = [
        'assessment_id',
    ];
    public function assessment()
    {
        return $this->belongsTo(Assessment::class);
    }
    public function routineProducts()
    {
        return $this->hasMany(RoutineProduct::class);
    }
}
