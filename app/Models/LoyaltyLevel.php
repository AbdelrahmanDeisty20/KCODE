<?php

namespace App\Models;

use App\Support\StaticCache;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

class LoyaltyLevel extends Model
{
    protected $table = 'loyalty_levels';

    protected $fillable = [
        'name_ar',
        'name_en',
        'description_ar',
        'description_en',
        'policy_ar',
        'policy_en',
        'min_points',
        'max_points',
        'icon',
        'sort_order',
        'is_active',
    ];

    protected $casts = [
        'min_points' => 'integer',
        'max_points' => 'integer',
        'is_active'  => 'boolean',
    ];

    // ---- Accessors ----

    /**
     * اسم المستوى حسب لغة التطبيق
     */
    public function getNameAttribute(): string
    {
        return app()->getLocale() === 'ar' ? $this->name_ar : $this->name_en;
    }

    /**
     * وصف المستوى حسب لغة التطبيق
     */
    public function getDescriptionAttribute(): ?string
    {
        return app()->getLocale() === 'ar' ? $this->description_ar : $this->description_en;
    }

    /**
     * سياسة المستوى حسب لغة التطبيق
     */
    public function getPolicyAttribute(): ?string
    {
        return app()->getLocale() === 'ar' ? $this->policy_ar : $this->policy_en;
    }

    // ---- Scopes ----

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    // ---- Static Helpers ----

    /**
     * Get all active loyalty levels from StaticCache.
     */
    public static function cachedActive(): Collection
    {
        return StaticCache::remember('loyalty_levels', 'active', function () {
            return static::active()->orderBy('sort_order', 'asc')->get();
        });
    }

    /**
     * ايجاد مستوى المستخدم بناءً على نقاطه
     */
    public static function forPoints(int $points): ?self
    {
        return static::cachedActive()
            ->filter(fn (self $level) => $level->min_points <= $points && ($level->max_points === null || $level->max_points >= $points))
            ->sortByDesc('min_points')
            ->first();
    }

    /**
     * المستوى التالي بعد المستوى الحالي
     */
    public static function nextAfter(int $currentMinPoints): ?self
    {
        return static::cachedActive()
            ->filter(fn (self $level) => $level->min_points > $currentMinPoints)
            ->sortBy('min_points')
            ->first();
    }
}
