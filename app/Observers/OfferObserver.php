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
            $bothUserIds          = array_values(array_intersect($cartUserIds, $favoriteUserIds));
            $cartOnlyUserIds      = array_values(array_diff($cartUserIds, $bothUserIds));
            $favoritesOnlyUserIds = array_values(array_diff($favoriteUserIds, $bothUserIds));

            // All targeted logged-in user IDs
            $targetedUserIds = array_unique(array_merge($bothUserIds, $cartOnlyUserIds, $favoritesOnlyUserIds));

            // Track tokens that received targeted notifications to prevent duplicate push
            $targetedTokens = [];

            // --- Group 1: Both Cart & Favorites (Exclusive Targeted Offer) ---
            if (!empty($bothUserIds)) {
                $titleAr = "عرض خاص على منتجك المفضل وفي سلتك! 🛒❤️";
                $titleEn = "Special Offer on your Favorite & Cart item!";
                $msgAr   = "خصم حصري لك! المنتج \"{$productNameAr}\" في مفضلتك وسلتك عليه خصم {$discount}% الآن!";
                $msgEn   = "Exclusive discount for you! The item \"{$productNameEn}\" in your favorites & cart has a {$discount}% discount now!";

                $this->createAppNotificationsForUsers($bothUserIds, $titleAr, $titleEn, $msgAr, $msgEn, 'cart_favorite_offer', $offer);
                $firebaseService->sendToUsers($titleAr, $msgAr, $bothUserIds, [
                    'type' => 'offer',
                    'category' => 'cart_favorite',
                    'product_id' => (string) $productId,
                    'offer_id' => (string) $offer->id,
                    'discount_percentage' => (string) $discount,
                ]);

                $tokens1 = UserFcmToken::whereIn('user_id', $bothUserIds)->pluck('token')->toArray();
                $targetedTokens = array_merge($targetedTokens, $tokens1);
            }

            // --- Group 2: Cart Only ---
            if (!empty($cartOnlyUserIds)) {
                $titleAr = "عرض خاص على منتج في سلتك! 🛒";
                $titleEn = "Special Offer on item in your Cart!";
                $msgAr   = "خصم خصيصاً لك! المنتج \"{$productNameAr}\" في سلتك عليه خصم {$discount}% الآن، اطلبه قبل نفاذ الكمية!";
                $msgEn   = "Special discount for you! The item \"{$productNameEn}\" in your cart has a {$discount}% discount now!";

                $this->createAppNotificationsForUsers($cartOnlyUserIds, $titleAr, $titleEn, $msgAr, $msgEn, 'cart_offer', $offer);
                $firebaseService->sendToUsers($titleAr, $msgAr, $cartOnlyUserIds, [
                    'type' => 'offer',
                    'category' => 'cart',
                    'product_id' => (string) $productId,
                    'offer_id' => (string) $offer->id,
                    'discount_percentage' => (string) $discount,
                ]);

                $tokens2 = UserFcmToken::whereIn('user_id', $cartOnlyUserIds)->pluck('token')->toArray();
                $targetedTokens = array_merge($targetedTokens, $tokens2);
            }

            // --- Group 3: Favorites Only ---
            if (!empty($favoritesOnlyUserIds)) {
                $titleAr = "عرض خاص على منتجك المفضل! ❤️";
                $titleEn = "Special Offer on your Favorite item!";
                $msgAr   = "خصم خصيصاً لك! المنتج \"{$productNameAr}\" في قائمة مفضلتك عليه خصم {$discount}% الآن!";
                $msgEn   = "Special discount for you! The item \"{$productNameEn}\" in your wishlist has a {$discount}% discount now!";

                $this->createAppNotificationsForUsers($favoritesOnlyUserIds, $titleAr, $titleEn, $msgAr, $msgEn, 'favorite_offer', $offer);
                $firebaseService->sendToUsers($titleAr, $msgAr, $favoritesOnlyUserIds, [
                    'type' => 'offer',
                    'category' => 'favorite',
                    'product_id' => (string) $productId,
                    'offer_id' => (string) $offer->id,
                    'discount_percentage' => (string) $discount,
                ]);

                $tokens3 = UserFcmToken::whereIn('user_id', $favoritesOnlyUserIds)->pluck('token')->toArray();
                $targetedTokens = array_merge($targetedTokens, $tokens3);
            }

            // --- Group 4: General Broadcast to Non-Targeted Users & Guests ---
            $generalTitleAr = "عرض جديد على {$productNameAr}! 🎉";
            $generalTitleEn = "New Offer on {$productNameEn}!";
            $generalMsgAr   = "احصل على خصم بنسبة {$discount}% على {$productNameAr}، تسوق الآن!";
            $generalMsgEn   = "Get {$discount}% off on {$productNameEn}, shop now!";

            // Create general App Notification record (user_id = null)
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

            // Exclude ALL tokens belonging to targeted logged-in users to ensure NO duplicate push notification!
            $targetedTokens = array_unique(array_filter($targetedTokens));

            $query = UserFcmToken::query();
            if (!empty($targetedUserIds)) {
                $query->where(function ($q) use ($targetedUserIds) {
                    $q->whereNotIn('user_id', $targetedUserIds)->orWhereNull('user_id');
                });
            }
            if (!empty($targetedTokens)) {
                $query->whereNotIn('token', $targetedTokens);
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
