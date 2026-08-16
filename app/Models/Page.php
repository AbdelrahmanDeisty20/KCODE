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
        $val = app()->getLocale() == 'ar' ? $this->value_ar : $this->value_en;
        if ($this->key_en === 'image' && $val && !filter_var($val, FILTER_VALIDATE_URL)) {
            $base = is_link(public_path('storage')) ? 'storage/' : 'storage/app/public/';
            return asset($base . 'pages/' . $val);
        }
        return $val;
    }

    public function getKeyAttribute()
    {
        return app()->getLocale() == 'ar' ? $this->key_ar : $this->key_en;
    }
}
