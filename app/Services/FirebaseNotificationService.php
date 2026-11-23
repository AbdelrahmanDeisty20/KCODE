<?php

namespace App\Services;

use Google\Auth\Credentials\ServiceAccountCredentials;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use App\Models\UserFcmToken;

class FirebaseNotificationService
{
    private function resolveCredentialsPath(): ?string
    {
        $configured = trim((string) config('services.firebase.credentials', ''));
        if ($configured !== '') {
            $candidate = str_starts_with($configured, DIRECTORY_SEPARATOR) || preg_match('/^[A-Za-z]:[\/\\\\]/', $configured)
                ? $configured
                : base_path($configured);

            if (is_file($candidate)) {
                return $candidate;
            }
        }

        // Fallback if .env references another filename (e.g. service-account.json) inside storage/app/firebase/
        $matches = glob(storage_path('app/firebase/*.json')) ?: [];
        if (count($matches) === 1 && is_file($matches[0])) {
            return $matches[0];
        }

        $expected = $configured !== '' ? base_path($configured) : storage_path('app/firebase/service-account.json');
        Log::error('Firebase Credentials File Not Found: ' . $expected);

        return null;
    }

    private function resolveProjectId(?string $credentialsPath): ?string
    {
        $projectId = trim((string) config('services.firebase.project_id', ''));
        if ($projectId !== '') {
            return $projectId;
        }

        if ($credentialsPath && is_file($credentialsPath)) {
            $json = json_decode((string) @file_get_contents($credentialsPath), true);
            if (!empty($json['project_id'])) {
                return trim((string) $json['project_id']);
            }
        }

        Log::error('Firebase Notification Error: FIREBASE_PROJECT_ID is not set in config/services.php.');

        return null;
    }

