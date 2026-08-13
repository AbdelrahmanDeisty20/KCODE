<?php

namespace App\Services;

use App\Models\UserFcmToken;
use Illuminate\Support\Facades\Log;

class FcmTokenService
{
    /**
     * Store or update FCM Token in database, ensuring zero duplicate tokens.
     */
    public function storeToken(?int $userId, array $data): array
    {
        try {
            $token = $data['token'];
            $deviceId = $data['device_id'] ?? null;
            $finalUserId = $userId ?? ($data['user_id'] ?? null);

            if (!empty($deviceId)) {
                // Remove older token records for this device that have a different token string
                UserFcmToken::where('device_id', $deviceId)
                    ->where('token', '!=', $token)
                    ->delete();

                $fcmRecord = UserFcmToken::updateOrCreate(
                    [
                        'device_id' => $deviceId,
                    ],
                    [
                        'user_id' => $finalUserId,
                        'token'   => $token,
                    ]
                );
            } else {
                $fcmRecord = UserFcmToken::updateOrCreate(
                    [
                        'token' => $token,
                    ],
                    [
                        'user_id'   => $finalUserId,
                        'device_id' => null,
                    ]
                );
            }

            // Cleanup any duplicate rows having the exact same token string across the table
            UserFcmToken::where('token', $token)
                ->where('id', '!=', $fcmRecord->id)
                ->delete();

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
