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
                $cart = Cart::firstOrCreate(['user_id' => $userId]);
            } elseif (!empty($sessionId)) {
                $cart = Cart::firstOrCreate(['session_id' => $sessionId]);
            } else {
                $sessionId = (string) \Illuminate\Support\Str::uuid();
                $cart = Cart::create(['session_id' => $sessionId]);
            }

            // 2. If routine_id is passed or product_ids is empty, resolve products from routine
            if (!empty($routineId) || empty($productIds)) {
                $routineProductIds = $this->getRoutineProductIds($routineId, $userId);
                if (!empty($routineProductIds)) {
                    $productIds = array_unique(array_merge($productIds, $routineProductIds));
                }
            }

            // 3. Validate product IDs exist
            if (empty($productIds)) {
                return [
                    'status' => false,
                    'message' => __('messages.no_valid_products_found'),
                    'data' => [],
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
                'data' => $cart->fresh(['items.product.brand']),
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
}
