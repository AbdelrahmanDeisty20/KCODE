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
        if (!$value) return null;
        if (filter_var($value, FILTER_VALIDATE_URL)) return $value;
        $base = is_link(public_path('storage')) ? 'storage/' : 'storage/app/public/';
        return asset($base . 'product_images/' . $value);
    }
}
