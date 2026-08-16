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
    public function getImagePathAttribute()
    {
        $value = $this->images;
        if (!$value) return null;
        if (filter_var($value, FILTER_VALIDATE_URL)) return $value;

        $path = ltrim(preg_replace('/^storage\//', '', $value), '/');
        if (!str_starts_with($path, 'product_images/')) {
            $path = 'product_images/' . $path;
        }

        if (request() && request()->is('admin*')) {
            return $path;
        }

        return asset('storage/' . $path);
    }
}
