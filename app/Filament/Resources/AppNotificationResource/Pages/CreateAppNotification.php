<?php

namespace App\Filament\Resources\AppNotificationResource\Pages;

use App\Filament\Resources\AppNotificationResource;
use App\Models\AppNotification;
use App\Models\User;
use App\Services\FirebaseNotificationService;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;

class CreateAppNotification extends CreateRecord
{
    protected static string $resource = AppNotificationResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    protected function handleRecordCreation(array $data): Model
    {
        Log::info("Dashboard CreateAppNotification form submitted with target_type: " . ($data['target_type'] ?? 'all'));

        $targetType = $data['target_type'] ?? 'all';
        $titleAr = $data['title_ar'];
        $titleEn = $data['title_en'] ?? null;
        $messageAr = $data['message_ar'];
        $messageEn = $data['message_en'] ?? null;
        $type = $data['type'] ?? 'general';

        $createdNotification = null;

        if ($targetType === 'all') {
            // General broadcast notification for all users & guest devices
            $createdNotification = AppNotification::create([
                'user_id'    => null,
                'title_ar'   => $titleAr,
                'title_en'   => $titleEn,
                'message_ar' => $messageAr,
                'message_en' => $messageEn,
                'type'       => $type,
                'is_read'    => false,
            ]);
        } else {
            // Selected specific users
            $userIds = $data['user_ids'] ?? [];
            if (!empty($userIds)) {
                foreach ($userIds as $userId) {
                    $createdNotification = AppNotification::create([
                        'user_id'    => $userId,
                        'title_ar'   => $titleAr,
                        'title_en'   => $titleEn,
                        'message_ar' => $messageAr,
                        'message_en' => $messageEn,
                        'type'       => $type,
                        'is_read'    => false,
                    ]);
                }
            }
        }

        // Send Push Notification via Firebase
        try {
            $firebaseService = app(FirebaseNotificationService::class);
            $pushTitle = $titleAr ?: $titleEn;
            $pushBody = $messageAr ?: $messageEn;
            $extraData = [
                'type' => $type,
            ];

            if ($targetType === 'all') {
                // Send to ALL tokens in database (Registered users + Guest devices)
                $firebaseService->sendToUsers($pushTitle, $pushBody, [], $extraData);
            } else if (!empty($userIds)) {
                // Send only to selected user IDs
                $firebaseService->sendToUsers($pushTitle, $pushBody, $userIds, $extraData);
            }
        } catch (\Exception $e) {
            Log::error('Dashboard Push Notification error: ' . $e->getMessage());
        }

        Notification::make()
            ->title('تم إرسال الإشعار بنجاح 📣')
            ->body('تم حفظ الإشعار وإرساله إشعاراً فورياً (Push Notification) عبر Firebase.')
            ->success()
            ->send();

        return $createdNotification;
    }
}

