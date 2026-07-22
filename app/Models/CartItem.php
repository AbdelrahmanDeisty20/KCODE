<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CartItem extends Model
{
    protected $fillable = [
        'cart_id',
        'product_id',
        'quantity',
        'unit_price',
        'discount_amount',
        'discount_percentage',
        'price_after_discount',
        'total_price',
    ];

    protected $casts = [
        'quantity' => 'integer',
        'unit_price' => 'float',
        'discount_amount' => 'float',
        'discount_percentage' => 'float',
        'price_after_discount' => 'float',
        'total_price' => 'float',
    ];

    /**
     * Relationship to Cart
     */
    public function cart(): BelongsTo
    {
        return $this->belongsTo(Cart::class);
    }

    /**
     * Relationship to Product
     */
    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }
}
