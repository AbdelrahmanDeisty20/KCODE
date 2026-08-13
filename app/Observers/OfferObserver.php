<?php

namespace App\Observers;

use App\Models\Offer;
use App\Models\AppNotification;
use App\Models\Cart;
use App\Models\Favourite;
use App\Models\UserFcmToken;
use App\Services\FirebaseNotificationService;
use Illuminate\Support\Facades\Log;

class OfferObserver
{
    /**
     * Handle the Offer "created" event.
     */
    public function created(Offer $offer): void
    {
        $this->sendOfferNotification($offer);
    }

    /**
     * Handle the Offer "updated" event.
     */
    public function updated(Offer $offer): void
    {
        if ($offer->wasChanged('is_active') && $offer->is_active) {
            $this->sendOfferNotification($offer);
        } elseif ($offer->is_active && $offer->wasChanged('discount_percentage')) {
            $this->sendOfferNotification($offer);
        }
    }

    /**
     * Send targeted & general real-time notifications for active offer.
     */
    protected function sendOfferNotification(Offer $offer): void
    {
        try {
            if (!$offer->is_active) {
                return;
            }

            $offer->loadMissing('product');
            $product = $offer->product;

            if (!$product) {
                return;
            }

            $productId = $offer->product_id;
            $productNameAr = $product->name_ar ?? $product->name_en ?? 'منتج';
            $productNameEn = $product->name_en ?? $product->name_ar ?? 'Product';
            $discount = (float) $offer->discount_percentage;

            $firebaseService = app(FirebaseNotificationService::class);

            // 1. Find User IDs with this product in Cart
            $cartUserIds = Cart::whereHas('items', function ($q) use ($productId) {
                $q->where('product_id', $productId);
            })->whereNotNull('user_id')->pluck('user_id')->unique()->toArray();

            // 2. Find User IDs with this product in Favorites
            $favoriteUserIds = Favourite::where('product_id', $productId)
                ->where(function ($q) {
                    $q->where('is_active', true)->orWhereNull('is_active');
                })
                ->whereNotNull('user_id')
                ->pluck('user_id')
                ->unique()
                ->toArray();

            // 3. Segment Users into Categories

            // Group 1: Both Cart AND Favorites (Highest Priority - emphasized cart notification)
            $bothUserIds = array_values(array_intersect($cartUserIds, $favoriteUserIds));

            // Group 2: Cart Only
            $cartOnlyUserIds = array_values(array_diff($cartUserIds, $bothUserIds));

            // Group 3: Favorites Only
            $favoritesOnlyUserIds = array_values(array_diff($favoriteUserIds, $bothUserIds));

            // All targeted users
            $targetedUserIds = array_unique(array_merge($bothUserIds, $cartOnlyUserIds, $favoritesOnlyUserIds));

            // --- Send Group 1: Both Cart & Favorites ---
            if (!empty($bothUserIds)) {
                $titleAr = "خصم مميز على منتج في سلتك ومفضلتك! 🛒❤️";
                $titleEn = "Special Discount on item in Cart & Favorites!";
                $msgAr   = "المنتج \"{$productNameAr}\" الموجود في سلتك ومفضلتك يتوفر عليه خصم {$discount}% الآن!";
                $msgEn   = "The item \"{$productNameEn}\" in your cart and wishlist has a {$discount}% discount now!";

                $this->createAppNotificationsForUsers($bothUserIds, $titleAr, $titleEn, $msgAr, $msgEn, 'cart_favorite_offer', $offer);
                $firebaseService->sendToUsers($titleAr, $msgAr, $bothUserIds, [
                    'type' => 'offer',
                    'category' => 'cart_favorite',
                    'product_id' => (string) $productId,
                    'offer_id' => (string) $offer->id,
                    'discount_percentage' => (string) $discount,
                ]);
            }

            // --- Send Group 2: Cart Only ---
            if (!empty($cartOnlyUserIds)) {
                $titleAr = "تخفيض على منتج في سلتك! 🛒";
                $titleEn = "Discount on an item in your Cart!";
                $msgAr   = "المنتج \"{$productNameAr}\" في سلتك أصبح عليه خصم {$discount}% الآن، سارع بالشراء!";
                $msgEn   = "The item \"{$productNameEn}\" in your cart has {$discount}% discount now, grab it!";

                $this->createAppNotificationsForUsers($cartOnlyUserIds, $titleAr, $titleEn, $msgAr, $msgEn, 'cart_offer', $offer);
                $firebaseService->sendToUsers($titleAr, $msgAr, $cartOnlyUserIds, [
                    'type' => 'offer',
                    'category' => 'cart',
                    'product_id' => (string) $productId,
                    'offer_id' => (string) $offer->id,
                    'discount_percentage' => (string) $discount,
                ]);
            }

            // --- Send Group 3: Favorites Only ---
            if (!empty($favoritesOnlyUserIds)) {
                $titleAr = "خصم على منتج في قائمة مفضلتك! ❤️";
                $titleEn = "Offer on an item in your Wishlist!";
                $msgAr   = "المنتج \"{$productNameAr}\" في قائمة مفضلتك عليه خصم {$discount}% الآن!";
                $msgEn   = "The item \"{$productNameEn}\" in your wishlist has {$discount}% discount now!";

                $this->createAppNotificationsForUsers($favoritesOnlyUserIds, $titleAr, $titleEn, $msgAr, $msgEn, 'favorite_offer', $offer);
                $firebaseService->sendToUsers($titleAr, $msgAr, $favoritesOnlyUserIds, [
                    'type' => 'offer',
                    'category' => 'favorite',
                    'product_id' => (string) $productId,
                    'offer_id' => (string) $offer->id,
                    'discount_percentage' => (string) $discount,
                ]);
            }

            // --- Group 4: General Broadcast to all remaining users & guest devices ---
            $generalTitleAr = "عرض جديد على {$productNameAr}! 🎉";
            $generalTitleEn = "New Offer on {$productNameEn}!";
            $generalMsgAr   = "احصل على خصم بنسبة {$discount}% على {$productNameAr}، تسوق الآن!";
            $generalMsgEn   = "Get {$discount}% off on {$productNameEn}, shop now!";

            // 1. Create general App Notification (user_id = null)
            $generalNotification = AppNotification::create([
                'user_id'    => null,
                'title_ar'   => $generalTitleAr,
                'title_en'   => $generalTitleEn,
                'message_ar' => $generalMsgAr,
                'message_en' => $generalMsgEn,
                'type'       => 'general_offer',
                'data'       => [
                    'product_id'          => (string) $productId,
                    'offer_id'            => (string) $offer->id,
                    'discount_percentage' => (string) $discount,
                ],
                'is_read'    => false,
            ]);

            // 2. Send FCM to all tokens excluding targeted users
            $query = UserFcmToken::query();
            if (!empty($targetedUserIds)) {
                $query->where(function ($q) use ($targetedUserIds) {
                    $q->whereNotIn('user_id', $targetedUserIds)->orWhereNull('user_id');
                });
            }

            $otherTokens = $query->pluck('token')->unique()->filter()->values()->toArray();

            foreach ($otherTokens as $token) {
                try {
                    $firebaseService->sendToToken($token, $generalTitleAr, $generalMsgAr, [
                        'type'            => 'offer',
                        'category'        => 'general',
                        'notification_id' => (string) $generalNotification->id,
                        'product_id'      => (string) $productId,
                        'offer_id'        => (string) $offer->id,
                        'discount_percentage' => (string) $discount,
                    ]);
                } catch (\Exception $e) {
                    Log::error("General broadcast send error: " . $e->getMessage());
                }
            }

            Log::info("OfferObserver notifications sent successfully for Offer ID {$offer->id}");

        } catch (\Exception $e) {
            Log::error("OfferObserver Error: " . $e->getMessage());
        }
    }

    /**
     * Helper method to bulk create in-app notifications for specific user IDs.
     */
    protected function createAppNotificationsForUsers(
        array $userIds,
        string $titleAr,
        string $titleEn,
        string $messageAr,
        string $messageEn,
        string $type,
        Offer $offer
    ): void {
        foreach ($userIds as $userId) {
            AppNotification::create([
                'user_id'    => $userId,
                'title_ar'   => $titleAr,
                'title_en'   => $titleEn,
                'message_ar' => $messageAr,
                'message_en' => $messageEn,
                'type'       => $type,
                'data'       => [
                    'product_id'          => (string) $offer->product_id,
                    'offer_id'            => (string) $offer->id,
                    'discount_percentage' => (string) $offer->discount_percentage,
                ],
                'is_read'    => false,
            ]);
        }
    }
}
