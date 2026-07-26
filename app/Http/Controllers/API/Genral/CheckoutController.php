<?php

namespace App\Http\Controllers\API\Genral;

use App\Http\Controllers\Controller;
use App\Http\Requests\API\CHECKOUT\CheckoutRequest;
use App\Http\Resources\API\ORDER\OrderResource;
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
            new OrderResource($result['data']),
            $result['message'],
            201
        );
    }

    /**
     * Get list of orders for authenticated user.
     */
    public function index()
    {
        $userId = auth('sanctum')->id();
        $result = $this->checkoutService->getUserOrders($userId);

        return $this->paginated(
            OrderResource::class,
            $result['data'],
            $result['message']
        );
    }

    /**
     * Get specific order details by ID.
     */
    public function show($id)
    {
        $userId = auth('sanctum')->id();
        $result = $this->checkoutService->getOrderDetails((int) $id, $userId);

        if (!$result['status']) {
            return $this->error($result['message'], $result['code'] ?? 404);
        }

        return $this->success(
            new OrderResource($result['data']),
            $result['message']
        );
    }
}
