<?php

namespace App\Observers;

use App\Models\Offer;
use App\Models\AppNotification;
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
        // Send notification if offer was activated or discount percentage was changed while active
        if ($offer->wasChanged('is_active') && $offer->is_active) {
            $this->sendOfferNotification($offer);
        } elseif ($offer->is_active && $offer->wasChanged('discount_percentage')) {
            $this->sendOfferNotification($offer);
        }
    }

    /**
     * Send notification to all device tokens when an offer is active.
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

            $productNameAr = $product->name_ar ?? $product->name_en ?? 'منتج';
            $productNameEn = $product->name_en ?? $product->name_ar ?? 'Product';
            $discount = (float) $offer->discount_percentage;

            $titleAr = "عرض جديد على {$productNameAr}!";
            $titleEn = "New Offer on {$productNameEn}!";
            $messageAr = "تم إضافة خصم بنسبة {$discount}% على {$productNameAr}، احصل عليه الآن!";
            $messageEn = "A {$discount}% discount has been added on {$productNameEn}, get it now!";

            // 1. Create general App Notification in DB (user_id = null for broadcast)
            $notification = AppNotification::create([
                'user_id'    => null,
                'title_ar'   => $titleAr,
                'title_en'   => $titleEn,
                'message_ar' => $messageAr,
                'message_en' => $messageEn,
                'type'       => 'offer',
                'data'       => [
                    'product_id'          => (string) $offer->product_id,
                    'offer_id'            => (string) $offer->id,
                    'discount_percentage' => (string) $discount,
                ],
                'is_read'    => false,
            ]);

            // 2. Send push notification to all FCM device tokens
            $firebaseService = app(FirebaseNotificationService::class);

            $fcmData = [
                'type'                => 'offer',
                'notification_id'     => (string) $notification->id,
                'product_id'          => (string) $offer->product_id,
                'offer_id'            => (string) $offer->id,
                'discount_percentage' => (string) $discount,
            ];

            $firebaseService->sendToUsers($titleAr, $messageAr, [], $fcmData);

            Log::info("Offer notification sent for Offer ID {$offer->id} (Product ID {$offer->product_id})");
        } catch (\Exception $e) {
            Log::error("OfferObserver Error sending notification: " . $e->getMessage());
        }
    }
}
