<?php

namespace App\Services;

use App\Models\AppNotification;
use Illuminate\Support\Facades\Log;

class AppNotificationService
{
    /**
     * Get user notifications paginated.
     */
    public function getUserNotifications(int $userId, int $perPage = 10): array
    {
        try {
            $notifications = AppNotification::where('user_id', $userId)
                ->orWhereNull('user_id')
                ->latest()
                ->paginate($perPage);

            return [
                'status'  => true,
                'message' => __('messages.notifications_retrieved_successfully'),
                'data'    => $notifications,
            ];
        } catch (\Exception $e) {
            Log::error('Notifications Fetch Error: ' . $e->getMessage());

            return [
                'status'  => false,
                'message' => __('messages.notifications_fetch_failed'),
                'data'    => [],
            ];
        }
    }

    /**
     * Mark a notification as read.
     */
    public function markAsRead(int $userId, int $notificationId): array
    {
        try {
            $notification = AppNotification::where('id', $notificationId)
                ->where(function ($q) use ($userId) {
                    $q->where('user_id', $userId)->orWhereNull('user_id');
                })
                ->first();

            if (!$notification) {
                return [
                    'status'  => false,
                    'message' => __('messages.notification_not_found'),
                    'code'    => 404,
                ];
            }

            $notification->update(['is_read' => true]);

            return [
                'status'  => true,
                'message' => __('messages.notification_marked_as_read'),
                'data'    => $notification,
            ];
        } catch (\Exception $e) {
            Log::error('Notification Mark As Read Error: ' . $e->getMessage());

            return [
                'status'  => false,
                'message' => __('messages.notification_update_failed'),
                'code'    => 500,
            ];
        }
    }
}
