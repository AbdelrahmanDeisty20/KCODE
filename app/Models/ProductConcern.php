<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProductConcern extends Model
{
    protected $fillable = [
        'product_id',
        'concern_id',
        'priority',
    ];

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function concern(): BelongsTo
    {
        return $this->belongsTo(Concern::class);
    }
}
