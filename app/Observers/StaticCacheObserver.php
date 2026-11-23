<?php

namespace App\Observers;

use App\Models\Brand;
use App\Models\Category;
use App\Models\PresetRoutine;
use App\Models\Product;
use App\Support\StaticCache;
use Illuminate\Contracts\Events\ShouldHandleEventsAfterCommit;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class StaticCacheObserver implements ShouldHandleEventsAfterCommit
{
    public function saved(Model $model): void
    {
        $this->flushModelCaches($model);
    }

    public function deleted(Model $model): void
    {
        $this->flushModelCaches($model);
    }

    public function restored(Model $model): void
    {
        $this->flushModelCaches($model);
    }

    public function forceDeleted(Model $model): void
    {
        $this->flushModelCaches($model);
    }

    private function flushModelCaches(Model $model): void
    {
        StaticCache::forgetForModel($model);

        if (
            $model instanceof Product
            || $model instanceof Category
            || $model instanceof Brand
            || $model instanceof PresetRoutine
        ) {
            Cache::forget('groq_store_context_ar');
            Cache::forget('groq_store_context_en');
        }
    }
}
