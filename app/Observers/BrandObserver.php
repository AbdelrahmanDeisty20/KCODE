<?php

namespace App\Observers;

use App\Models\Brand;
use App\Services\ActivityLogger;

class BrandObserver
{
    public function created(Brand $brand): void
    {
        ActivityLogger::log(
            event: 'created',
            description: "تم إضافة علامة تجارية جديدة: [{$brand->name_ar}]",
            subjectType: 'Brand',
            subjectId: $brand->id,
            newValues: ['name_ar' => $brand->name_ar, 'name_en' => $brand->name_en]
        );
    }

    public function updated(Brand $brand): void
    {
        if ($brand->wasChanged()) {
            ActivityLogger::log(
                event: 'updated',
                description: "تم تحديث العلامة التجارية: [{$brand->name_ar}]",
                subjectType: 'Brand',
                subjectId: $brand->id,
                oldValues: array_intersect_key($brand->getOriginal(), $brand->getChanges()),
                newValues: $brand->getChanges()
            );
        }
    }

    public function deleted(Brand $brand): void
    {
        ActivityLogger::log(
            event: 'deleted',
            description: "تم حذف العلامة التجارية: [{$brand->name_ar}]",
            subjectType: 'Brand',
            subjectId: $brand->id
        );
    }
}
