<?php

namespace App\Filament\Resources\AppNotificationResource\Pages;

use App\Filament\Resources\AppNotificationResource;
use App\Models\UserFcmToken;
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
                // Send push notification to specific user's tokens
                $tokens = UserFcmToken::where('user_id', $notification->user_id)->pluck('token');

                foreach ($tokens as $token) {
                    try {
                        $firebaseService->sendToToken($token, $title, $body, $data);
                    } catch (\Exception $e) {
                        Log::error("Individual token send error: " . $e->getMessage());
                    }
                }
            } else {
                // Broadcast push notification to all tokens (registered users & guest devices)
                $tokens = UserFcmToken::all();

                foreach ($tokens as $tokenModel) {
                    try {
                        $firebaseService->sendToToken($tokenModel->token, $title, $body, $data);
                    } catch (\Exception $e) {
                        Log::error("Broadcast token send error: " . $e->getMessage());
                    }
                }
            }
        } catch (\Exception $e) {
            Log::error('Firebase afterCreate notification error: ' . $e->getMessage());
        }
    }
}


