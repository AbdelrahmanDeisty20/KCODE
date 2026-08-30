<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PresetRoutineProduct extends Model
{
    use HasFactory;

    protected $fillable = [
        'preset_routine_id',
        'product_id',
        'display_order',
        'step_name_ar',
        'step_name_en',
        'morning',
        'night',
        'use_time_ar',
    ];

    public function presetRoutine()
    {
        return $this->belongsTo(PresetRoutine::class, 'preset_routine_id');
    }

    public function product()
    {
        return $this->belongsTo(Product::class, 'product_id');
    }
}
