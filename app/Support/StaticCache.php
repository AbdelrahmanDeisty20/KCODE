<?php

namespace App\Support;

use Closure;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class StaticCache
{
    /**
     * Default cache TTL in seconds (24 hours).
     * Automatically invalidated whenever the underlying model is changed in Filament or anywhere else.
     */
    public const DEFAULT_TTL = 86400;

    /**
     * Sentinel constants to distinguish cache misses from cached null values.
     */
    private const MISS_SENTINEL = '__KCODE_STATIC_CACHE_MISS__';
    private const NULL_SENTINEL = '__KCODE_STATIC_CACHE_NULL__';

    /**
     * In-request runtime memoization (avoids repeated reads from database/file cache within one request).
     *
     * @var array<string, mixed>
     */
    private static array $runtime = [];

    /**
     * In-request cached group versions.
     *
     * @var array<string, int>
     */
    private static array $runtimeVersions = [];

    /**
     * Eloquent Models mapped to the static cache group(s) they invalidate on create/update/delete.
     *
     * @var array<class-string, list<string>>
     */
    public const MODEL_GROUPS = [
        \App\Models\Setting::class                  => ['settings'],
        \App\Models\Faq::class                      => ['faqs'],
        \App\Models\Page::class                     => ['pages'],
        \App\Models\ShippingPolicy::class           => ['policies'],
        \App\Models\ReturnPolicy::class             => ['policies'],
        \App\Models\TermsOfUse::class               => ['policies'],
        \App\Models\PrivacyPolicy::class            => ['policies'],
        \App\Models\CouponPolicy::class             => ['policies'],
        \App\Models\PointsProgramPolicy::class      => ['policies'],
        \App\Models\Country::class                  => ['locations'],
        \App\Models\State::class                    => ['locations'],
        \App\Models\City::class                     => ['locations'],
        \App\Models\Category::class                 => ['categories'],
        \App\Models\Brand::class                    => ['brands'],
        \App\Models\SkinType::class                 => ['skin_types'],
        \App\Models\Concern::class                  => ['concerns'],
        \App\Models\RoutineGoal::class              => ['routine_goals'],
        \App\Models\QuizQuestion::class             => ['quiz_questions'],
        \App\Models\QuizOption::class               => ['quiz_questions'],
        \App\Models\CareGuide::class                => ['care_guide'],
        \App\Models\CareGuideItem::class            => ['care_guide'],
        \App\Models\ProductSelectionCriteria::class => ['selection_criteria'],
        \App\Models\LoyaltyLevel::class             => ['loyalty_levels'],
        \App\Models\ChatbotSuggestion::class        => ['chatbot_suggestions'],
        \App\Models\BlogCategory::class             => ['blog_categories', 'sitemap'],
        \App\Models\BlogTag::class                  => ['blog_tags'],
        \App\Models\Blog::class                     => ['blog_categories', 'sitemap'],
        \App\Models\Product::class                  => ['sitemap'],
        \App\Models\PresetRoutine::class            => ['sitemap'],
    ];

    /**
     * Remember a value inside a named cache group.
     * Works reliably on ALL Laravel cache drivers (database, file, redis, array).
     */
    public static function remember(string $group, string $key, Closure $callback, int $ttl = self::DEFAULT_TTL): mixed
    {
        $runtimeKey = "{$group}:{$key}";

        if (array_key_exists($runtimeKey, self::$runtime)) {
            return self::$runtime[$runtimeKey];
        }

        try {
            $version = self::getGroupVersion($group);
            $fullKey = "kcode_sc:{$group}:v{$version}:{$key}";

            $cached = Cache::get($fullKey, self::MISS_SENTINEL);
            if ($cached !== self::MISS_SENTINEL) {
                $value = $cached === self::NULL_SENTINEL ? null : $cached;
                self::$runtime[$runtimeKey] = $value;
                return $value;
            }

            $value = $callback();
            self::$runtime[$runtimeKey] = $value;

            Cache::put($fullKey, $value === null ? self::NULL_SENTINEL : $value, $ttl);
            self::trackKey($group, $fullKey, $ttl);

            return $value;
        } catch (\Throwable $e) {
            Log::warning("StaticCache fallback for [{$group}:{$key}]: " . $e->getMessage());
            $value = $callback();
            self::$runtime[$runtimeKey] = $value;
            return $value;
        }
    }

    /**
     * Invalidate one or more cache groups immediately.
     */
    public static function forget(string ...$groups): void
    {
        foreach ($groups as $group) {
            self::clearRuntimeGroup($group);

            try {
                $keysKey = "kcode_sc_keys:{$group}";
                $trackedKeys = Cache::get($keysKey, []);

                if (is_array($trackedKeys)) {
                    foreach ($trackedKeys as $trackedKey) {
                        Cache::forget($trackedKey);
                    }
                }

                Cache::forget($keysKey);

                $versionKey = "kcode_sc_v:{$group}";
                $nextVersion = ((int) Cache::get($versionKey, 1)) + 1;
                Cache::forever($versionKey, $nextVersion);
                self::$runtimeVersions[$group] = $nextVersion;
            } catch (\Throwable $e) {
                Log::warning("StaticCache forget failed for [{$group}]: " . $e->getMessage());
            }
        }
    }

    /**
     * Invalidate all cache groups linked to a given Eloquent model instance or class.
     */
    public static function forgetForModel(object|string $model): void
    {
        $class = is_object($model) ? get_class($model) : $model;
        $groups = self::MODEL_GROUPS[$class] ?? [];

        if (!empty($groups)) {
            self::forget(...$groups);
        }
    }

    /**
     * Flush all managed static cache groups.
     */
    public static function flushAll(): void
    {
        $allGroups = array_values(array_unique(array_merge(...array_values(self::MODEL_GROUPS))));
        self::forget(...$allGroups);
        self::$runtime = [];
        self::$runtimeVersions = [];
    }

    /**
     * Clear in-request runtime cache only (useful for tests or long-running workers).
     */
    public static function clearRuntime(): void
    {
        self::$runtime = [];
        self::$runtimeVersions = [];
    }

    /**
     * Helper to paginate a cached Collection while keeping exact Laravel LengthAwarePaginator structure.
     */
    public static function paginateCollection(Collection $items, int $perPage = 10, ?int $page = null): LengthAwarePaginator
    {
        $page = $page ?: Paginator::resolveCurrentPage('page');
        $page = max(1, (int) $page);

        return new LengthAwarePaginator(
            $items->forPage($page, $perPage)->values(),
            $items->count(),
            $perPage,
            $page,
            [
                'path'  => Paginator::resolveCurrentPath(),
                'query' => request()->query(),
            ]
        );
    }

    /**
     * Get the current version number for a cache group.
     */
    private static function getGroupVersion(string $group): int
    {
        if (isset(self::$runtimeVersions[$group])) {
            return self::$runtimeVersions[$group];
        }

        $versionKey = "kcode_sc_v:{$group}";
        $version = (int) Cache::get($versionKey, 0);

        if ($version < 1) {
            $version = 1;
            Cache::forever($versionKey, $version);
        }

        self::$runtimeVersions[$group] = $version;
        return $version;
    }

    /**
     * Track a cache key under its group so explicit Cache::forget() cleans old rows from DB cache table.
     */
    private static function trackKey(string $group, string $fullKey, int $ttl): void
    {
        $keysKey = "kcode_sc_keys:{$group}";
        $trackedKeys = Cache::get($keysKey, []);

        if (!is_array($trackedKeys)) {
            $trackedKeys = [];
        }

        if (!in_array($fullKey, $trackedKeys, true)) {
            $trackedKeys[] = $fullKey;
            Cache::put($keysKey, $trackedKeys, max($ttl, self::DEFAULT_TTL));
        }
    }

    /**
     * Clear in-memory runtime keys for a specific group.
     */
    private static function clearRuntimeGroup(string $group): void
    {
        $prefix = "{$group}:";
        foreach (array_keys(self::$runtime) as $key) {
            if (str_starts_with($key, $prefix)) {
                unset(self::$runtime[$key]);
            }
        }
        unset(self::$runtimeVersions[$group]);
    }
}
