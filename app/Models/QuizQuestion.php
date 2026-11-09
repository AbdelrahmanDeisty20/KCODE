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
        $locale = app()->getLocale();
        return $this->{'title_' . $locale} ?: ($this->title_ar ?: $this->title_en);
    }

    public function getDescriptionAttribute()
    {
        $locale = app()->getLocale();
        return $this->{'description_' . $locale} ?: ($this->description_ar ?: $this->description_en);
    }

    public function getStepLabelAttribute()
    {
        $locale = app()->getLocale();
        return $this->{'step_label_' . $locale} ?: ($this->step_label_ar ?: $this->step_label_en);
    }

    public function getHelperAttribute()
    {
        $locale = app()->getLocale();
        return $this->{'helper_' . $locale} ?: ($this->helper_ar ?: $this->helper_en);
    }
}