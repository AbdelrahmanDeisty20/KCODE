<?php

namespace App\Filament\Resources\AppNotificationResource\Pages;

use App\Filament\Resources\AppNotificationResource;
use App\Services\FirebaseNotificationService;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Facades\Log;

class CreateAppNotification extends CreateRecord
{
    protected static string $resource = AppNotificationResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    protected function afterCreate(): void
    {
        $notification = $this->record;

        try {
            $firebaseService = app(FirebaseNotificationService::class);

            $data = [
                'type'            => $notification->type ?? 'general',
                'notification_id' => (string) $notification->id,
            ];

            $title = $notification->title_ar ?: ($notification->title_en ?? '');
            $body = $notification->message_ar ?: ($notification->message_en ?? '');

            if ($notification->user_id) {
                // Send deduplicated push notification to specific user
                $firebaseService->sendToUser((int) $notification->user_id, $title, $body, $data);
            } else {
                // Broadcast deduplicated push notification to all users/devices
                $firebaseService->sendToUsers($title, $body, [], $data);
            }
        } catch (\Exception $e) {
            Log::error('Firebase afterCreate notification error: ' . $e->getMessage());
        }
    }
}
