<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Page extends Model
{
    protected $fillable = [
        'key_ar',
        'key_en',
        'value_ar',
        'value_en',
        'type',
    ];

    public function getValueAttribute()
    {
        return app()->getLocale() == 'ar' ? $this->value_ar : $this->value_en;
    }

    public function getKeyAttribute()
    {
        return app()->getLocale() == 'ar' ? $this->key_ar : $this->key_en;
    }
    public function getImagePath($value)
    {
        if (!$value) return null;
        if (filter_var($value, FILTER_VALIDATE_URL)) return $value;
        $base = is_link(public_path('storage')) ? 'storage/' : 'storage/app/public/';
        return asset($base . 'pages/' . $value);
    }
}
