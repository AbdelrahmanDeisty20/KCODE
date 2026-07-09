<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductRoutine extends Model
{
    protected $fillable = [
        'product_id',
        'routine_step_id',
        'morning',
        'night',
        'layer_order',
        'is_core',
        'is_addon',
    ];
    public function product()
    {
        return $this->belongsTo(Product::class);
    }
    public function routineStep()
    {
        return $this->belongsTo(RoutineStep::class);
    }
}
