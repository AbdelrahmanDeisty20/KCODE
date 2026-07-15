<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Offer extends Model
{
    protected $fillable = [
        'product_id',
        'discount_percentage',
        'start_date',
        'end_date',
        'is_active',
    ];

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    /**
     * Scope active offers
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true)
            ->where(function ($q) {
                $q->whereDate('start_date', '<=', now())
                    ->whereDate('end_date', '>=', now());
            });
    }

    /**
     * Check if offer is currently active
     */
    public function isActive(): bool
    {
        if (!$this->is_active) {
            return false;
        }

        $now = now();

        if ($this->start_date && $this->start_date > $now) {
            return false;
        }

        if ($this->end_date && $this->end_date < $now) {
            return false;
        }

        return true;
    }

    /**
     * Get discounted price
     */
    public function getDiscountedPriceAttribute(): float
    {
        if (!$this->isActive()) {
            return (float)$this->product->price;
        }

        return $this->product->price * (1 - ($this->discount_percentage / 100));
    }

    /**
     * Get discount amount
     */
    public function getDiscountAmountAttribute(): float
    {
        if (!$this->isActive()) {
            return 0;
        }

        return $this->product->price - $this->discounted_price;
    }
}
