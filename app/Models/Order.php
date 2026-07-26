<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    protected $fillable = [
        'order_number',
        'user_id',
        'user_name',
        'user_phone',
        'address_id',
        'shipping_address',
        'payment_method',
        'payment_status',
        'order_status',
        'subtotal',
        'discount_amount',
        'shipping_fee',
        'total',
        'coupon_code',
        'notes',
    ];

    protected $casts = [
        'shipping_address' => 'array',
        'subtotal'        => 'float',
        'discount_amount' => 'float',
        'shipping_fee'    => 'float',
        'total'           => 'float',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function address()
    {
        return $this->belongsTo(Address::class, 'address_id');
    }

    public function items()
    {
        return $this->hasMany(OrderItem::class);
    }
}
