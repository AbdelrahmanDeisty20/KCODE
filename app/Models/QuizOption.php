<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class QuizOption extends Model
{
    use HasFactory;

    protected $fillable = [
        'quiz_question_id',
        'code',
        'title_ar',
        'title_en',
        'description_ar',
        'description_en',
        'image',
        'option_type',
        'mapped_id',
    ];

    public function question()
    {
        return $this->belongsTo(QuizQuestion::class, 'quiz_question_id');
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

    public function getImagePathAttribute()
    {
        if (!$this->image)
            return null;
        if (filter_var($this->image, FILTER_VALIDATE_URL))
            return $this->image;

        $path = ltrim(preg_replace('/^(storage\/)?(app\/public\/)?/', '', $this->image), '/');
        if (!str_starts_with(strtolower($path), 'quiz/')) {
            $path = 'quiz/' . $path;
        }

        return asset('storage/' . $path);
    }
}
