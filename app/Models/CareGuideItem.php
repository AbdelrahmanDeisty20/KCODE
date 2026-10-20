<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CareGuideItem extends Model
{
    use HasFactory;

    protected $table = 'care_guide_items';

    protected $fillable = [
        'care_guide_id',
        'title_ar',
        'title_en',
        'description_ar',
        'description_en',
        'icon',
        'sort_order',
        'is_active',
    ];

    protected $casts = [
        'is_active'  => 'boolean',
        'sort_order' => 'integer',
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

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function careGuide(): BelongsTo
    {
        return $this->belongsTo(CareGuide::class, 'care_guide_id');
    }
}
