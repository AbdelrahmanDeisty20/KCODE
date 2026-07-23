<?php

namespace App\Services;

use App\Models\Cart;
use App\Models\CartItem;
use App\Models\Product;
use App\Models\Routine;
use App\Models\RoutineProduct;
use App\Models\FinalRoutine;
use App\Models\FinalRoutineProduct;
use App\Models\Assessment;
use Illuminate\Support\Facades\DB;

class CartService
{
    /**
     * Add multiple products or an entire routine to the cart at once inside a database transaction (bulk add).
     */
    public function addProductsToCart(array $productIds = [], ?string $sessionId = null, ?int $routineId = null): array
    {
        $userId = auth('sanctum')->id();

        return DB::transaction(function () use ($productIds, $sessionId, $routineId, $userId) {
            // 1. Get or create Cart
            $cart = null;
            if ($userId) {
                $guestCart = !empty($sessionId) ? Cart::where('session_id', $sessionId)->whereNull('user_id')->first() : null;
                $userCart  = Cart::where('user_id', $userId)->first();

                if ($guestCart && !$userCart) {
                    $guestCart->update(['user_id' => $userId]);
                    $cart = $guestCart;
                } elseif ($guestCart && $userCart && $guestCart->id !== $userCart->id) {
                    foreach ($guestCart->items as $gItem) {
                        $existing = CartItem::where('cart_id', $userCart->id)->where('product_id', $gItem->product_id)->first();
                        if ($existing) {
                            $existing->update(['quantity' => $existing->quantity + $gItem->quantity]);
                        } else {
                            $gItem->update(['cart_id' => $userCart->id]);
                        }
                    }
                    $guestCart->delete();
                    $cart = $userCart;
                } else {
                    $cart = $userCart ?? Cart::create([
                        'user_id'    => $userId,
                        'session_id' => !empty($sessionId) ? $sessionId : (string) \Illuminate\Support\Str::uuid()
                    ]);
                }

                if (empty($cart->session_id)) {
                    $cart->update(['session_id' => !empty($sessionId) ? $sessionId : (string) \Illuminate\Support\Str::uuid()]);
                } elseif (!empty($sessionId) && $cart->session_id !== $sessionId) {
                    $cart->update(['session_id' => $sessionId]);
                }
            } elseif (!empty($sessionId)) {
                $cart = Cart::firstOrCreate(['session_id' => $sessionId]);
            } else {
                $sessionId = (string) \Illuminate\Support\Str::uuid();
                $cart = Cart::create(['session_id' => $sessionId]);
            }

            // 2. Resolve products from routine_id
            if (!empty($routineId)) {
                $routineProductIds = $this->getRoutineProductIds($routineId, $userId);

                if (empty($routineProductIds)) {
                    return [
                        'status'  => false,
                        'message' => __('messages.no_routine_found'),
                        'data'    => [],
                    ];
                }

                // Check if any product from this routine already exists in cart
                $alreadyInCart = CartItem::where('cart_id', $cart->id)
                    ->whereIn('product_id', $routineProductIds)
                    ->exists();

                if ($alreadyInCart) {
                    return [
                        'status'  => false,
                        'message' => __('messages.routine_already_in_cart'),
                        'data'    => [],
                    ];
                }

                $productIds = array_unique(array_merge($productIds, $routineProductIds));
            }

            // 3. Validate product IDs exist
            if (empty($productIds)) {
                return [
                    'status'  => false,
                    'message' => __('messages.no_valid_products_found'),
                    'data'    => [],
                ];
            }

            $lang = request()->header('lang') ?? app()->getLocale();
            $products = Product::whereIn('id', $productIds)->lockForUpdate()->with('offers')->get();

            if ($products->isEmpty()) {
                return [
                    'status' => false,
                    'message' => __('messages.no_valid_products_found'),
                    'data' => [],
                ];
            }

            // 4. Check stock & Race Condition and add products in bulk with discount calculations
            foreach ($products as $product) {
                $productName = $lang === 'ar' ? $product->name_ar : $product->name_en;

                if ($product->stock <= 0) {
                    return [
                        'status' => false,
                        'message' => __('messages.product_out_of_stock', ['name' => $productName]),
                        'data' => [],
                    ];
                }

                $existingItem = CartItem::where('cart_id', $cart->id)->where('product_id', $product->id)->first();
                $currentCartQuantity = $existingItem ? (int)$existingItem->quantity : 0;
                $targetQuantity = $currentCartQuantity > 0 ? $currentCartQuantity : 1;

                if ($targetQuantity > $product->stock) {
                    return [
                        'status' => false,
                        'message' => __('messages.product_stock_insufficient', [
                            'name' => $productName,
                            'stock' => $product->stock
                        ]),
                        'data' => [],
                    ];
                }

                $unitPrice = (float) $product->price;

                // Calculate active offer discount if exists
                $activeOffer = $product->offers ? $product->offers->first(function ($offer) {
                    return $offer->isActive();
                }) : null;

                $discountPercentage = 0.00;
                $discountAmount = 0.00;
                $priceAfterDiscount = $unitPrice;

                if ($activeOffer) {
                    $discountPercentage = (float) $activeOffer->discount_percentage;
                    $discountAmount = round($unitPrice * ($discountPercentage / 100), 2);
                    $priceAfterDiscount = max(0, round($unitPrice - $discountAmount, 2));
                }

                $totalPrice = round($priceAfterDiscount * $targetQuantity, 2);

                CartItem::updateOrCreate(
                    ['cart_id' => $cart->id, 'product_id' => $product->id],
                    [
                        'quantity'             => $targetQuantity,
                        'unit_price'           => $unitPrice,
                        'discount_amount'      => $discountAmount,
                        'discount_percentage'  => $discountPercentage,
                        'price_after_discount' => $priceAfterDiscount,
                        'total_price'          => $totalPrice,
                    ]
                );
            }

            return [
                'status' => true,
                'message' => __('messages.products_added_to_cart_successfully'),
                'data' => $cart->fresh(['items.product.brand', 'user']),
            ];
        });
    }

