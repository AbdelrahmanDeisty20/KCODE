<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductImage extends Model
{
    protected $fillable = [
        'product_id',
        'images',
    ];
    public function product()
    {
        return $this->belongsTo(Product::class);
    }
    public function getImagePathAttribute($value)
    {
        if($value){
            return asset('storage/product_images/' . $value);
        }
        return asset('storage/product_images/default.jpg');
    }
}
