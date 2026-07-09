<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductConflict extends Model
{
    protected $fillable = [
        'product_id',
        'conflicting_product_id',
        'reason_ar',
        'reason_en',
    ];
    public function product()
    {
        return $this->belongsTo(Product::class);
    }
    public function conflictingProduct()
    {
        return $this->belongsTo(Product::class, 'conflicting_product_id');
    }
    public function getReasonAttribute($value)
    {
        return app()->getLocale() == 'ar' ? $this->reason_ar : $this->reason_en; 
    }   
}
