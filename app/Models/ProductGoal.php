<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
class ProductGoal extends Model
{
    protected $fillable = [
        'product_id',
        'goal_id',
        'priority',
    ];
    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }
    public function goal(): BelongsTo
    {
        return $this->belongsTo(RoutineGoal::class);
    }
}