    /**
     * Helper to resolve product IDs belonging to a routine.
     */
    private function getRoutineProductIds(?int $routineId = null, ?int $userId = null): array
    {
        // Check by explicit routine_id first
        if ($routineId) {
            // Check FinalRoutine
            $finalRoutine = FinalRoutine::where('id', $routineId)->first();
            if ($finalRoutine) {
                return FinalRoutineProduct::where('final_routine_id', $finalRoutine->id)
                    ->pluck('product_id')
                    ->toArray();
            }

            // Check Routine
            $routine = Routine::where('id', $routineId)->first();
            if ($routine) {
                $routineProducts = RoutineProduct::where('routine_id', $routine->id)->get();
                $ids = [];
                foreach ($routineProducts as $rp) {
                    $ids[] = $rp->replaced_with_product_id ?: $rp->product_id;
                }
                return array_filter($ids);
            }
        }

        // Fallback to user's latest routine if no routine_id passed
        if ($userId) {
            $finalRoutine = FinalRoutine::where('user_id', $userId)->latest()->first();
            if ($finalRoutine) {
                return FinalRoutineProduct::where('final_routine_id', $finalRoutine->id)
                    ->pluck('product_id')
                    ->toArray();
            }

            $assessment = Assessment::where('user_id', $userId)->latest()->first();
            if ($assessment) {
                $routine = Routine::where('assessment_id', $assessment->id)->first();
                if ($routine) {
                    $routineProducts = RoutineProduct::where('routine_id', $routine->id)->get();
                    $ids = [];
                    foreach ($routineProducts as $rp) {
                        $ids[] = $rp->replaced_with_product_id ?: $rp->product_id;
                    }
                    return array_filter($ids);
                }
            }
        }

        return [];
    }

    /**
     * Get user or guest cart.
     */
    public function getCart(string $sessionId): array
    {
        $userId = auth('sanctum')->id();
        $cart = Cart::where('session_id', $sessionId)->first();

        if (!$cart && $userId) {
            $cart = Cart::where('user_id', $userId)->first();
        }

        if (!$cart) {
            return [
                'status'  => false,
                'message' => __('messages.cart_not_found'),
                'code'    => 404,
            ];
        }

        return [
            'status'  => true,
            'message' => __('messages.cart_retrieved_successfully'),
            'data'    => $cart->load(['items.product.brand', 'user']),
        ];
    }

