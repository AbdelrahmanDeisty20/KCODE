<?php

namespace App\Observers;

use App\Models\Blog;
use App\Models\User;
use App\Models\AppNotification;
use App\Services\FirebaseNotificationService;
use App\Services\ActivityLogger;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;

class BlogObserver
{
    /**
     * Handle the Blog "created" event.
     */
    public function created(Blog $blog): void
    {
        $authorName = Auth::user()?->name ?? ($blog->user?->name ?? 'كاتب المقال');
        $blogTitle  = $blog->title_ar ?: ($blog->title_en ?: ($blog->name_ar ?: 'مقالة بدون عنوان'));

        // 1. Log in Activity Logs
        ActivityLogger::log(
            event: 'created',
            description: "قام الكاتب [{$authorName}] بإنشاء مقالة جديدة بعنوان \"{$blogTitle}\"",
            subjectType: 'Blog',
            subjectId: $blog->id,
            newValues: [
                'title'  => $blogTitle,
                'author' => $authorName,
                'status' => $blog->status ?? 'draft',
            ]
        );

        // 2. Send Filament Database Notification to Admins
        $this->notifyAdmins(
            title: "مقالة جديدة من الكاتب: {$authorName} 📝",
            body: "قام الكاتب [{$authorName}] بإنشاء مقالة جديدة بعنوان: \"{$blogTitle}\"",
            icon: 'heroicon-o-document-plus',
            status: 'warning'
        );

        // 3. Broadcast push notification to mobile app users if published
        if ($blog->status === 'published') {
            $this->sendBlogAppNotification($blog);
        }
    }

    /**
     * Handle the Blog "updated" event.
     */
    public function updated(Blog $blog): void
    {
        $authorName = Auth::user()?->name ?? ($blog->user?->name ?? 'محرر المقال');
        $blogTitle  = $blog->title_ar ?: ($blog->title_en ?: ($blog->name_ar ?: 'مقالة بدون عنوان'));

        // 1. Log in Activity Logs
        ActivityLogger::log(
            event: 'updated',
            description: "قام الكاتب / المحرر [{$authorName}] بتعديل المقالة \"{$blogTitle}\"",
            subjectType: 'Blog',
            subjectId: $blog->id,
            oldValues: $blog->getOriginal(),
            newValues: $blog->getChanges()
        );

        // 2. Send Filament Database Notification to Admins
        $this->notifyAdmins(
            title: "تعديل مقالة بواسطة: {$authorName} ✏️",
            body: "قام الكاتب / المحرر [{$authorName}] بتعديل المقالة: \"{$blogTitle}\"",
            icon: 'heroicon-o-pencil-square',
            status: 'info'
        );

        // 3. Broadcast push notification if status was changed to 'published'
        if ($blog->wasChanged('status') && $blog->status === 'published') {
            $this->sendBlogAppNotification($blog);
        }
    }

    /**
     * Handle the Blog "deleted" event.
     */
    public function deleted(Blog $blog): void
    {
        $authorName = Auth::user()?->name ?? ($blog->user?->name ?? 'المشرف');
        $blogTitle  = $blog->title_ar ?: ($blog->title_en ?: ($blog->name_ar ?: 'مقالة بدون عنوان'));

        // 1. Log in Activity Logs
        ActivityLogger::log(
            event: 'deleted',
            description: "قام [{$authorName}] بحذف المقالة \"{$blogTitle}\"",
            subjectType: 'Blog',
            subjectId: $blog->id,
            oldValues: ['title' => $blogTitle, 'author' => $authorName]
        );

        // 2. Send Filament Database Notification to Admins
        $this->notifyAdmins(
            title: "حذف مقالة بواسطة: {$authorName} 🗑️",
            body: "قام [{$authorName}] بحذف المقالة: \"{$blogTitle}\"",
            icon: 'heroicon-o-trash',
            status: 'danger'
        );
    }

    /**
     * Send Database Notification to Admin users in Filament Admin Panel.
     */
    protected function notifyAdmins(string $title, string $body, string $icon, string $status = 'info'): void
    {
        try {
            $admins = User::whereHas('roles', function ($q) {
                $q->whereIn('name', ['admin', 'super_admin']);
            })->get();

            if ($admins->isEmpty()) {
                $admins = User::where('email', 'like', '%admin%')->orWhere('id', 1)->get();
            }

            if ($admins->isNotEmpty()) {
                Notification::make()
                    ->title($title)
                    ->body($body)
                    ->icon($icon)
                    ->{$status}()
                    ->sendToDatabase($admins);
            }
        } catch (\Exception $e) {
            Log::error("Failed to send admin Filament notification: " . $e->getMessage());
        }
    }

    /**
     * Send general broadcast push notification to mobile app users for published article.
     */
    protected function sendBlogAppNotification(Blog $blog): void
    {
        try {
            $blogTitleAr = $blog->title_ar ?: ($blog->name_ar ?: ($blog->title_en ?: 'مقالة جديدة'));
            $blogTitleEn = $blog->title_en ?: ($blog->name_en ?: ($blog->title_ar ?: 'New Article'));

            $titleAr   = "مقالة جديدة: {$blogTitleAr} 📰";
            $titleEn   = "New Article: {$blogTitleEn} 📰";
            $messageAr = "اقرأ مقالنا الجديد \"{$blogTitleAr}\" على التطبيق الآن!";
            $messageEn = "Read our new article \"{$blogTitleEn}\" on the app now!";

            $notification = AppNotification::create([
                'user_id'    => null,
                'title_ar'   => $titleAr,
                'title_en'   => $titleEn,
                'message_ar' => $messageAr,
                'message_en' => $messageEn,
                'type'       => 'blog',
                'data'       => [
                    'blog_id' => (string) $blog->id,
                    'slug'    => (string) $blog->slug,
                ],
                'is_read'    => false,
            ]);

            $firebaseService = app(FirebaseNotificationService::class);
            $firebaseService->sendToUsers($titleAr, $messageAr, [], [
                'type'            => 'blog',
                'notification_id' => (string) $notification->id,
                'blog_id'         => (string) $blog->id,
                'slug'            => (string) $blog->slug,
            ]);

            Log::info("BlogObserver app notification sent for Blog ID {$blog->id}");
        } catch (\Exception $e) {
            Log::error("BlogObserver Error sending app notification: " . $e->getMessage());
        }
    }
}
