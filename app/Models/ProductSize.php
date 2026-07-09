<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductSize extends Model
{
    protected $fillable = [
        'product_id',
        'size_ar',
        'size_en',
        'price',
        'stock',
        'status',
    ];
    public function product()
    {
        return $this->belongsTo(Product::class);
    }
    public function getSizeAttribute($value)
    {
        return app()->getLocale() == 'ar' ? $this->size_ar : $this->size_en;
    }
}
