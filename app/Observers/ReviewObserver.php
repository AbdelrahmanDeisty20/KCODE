<?php

namespace App\Observers;

use App\Models\Review;
use App\Services\ActivityLogger;

class ReviewObserver
{
    public function created(Review $review): void
    {
        $userName = $review->user?->name ?: ($review->user_name ?: 'عميل');
        $productName = $review->product?->name_ar ?: 'منتج';

        ActivityLogger::log(
            event: 'created',
            description: "قام العميل [{$userName}] بإضافة تقييم جديد للمنتج [{$productName}] (التقييم: {$review->rating}/5)",
            subjectType: 'Review',
            subjectId: $review->id,
            newValues: [
                'rating'  => $review->rating,
                'comment' => $review->comment,
            ]
        );
    }

    public function deleted(Review $review): void
    {
        ActivityLogger::log(
            event: 'deleted',
            description: "تم حذف تقييم العميل للمنتج #{$review->product_id}",
            subjectType: 'Review',
            subjectId: $review->id
        );
    }
}
