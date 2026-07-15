<?php

namespace App\Http\Controllers\API\Genral;

use App\Http\Controllers\Controller;
use App\Http\Requests\API\CART\AddBulkCartRequest;
use App\Http\Resources\API\CART\CartResource;
use App\Services\CartService;
use App\Traits\ApiResponse;

class CartController extends Controller
{
    use ApiResponse;

    /**
     * Inject CartService
     */
    public function __construct(private CartService $cartService) {}

    /**
     * Add multiple products to the cart at once (bulk add).
     */
    public function addBulk(AddBulkCartRequest $request)
    {
        $data = $request->validated();
        $result = $this->cartService->addProductsToCart(
            $data['product_ids'],
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
}
