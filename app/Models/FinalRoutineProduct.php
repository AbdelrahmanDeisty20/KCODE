<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FinalRoutineProduct extends Model
{
    protected $fillable = [
        'final_routine_id',
        'product_id',
        'step',
        'routine_step_id',
    ];

    public function finalRoutine(): BelongsTo
    {
        return $this->belongsTo(FinalRoutine::class);
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function routineStep(): BelongsTo
    {
        return $this->belongsTo(RoutineStep::class, 'routine_step_id');
    }
}
