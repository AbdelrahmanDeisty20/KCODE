<?php

namespace App\Services;

use App\Models\ShippingPolicy;
use App\Models\ReturnPolicy;
use App\Models\TermsOfUse;
use App\Models\PrivacyPolicy;
use App\Models\CouponPolicy;
use App\Models\PointsProgramPolicy;

class PolicyService
{
    /**
     * Get Shipping Policy.
     */
    public function getShippingPolicy(): array
    {
        $policy = ShippingPolicy::where('is_active', true)->first();
        return $this->formatResponse($policy);
    }

    /**
     * Get Return Policy.
     */
    public function getReturnPolicy(): array
    {
        $policy = ReturnPolicy::where('is_active', true)->first();
        return $this->formatResponse($policy);
    }

    /**
     * Get Terms of Use.
     */
    public function getTermsOfUse(): array
    {
        $policy = TermsOfUse::where('is_active', true)->first();
        return $this->formatResponse($policy);
    }

    /**
     * Get Privacy Policy.
     */
    public function getPrivacyPolicy(): array
    {
        $policy = PrivacyPolicy::where('is_active', true)->first();
        return $this->formatResponse($policy);
    }

    /**
     * Get Coupon Policy.
     */
    public function getCouponPolicy(): array
    {
        $policy = CouponPolicy::where('is_active', true)->first();
        return $this->formatResponse($policy);
    }

    /**
     * Get Points Program Policy.
     */
    public function getPointsProgramPolicy(): array
    {
        $policy = PointsProgramPolicy::where('is_active', true)->first();
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
