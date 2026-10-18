<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PresetRoutine extends Model
{
    use HasFactory;

    protected $fillable = [
        'title_ar',
        'title_en',
        'description_ar',
        'description_en',
        'badge_ar',
        'badge_en',
        'skin_type_ar',
        'skin_type_en',
        'goal_ar',
        'goal_en',
        'skin_type_id',
        'goal_id',
        'is_for_men',
        'status',
        'benefits_json',
        'what_it_offers_json',
        'usage_guide_json',
        'how_to_use_ar',
        'how_to_use_en',
        'complementary_products_json',
    ];

    protected $casts = [
        'is_for_men' => 'boolean',
        'benefits_json' => 'array',
        'what_it_offers_json' => 'array',
        'usage_guide_json' => 'array',
        'complementary_products_json' => 'array',
    ];

    public function scopeForMen($query)
    {
        return $query->where('is_for_men', true);
    }

    public function scopeForWomen($query)
    {
        return $query->where('is_for_men', false);
    }

    public function items()
    {
        return $this->hasMany(PresetRoutineProduct::class, 'preset_routine_id')->orderBy('display_order', 'asc');
    }

    public function skinType()
    {
        return $this->belongsTo(SkinType::class);
    }

    public function goal()
    {
        return $this->belongsTo(RoutineGoal::class, 'goal_id');
    }
}
