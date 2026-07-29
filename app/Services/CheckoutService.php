<?php

namespace App\Services;

use App\Models\Address;
use App\Models\Cart;
use App\Models\CartItem;
use App\Models\City;
use App\Models\Country;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\Setting;
use App\Models\State;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class CheckoutService
{
    /**
     * Process Cash on Delivery (COD) Checkout.
     */
    public function processCashCheckout(array $data, ?int $userId = null): array
    {
        try {
            $sessionId  = $data['session_id'] ?? null;
            $addressId  = isset($data['address_id']) ? (int) $data['address_id'] : null;
            $couponCode = $data['coupon_code'] ?? null;
            $notes      = $data['notes'] ?? null;

            $user = $userId ? User::find($userId) : null;

            // 1. Resolve user_name and phone
            $userName = $data['user_name'] ?? ($user ? $user->name : 'عميل KCODE');
            $phone    = $data['user_phone'] ?? $data['phone'] ?? ($user ? $user->phone : null);

            // 2. Resolve Address (from pre-existing address_id if provided)
            $address = null;
            if (!empty($addressId)) {
                $address = Address::with(['country', 'state', 'city'])->find($addressId);

                if (!$address) {
                    return [
                        'status'  => false,
                        'message' => __('messages.invalid_address'),
                        'code'    => 422,
                    ];
                }

                if ($userId && $address->user_id !== $userId) {
                    return [
                        'status'  => false,
                        'message' => __('messages.invalid_address'),
                        'code'    => 403,
                    ];
                }
            }

            // 3. Resolve Cart
            $cart = null;
            if ($userId) {
                $cart = Cart::where('user_id', $userId)->first();
            }
            if (!$cart && !empty($sessionId)) {
                $cart = Cart::where('session_id', $sessionId)->first();
            }

            if (!$cart) {
                return [
                    'status'  => false,
                    'message' => __('messages.cart_not_found'),
                    'code'    => 404,
                ];
            }

            $cartItems = CartItem::where('cart_id', $cart->id)->get();
            if ($cartItems->isEmpty()) {
                return [
                    'status'  => false,
                    'message' => __('messages.cart_is_empty'),
                    'code'    => 400,
                ];
            }

            $lang = request()->header('lang') ?? app()->getLocale();

            // 4. DB Transaction for atomic processing
            return DB::transaction(function () use ($cart, $cartItems, $address, $userId, $couponCode, $notes, $userName, $phone, $data, $lang) {
                $productIds = $cartItems->pluck('product_id')->toArray();

                // Lock products for update
                $products = Product::whereIn('id', $productIds)->lockForUpdate()->get()->keyBy('id');

                $subtotal = 0.00;
                $orderItemsData = [];

                foreach ($cartItems as $item) {
                    $product = $products->get($item->product_id);
                    if (!$product) {
                        return [
                            'status'  => false,
                            'message' => __('messages.product_not_found'),
                            'code'    => 404,
                        ];
                    }

                    $productName = $lang === 'ar' ? $product->name_ar : $product->name_en;

                    if ($product->stock <= 0) {
                        return [
                            'status'  => false,
                            'message' => __('messages.product_out_of_stock', ['name' => $productName]),
                            'code'    => 422,
                        ];
                    }

                    if ($item->quantity > $product->stock) {
                        return [
                            'status'  => false,
                            'message' => __('messages.product_stock_insufficient', [
                                'name'  => $productName,
                                'stock' => $product->stock,
                            ]),
                            'code'    => 422,
                        ];
                    }

                    $unitPrice = (float) ($item->unit_price > 0 ? $item->unit_price : ($product->price_after_discount ?: $product->price));
                    $itemTotalPrice = (float) ($item->total_price > 0 ? $item->total_price : ($unitPrice * $item->quantity));
                    $subtotal += $itemTotalPrice;

                    $orderItemsData[] = [
                        'product'          => $product,
                        'product_id'       => $product->id,
                        'product_name'     => $productName,
                        'quantity'         => $item->quantity,
                        'unit_price'       => $unitPrice,
                        'discount_amount'  => (float) ($item->discount_amount ?? 0.00),
                        'total_price'      => $itemTotalPrice,
                    ];
                }

                // 5. Calculate coupon discount if provided
                $discountAmount = 0.00;
                $coupon = null;
                if (!empty($couponCode)) {
                    $couponService = new CouponService();
                    $couponResult = $couponService->applyCoupon($couponCode, $subtotal, $userId);

                    if (!$couponResult['status']) {
                        return [
                            'status'  => false,
                            'message' => $couponResult['message'],
                            'code'    => 422,
                        ];
                    }

                    $discountAmount = (float) $couponResult['data']['discount_amount'];
                    $coupon = $couponResult['data']['coupon'] ?? null;
                }

                // 6. Calculate shipping fee based on city's shipping_fee
                $shippingFee = 0.00;
                if ($address && $address->city) {
                    $shippingFee = (float) ($address->city->shipping_fee ?? 0.00);
                } else {
                    $cityId = $data['city_id'] ?? null;
                    if ($cityId) {
                        $cityObj = City::find($cityId);
                        $shippingFee = $cityObj ? (float) ($cityObj->shipping_fee ?? 0.00) : 0.00;
                    }
                }
                $finalTotal = max(0.00, round($subtotal - $discountAmount + $shippingFee, 2));

                // 7. Order Number & Shipping Address Snapshot
                $orderNumber = 'KCODE-' . date('Ymd') . '-' . strtoupper(Str::random(5));

                if ($address) {
                    $shippingAddressSnapshot = [
                        'user_name'   => $userName,
                        'user_phone'  => $phone ?? $address->phone,
                        'title'       => $address->title,
                        'address'     => $address->address,
                        'street'      => $data['street'] ?? $address->address,
                        'building_no' => $data['building_no'] ?? null,
                        'city'        => $address->city ? $address->city->name : null,
                        'state'       => $address->state ? $address->state->name : null,
                        'country'     => $address->country ? $address->country->name : null,
                        'country_id'  => $address->country_id,
                        'state_id'    => $address->state_id,
                        'city_id'     => $address->city_id,
                        'notes'       => $notes,
                    ];
                } else {
                    $country = !empty($data['country_id']) ? Country::find($data['country_id']) : null;
                    $state   = !empty($data['state_id']) ? State::find($data['state_id']) : null;
                    $city    = !empty($data['city_id']) ? City::find($data['city_id']) : null;

                    $shippingAddressSnapshot = [
                        'user_name'   => $userName,
                        'user_phone'  => $phone,
                        'address'     => $data['address'] ?? $data['street'] ?? null,
                        'city'        => $city ? $city->name : null,
                        'state'       => $state ? $state->name : null,
                        'country'     => $country ? $country->name : null,
                        'notes'       => $notes,
                    ];
                }

                // 8. Create Order Record
                $order = Order::create([
                    'order_number'     => $orderNumber,
                    'user_id'          => $userId,
                    'user_name'        => $userName,
                    'user_phone'       => $phone,
                    'address_id'       => $address ? $address->id : null,
                    'shipping_address' => $shippingAddressSnapshot,
                    'payment_method'   => $data['payment_method'] ?? 'cash',
                    'payment_status'   => 'pending',
                    'order_status'     => 'pending',
                    'subtotal'         => round($subtotal, 2),
                    'discount_amount'  => round($discountAmount, 2),
                    'shipping_fee'     => round($shippingFee, 2),
                    'total'            => $finalTotal,
                    'coupon_code'      => $couponCode,
                    'notes'            => $notes,
                ]);

                // 9. Create Order Items & Decrement Stock
                foreach ($orderItemsData as $itemData) {
                    OrderItem::create([
                        'order_id'        => $order->id,
                        'product_id'      => $itemData['product_id'],
                        'product_name'    => $itemData['product_name'],
                        'quantity'        => $itemData['quantity'],
                        'unit_price'      => $itemData['unit_price'],
                        'discount_amount' => $itemData['discount_amount'],
                        'total_price'     => $itemData['total_price'],
                    ]);

                    /** @var Product $prod */
                    $prod = $itemData['product'];
                    $prod->decrement('stock', $itemData['quantity']);
                    $prod->increment('sales_count', $itemData['quantity']);
                }

                // 10. Increment coupon used count if applicable
                if ($coupon) {
                    $coupon->increment('used_count');
                }

                // 11. Clear cart
                CartItem::where('cart_id', $cart->id)->delete();

                return [
                    'status'  => true,
                    'message' => __('messages.order_placed_successfully'),
                    'data'    => $order->load(['items.product.brand', 'address']),
                ];
            });
        } catch (\Exception $e) {
            Log::error('Error processing cash checkout: ' . $e->getMessage());
            return [
                'status'  => false,
                'message' => $e->getMessage(),
                'code'    => 500,
            ];
        }
    }
}
