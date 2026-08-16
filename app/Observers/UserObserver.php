<?php

namespace App\Observers;

use App\Models\User;
use App\Services\ActivityLogger;
use Filament\Actions\Action;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\Log;

class UserObserver
{
    /**
     * Handle the User "created" event.
     * Logs activity & sends database notification ONLY to Admin users when a new account is registered.
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

            // 2. Send Filament Database Notification ONLY to Admins
            $admins = User::where(function ($query) {
                $query->where('type', 'admin')
                    ->orWhereHas('roles', fn ($q) => $q->whereIn('name', ['admin', 'super_admin', 'Admin', 'Super Admin']))
                    ->orWhere('email', 'admin@kcode.com');
            })->where('id', '!=', $user->id)->get();

            if ($admins->isEmpty()) {
                $admins = User::where('id', '!=', $user->id)->limit(5)->get();
            }

            if ($admins->isNotEmpty()) {
                $userUrl = \App\Filament\Resources\UserResource::getUrl('edit', ['record' => $user->id]);

                Notification::make()
                    ->title("تسجيل حساب جديد 👤")
                    ->body("قام العميل [{$userName}] ({$userEmail}) بتسجيل حساب جديد بالتطبيق")
                    ->icon('heroicon-o-user-plus')
                    ->iconColor('success')
                    ->actions([
                        Action::make('view_user')
                            ->label('عرض بيانات العميل 👁️')
                            ->url($userUrl)
                            ->button()
                            ->color('primary')
                            ->markAsRead(),
                    ])
                    ->sendToDatabase($admins);
            }

            Log::info("UserObserver admin notification sent for new user registration (User ID {$user->id})");
        } catch (\Exception $e) {
            Log::error("UserObserver Error: " . $e->getMessage());
        }
    }

    /**
     * Handle the User "updated" event.
     */
    public function updated(User $user): void
    {
        if ($user->wasChanged()) {
            ActivityLogger::log(
                event: 'updated',
                description: "تم تحديث بيانات حساب المستخدم: [{$user->name}]",
                subjectType: 'User',
                subjectId: $user->id,
                oldValues: array_intersect_key($user->getOriginal(), $user->getChanges()),
                newValues: $user->getChanges()
            );
        }
    }

    /**
     * Handle the User "deleted" event.
     */
    public function deleted(User $user): void
    {
        ActivityLogger::log(
            event: 'deleted',
            description: "تم حذف حساب المستخدم: [{$user->name}] ({$user->email})",
            subjectType: 'User',
            subjectId: $user->id
        );
    }
}
