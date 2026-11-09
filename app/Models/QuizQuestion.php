<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class QuizQuestion extends Model
{
    use HasFactory;

    protected $fillable = [
        'key',
        'step_label_ar',
        'step_label_en',
        'title_ar',
        'title_en',
        'description_ar',
        'description_en',
        'helper_ar',
        'helper_en',
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
        return $this->{'title_' . app()->getLocale()} ?: $this->title_ar;
    }

    public function getDescriptionAttribute()
    {
        return $this->{'description_' . app()->getLocale()} ?: $this->description_ar;
    }

    public function getStepLabelAttribute()
    {
        return $this->{'step_label_' . app()->getLocale()} ?: $this->step_label_ar;
    }

    public function getHelperAttribute()
    {
        return $this->{'helper_' . app()->getLocale()} ?: $this->helper_ar;
    }
}