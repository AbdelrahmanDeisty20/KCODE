<?php

namespace App\Services;

use Google\Auth\Credentials\ServiceAccountCredentials;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use App\Models\UserFcmToken;

class FirebaseNotificationService
{
    private function accessToken()
    {
        $credentialsFile = env('FIREBASE_CREDENTIALS');

        if (!$credentialsFile || !file_exists(base_path($credentialsFile))) {
            Log::error("Firebase Credentials File Not Found: " . base_path($credentialsFile ?? ''));
            return null;
        }

        try {
            $credentials = new ServiceAccountCredentials(
                ['https://www.googleapis.com/auth/firebase.messaging'],
                json_decode(
                    file_get_contents(base_path($credentialsFile)),
                    true
                )
            );

            $credentials->fetchAuthToken();
            return $credentials->getLastReceivedToken()['access_token'] ?? null;
        } catch (\Exception $e) {
            Log::error("Firebase Access Token Fetch Error: " . $e->getMessage());
            return null;
        }
    }

    /**
     * Send real-time high-priority push notification to a single FCM token.
     */
    public function sendToToken(string $token, string $title, string $body, array $data = [])
    {
        $tokenAccess = $this->accessToken();
        if (!$tokenAccess) {
            Log::error("Firebase Notification Error: Access token could not be generated.");
            return null;
        }

        $projectId = env('FIREBASE_PROJECT_ID');
        if (!$projectId) {
            Log::error("Firebase Notification Error: FIREBASE_PROJECT_ID is not set in env.");
            return null;
        }

        $payload = [
            'message' => [
                'token' => $token,
                'notification' => [
                    'title' => $title,
                    'body'  => $body,
                ],
                'android' => [
                    'priority' => 'HIGH',
                    'notification' => [
                        'sound' => 'default',
                        'default_sound' => true,
                        'default_vibrate_timings' => true,
                        'notification_priority' => 'PRIORITY_MAX',
                    ],
                ],
                'apns' => [
                    'headers' => [
                        'apns-priority' => '10',
                    ],
                    'payload' => [
                        'aps' => [
                            'sound' => 'default',
                            'content-available' => 1,
                        ],
                    ],
                ],
            ]
        ];

        if (!empty($data)) {
            // FCM v1 requires data values to be strings
            $payload['message']['data'] = array_map('strval', $data);
        }

        try {
            $response = Http::withToken($tokenAccess)
                ->post(
                    "https://fcm.googleapis.com/v1/projects/{$projectId}/messages:send",
                    $payload
                )
                ->json();

            // Auto-cleanup if token is unregistered or expired on the device
            if (isset($response['error'])) {
                $message = $response['error']['message'] ?? '';
                $details = $response['error']['details'][0]['errorCode'] ?? '';
                
                if ($message === 'NotRegistered' || $details === 'UNREGISTERED') {
                    UserFcmToken::where('token', $token)->delete();
                    Log::info("Cleaned up expired/unregistered FCM Token from database.");
                }
            }

            return $response;
        } catch (\Exception $e) {
            Log::error("Firebase Notification Send Error: " . $e->getMessage());
            return null;
        }
    }

    /**
     * Send push notification to a specific user.
     */
    public function sendToUser(int $userId, string $title, string $body, array $data = [])
    {
        $tokens = UserFcmToken::where('user_id', $userId)->pluck('token')->unique()->filter()->toArray();
        $responses = [];

        foreach ($tokens as $token) {
            $responses[] = $this->sendToToken($token, $title, $body, $data);
        }

        return $responses;
    }

    /**
     * Send real-time push notification to multiple users (or all users if $userIds is empty).
     */
    public function sendToUsers(string $title, string $body, array $userIds = [], array $data = [])
    {
        $query = UserFcmToken::query();

        if (!empty($userIds)) {
            $query->whereIn('user_id', $userIds);
        }

        $tokens = $query->pluck('token')->unique()->filter()->values()->toArray();
        if (empty($tokens)) {
            Log::info("Firebase Push: No tokens found for broadcasting.");
            return [];
        }

        $responses = [];

        Log::info("Firebase Push Broadcasting started: Title '{$title}', Total target tokens: " . count($tokens));

        foreach ($tokens as $token) {
            try {
                $responses[] = $this->sendToToken($token, $title, $body, $data);
            } catch (\Exception $e) {
                Log::error("Firebase sendToToken error for token '{$token}': " . $e->getMessage());
            }
        }

        Log::info("Firebase Push Broadcasting completed for " . count($tokens) . " tokens.");

        return $responses;
    }
}
