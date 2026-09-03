<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class Brand extends Model
{
    protected $fillable = [
        'name_en',
        'name_ar',
        'image',
    ];

    public function getNameAttribute()
    {
        if (app()->getLocale() == 'ar') {
            return $this->name_ar ?: $this->name_en;
        }
        return $this->name_en ?: $this->name_ar;
    }

    public function getImagePathAttribute()
    {
        if (!$this->image)
            return null;
        if (filter_var($this->image, FILTER_VALIDATE_URL))
            return $this->image;

        $path = ltrim(preg_replace('/^(storage\/)?(app\/public\/)?/', '', $this->image), '/');
        $path = ltrim(preg_replace('/^brands\//i', '', $path), '/');
        $path = 'brands/' . $path;

        return asset('storage/app/public/' . $path);
    }

    public function products()
    {
        return $this->hasMany(Product::class);
    }
}
