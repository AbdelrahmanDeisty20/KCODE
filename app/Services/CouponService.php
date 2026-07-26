<?php

namespace App\Services;

use App\Models\Coupon;
use App\Models\Order;
use App\Models\Setting;

class CouponService
{
    /**
     * Apply and validate a coupon code against an order amount.
     */
    public function applyCoupon(string $code, float $orderAmount = 0, ?int $userId = null): array
    {
        $coupon = Coupon::where('code', strtoupper(trim($code)))->first();

        if (!$coupon) {
            return [
                'status'  => false,
                'message' => __('messages.coupon_not_found'),
            ];
        }

        if (!$coupon->is_active) {
            return [
                'status'  => false,
                'message' => __('messages.coupon_invalid_or_expired'),
            ];
        }

        if ($coupon->start_date && now()->lt($coupon->start_date)) {
            return [
                'status'  => false,
                'message' => __('messages.coupon_not_started_yet'),
            ];
        }

        if ($coupon->end_date && now()->gt($coupon->end_date)) {
            return [
                'status'  => false,
                'message' => __('messages.coupon_expired'),
            ];
        }

        if ($coupon->usage_limit && $coupon->used_count >= $coupon->usage_limit) {
            return [
                'status'  => false,
                'message' => __('messages.coupon_usage_limit_reached'),
            ];
        }

        // Check if coupon is assigned to a specific user
        if ($coupon->user_id && $userId && (int)$coupon->user_id !== (int)$userId) {
            return [
                'status'  => false,
                'message' => __('messages.coupon_not_for_user'),
            ];
        }

        // Check per-user limit
        if ($userId && Order::class) {
            $userUsedCount = Order::where('user_id', $userId)
                ->where('coupon_code', $coupon->code)
                ->count();

            $userLimit = $coupon->user_limit ?? 1;
            if ($userUsedCount >= $userLimit) {
                return [
                    'status'  => false,
                    'message' => __('messages.coupon_already_used_by_user'),
                ];
            }
        }

        if ($orderAmount > 0 && $orderAmount < $coupon->min_order_amount) {
            $currencySetting = Setting::where('key_en', 'currency_symbol')->first();
            $currency = $currencySetting ? ($currencySetting->value_ar ?: $currencySetting->value_en) : 'ر.ع';
            return [
                'status'  => false,
                'message' => __('messages.coupon_min_order_required', [
                    'amount'   => $coupon->min_order_amount,
                    'currency' => $currency
                ]),
            ];
        }

        $discountAmount = $coupon->calculateDiscount($orderAmount);
        $finalAmount = max(0, $orderAmount - $discountAmount);

        return [
            'status'  => true,
            'message' => __('messages.coupon_applied_successfully'),
            'data'    => [
                'coupon'          => $coupon,
                'order_amount'    => round($orderAmount, 2),
                'discount_amount' => round($discountAmount, 2),
                'final_amount'    => round($finalAmount, 2),
            ],
        ];
    }

    /**
     * Get active general coupon.
     */
    public function getGeneralCoupon(): array
    {
        $coupon = Coupon::where('is_general', true)->where('is_active', true)->first();

        if (!$coupon) {
            return [
                'status'  => false,
                'message' => __('messages.coupon_not_found'),
            ];
        }

        return [
            'status'  => true,
            'message' => __('messages.coupon_retrieved_successfully'),
            'data'    => $coupon,
        ];
    }

    /**
     * Get announcement banner data (Free shipping min amount + General Coupon).
     */
    public function getAnnouncementBanner(): array
    {
        $lang = request()->header('lang') ?? request()->query('lang') ?? app()->getLocale();
        $lang = strtolower(substr($lang, 0, 2));
        if (!in_array($lang, ['ar', 'en'])) {
            $lang = 'ar';
        }

        // 1. Text: Read strictly from settings table (returns null if empty)
        $announcementSetting = Setting::where('key_en', 'announcement_text')->first();
        $settingText = $announcementSetting ? trim($lang === 'en' ? ($announcementSetting->value_en ?: $announcementSetting->value_ar) : ($announcementSetting->value_ar ?: $announcementSetting->value_en)) : '';
        $bannerText = !empty($settingText) ? $settingText : null;

        // 2. Code: Read strictly from settings table (returns null if empty)
        $codeSetting = Setting::where('key_en', 'announcement_code')->first();
        $settingCode = $codeSetting ? trim($codeSetting->value_en ?: $codeSetting->value_ar) : '';
        $couponCode = !empty($settingCode) ? $settingCode : null;

        return [
            'status'  => true,
            'message' => __('messages.banner_retrieved_successfully'),
            'data'    => [
                'banner_text' => $bannerText,
                'code'        => $couponCode,
            ],
        ];
    }
}
