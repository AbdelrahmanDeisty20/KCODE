<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductSelectionCriteria extends Model
{
    use HasFactory;

    protected $table = 'product_selection_criterias';

    protected $fillable = [
        'title_ar',
        'title_en',
        'description_ar',
        'description_en',
        'icon',
        'badge_text_ar',
        'badge_text_en',
        'type',
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
        return ($lang === 'en' && !empty($this->title_en)) ? $this->title_en : $this->title_ar;
    }

    public function getDescriptionAttribute(): string
    {
        $lang = request()->header('lang') ?? app()->getLocale();
        $lang = strtolower(substr($lang, 0, 2));
        return ($lang === 'en' && !empty($this->description_en)) ? $this->description_en : $this->description_ar;
    }

    public function getBadgeTextAttribute(): ?string
    {
        $lang = request()->header('lang') ?? app()->getLocale();
        $lang = strtolower(substr($lang, 0, 2));
        return ($lang === 'en' && !empty($this->badge_text_en)) ? $this->badge_text_en : $this->badge_text_ar;
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeModalCriteria($query)
    {
        return $query->where('type', 'modal_criteria');
    }

    public function scopeAccordionItems($query)
    {
        return $query->where('type', 'accordion_item');
    }
}
