<?php

namespace App\Services;

use App\Models\ShippingPolicy;
use App\Models\ReturnPolicy;
use App\Models\TermsOfUse;
use App\Models\PrivacyPolicy;
use App\Models\CouponPolicy;
use App\Models\PointsProgramPolicy;
use App\Support\StaticCache;

class PolicyService
{
    /**
     * Get Shipping Policy (cached).
     */
    public function getShippingPolicy(): array
    {
        $policy = StaticCache::remember('policies', 'shipping', fn () => ShippingPolicy::where('is_active', true)->first());
        return $this->formatResponse($policy);
    }

    /**
     * Get Return Policy (cached).
     */
    public function getReturnPolicy(): array
    {
        $policy = StaticCache::remember('policies', 'return', fn () => ReturnPolicy::where('is_active', true)->first());
        return $this->formatResponse($policy);
    }

    /**
     * Get Terms of Use (cached).
     */
    public function getTermsOfUse(): array
    {
        $policy = StaticCache::remember('policies', 'terms', fn () => TermsOfUse::where('is_active', true)->first());
        return $this->formatResponse($policy);
    }

    /**
     * Get Privacy Policy (cached).
     */
    public function getPrivacyPolicy(): array
    {
        $policy = StaticCache::remember('policies', 'privacy', fn () => PrivacyPolicy::where('is_active', true)->first());
        return $this->formatResponse($policy);
    }

    /**
     * Get Coupon Policy (cached).
     */
    public function getCouponPolicy(): array
    {
        $policy = StaticCache::remember('policies', 'coupon', fn () => CouponPolicy::where('is_active', true)->first());
        return $this->formatResponse($policy);
    }

    /**
     * Get Points Program Policy (cached).
     */
    public function getPointsProgramPolicy(): array
    {
        $policy = StaticCache::remember('policies', 'points_program', fn () => PointsProgramPolicy::where('is_active', true)->first());
        return $this->formatResponse($policy);
    }

    /**
     * Helper to format policy response.
     */
    private function formatResponse($policy): array
    {
        if (!$policy) {
            return [
                'status'  => false,
                'message' => __('messages.policy_not_found'),
            ];
        }

        return [
            'status'  => true,
            'message' => __('messages.policy_retrieved_successfully'),
            'data'    => $policy,
        ];
    }
}
