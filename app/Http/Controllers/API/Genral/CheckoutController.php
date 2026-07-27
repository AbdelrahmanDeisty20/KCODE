<?php

namespace App\Http\Controllers\API\Genral;

use App\Http\Controllers\Controller;
use App\Http\Requests\API\CHECKOUT\CheckoutRequest;
use App\Http\Resources\API\CHECKOUTS\CheckoutResource;
use App\Services\CheckoutService;
use App\Traits\ApiResponse;

class CheckoutController extends Controller
{
    use ApiResponse;

    public function __construct(private CheckoutService $checkoutService) {}

    /**
     * Process Cash on Delivery (COD) Checkout.
     */
    public function checkout(CheckoutRequest $request)
    {
        $userId = auth('sanctum')->id();
        $data = $request->validated();

        $result = $this->checkoutService->processCashCheckout($data, $userId);

        if (!$result['status']) {
            return $this->error($result['message'], $result['code'] ?? 400);
        }

        return $this->success(
            new CheckoutResource($result['data']),
            $result['message'],
            201
        );
    }
}
