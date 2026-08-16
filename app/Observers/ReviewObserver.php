<?php

namespace App\Observers;

use App\Models\Review;
use App\Models\User;
use App\Services\ActivityLogger;
use Filament\Actions\Action;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\Log;

class ReviewObserver
{
    public function created(Review $review): void
    {
        try {
            $userName = $review->user?->name ?: 'عميل';
            $productName = $review->product?->name_ar ?: 'منتج';

            // 1. Log activity in Activity Log
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

            // 2. Send Filament Database Notification ONLY to Admins
            $admins = User::where(function ($query) {
                $query->where('type', 'admin')
                    ->orWhereHas('roles', fn ($q) => $q->whereIn('name', ['admin', 'super_admin', 'Admin', 'Super Admin']))
                    ->orWhere('email', 'admin@kcode.com');
            })->get();

            if ($admins->isEmpty()) {
                $admins = User::where('id', 1)->get();
            }

            if ($admins->isNotEmpty()) {
                $reviewUrl = \App\Filament\Resources\ReviewResource::getUrl('edit', ['record' => $review->id]);

                Notification::make()
                    ->title("تقييم جديد للمنتج ⭐")
                    ->body("قام العميل [{$userName}] بإضافة تقييم ({$review->rating}/5) للمنتج [{$productName}]")
                    ->icon('heroicon-o-star')
                    ->iconColor('warning')
                    ->actions([
                        Action::make('view_review')
                            ->label('عرض التقييم 👁️')
                            ->url($reviewUrl)
                            ->button()
                            ->color('warning')
                            ->markAsRead(),
                    ])
                    ->sendToDatabase($admins);
            }

            Log::info("ReviewObserver admin notification sent for Review ID {$review->id}");
        } catch (\Exception $e) {
            Log::error("ReviewObserver Error: " . $e->getMessage());
        }
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
