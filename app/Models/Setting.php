<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    protected $fillable = [
        'key_ar',
        'key_en',
        'value_ar',
        'value_en',
    ];

    public function getValueAttribute()
    {
        return app()->getLocale() == 'ar' ? $this->value_ar : $this->value_en;
    }

    public function getKeyAttribute()
    {
        return app()->getLocale() == 'ar' ? $this->key_ar : $this->key_en;
    }

    /**
     * Get setting value by key_en safely.
     */
    public static function get(string $key, $default = null)
    {
        $setting = self::where('key_en', $key)->first();
        if (!$setting) {
            return $default;
        }

        $lang = request()->header('lang') ?? app()->getLocale();
        $lang = strtolower(substr($lang, 0, 2));
        return $lang === 'en' ? ($setting->value_en ?: $setting->value_ar) : ($setting->value_ar ?: $setting->value_en);
    }
}
