<?php

namespace App\Http\Controllers\API\Genral;

use App\Http\Controllers\Controller;
use App\Http\Requests\API\COUPON\ApplyCouponRequest;
use App\Http\Resources\API\COUPON\CouponResource;
use App\Services\CouponService;
use App\Traits\ApiResponse;

use App\Http\Resources\API\BANNER\AnnouncementBannerResource;

class CouponController extends Controller
{
    use ApiResponse;

    public function __construct(private CouponService $couponService) {}

    /**
     * Apply and validate coupon code.
     */
    public function applyCoupon(ApplyCouponRequest $request)
    {
        $orderAmount = (float) $request->input('order_amount', 0);
        $result = $this->couponService->applyCoupon($request->code, $orderAmount);

        if (!$result['status']) {
            return $this->error($result['message'], 422);
        }

        $data = $result['data'];
        $data['coupon'] = new CouponResource($data['coupon']);

        return $this->success($data, $result['message']);
    }

    /**
     * Get active general coupon.
     */
    public function getGeneralCoupon()
    {
        $result = $this->couponService->getGeneralCoupon();

        if (!$result['status']) {
            return $this->error($result['message'], 404);
        }

        return $this->success(new CouponResource($result['data']), $result['message']);
    }

    /**
     * Get Announcement Banner data.
     */
    public function getAnnouncementBanner()
    {
        $result = $this->couponService->getAnnouncementBanner();

        if (!$result['status']) {
            return $this->error($result['message']);
        }

        return $this->success(new AnnouncementBannerResource($result['data']), $result['message']);
    }
}
