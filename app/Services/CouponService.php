<?php

namespace App\Services;

use App\Models\Coupon;
use App\Models\Setting;

class CouponService
{
    /**
     * Apply and validate a coupon code against an order amount.
     */
    public function applyCoupon(string $code, float $orderAmount = 0): array
    {
        $coupon = Coupon::where('code', strtoupper(trim($code)))->first();

        if (!$coupon) {
            return [
                'status'  => false,
                'message' => __('messages.coupon_invalid_or_expired'),
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

        if ($orderAmount > 0 && $orderAmount < $coupon->min_order_amount) {
            $currency = Setting::where('key_en', 'currency_symbol')->first()?->value ?? 'ر.ع';
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

        $freeShippingSetting = Setting::where('key_en', 'free_shipping_min_amount')->first();
        $currencySetting = Setting::where('key_en', 'currency_symbol')->first();

        $minAmount = $freeShippingSetting ? ($freeShippingSetting->value_en ?: $freeShippingSetting->value_ar) : '25';
        $currencyAr = $currencySetting ? $currencySetting->value_ar : 'ر.ع';
        $currencyEn = $currencySetting ? $currencySetting->value_en : 'OMR';

        $generalCoupon = Coupon::where('is_general', true)->where('is_active', true)->first();
        $couponCode = $generalCoupon ? $generalCoupon->code : 'KCODE10';

        $bannerTextAr = "شحن مجاني للطلبات فوق {$minAmount} {$currencyAr} | كود الخصم: {$couponCode}";
        $bannerTextEn = "Free shipping on orders over {$minAmount} {$currencyEn} | Discount Code: {$couponCode}";

        $bannerText = $lang === 'en' ? $bannerTextEn : $bannerTextAr;
        $currency = $lang === 'en' ? $currencyEn : $currencyAr;

        return [
            'status'  => true,
            'message' => __('messages.banner_retrieved_successfully'),
            'data'    => [
                'free_shipping_min_amount' => (float)$minAmount,
                'currency'                 => $currency,
                'coupon_code'              => $couponCode,
                'general_coupon'           => $generalCoupon,
                'banner_text'              => $bannerText,
                'banner_text_ar'           => $bannerTextAr,
                'banner_text_en'           => $bannerTextEn,
            ],
        ];
    }
}
