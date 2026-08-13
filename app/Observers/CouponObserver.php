<?php

namespace App\Observers;

use App\Models\Coupon;
use App\Models\User;
use App\Models\NewsletterSubscription;
use App\Models\AppNotification;
use App\Services\FirebaseNotificationService;
use App\Mail\CouponMail;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;

class CouponObserver
{
    /**
     * Handle the Coupon "created" event.
     */
    public function created(Coupon $coupon): void
    {
        $this->processCouponNotification($coupon);
    }

    /**
     * Handle the Coupon "updated" event.
     */
    public function updated(Coupon $coupon): void
    {
        if ($coupon->wasChanged('is_active') && $coupon->is_active) {
            $this->processCouponNotification($coupon);
        }
    }

    /**
     * Process coupon notifications:
     * - General Coupon: Real-time Push Notification to all users + Email Broadcast to NewsletterSubscription.
     * - Private Coupon: Real-time Push Notification ONLY to target user (No email).
     */
    protected function processCouponNotification(Coupon $coupon): void
    {
        try {
            if (!$coupon->is_active) {
                return;
            }

            $firebaseService = app(FirebaseNotificationService::class);
            $discountText = $coupon->discount_type === 'percentage'
                ? "{$coupon->discount_value}%"
                : "{$coupon->discount_value} ج.م";

            // Check if coupon is General vs Private
            $isGeneral = (bool) ($coupon->is_general || is_null($coupon->user_id));

            if ($isGeneral) {
                // --- CASE 1: GENERAL COUPON (الكوبون العام) ---
                $titleAr   = "كوبون خصم جديد للجميع! 🎁";
                $titleEn   = "New Discount Coupon for Everyone! 🎁";
                $messageAr = "استخدم كود الخصم [{$coupon->code}] واحصل على خصم {$discountText} عند الشراء!";
                $messageEn = "Use promo code [{$coupon->code}] and get {$discountText} off!";

                // 1. Create general AppNotification in DB
                $notification = AppNotification::create([
                    'user_id'    => null,
                    'title_ar'   => $titleAr,
                    'title_en'   => $titleEn,
                    'message_ar' => $messageAr,
                    'message_en' => $messageEn,
                    'type'       => 'general_coupon',
                    'data'       => [
                        'coupon_id'      => (string) $coupon->id,
                        'coupon_code'    => (string) $coupon->code,
                        'discount_value' => (string) $coupon->discount_value,
                        'discount_type'  => (string) $coupon->discount_type,
                    ],
                    'is_read'    => false,
                ]);

                // 2. Broadcast FCM Push Notification to all users/devices
                $firebaseService->sendToUsers($titleAr, $messageAr, [], [
                    'type'            => 'coupon',
                    'category'        => 'general',
                    'notification_id' => (string) $notification->id,
                    'coupon_code'     => (string) $coupon->code,
                ]);

                // 3. Broadcast Email to ALL subscribers in NewsletterSubscription
                $subscriberEmails = NewsletterSubscription::where('is_active', true)
                    ->pluck('email')
                    ->filter()
                    ->unique();

                foreach ($subscriberEmails as $email) {
                    try {
                        Mail::to($email)->send(new CouponMail($coupon, null));
                        Log::info("General Coupon email sent to newsletter subscriber {$email} for code {$coupon->code}");
                    } catch (\Exception $e) {
                        Log::error("Failed sending general coupon email to {$email}: " . $e->getMessage());
                    }
                }

                Log::info("General Coupon notification and email broadcast processed for code {$coupon->code}");

            } else {
                // --- CASE 2: PRIVATE COUPON (الكوبون الخاص - إشعار لحظي فقط) ---
                $user = User::find($coupon->user_id);
                if (!$user) {
                    return;
                }

                $titleAr   = "كوبون خصم خاص بك! 🎁";
                $titleEn   = "Private Discount Coupon for You! 🎁";
                $messageAr = "خصيصاً لك! استخدم كود الخصم الحصري [{$coupon->code}] واحصل على خصم {$discountText}!";
                $messageEn = "Exclusively for you! Use promo code [{$coupon->code}] and get {$discountText} off!";

                // 1. Create personal AppNotification in DB
                AppNotification::create([
                    'user_id'    => $user->id,
                    'title_ar'   => $titleAr,
                    'title_en'   => $titleEn,
                    'message_ar' => $messageAr,
                    'message_en' => $messageEn,
                    'type'       => 'private_coupon',
                    'data'       => [
                        'coupon_id'      => (string) $coupon->id,
                        'coupon_code'    => (string) $coupon->code,
                        'discount_value' => (string) $coupon->discount_value,
                        'discount_type'  => (string) $coupon->discount_type,
                    ],
                    'is_read'    => false,
                ]);

                // 2. Send Real-time FCM Push Notification ONLY to the target user (NO EMAIL)
                $firebaseService->sendToUser($user->id, $titleAr, $messageAr, [
                    'type'        => 'coupon',
                    'category'    => 'private',
                    'coupon_code' => (string) $coupon->code,
                ]);

                Log::info("Private Coupon push notification sent to User ID {$user->id} for code {$coupon->code} (No email as configured)");
            }
        } catch (\Exception $e) {
            Log::error("CouponObserver Error: " . $e->getMessage());
        }
    }
}
