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
        'status',
    ];

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
