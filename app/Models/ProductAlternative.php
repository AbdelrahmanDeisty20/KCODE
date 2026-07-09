<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductAlternative extends Model
{
    protected $fillable = [
        'product_id',
        'alternative_id',
        'priority',
        'reason_ar',
        'reason_en',
    ];
    public function product()
    {
        return $this->belongsTo(Product::class);
    }
    public function alternative()
    {
        return $this->belongsTo(Product::class);
    }
    public function getReasonAttribute($value)
    {
        return app()->getLocale() == 'ar' ? $this->reason_ar : $this->reason_en;
    } 
}