    /**
     * Update quantity of a single cart item.
     */
    public function updateQuantity(int $productId, int $quantity, ?string $sessionId = null): array
    {
        $userId = auth('sanctum')->id();
        $cart = $this->resolveCart($userId, $sessionId);

        if (!$cart) {
            return [
                'status'  => false,
                'message' => __('messages.cart_not_found'),
                'code'    => 404,
            ];
        }

        $cartItem = CartItem::where('cart_id', $cart->id)->where('product_id', $productId)->first();
        if (!$cartItem) {
            return [
                'status'  => false,
                'message' => __('messages.cart_item_not_found'),
                'code'    => 404,
            ];
        }

        $product = Product::with('offers')->find($productId);
        if (!$product) {
            return [
                'status'  => false,
                'message' => __('messages.product_not_found'),
                'code'    => 404,
            ];
        }

        $lang = request()->header('lang') ?? app()->getLocale();
        $productName = $lang === 'ar' ? $product->name_ar : $product->name_en;

        if ($product->stock < $quantity) {
            return [
                'status'  => false,
                'message' => __('messages.product_stock_insufficient', [
                    'name'  => $productName,
                    'stock' => $product->stock
                ]),
                'code'    => 422,
            ];
        }

        $unitPrice = (float) $product->price;
        $activeOffer = $product->offers ? $product->offers->first(fn($o) => $o->isActive()) : null;

        $discountPercentage = 0.00;
        $discountAmount = 0.00;
        $priceAfterDiscount = $unitPrice;

        if ($activeOffer) {
            $discountPercentage = (float) $activeOffer->discount_percentage;
            $discountAmount = round($unitPrice * ($discountPercentage / 100), 2);
            $priceAfterDiscount = max(0, round($unitPrice - $discountAmount, 2));
        }

        $totalPrice = round($priceAfterDiscount * $quantity, 2);

        $cartItem->update([
            'quantity'             => $quantity,
            'unit_price'           => $unitPrice,
            'discount_amount'      => $discountAmount,
            'discount_percentage'  => $discountPercentage,
            'price_after_discount' => $priceAfterDiscount,
            'total_price'          => $totalPrice,
        ]);

        return [
            'status'  => true,
            'message' => __('messages.cart_item_updated_successfully'),
            'data'    => $cart->fresh(['items.product.brand', 'user']),
        ];
    }

    /**
     * Remove a single item from the cart.
     */
    public function removeItem(int $productId, ?string $sessionId = null): array
    {
        $userId = auth('sanctum')->id();
        $cart = $this->resolveCart($userId, $sessionId);

        if (!$cart) {
            return [
                'status'  => false,
                'message' => __('messages.cart_not_found'),
                'code'    => 404,
            ];
        }

        $cartItem = CartItem::where('cart_id', $cart->id)->where('product_id', $productId)->first();
        if (!$cartItem) {
            return [
                'status'  => false,
                'message' => __('messages.cart_item_not_found'),
                'code'    => 404,
            ];
        }

        $cartItem->delete();

        return [
            'status'  => true,
            'message' => __('messages.cart_item_removed_successfully'),
            'data'    => $cart->fresh(['items.product.brand', 'user']),
        ];
    }

    /**
     * Clear all items from the cart.
     */
    public function clearCart(?string $sessionId = null): array
    {
        $userId = auth('sanctum')->id();
        $cart = $this->resolveCart($userId, $sessionId);

        if (!$cart) {
            return [
                'status'  => false,
                'message' => __('messages.cart_not_found'),
                'code'    => 404,
            ];
        }

        $cart->items()->delete();

        return [
            'status'  => true,
            'message' => __('messages.cart_cleared_successfully'),
            'data'    => $cart->fresh(['items.product.brand', 'user']),
        ];
    }

    /**
     * Helper to find existing cart for user or session.
     */
    private function resolveCart(?int $userId = null, ?string $sessionId = null): ?Cart
    {
        if ($userId) {
            $cart = Cart::where('user_id', $userId)->first();
            if ($cart) {
                return $cart;
            }
        }

        if (!empty($sessionId)) {
            return Cart::where('session_id', $sessionId)->first();
        }

        return null;
    }
}
