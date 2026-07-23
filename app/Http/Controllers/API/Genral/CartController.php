<?php

namespace App\Http\Controllers\API\Genral;

use App\Http\Controllers\Controller;
use App\Http\Requests\API\CART\AddBulkCartRequest;
use App\Http\Requests\API\CART\AddSingleCartRequest;
use App\Http\Requests\API\CART\GetCartRequest;
use App\Http\Requests\API\CART\UpdateCartQuantityRequest;
use App\Http\Requests\API\CART\RemoveCartItemRequest;
use App\Http\Resources\API\AUHT\UserResource;
use App\Http\Resources\API\CART\CartItemResource;
use App\Http\Resources\API\CART\CartResource;
use App\Services\CartService;
use App\Traits\ApiResponse;

class CartController extends Controller
{
    use ApiResponse;

    /**
     * Inject CartService
     */
    public function __construct(
        private CartService $cartService
    ) {}

    /**
     * Add a single product to the cart.
     */
    public function addSingle(AddSingleCartRequest $request)
    {
        $data = $request->validated();
        $result = $this->cartService->addSingleProduct(
            (int) $data['product_id'],
            (int) ($data['quantity'] ?? 1),
            $data['session_id'] ?? null
        );

        if (!$result['status']) {
            return $this->error($result['message']);
        }

        return $this->success(
            new CartResource($result['data']),
            $result['message']
        );
    }

    /**
     * Add multiple products or an entire routine to the cart at once (bulk add).
     */
    public function addBulk(AddBulkCartRequest $request)
    {
        $data = $request->validated();
        $result = $this->cartService->addProductsToCart(
            $data['product_ids'] ?? [],
            $data['session_id'] ?? null,
            $data['routine_id'] ?? null
        );

        if (!$result['status']) {
            return $this->error($result['message']);
        }

        return $this->success(
            new CartResource($result['data']),
            $result['message']
        );
    }

    /**
     * Get user or guest cart.
     */
    public function getCart(GetCartRequest $request)
    {
        $data = $request->validated();
        $result = $this->cartService->getCart($data['session_id']);

        if (!$result['status']) {
            $code = $result['code'] ?? 400;
            return $this->error($result['message'], $code);
        }

        return $this->success(
            new CartResource($result['data']),
            $result['message']
        );
    }

    /**
     * Update quantity of a product in the cart.
     */
    public function updateQuantity(UpdateCartQuantityRequest $request)
    {
        $data = $request->validated();
        $result = $this->cartService->updateQuantity(
            (int) $data['product_id'],
            (int) $data['quantity'],
            $data['session_id'] ?? null
        );

        if (!$result['status']) {
            $code = $result['code'] ?? 400;
            return $this->error($result['message'], $code);
        }

        return $this->success(
            new CartResource($result['data']),
            $result['message']
        );
    }

    /**
     * Remove an item from the cart.
     */
    public function removeItem(RemoveCartItemRequest $request)
    {
        $data = $request->validated();
        $result = $this->cartService->removeItem(
            (int) $data['product_id'],
            $data['session_id'] ?? null
        );

        if (!$result['status']) {
            $code = $result['code'] ?? 400;
            return $this->error($result['message'], $code);
        }

        return $this->success(
            new CartResource($result['data']),
            $result['message']
        );
    }

    /**
     * Clear all items from the cart.
     */
    public function clearCart(\Illuminate\Http\Request $request)
    {
        $sessionId = $request->query('session_id') ?? $request->input('session_id');
        $result = $this->cartService->clearCart($sessionId);

        if (!$result['status']) {
            $code = $result['code'] ?? 400;
            return $this->error($result['message'], $code);
        }

        return $this->success(
            new CartResource($result['data']),
            $result['message']
        );
    }
}
