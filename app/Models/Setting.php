<?php

namespace App\Models;

use App\Support\StaticCache;
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
     * Get all settings as a cached associative array keyed by key_en.
     */
    public static function allMap(): array
    {
        return StaticCache::remember('settings', 'all_map', function () {
            $map = [];
            foreach (self::all() as $setting) {
                $map[$setting->key_en] = [
                    'key_ar'   => $setting->key_ar,
                    'key_en'   => $setting->key_en,
                    'value_ar' => $setting->value_ar,
                    'value_en' => $setting->value_en,
                ];
            }
            return $map;
        });
    }

    /**
     * Get setting value by key_en safely (backed by StaticCache).
     */
    public static function get(string $key, $default = null)
    {
        $raw = request()->query('lang')
            ?? request()->header('lang')
            ?? request()->header('X-Language')
            ?? app()->getLocale()
            ?? 'ar';

        return self::getForLocale($key, (string) $raw, $default);
    }

    /**
     * Get setting value for an explicit locale ('ar' or 'en') safely (backed by StaticCache).
     */
    public static function getForLocale(string $key, string $lang, $default = null)
    {
        try {
            $map = self::allMap();
        } catch (\Throwable $e) {
            return $default;
        }

        if (!isset($map[$key])) {
            return $default;
        }

        $setting = $map[$key];
        $isEn = str_starts_with(strtolower(trim($lang)), 'en');

        return $isEn
            ? ($setting['value_en'] ?: $default)
            : ($setting['value_ar'] ?: ($setting['value_en'] ?: $default));
    }
}
