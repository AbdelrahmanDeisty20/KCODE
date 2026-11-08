<?php

namespace App\Filament\Resources\AppNotificationResource\Pages;

use App\Filament\Resources\AppNotificationResource;
use App\Mail\BroadcastNotificationMail;
use App\Models\NewsletterSubscription;
use App\Models\User;
use App\Services\FirebaseNotificationService;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

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
        $channels = $notification->channels ?: ['app'];
        $targetAudience = $notification->target_audience ?: ($notification->user_id ? 'specific_user' : 'all_users');

        $title = $notification->title_ar ?: ($notification->title_en ?? '');
        $body  = $notification->message_ar ?: ($notification->message_en ?? '');

        $sentPush = false;
        $sentEmailCount = 0;

        // 1. CHANNEL: APP / WEB PUSH NOTIFICATION
        if (in_array('app', $channels)) {
            try {
                $firebaseService = app(FirebaseNotificationService::class);
                $fcmData = [
                    'type'            => $notification->type ?? 'general',
                    'notification_id' => (string) $notification->id,
                    'action_url'      => (string) ($notification->action_url ?? ''),
                ];

                if ($targetAudience === 'specific_user' && $notification->user_id) {
                    $firebaseService->sendToUser((int) $notification->user_id, $title, $body, $fcmData);
                    $sentPush = true;
                } elseif (in_array($targetAudience, ['all_users', 'all_everyone'])) {
                    $firebaseService->sendToUsers($title, $body, [], $fcmData);
                    $sentPush = true;
                }
            } catch (\Throwable $e) {
                Log::error('Firebase afterCreate notification error: ' . $e->getMessage());
            }
        }

        // 2. CHANNEL: EMAIL BROADCAST
        if (in_array('email', $channels)) {
            try {
                $recipientEmails = collect();

                if ($targetAudience === 'specific_user' && $notification->user_id) {
                    $user = User::find($notification->user_id);
                    if ($user && !empty($user->email)) {
                        $recipientEmails->push($user->email);
                    }
                } elseif ($targetAudience === 'all_users') {
                    $recipientEmails = User::whereNotNull('email')
                        ->where('email', '!=', '')
                        ->pluck('email');
                } elseif ($targetAudience === 'newsletter') {
                    $recipientEmails = NewsletterSubscription::where('is_active', true)
                        ->pluck('email');
                } elseif ($targetAudience === 'all_everyone') {
                    $userEmails = User::whereNotNull('email')
                        ->where('email', '!=', '')
                        ->pluck('email');
                    $subEmails = NewsletterSubscription::where('is_active', true)
                        ->pluck('email');
                    $recipientEmails = $userEmails->concat($subEmails);
                }

                $recipientEmails = $recipientEmails->filter()->unique()->values();

                if ($recipientEmails->isNotEmpty()) {
                    $mailable = new BroadcastNotificationMail(
                        title: $title,
                        body: $body,
                        actionUrl: $notification->action_url,
                        type: $notification->type ?? 'general'
                    );

                    foreach ($recipientEmails as $email) {
                        try {
                            Mail::to($email)->send($mailable);
                            $sentEmailCount++;
                        } catch (\Throwable $me) {
                            Log::warning("Failed sending email notification to {$email}: " . $me->getMessage());
                        }
                    }

                    Log::info("Broadcast email sent to {$sentEmailCount} recipients for notification #{$notification->id}");
                }
            } catch (\Throwable $e) {
                Log::error('Email broadcast error: ' . $e->getMessage());
            }
        }

        // Feedback toast notification for admin
        $channelLabels = [];
        if (in_array('app', $channels)) {
            $channelLabels[] = '📱 التطبيق والموقع';
        }
        if (in_array('email', $channels)) {
            $channelLabels[] = "📧 البريد الإلكتروني ({$sentEmailCount} مستلم)";
        }

        Notification::make()
            ->title('تم إرسال الإشعار بنجاح 🚀')
            ->body('تم الإرسال عبر: ' . implode(' و ', $channelLabels))
            ->success()
            ->send();
    }
}
