<?php

namespace App\Observers;

use App\Support\StaticCache;
use Illuminate\Contracts\Events\ShouldHandleEventsAfterCommit;
use Illuminate\Database\Eloquent\Model;

class StaticCacheObserver implements ShouldHandleEventsAfterCommit
{
    public function saved(Model $model): void
    {
        StaticCache::forgetForModel($model);
    }

    public function deleted(Model $model): void
    {
        StaticCache::forgetForModel($model);
    }

    public function restored(Model $model): void
    {
        StaticCache::forgetForModel($model);
    }

    public function forceDeleted(Model $model): void
    {
        StaticCache::forgetForModel($model);
    }
}
