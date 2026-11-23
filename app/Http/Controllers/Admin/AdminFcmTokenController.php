<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\FcmTokenService;
use App\Services\FirebaseNotificationService;
use Filament\Facades\Filament;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class AdminFcmTokenController extends Controller
{
    /**
     * Store or update an authenticated admin's browser FCM token.
     */
    public function store(Request $request, FcmTokenService $fcmTokenService): JsonResponse
    {
        $user = Filament::auth()->user() ?? $request->user();

        if (!$user || (!$user->hasAnyRole(['admin', 'super_admin']) && $user->type !== 'admin')) {
            abort(403, 'Unauthorized.');
        }

        $validated = $request->validate([
            'token'     => ['required', 'string', 'max:4096'],
            'device_id' => ['nullable', 'string', 'max:255'],
        ]);

        $result = $fcmTokenService->storeToken((int) $user->id, $validated);

        return response()->json($result, ($result['status'] ?? false) ? 200 : 422);
    }

    /**
     * Serve Firebase Cloud Messaging Service Worker dynamically using configured public Web SDK keys.
     */
    public function serviceWorker(): Response
    {
        $webConfig = FirebaseNotificationService::getWebPushPublicConfig();

        if (!$webConfig) {
            return response("// Firebase Web Push is not configured yet.\n", 200, [
                'Content-Type'           => 'application/javascript; charset=UTF-8',
                'Service-Worker-Allowed' => '/',
                'Cache-Control'          => 'no-cache, private',
            ]);
        }

        $firebaseConfigJson = json_encode($webConfig['firebaseConfig'], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);

        $script = <<<JS
importScripts('https://www.gstatic.com/firebasejs/10.14.1/firebase-app-compat.js');
importScripts('https://www.gstatic.com/firebasejs/10.14.1/firebase-messaging-compat.js');

firebase.initializeApp({$firebaseConfigJson});

const messaging = firebase.messaging();

self.addEventListener('notificationclick', function (event) {
    const fcmMsg = event.notification?.data?.FCM_MSG || {};
    const fcmLink = fcmMsg?.fcmOptions?.link;

    if (fcmLink) {
        return;
    }

    event.notification.close();

    const data = fcmMsg?.data || event.notification?.data || {};
    let targetUrl = '/admin';

    if (data.type === 'new_order' && data.order_id) {
        targetUrl = '/admin/orders/' + encodeURIComponent(data.order_id) + '/edit';
    } else if (data.url) {
        targetUrl = data.url;
    }

    const absoluteTarget = targetUrl.startsWith('http')
        ? targetUrl
        : self.location.origin + targetUrl;

    event.waitUntil(
        clients.matchAll({ type: 'window', includeUncontrolled: true }).then(function (clientList) {
            for (const client of clientList) {
                if (client.url === absoluteTarget && 'focus' in client) {
                    return client.focus();
                }
            }

            if (clients.openWindow) {
                return clients.openWindow(absoluteTarget);
            }
        })
    );
});
JS;

        return response($script . "\n", 200, [
            'Content-Type'           => 'application/javascript; charset=UTF-8',
            'Service-Worker-Allowed' => '/',
            'Cache-Control'          => 'no-cache, private',
        ]);
    }
}
