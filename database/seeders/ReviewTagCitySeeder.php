<?php

namespace Database\Seeders;

use App\Models\Review;
use App\Models\User;
use Illuminate\Database\Seeder;

/**
 * Fills tag_ar / tag_en / featured on GENERAL reviews (product_id = null)
 * and city on their users.
 *
 * SAFE: only fills NULL values — never overwrites existing data.
 * Idempotent: can be run multiple times.
 */
class ReviewTagCitySeeder extends Seeder
{
    private array $tags = [
        ['ar' => 'الطلب والتغليف',   'en' => 'Order & Packaging'],
        ['ar' => 'خدمة العملاء',     'en' => 'Customer Service'],
        ['ar' => 'الروتين المقترَح', 'en' => 'Suggested Routine'],
        ['ar' => 'تجربة KCODE',      'en' => 'KCODE Experience'],
    ];

    private array $cities = ['مسقط', 'السيب', 'صحار', 'نزوى', 'صلالة', 'بوشر', 'العامرات', 'صور'];

    public function run(): void
    {
        $reviews = Review::whereNull('product_id')->orderBy('id')->get();

        // 1. Tags (round-robin) — only where empty
        foreach ($reviews->values() as $i => $review) {
            $tag     = $this->tags[$i % count($this->tags)];
            $updates = [];

            if (blank($review->tag_ar)) $updates['tag_ar'] = $tag['ar'];
            if (blank($review->tag_en)) $updates['tag_en'] = $tag['en'];

            if ($updates) {
                $review->update($updates);
            }
        }

        // 2. Featured — mark top 4 (highest rating, newest) only if none featured yet
        if (!Review::whereNull('product_id')->where('featured', true)->exists()) {
            Review::whereNull('product_id')
                ->orderByDesc('rating')->orderByDesc('created_at')
                ->limit(4)->pluck('id')
                ->each(fn ($id) => Review::whereKey($id)->update(['featured' => true]));
        }

        // 3. City on reviewers — only where empty
        $userIds = $reviews->pluck('user_id')->filter()->unique()->values();

        User::whereIn('id', $userIds)->whereNull('city')->orderBy('id')->get()
            ->values()
            ->each(fn ($user, $i) => $user->update(['city' => $this->cities[$i % count($this->cities)]]));

        $this->command?->info("ReviewTagCitySeeder: processed {$reviews->count()} general reviews.");
    }
}
