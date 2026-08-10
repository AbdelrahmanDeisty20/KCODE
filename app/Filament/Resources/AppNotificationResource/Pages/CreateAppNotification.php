<?php

namespace App\Filament\Resources\AppNotificationResource\Pages;

use App\Filament\Resources\AppNotificationResource;
use App\Models\AppNotification;
use App\Models\User;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Database\Eloquent\Model;

class CreateAppNotification extends CreateRecord
{
    protected static string $resource = AppNotificationResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    protected function handleRecordCreation(array $data): Model
    {
        $targetType = $data['target_type'] ?? 'all';
        $titleAr = $data['title_ar'];
        $titleEn = $data['title_en'] ?? null;
        $messageAr = $data['message_ar'];
        $messageEn = $data['message_en'] ?? null;
        $type = $data['type'] ?? 'general';

        $userIds = [];

        if ($targetType === 'all') {
            $userIds = User::pluck('id')->toArray();
        } else {
            $userIds = $data['user_ids'] ?? [];
        }

        $createdNotification = null;

        if (!empty($userIds)) {
            foreach ($userIds as $userId) {
                $createdNotification = AppNotification::create([
                    'user_id' => $userId,
                    'title_ar' => $titleAr,
                    'title_en' => $titleEn,
                    'message_ar' => $messageAr,
                    'message_en' => $messageEn,
                    'type' => $type,
                    'is_read' => false,
                ]);
            }
        } else {
            $createdNotification = AppNotification::create([
                'user_id' => null,
                'title_ar' => $titleAr,
                'title_en' => $titleEn,
                'message_ar' => $messageAr,
                'message_en' => $messageEn,
                'type' => $type,
                'is_read' => false,
            ]);
        }

        Notification::make()
            ->title('تم إرسال الإشعار بنجاح 📣')
            ->body('تم حفظ وإرسال الإشعار لجميع المستهدفين المعنيين.')
            ->success()
            ->send();

        return $createdNotification;
    }
}
