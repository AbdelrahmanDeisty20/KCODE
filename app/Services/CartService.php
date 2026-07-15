<?php

namespace App\Services;

use App\Models\Cart;
use App\Models\CartItem;
use App\Models\Product;

class CartService
{
    /**
     * Add multiple products to the cart at once (bulk add).
     */
    public function addProductsToCart(array $productIds, ?string $sessionId = null): array
    {
        $userId = auth('sanctum')->id();

        // 1. Get or create Cart
        $cart = null;
        if ($userId) {
            $cart = Cart::firstOrCreate(['user_id' => $userId]);
        } elseif ($sessionId) {
            $cart = Cart::firstOrCreate(['session_id' => $sessionId]);
        } else {
            return [
                'status' => false,
                'message' => __('messages.cart_identifier_required'),
                'data' => [],
            ];
        }

        // 2. Validate product IDs exist
        $products = Product::whereIn('id', $productIds)->get();

        if ($products->isEmpty()) {
            return [
                'status' => false,
                'message' => __('messages.no_valid_products_found'),
                'data' => [],
            ];
        }

        // 3. Add products in bulk
        foreach ($products as $product) {
            $quantity = 1;
            $unitPrice = $product->price;
            $discountAmount = 0.00;
            $totalPrice = ($unitPrice - $discountAmount) * $quantity;

            CartItem::updateOrCreate(
                ['cart_id' => $cart->id, 'product_id' => $product->id],
                [
                    'quantity' => $quantity,
                    'unit_price' => $unitPrice,
                    'discount_amount' => $discountAmount,
                    'total_price' => $totalPrice,
                ]
            );
        }

        return [
            'status' => true,
            'message' => __('messages.products_added_to_cart_successfully'),
            'data' => $cart->load('items.product.brand'),
        ];
    }
}
