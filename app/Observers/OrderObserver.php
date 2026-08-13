<?php

namespace App\Observers;

use App\Models\Order;
use App\Models\User;
use App\Models\AppNotification;
use App\Services\FirebaseNotificationService;
use Filament\Notifications\Notification as FilamentNotification;
use Illuminate\Support\Facades\Log;

class OrderObserver
{
    /**
     * Handle the Order "created" event.
     */
    public function created(Order $order): void
    {
        $this->notifyAdminsNewOrder($order);
    }

    /**
     * Notify admin users about a new order.
     */
    protected function notifyAdminsNewOrder(Order $order): void
    {
        try {
            // Get all admin users
            $adminUsers = User::where('type', 'admin')
                ->orWhereHas('roles', fn ($q) => $q->whereIn('name', ['admin', 'super_admin']))
                ->get();

            if ($adminUsers->isEmpty()) {
                $adminUsers = User::where('email', 'admin@admin.com')->orWhere('id', 1)->get();
            }

            if ($adminUsers->isEmpty()) {
                Log::warning("OrderObserver: No admin users found to notify for Order ID {$order->id}");
                return;
            }

            $orderNumber  = $order->order_number ?? $order->id;
            $customerName = $order->user_name ?: ($order->user?->name ?: 'عميل');
            $totalAmount  = (float) $order->total;

            $titleAr   = "طلب جديد برقم #{$orderNumber} 🛒";
            $titleEn   = "New Order #{$orderNumber} 🛒";
            $messageAr = "وصلك طلب جديد برقم #{$orderNumber} بقيمة {$totalAmount} ج.م من العميل ({$customerName})، يرجى التجهيز والمراجعة.";
            $messageEn = "New order #{$orderNumber} received for {$totalAmount} EGP from {$customerName}.";

            $firebaseService = app(FirebaseNotificationService::class);

            foreach ($adminUsers as $admin) {
                // 1. Send Filament Dashboard Notification (bell icon in admin panel)
                try {
                    FilamentNotification::make()
                        ->title("طلب جديد برقم: #{$orderNumber}")
                        ->body("تم استلام طلب جديد بمبلغ {$totalAmount} ج.م من العميل ({$customerName})")
                        ->icon('heroicon-o-shopping-bag')
                        ->iconColor('success')
                        ->sendToDatabase($admin);
                } catch (\Exception $e) {
                    Log::error("Filament Notification Error for Admin {$admin->id}: " . $e->getMessage());
                }

                // 2. Create AppNotification in database
                try {
                    AppNotification::create([
                        'user_id'    => $admin->id,
                        'title_ar'   => $titleAr,
                        'title_en'   => $titleEn,
                        'message_ar' => $messageAr,
                        'message_en' => $messageEn,
                        'type'       => 'new_order',
                        'data'       => [
                            'order_id'     => (string) $order->id,
                            'order_number' => (string) $orderNumber,
                            'total'        => (string) $totalAmount,
                        ],
                        'is_read'    => false,
                    ]);
                } catch (\Exception $e) {
                    Log::error("AppNotification create error for Admin {$admin->id}: " . $e->getMessage());
                }

                // 3. Send Real-Time Firebase FCM Push Notification to Admin
                try {
                    $firebaseService->sendToUser(
                        $admin->id,
                        $titleAr,
                        $messageAr,
                        [
                            'type'         => 'new_order',
                            'order_id'     => (string) $order->id,
                            'order_number' => (string) $orderNumber,
                            'total'        => (string) $totalAmount,
                        ]
                    );
                } catch (\Exception $e) {
                    Log::error("Firebase push error for Admin {$admin->id}: " . $e->getMessage());
                }
            }

            Log::info("OrderObserver notified admins for Order #{$orderNumber}");

        } catch (\Exception $e) {
            Log::error("OrderObserver Error: " . $e->getMessage());
        }
    }
}