    private function accessToken(?string $credentialsPath = null): ?string
    {
        $credentialsPath ??= $this->resolveCredentialsPath();
        if (!$credentialsPath) {
            return null;
        }

        $cacheKey = 'firebase_oauth_token_' . md5($credentialsPath . '|' . (@filemtime($credentialsPath) ?: 0));
        if ($cached = Cache::get($cacheKey)) {
            return $cached;
        }

        try {
            $json = json_decode((string) file_get_contents($credentialsPath), true);
            if (!is_array($json)) {
                Log::error('Firebase Credentials JSON invalid: ' . $credentialsPath);
                return null;
            }

            $credentials = new ServiceAccountCredentials(
                ['https://www.googleapis.com/auth/firebase.messaging'],
                $json
            );

            $credentials->fetchAuthToken();
            $token = $credentials->getLastReceivedToken()['access_token'] ?? null;

            if ($token) {
                Cache::put($cacheKey, $token, 3000);
            }

            return $token;
        } catch (\Exception $e) {
            Log::error('Firebase Access Token Fetch Error: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Public Firebase Web SDK + VAPID configuration when all required keys are present.
     */
    public static function getWebPushPublicConfig(): ?array
    {
        $web = config('services.firebase.web', []);
        $projectId = trim((string) ($web['project_id'] ?? config('services.firebase.project_id', '')));
        $apiKey = trim((string) ($web['api_key'] ?? ''));
        $messagingSenderId = trim((string) ($web['messaging_sender_id'] ?? ''));
        $appId = trim((string) ($web['app_id'] ?? ''));
        $vapidKey = trim((string) ($web['vapid_key'] ?? ''));

        if ($apiKey === '' || $projectId === '' || $messagingSenderId === '' || $appId === '' || $vapidKey === '') {
            return null;
        }

        return [
            'firebaseConfig' => [
                'apiKey'            => $apiKey,
                'authDomain'        => trim((string) ($web['auth_domain'] ?? '')) ?: "{$projectId}.firebaseapp.com",
                'projectId'         => $projectId,
                'storageBucket'     => trim((string) ($web['storage_bucket'] ?? '')) ?: "{$projectId}.firebasestorage.app",
                'messagingSenderId' => $messagingSenderId,
                'appId'             => $appId,
            ],
            'vapidKey' => $vapidKey,
        ];
    }

    /**
     * Resolve relative path and optional HTTPS link matching the live storefront & admin routes.
     *
     * @return array{path: string, httpsUrl: ?string}
     */
    private function resolveWebPushDestination(array $data): array
    {
        $adminBaseUrl = rtrim((string) config('app.url', ''), '/');
        $frontendUrl = rtrim((string) config('app.frontend_url', ''), '/');
        $type = $data['type'] ?? null;

        [$baseUrl, $path] = match ($type) {
            'new_order' => [
                $adminBaseUrl,
                !empty($data['order_id']) ? '/admin/orders/' . $data['order_id'] . '/edit' : '/admin/orders',
            ],
            'order_status' => [$frontendUrl, '/account?tab=orders'],
            'blog'         => [
                $frontendUrl,
                !empty($data['slug']) ? '/blog/' . $data['slug'] : '/blog',
            ],
            'offer'        => [
                $frontendUrl,
                !empty($data['product_id']) ? '/product/' . $data['product_id'] : '/account?tab=offers',
            ],
            'coupon'       => [$frontendUrl, '/account?tab=offers'],
            default        => [$frontendUrl !== '' ? $frontendUrl : $adminBaseUrl, '/'],
        };

        $fullUrl = ($baseUrl !== '') ? ($baseUrl . ($path === '/' ? '' : $path)) : null;

        return [
            'path'     => $path,
            'httpsUrl' => ($fullUrl && str_starts_with($fullUrl, 'https://')) ? $fullUrl : null,
        ];
    }

    /**
     * Build Web Push headers, notification fields, and click link for Windows/macOS/Web browsers.
     */
    private function buildWebPushPayload(string $title, string $body, array $data): array
    {
        $notification = [
            'title'              => $title,
            'body'               => $body,
            'requireInteraction' => ($data['type'] ?? null) === 'new_order',
        ];

        $appUrl = rtrim((string) config('app.url', ''), '/');
        if (str_starts_with($appUrl, 'https://')) {
            $notification['icon'] = $appUrl . '/favicon.svg';
        }

        $webPush = [
            'headers' => [
                'Urgency' => 'high',
                'TTL'     => '86400',
            ],
            'notification' => $notification,
        ];

        // FCM HTTP v1 rejects non-HTTPS fcm_options.link values with HTTP 400.
        $destination = $this->resolveWebPushDestination($data);
        if ($destination['httpsUrl']) {
            $webPush['fcm_options'] = ['link' => $destination['httpsUrl']];
        }

        return $webPush;
    }

    /**
     * Send real-time high-priority push notification to a single FCM token.
     */
    public function sendToToken(string $token, string $title, string $body, array $data = [])
    {
        $credentialsPath = $this->resolveCredentialsPath();
        $tokenAccess = $this->accessToken($credentialsPath);
        if (!$tokenAccess) {
            Log::error("Firebase Notification Error: Access token could not be generated.");
            return null;
        }

        $projectId = $this->resolveProjectId($credentialsPath);
        if (!$projectId) {
            return null;
        }

        $effectiveData = $data;
        if (!empty($effectiveData) && empty($effectiveData['url']) && empty($effectiveData['click_action'])) {
            $effectiveData['url'] = $this->resolveWebPushDestination($effectiveData)['path'];
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
                'webpush' => $this->buildWebPushPayload($title, $body, $effectiveData),
            ]
        ];

        if (!empty($effectiveData)) {
            // FCM v1 requires data values to be strings
            $payload['message']['data'] = array_map('strval', $effectiveData);
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

        // Fail fast once before iterating all device tokens if credentials or OAuth token cannot be resolved
        $credentialsPath = $this->resolveCredentialsPath();
        if (!$this->accessToken($credentialsPath) || !$this->resolveProjectId($credentialsPath)) {
            Log::error("Firebase Push Broadcasting aborted: missing valid Firebase credentials/token.");
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
