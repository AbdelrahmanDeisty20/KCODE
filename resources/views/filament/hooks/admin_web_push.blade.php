@php
    $authUser = auth()->user();
    $isAdminPushUser = $authUser
        && ($authUser->type === 'admin' || (method_exists($authUser, 'hasAnyRole') && $authUser->hasAnyRole(['admin', 'super_admin'])));
    $pushConfig = $isAdminPushUser
        ? \App\Services\FirebaseNotificationService::getWebPushPublicConfig()
        : null;
    $isEnglish = app()->getLocale() === 'en';
@endphp

@if ($pushConfig)
    <div id="kcode-webpush-banner"
         style="display:none; position:fixed; bottom:1.15rem; left:50%; transform:translateX(-50%); z-index:45; background:#2E1633; color:#FFFFFF; padding:0.55rem 0.9rem; border-radius:9999px; box-shadow:0 8px 24px rgba(46,22,51,0.32); align-items:center; gap:0.65rem; font-size:0.84rem; font-weight:600; border:1px solid rgba(255,255,255,0.14);">
        <span style="white-space:nowrap;">
            {{ $isEnglish ? 'Get Windows push alerts for new orders' : 'فعّل إشعارات الويندوز الفورية للطلبات الجديدة' }}
        </span>
        <button id="kcode-webpush-enable-btn" type="button"
                style="background:#93254E; color:#FFFFFF; border:none; border-radius:9999px; padding:0.32rem 0.78rem; font-size:0.78rem; font-weight:700; cursor:pointer; white-space:nowrap;">
            {{ $isEnglish ? 'Enable' : 'تفعيل الآن' }}
        </button>
        <button id="kcode-webpush-dismiss-btn" type="button" aria-label="Close"
                style="background:transparent; color:rgba(255,255,255,0.75); border:none; cursor:pointer; font-size:0.95rem; line-height:1; padding:0 0.2rem;">
            ×
        </button>
    </div>

    <script type="module">
        import { initializeApp } from 'https://www.gstatic.com/firebasejs/10.14.1/firebase-app.js';
        import { getMessaging, getToken, onMessage } from 'https://www.gstatic.com/firebasejs/10.14.1/firebase-messaging.js';

        (function initKcodeAdminWebPush() {
            if (!window.isSecureContext || !('Notification' in window) || !('serviceWorker' in navigator)) {
                return;
            }

            const pushConfig = @json($pushConfig);
            const saveEndpoint = {!! json_encode(route('filament.admin.fcm-token.store', [], false), JSON_UNESCAPED_SLASHES) !!};
            const userId = @json((string) auth()->id());
            const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || @json(csrf_token());
            const banner = document.getElementById('kcode-webpush-banner');
            const enableBtn = document.getElementById('kcode-webpush-enable-btn');
            const dismissBtn = document.getElementById('kcode-webpush-dismiss-btn');
            const dismissedKey = 'kcode_admin_webpush_dismissed_' + userId;
            const syncedKey = 'kcode_admin_fcm_synced_' + userId;
            const deviceStorageKey = 'kcode_admin_fcm_device_id';

            const getDeviceId = () => {
                let deviceId = localStorage.getItem(deviceStorageKey);
                if (!deviceId) {
                    const rand = (window.crypto && typeof window.crypto.randomUUID === 'function')
                        ? window.crypto.randomUUID()
                        : Math.random().toString(36).slice(2) + Date.now().toString(36);
                    deviceId = 'kcode-admin-' + rand;
                    localStorage.setItem(deviceStorageKey, deviceId);
                }
                return deviceId;
            };

            const app = initializeApp(pushConfig.firebaseConfig);
            const messaging = getMessaging(app);
            let messagingBound = false;

            const syncTokenWithServer = async () => {
                try {
                    const registration = await navigator.serviceWorker.register('/firebase-messaging-sw.js', { scope: '/' });
                    await navigator.serviceWorker.ready;

                    const token = await getToken(messaging, {
                        vapidKey: pushConfig.vapidKey,
                        serviceWorkerRegistration: registration,
                    });

                    if (!token) {
                        return false;
                    }

                    if (localStorage.getItem(syncedKey) !== token) {
                        const response = await fetch(saveEndpoint, {
                            method: 'POST',
                            credentials: 'same-origin',
                            headers: {
                                'Content-Type': 'application/json',
                                'Accept': 'application/json',
                                'X-CSRF-TOKEN': csrfToken,
                            },
                            body: JSON.stringify({
                                token: token,
                                device_id: getDeviceId(),
                            }),
                        });

                        if (response.ok) {
                            localStorage.setItem(syncedKey, token);
                        }
                    }

                    if (!messagingBound) {
                        messagingBound = true;
                        onMessage(messaging, (payload) => {
                            const title = payload?.notification?.title || payload?.data?.title || 'KCODE';
                            const body = payload?.notification?.body || payload?.data?.body || '';
                            const orderId = payload?.data?.order_id;
                            const type = payload?.data?.type;

                            if (Notification.permission === 'granted') {
                                const notice = new Notification(title, {
                                    body: body,
                                    icon: '/favicon.svg',
                                    tag: orderId ? ('kcode-order-' + orderId) : undefined,
                                });
                                notice.onclick = () => {
                                    window.focus();
                                    if (type === 'new_order' && orderId) {
                                        window.location.href = '/admin/orders/' + encodeURIComponent(orderId) + '/edit';
                                    }
                                    notice.close();
                                };
                            }
                        });
                    }

                    return true;
                } catch (err) {
                    console.warn('[KCODE WebPush] Failed to initialize:', err?.message || err);
                    return false;
                }
            };

            if (Notification.permission === 'granted') {
                syncTokenWithServer();
                return;
            }

            if (Notification.permission === 'default' && !sessionStorage.getItem(dismissedKey) && banner) {
                banner.style.display = 'flex';
            }

            dismissBtn?.addEventListener('click', () => {
                sessionStorage.setItem(dismissedKey, '1');
                banner.style.display = 'none';
            });

            enableBtn?.addEventListener('click', async () => {
                enableBtn.disabled = true;
                try {
                    const permission = await Notification.requestPermission();
                    if (permission === 'granted') {
                        banner.style.display = 'none';
                        await syncTokenWithServer();
                    } else if (permission === 'denied') {
                        sessionStorage.setItem(dismissedKey, '1');
                        banner.style.display = 'none';
                    }
                } finally {
                    enableBtn.disabled = false;
                }
            });
        })();
    </script>
@endif
