<?php

namespace App\Observers;

use App\Models\SubCategory;
use App\Services\ActivityLogger;

class SubCategoryObserver
{
    public function created(SubCategory $subCategory): void
    {
        ActivityLogger::log(
            event: 'created',
            description: "تم إضافة قسم فرعي جديد: [{$subCategory->name_ar}]",
            subjectType: 'SubCategory',
            subjectId: $subCategory->id,
            newValues: ['name_ar' => $subCategory->name_ar, 'name_en' => $subCategory->name_en]
        );
    }

    public function updated(SubCategory $subCategory): void
    {
        if ($subCategory->wasChanged()) {
            ActivityLogger::log(
                event: 'updated',
                description: "تم تحديث القسم الفرعي: [{$subCategory->name_ar}]",
                subjectType: 'SubCategory',
                subjectId: $subCategory->id,
                oldValues: array_intersect_key($subCategory->getOriginal(), $subCategory->getChanges()),
                newValues: $subCategory->getChanges()
            );
        }
    }

    public function deleted(SubCategory $subCategory): void
    {
        ActivityLogger::log(
            event: 'deleted',
            description: "تم حذف القسم الفرعي: [{$subCategory->name_ar}]",
            subjectType: 'SubCategory',
            subjectId: $subCategory->id
        );
    }
}
