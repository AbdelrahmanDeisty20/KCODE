<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class QuizQuestion extends Model
{
    use HasFactory;

    protected $fillable = [
        'title_ar',
        'title_en',
        'description_ar',
        'description_en',
        'selection_type',
        'step_number',
        'is_optional',
    ];

    public function options()
    {
        return $this->hasMany(QuizOption::class);
    }

    public function getTitleAttribute()
    {
        return $this->{'title_' . app()->getLocale()};
    }
    public function getDescriptionAttribute()
    {
        return $this->{'description_' . app()->getLocale()};
    }
}