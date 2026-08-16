<?php

namespace App\Observers;

use App\Models\Order;
use App\Models\User;
use App\Models\AppNotification;
use App\Services\ActivityLogger;
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
        try {
            $orderNumber  = $order->order_number ?? $order->id;
            $customerName = $order->user_name ?: ($order->user?->name ?: 'عميل جديد');
            $contact      = $order->user_phone ?: ($order->user_email ?: ($order->user?->phone ?: $order->user?->email));

            ActivityLogger::log(
                event: 'created',
                description: "قام العميل [{$customerName}] (" . ($contact ? $contact : 'بدون تفاصيل تواصل') . ") بطلب جديد رقم #{$orderNumber} إجمالي {$order->total} ج.م",
                subjectType: 'Order',
                subjectId: $order->id,
                newValues: [
                    'order_number'  => $orderNumber,
                    'total'         => $order->total,
                    'status'        => $order->order_status,
                    'customer_name' => $customerName,
                    'contact'       => $contact,
                ]
            );
        } catch (\Exception $e) {
            Log::error("OrderObserver ActivityLogger created error: " . $e->getMessage());
        }

        $this->notifyAdminsNewOrder($order);
    }

    /**
     * Handle the Order "updated" event.
     */
    public function updated(Order $order): void
    {
        if ($order->wasChanged('order_status')) {
            $orderNumber = $order->order_number ?? $order->id;
            ActivityLogger::log(
                event: 'updated',
                description: "تم تحديث حالة الطلب رقم #{$orderNumber} إلى ({$order->order_status})",
                subjectType: 'Order',
                subjectId: $order->id,
                oldValues: ['order_status' => $order->getOriginal('order_status')],
                newValues: ['order_status' => $order->order_status]
            );

            if ($order->user_id) {
                $this->notifyUserOrderStatusChange($order);
            }
        }
    }

    /**
     * Handle the Order "deleted" event.
     */
    public function deleted(Order $order): void
    {
        $orderNumber = $order->order_number ?? $order->id;
        ActivityLogger::log(
            event: 'deleted',
            description: "تم حذف الطلب رقم #{$orderNumber}",
            subjectType: 'Order',
            subjectId: $order->id
        );
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

            $orderUrl = \App\Filament\Resources\OrderResource::getUrl('edit', ['record' => $order->id]);

            foreach ($adminUsers as $admin) {
                // 1. Send Filament Dashboard Notification (bell icon in admin panel)
                try {
                    FilamentNotification::make()
                        ->title("طلب جديد برقم: #{$orderNumber} 🛒")
                        ->body("تم استلام طلب جديد بمبلغ {$totalAmount} ج.م من العميل ({$customerName})")
                        ->icon('heroicon-o-shopping-bag')
                        ->iconColor('success')
                        ->actions([
                            \Filament\Actions\Action::make('view_order')
                                ->label('عرض تفاصيل الطلب 📦')
                                ->url($orderUrl)
                                ->button()
                                ->color('success'),
                        ])
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

    /**
     * Notify customer about order status change.
     */
    protected function notifyUserOrderStatusChange(Order $order): void
    {
        try {
            $orderNumber = $order->order_number ?? $order->id;
            $status = $order->order_status;

            $statusTextAr = match ($status) {
                'pending'   => 'قيد الانتظار',
                'accepted'  => 'مقبول',
                'delivered' => 'تم التسليم',
                'cancelled' => 'ملغي',
                default     => $status,
            };

            $statusTextEn = match ($status) {
                'pending'   => 'Pending',
                'accepted'  => 'Accepted',
                'delivered' => 'Delivered',
                'cancelled' => 'Cancelled',
                default     => $status,
            };

            $titleAr   = "تحديث حالة الطلب #{$orderNumber} 📦";
            $titleEn   = "Order Status Updated #{$orderNumber} 📦";
            $messageAr = "تم تغيير حالة طلبك رقم #{$orderNumber} إلى ({$statusTextAr}).";
            $messageEn = "Your order #{$orderNumber} status has been updated to ({$statusTextEn}).";

            // 1. Create AppNotification in database for customer
            AppNotification::create([
                'user_id'    => $order->user_id,
                'title_ar'   => $titleAr,
                'title_en'   => $titleEn,
                'message_ar' => $messageAr,
                'message_en' => $messageEn,
                'type'       => 'order_status',
                'data'       => [
                    'order_id'     => (string) $order->id,
                    'order_number' => (string) $orderNumber,
                    'order_status' => (string) $status,
                ],
                'is_read'    => false,
            ]);

            // 2. Send Real-Time Firebase FCM Push Notification to Customer
            $firebaseService = app(FirebaseNotificationService::class);
            $firebaseService->sendToUser(
                $order->user_id,
                $titleAr,
                $messageAr,
                [
                    'type'         => 'order_status',
                    'order_id'     => (string) $order->id,
                    'order_number' => (string) $orderNumber,
                    'order_status' => (string) $status,
                ]
            );

            Log::info("OrderObserver notified customer User ID {$order->user_id} of status change for Order #{$orderNumber}");
        } catch (\Exception $e) {
            Log::error("OrderObserver Error notifying status change: " . $e->getMessage());
        }
    }
}
