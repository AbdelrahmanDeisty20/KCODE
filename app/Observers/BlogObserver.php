<?php

namespace App\Observers;

use App\Models\Blog;
use App\Models\AppNotification;
use App\Services\FirebaseNotificationService;
use Illuminate\Support\Facades\Log;

class BlogObserver
{
    /**
     * Handle the Blog "created" event.
     */
    public function created(Blog $blog): void
    {
        $this->sendBlogNotification($blog);
    }

    /**
     * Handle the Blog "updated" event.
     */
    public function updated(Blog $blog): void
    {
        // Send notification if status was changed to 'published'
        if ($blog->wasChanged('status') && $blog->status === 'published') {
            $this->sendBlogNotification($blog);
        }
    }

    /**
     * Send general broadcast notification for a published blog post.
     */
    protected function sendBlogNotification(Blog $blog): void
    {
        try {
            // Only send notification if status is published (or not explicitly draft)
            if ($blog->status && $blog->status !== 'published') {
                return;
            }

            $blogTitleAr = $blog->title_ar ?: ($blog->name_ar ?: ($blog->title_en ?: 'مقالة جديدة'));
            $blogTitleEn = $blog->title_en ?: ($blog->name_en ?: ($blog->title_ar ?: 'New Article'));

            $titleAr   = "مقالة جديدة: {$blogTitleAr} 📰";
            $titleEn   = "New Article: {$blogTitleEn} 📰";
            $messageAr = "اقرأ مقالنا الجديد \"{$blogTitleAr}\" على التطبيق الآن!";
            $messageEn = "Read our new article \"{$blogTitleEn}\" on the app now!";

            // 1. Create general AppNotification record in database (user_id = null for broadcast)
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

            // 2. Broadcast push notification to all FCM device tokens
            $firebaseService = app(FirebaseNotificationService::class);

            $fcmData = [
                'type'            => 'blog',
                'notification_id' => (string) $notification->id,
                'blog_id'         => (string) $blog->id,
                'slug'            => (string) $blog->slug,
            ];

            $firebaseService->sendToUsers($titleAr, $messageAr, [], $fcmData);

            Log::info("BlogObserver notification sent for Blog ID {$blog->id}");
        } catch (\Exception $e) {
            Log::error("BlogObserver Error sending notification: " . $e->getMessage());
        }
    }
}
