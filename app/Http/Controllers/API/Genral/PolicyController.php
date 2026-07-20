<?php

namespace App\Http\Controllers\API\Genral;

use App\Http\Controllers\Controller;
use App\Http\Resources\API\POLICY\ShippingPolicyResource;
use App\Http\Resources\API\POLICY\ReturnPolicyResource;
use App\Http\Resources\API\POLICY\TermsOfUseResource;
use App\Http\Resources\API\POLICY\PrivacyPolicyResource;
use App\Http\Resources\API\POLICY\CouponPolicyResource;
use App\Http\Resources\API\POLICY\PointsProgramPolicyResource;
use App\Services\PolicyService;
use App\Traits\ApiResponse;

class PolicyController extends Controller
{
    use ApiResponse;

    public function __construct(private PolicyService $policyService) {}

    /**
     * Get Shipping Policy.
     */
    public function getShippingPolicy()
    {
        $result = $this->policyService->getShippingPolicy();
        if (!$result['status']) {
            return $this->error($result['message']);
        }
        return $this->success(new ShippingPolicyResource($result['data']), $result['message']);
    }

    /**
     * Get Return Policy.
     */
    public function getReturnPolicy()
    {
        $result = $this->policyService->getReturnPolicy();
        if (!$result['status']) {
            return $this->error($result['message']);
        }
        return $this->success(new ReturnPolicyResource($result['data']), $result['message']);
    }

    /**
     * Get Terms of Use.
     */
    public function getTermsOfUse()
    {
        $result = $this->policyService->getTermsOfUse();
        if (!$result['status']) {
            return $this->error($result['message']);
        }
        return $this->success(new TermsOfUseResource($result['data']), $result['message']);
    }

    /**
     * Get Privacy Policy.
     */
    public function getPrivacyPolicy()
    {
        $result = $this->policyService->getPrivacyPolicy();
        if (!$result['status']) {
            return $this->error($result['message']);
        }
        return $this->success(new PrivacyPolicyResource($result['data']), $result['message']);
    }

    /**
     * Get Coupon Policy.
     */
    public function getCouponPolicy()
    {
        $result = $this->policyService->getCouponPolicy();
        if (!$result['status']) {
            return $this->error($result['message']);
        }
        return $this->success(new CouponPolicyResource($result['data']), $result['message']);
    }

    /**
     * Get Points Program Policy.
     */
    public function getPointsProgramPolicy()
    {
        $result = $this->policyService->getPointsProgramPolicy();
        if (!$result['status']) {
            return $this->error($result['message']);
        }
        return $this->success(new PointsProgramPolicyResource($result['data']), $result['message']);
    }
}
