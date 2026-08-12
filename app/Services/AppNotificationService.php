<?php

namespace App\Services;

use App\Models\AppNotification;
use App\Models\AppNotificationUserStatus;
use App\Models\UserFcmToken;
use Illuminate\Support\Facades\Log;

class AppNotificationService
{
    /**
     * Get user notifications paginated (Personal + General, excluding deleted).
     */
    public function getUserNotifications(int $userId, int $perPage = 10): array
    {
        try {
            $deletedIds = AppNotificationUserStatus::where('user_id', $userId)
                ->where('is_deleted', true)
                ->pluck('app_notification_id')
                ->toArray();

            $readIds = AppNotificationUserStatus::where('user_id', $userId)
                ->where('is_read', true)
                ->pluck('app_notification_id')
                ->toArray();

            $notifications = AppNotification::where(function ($q) use ($userId) {
                    $q->where('user_id', $userId)->orWhereNull('user_id');
                })
                ->whereNotIn('id', $deletedIds)
                ->latest()
                ->paginate($perPage);

            $notifications->getCollection()->transform(function ($item) use ($readIds) {
                $item->is_read = (bool) ($item->is_read || in_array($item->id, $readIds));
                return $item;
            });

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
     * Get public general notifications paginated (where user_id IS NULL, no auth required).
     */
    public function getPublicGeneralNotifications(?string $deviceId = null, int $perPage = 10): array
    {
        try {
            $query = AppNotification::whereNull('user_id');

            if (!empty($deviceId)) {
                $fcmTokenRecord = UserFcmToken::where('device_id', $deviceId)->first();
                if ($fcmTokenRecord) {
                    $deletedIds = AppNotificationUserStatus::where('user_fcm_token_id', $fcmTokenRecord->id)
                        ->where('is_deleted', true)
                        ->pluck('app_notification_id')
                        ->toArray();

                    if (!empty($deletedIds)) {
                        $query->whereNotIn('id', $deletedIds);
                    }
                }
            }

            $notifications = $query->latest()->paginate($perPage);

            return [
                'status'  => true,
                'message' => __('messages.notifications_retrieved_successfully'),
                'data'    => $notifications,
            ];
        } catch (\Exception $e) {
            Log::error('Public General Notifications Fetch Error: ' . $e->getMessage());

            return [
                'status'  => false,
                'message' => __('messages.notifications_fetch_failed'),
                'data'    => [],
            ];
        }
    }

    /**
     * Mark a notification as read for a specific user.
     */
    public function markAsRead(int $userId, int $notificationId): array
    {
        try {
            $deletedIds = AppNotificationUserStatus::where('user_id', $userId)
                ->where('is_deleted', true)
                ->pluck('app_notification_id')
                ->toArray();

            if (in_array($notificationId, $deletedIds)) {
                return [
                    'status'  => false,
                    'message' => __('messages.notification_not_found'),
                    'code'    => 404,
                ];
            }

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

            if ($notification->user_id === $userId) {
                $notification->update(['is_read' => true]);
            }

            AppNotificationUserStatus::updateOrCreate(
                [
                    'user_id'             => $userId,
                    'app_notification_id' => $notificationId,
                ],
                [
                    'is_read' => true,
                    'read_at' => now(),
                ]
            );

            $notification->is_read = true;

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

    /**
     * Mark all notifications as read for a user.
     */
    public function markAllAsRead(int $userId): array
    {
        try {
            $deletedIds = AppNotificationUserStatus::where('user_id', $userId)
                ->where('is_deleted', true)
                ->pluck('app_notification_id')
                ->toArray();

            $notifications = AppNotification::where(function ($q) use ($userId) {
                    $q->where('user_id', $userId)->orWhereNull('user_id');
                })
                ->whereNotIn('id', $deletedIds)
                ->get();

            foreach ($notifications as $notification) {
                if ($notification->user_id === $userId) {
                    $notification->update(['is_read' => true]);
                }

                AppNotificationUserStatus::updateOrCreate(
                    [
                        'user_id'             => $userId,
                        'app_notification_id' => $notification->id,
                    ],
                    [
                        'is_read' => true,
                        'read_at' => now(),
                    ]
                );
            }

            return [
                'status'  => true,
                'message' => __('messages.all_notifications_marked_as_read'),
            ];
        } catch (\Exception $e) {
            Log::error('Notifications Mark All As Read Error: ' . $e->getMessage());

            return [
                'status'  => false,
                'message' => __('messages.notification_update_failed'),
                'code'    => 500,
            ];
        }
    }

    /**
     * Delete a specific notification for a user.
     */
    public function deleteNotification(int $userId, int $notificationId): array
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

            $existingStatus = AppNotificationUserStatus::where('user_id', $userId)
                ->where('app_notification_id', $notificationId)
                ->first();

            if ($existingStatus && $existingStatus->is_deleted) {
                return [
                    'status'  => false,
                    'message' => __('messages.notification_already_deleted'),
                    'code'    => 400,
                ];
            }

            AppNotificationUserStatus::updateOrCreate(
                [
                    'user_id'             => $userId,
                    'app_notification_id' => $notificationId,
                ],
                [
                    'is_deleted' => true,
                    'deleted_at' => now(),
                ]
            );

            return [
                'status'  => true,
                'message' => __('messages.notification_deleted_successfully'),
            ];
        } catch (\Exception $e) {
            Log::error('Notification Delete Error: ' . $e->getMessage());

            return [
                'status'  => false,
                'message' => __('messages.notification_delete_failed'),
                'code'    => 500,
            ];
        }
    }

    /**
     * Clear / Delete all notifications for a user.
     */
    public function clearAllNotifications(int $userId): array
    {
        try {
            $deletedIds = AppNotificationUserStatus::where('user_id', $userId)
                ->where('is_deleted', true)
                ->pluck('app_notification_id')
                ->toArray();

            $notificationIds = AppNotification::where(function ($q) use ($userId) {
                    $q->where('user_id', $userId)->orWhereNull('user_id');
                })
                ->whereNotIn('id', $deletedIds)
                ->pluck('id');

            foreach ($notificationIds as $notificationId) {
                AppNotificationUserStatus::updateOrCreate(
                    [
                        'user_id'             => $userId,
                        'app_notification_id' => $notificationId,
                    ],
                    [
                        'is_deleted' => true,
                        'deleted_at' => now(),
                    ]
                );
            }

            return [
                'status'  => true,
                'message' => __('messages.all_notifications_cleared_successfully'),
            ];
        } catch (\Exception $e) {
            Log::error('Clear All Notifications Error: ' . $e->getMessage());

            return [
                'status'  => false,
                'message' => __('messages.notification_delete_failed'),
                'code'    => 500,
            ];
        }
    }

    /**
     * Delete a specific public general notification for a device using device_id.
     */
    public function deleteGeneralNotificationByDeviceId(string $deviceId, int $notificationId): array
    {
        try {
            $notification = AppNotification::where('id', $notificationId)
                ->whereNull('user_id')
                ->first();

            if (!$notification) {
                return [
                    'status'  => false,
                    'message' => __('messages.notification_not_found'),
                    'code'    => 404,
                ];
            }

            $fcmTokenRecord = UserFcmToken::where('device_id', $deviceId)->first();

            if (!$fcmTokenRecord) {
                $fcmTokenRecord = UserFcmToken::create([
                    'device_id' => $deviceId,
                    'token'     => $deviceId,
                ]);
            }

            $existingStatus = AppNotificationUserStatus::where('user_fcm_token_id', $fcmTokenRecord->id)
                ->where('app_notification_id', $notificationId)
                ->first();

            if ($existingStatus && $existingStatus->is_deleted) {
                return [
                    'status'  => false,
                    'message' => __('messages.notification_already_deleted'),
                    'code'    => 400,
                ];
            }

            AppNotificationUserStatus::updateOrCreate(
                [
                    'user_fcm_token_id'   => $fcmTokenRecord->id,
                    'app_notification_id' => $notificationId,
                ],
                [
                    'is_deleted' => true,
                    'deleted_at' => now(),
                ]
            );

            return [
                'status'  => true,
                'message' => __('messages.notification_deleted_successfully'),
            ];
        } catch (\Exception $e) {
            Log::error('General Notification Delete Error: ' . $e->getMessage());

            return [
                'status'  => false,
                'message' => __('messages.notification_delete_failed'),
                'code'    => 500,
            ];
        }
    }

    /**
     * Clear all public general notifications for a device using device_id.
     */
    public function clearAllGeneralNotificationsByDeviceId(string $deviceId): array
    {
        try {
            $fcmTokenRecord = UserFcmToken::where('device_id', $deviceId)->first();

            if (!$fcmTokenRecord) {
                $fcmTokenRecord = UserFcmToken::create([
                    'device_id' => $deviceId,
                    'token'     => $deviceId,
                ]);
            }

            $deletedIds = AppNotificationUserStatus::where('user_fcm_token_id', $fcmTokenRecord->id)
                ->where('is_deleted', true)
                ->pluck('app_notification_id')
                ->toArray();

            $notificationIds = AppNotification::whereNull('user_id')
                ->whereNotIn('id', $deletedIds)
                ->pluck('id');

            foreach ($notificationIds as $notificationId) {
                AppNotificationUserStatus::updateOrCreate(
                    [
                        'user_fcm_token_id'   => $fcmTokenRecord->id,
                        'app_notification_id' => $notificationId,
                    ],
                    [
                        'is_deleted' => true,
                        'deleted_at' => now(),
                    ]
                );
            }

            return [
                'status'  => true,
                'message' => __('messages.all_notifications_cleared_successfully'),
            ];
        } catch (\Exception $e) {
            Log::error('Clear All General Notifications Error: ' . $e->getMessage());

            return [
                'status'  => false,
                'message' => __('messages.notification_delete_failed'),
                'code'    => 500,
            ];
        }
    }
}
