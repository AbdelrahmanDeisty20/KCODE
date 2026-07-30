<?php

namespace App\Services;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\Coupon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class OrderService
{
    /**
     * Get orders list for a user.
     */
    public function getUserOrders(int $userId): array
    {
        $orders = Order::where('user_id', $userId)
            ->with(['items.product.brand', 'address'])
            ->orderBy('id', 'desc')
            ->paginate(10);

        return [
            'status'  => true,
            'message' => __('messages.orders_retrieved_successfully'),
            'data'    => $orders,
        ];
    }

    /**
     * Get specific order details.
     */
    public function getOrderDetails(int $orderId, ?int $userId = null): array
    {
        $query = Order::where('id', $orderId)->with(['items.product.brand', 'address']);
        if ($userId) {
            $query->where('user_id', $userId);
        }

        $order = $query->first();

        if (!$order) {
            return [
                'status'  => false,
                'message' => __('messages.order_not_found'),
                'code'    => 404,
            ];
        }

        return [
            'status'  => true,
            'message' => __('messages.order_retrieved_successfully'),
            'data'    => $order,
        ];
    }

    /**
     * Delete/Cancel an order (Allowed for 'pending' and 'delivered' statuses).
     */
    public function deleteOrder(int $orderId, ?int $userId = null): array
    {
        try {
            $query = Order::where('id', $orderId)->with('items');
            if ($userId) {
                $query->where('user_id', $userId);
            }

            $order = $query->first();

            if (!$order) {
                return [
                    'status'  => false,
                    'message' => __('messages.order_not_found'),
                    'code'    => 404,
                ];
            }

            $allowedStatuses = ['pending', 'delivered','cancelled'];
            $currentStatus = strtolower($order->order_status);

            if (!in_array($currentStatus, $allowedStatuses)) {
                return [
                    'status'  => false,
                    'message' => __('messages.cannot_delete_order_status', ['status' => $order->order_status]),
                    'code'    => 422,
                ];
            }

            return DB::transaction(function () use ($order, $currentStatus) {
                // If status is pending, restore product stock & sales_count and coupon used_count
                if ($currentStatus === 'pending') {
                    foreach ($order->items as $item) {
                        $product = Product::find($item->product_id);
                        if ($product) {
                            $product->increment('stock', $item->quantity);
                            if ($product->sales_count >= $item->quantity) {
                                $product->decrement('sales_count', $item->quantity);
                            }
                        }
                    }

                    if (!empty($order->coupon_code)) {
                        $coupon = Coupon::where('code', $order->coupon_code)->first();
                        if ($coupon && $coupon->used_count > 0) {
                            $coupon->decrement('used_count');
                        }
                    }
                }

                // Delete order items and order record
                $order->items()->delete();
                $order->delete();

                return [
                    'status'  => true,
                    'message' => __('messages.order_deleted_successfully'),
                ];
            });
        } catch (\Exception $e) {
            Log::error('Error deleting order: ' . $e->getMessage());
            return [
                'status'  => false,
                'message' => $e->getMessage(),
                'code'    => 500,
            ];
        }
    }
}
