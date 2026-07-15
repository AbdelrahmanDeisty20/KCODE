<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RoutineProduct extends Model
{
    protected $fillable = [
        'routine_id',
        'product_id',
        'step',
        'replaced_with_product_id',
        'accepted',
    ];

    /**
     * Relationship to routine
     */
    public function routine(): BelongsTo
    {
        return $this->belongsTo(Routine::class);
    }

    /**
     * Relationship to product
     */
    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    /**
     * Relationship to replaced product
     */
    public function replacedProduct(): BelongsTo
    {
        return $this->belongsTo(Product::class, 'replaced_with_product_id');
    }

    /**
     * Relationship to routine step (mapped on order)
     */
    public function routineStep(): BelongsTo
    {
        return $this->belongsTo(RoutineStep::class, 'step', 'order');
    }
}
