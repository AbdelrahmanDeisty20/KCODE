<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CareGuide extends Model
{
    use HasFactory;

    protected $table = 'care_guides';

    protected $fillable = [
        'title_ar',
        'title_en',
        'description_ar',
        'description_en',
        'note_ar',
        'note_en',
        'button_quiz_text_ar',
        'button_quiz_text_en',
        'button_quiz_action',
        'button_routines_text_ar',
        'button_routines_text_en',
        'button_routines_action',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function getTitleAttribute(): string
    {
        $lang = request()->header('lang') ?? app()->getLocale();
        $lang = strtolower(substr($lang, 0, 2));
        return ($lang === 'en' && !empty($this->title_en)) ? $this->title_en : ($this->title_ar ?? '');
    }

    public function getDescriptionAttribute(): string
    {
        $lang = request()->header('lang') ?? app()->getLocale();
        $lang = strtolower(substr($lang, 0, 2));
        return ($lang === 'en' && !empty($this->description_en)) ? $this->description_en : ($this->description_ar ?? '');
    }

    public function getNoteAttribute(): ?string
    {
        $lang = request()->header('lang') ?? app()->getLocale();
        $lang = strtolower(substr($lang, 0, 2));
        return ($lang === 'en' && !empty($this->note_en)) ? $this->note_en : $this->note_ar;
    }

    public function getButtonQuizTextAttribute(): ?string
    {
        $lang = request()->header('lang') ?? app()->getLocale();
        $lang = strtolower(substr($lang, 0, 2));
        return ($lang === 'en' && !empty($this->button_quiz_text_en)) ? $this->button_quiz_text_en : $this->button_quiz_text_ar;
    }

    public function getButtonRoutinesTextAttribute(): ?string
    {
        $lang = request()->header('lang') ?? app()->getLocale();
        $lang = strtolower(substr($lang, 0, 2));
        return ($lang === 'en' && !empty($this->button_routines_text_en)) ? $this->button_routines_text_en : $this->button_routines_text_ar;
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function items(): HasMany
    {
        return $this->hasMany(CareGuideItem::class, 'care_guide_id')->orderBy('sort_order', 'asc');
    }
}
