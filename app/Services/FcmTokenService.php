<?php

namespace App\Services;

use App\Models\UserFcmToken;
use Illuminate\Support\Facades\Log;

class FcmTokenService
{
    /**
     * Store or update FCM Token in database.
     */
    public function storeToken(?int $userId, array $data): array
    {
        try {
            $token = $data['token'];
            $deviceId = $data['device_id'] ?? null;
            $finalUserId = $userId ?? ($data['user_id'] ?? null);

            $fcmRecord = UserFcmToken::updateOrCreate(
                [
                    'token' => $token,
                ],
                [
                    'user_id'   => $finalUserId,
                    'device_id' => $deviceId,
                ]
            );

            return [
                'status'  => true,
                'message' => __('messages.fcm_token_saved_successfully'),
                'data'    => $fcmRecord,
            ];
        } catch (\Exception $e) {
            Log::error('FCM Token Save Error: ' . $e->getMessage());

            return [
                'status'  => false,
                'message' => __('messages.fcm_token_save_failed'),
                'data'    => null,
            ];
        }
    }
}
