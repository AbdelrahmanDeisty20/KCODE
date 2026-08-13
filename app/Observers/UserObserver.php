<?php

namespace App\Observers;

use App\Models\User;
use App\Services\ActivityLogger;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\Log;

class UserObserver
{
    /**
     * Handle the User "created" event.
     */
    public function created(User $user): void
    {
        try {
            $userName  = $user->name ?: 'عميل جديد';
            $userEmail = $user->email ?: ($user->phone ?: 'غير محدد');

            // 1. Log activity in Activity Logs
            ActivityLogger::log(
                event: 'created',
                description: "قام العميل [{$userName}] ({$userEmail}) بتسجيل حساب جديد في المتجر",
                subjectType: 'User',
                subjectId: $user->id,
                newValues: [
                    'name'  => $userName,
                    'email' => $user->email,
                    'phone' => $user->phone,
                ]
            );

            // 2. Send Filament Database Notification to Admins
            $admins = User::whereHas('roles', function ($q) {
                $q->whereIn('name', ['admin', 'super_admin']);
            })->get();

            if ($admins->isEmpty()) {
                $admins = User::where('id', '!=', $user->id)->limit(5)->get();
            }

            if ($admins->isNotEmpty()) {
                Notification::make()
                    ->title("تسجيل عميل جديد 👤")
                    ->body("قام العميل [{$userName}] ({$userEmail}) بتسجيل حساب جديد بالتطبيق")
                    ->icon('heroicon-o-user-plus')
                    ->success()
                    ->sendToDatabase($admins);
            }

            Log::info("UserObserver notification and activity log processed for User ID {$user->id}");
        } catch (\Exception $e) {
            Log::error("UserObserver Error: " . $e->getMessage());
        }
    }
}
