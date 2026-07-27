<?php

namespace App\Http\Controllers\API\Genral;

use App\Http\Controllers\Controller;
use App\Http\Resources\API\ORDER\OrderItemResource;
use App\Http\Resources\API\ORDER\OrderResource;
use App\Services\OrderService;
use App\Traits\ApiResponse;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    use ApiResponse;

    public function __construct(private OrderService $orderService) {}

    /**
     * Get list of orders for authenticated user.
     */
    public function index()
    {
        $userId = auth('sanctum')->id();
        $result = $this->orderService->getUserOrders($userId);

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
        $result = $this->orderService->getOrderDetails((int) $id, $userId);

        if (!$result['status']) {
            return $this->error($result['message'], $result['code'] ?? 404);
        }

        return $this->success(
            new OrderItemResource($result['data']),
            $result['message']
        );
    }

    /**
     * Delete/cancel order by ID (allowed for pending and delivered statuses).
     */
    public function destroy($id)
    {
        $userId = auth('sanctum')->id();
        $result = $this->orderService->deleteOrder((int) $id, $userId);

        if (!$result['status']) {
            return $this->error($result['message'], $result['code'] ?? 400);
        }

        return $this->messageOnly($result['message']);
    }
}
