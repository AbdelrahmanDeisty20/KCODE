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
     * Process coupon notifications (Push, DB, and optional Email for subscribers).
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
                // --- CASE 1: GENERAL COUPON (لكل الناس) ---
                // DO NOT send to NewsletterSubscription list
                // DO send Real-time FCM Push Notification & DB notification to all users/devices

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

                // 2. Broadcast FCM Push Notification to all users
                $firebaseService->sendToUsers($titleAr, $messageAr, [], [
                    'type'            => 'coupon',
                    'category'        => 'general',
                    'notification_id' => (string) $notification->id,
                    'coupon_code'     => (string) $coupon->code,
                ]);

                Log::info("General Coupon notification sent for code {$coupon->code}");

            } else {
                // --- CASE 2: PRIVATE COUPON (لكوبون خاص لمستخدم معين) ---
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

                // 2. Send Real-time FCM Push Notification to the user
                $firebaseService->sendToUser($user->id, $titleAr, $messageAr, [
                    'type'        => 'coupon',
                    'category'    => 'private',
                    'coupon_code' => (string) $coupon->code,
                ]);

                // 3. Check if user's email is subscribed in NewsletterSubscription
                if (!empty($user->email)) {
                    $userEmail = strtolower(trim($user->email));

                    $isSubscribed = NewsletterSubscription::whereRaw('LOWER(TRIM(email)) = ?', [$userEmail])
                        ->where('is_active', true)
                        ->exists();

                    if ($isSubscribed) {
                        try {
                            Mail::to($user->email)->send(new CouponMail($coupon, $user));
                            Log::info("Private Coupon email sent to subscribed user {$user->email} for code {$coupon->code}");
                        } catch (\Exception $e) {
                            Log::error("Failed sending private coupon email to {$user->email}: " . $e->getMessage());
                        }
                    } else {
                        Log::info("Private Coupon email skipped for {$user->email} (Not in NewsletterSubscription)");
                    }
                }
            }
        } catch (\Exception $e) {
            Log::error("CouponObserver Error: " . $e->getMessage());
        }
    }
}
