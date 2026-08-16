<?php

namespace App\Observers;

use App\Models\Category;
use App\Services\ActivityLogger;

class CategoryObserver
{
    public function created(Category $category): void
    {
        ActivityLogger::log(
            event: 'created',
            description: "تم إضافة قسم رئيسي جديد: [{$category->name_ar}]",
            subjectType: 'Category',
            subjectId: $category->id,
            newValues: ['name_ar' => $category->name_ar, 'name_en' => $category->name_en]
        );
    }

    public function updated(Category $category): void
    {
        if ($category->wasChanged()) {
            ActivityLogger::log(
                event: 'updated',
                description: "تم تحديث القسم الرئيسي: [{$category->name_ar}]",
                subjectType: 'Category',
                subjectId: $category->id,
                oldValues: array_intersect_key($category->getOriginal(), $category->getChanges()),
                newValues: $category->getChanges()
            );
        }
    }

    public function deleted(Category $category): void
    {
        ActivityLogger::log(
            event: 'deleted',
            description: "تم حذف القسم الرئيسي: [{$category->name_ar}]",
            subjectType: 'Category',
            subjectId: $category->id
        );
    }
}
