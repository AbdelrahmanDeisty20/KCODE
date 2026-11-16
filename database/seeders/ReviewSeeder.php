<?php

namespace Database\Seeders;

use App\Models\Review;
use App\Models\Product;
use App\Models\User;
use Illuminate\Database\Seeder;

class ReviewSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. Ensure we have users to write reviews
        $user1 = User::updateOrCreate(
            ['email' => 'user1@example.com'],
            [
                'name' => 'أحمد محمد',
                'password' => bcrypt('password123'),
                'image' => 'default.png',
            ]
        );

        $user2 = User::updateOrCreate(
            ['email' => 'user2@example.com'],
            [
                'name' => 'سارة علي',
                'password' => bcrypt('password123'),
                'image' => 'default.png',
            ]
        );

        $user3 = User::updateOrCreate(
            ['email' => 'user3@example.com'],
            [
                'name' => 'John Doe',
                'password' => bcrypt('password123'),
                'image' => 'default.png',
            ]
        );

        // 2. Remove any previously seeded dummy product-specific reviews to ensure clean ratings
        Review::whereNotNull('product_id')
            ->whereIn('user_id', [$user1->id, $user2->id, $user3->id])
            ->delete();

        // 3. Seed general store/website reviews (product_id = null)
        $generalReviews = [
            [
                'product_id' => null,
                'user_id' => $user1->id,
                'rating' => 5,
                'comment' => 'موقع ممتاز وسرعة في التوصيل وتجربة شراء سلسة جداً.',
            ],
            [
                'product_id' => null,
                'user_id' => $user2->id,
                'rating' => 4,
                'comment' => 'الخدمة ممتازة والمنتجات أصلية، أتمنى توفير خيارات دفع أكثر.',
            ],
            [
                'product_id' => null,
                'user_id' => $user3->id,
                'rating' => 5,
                'comment' => 'Highly recommended! Authentic skincare products and fast customer service support.',
            ],
        ];

        foreach ($generalReviews as $generalReview) {
            Review::firstOrCreate([
                'product_id' => null,
                'user_id' => $generalReview['user_id'],
            ], [
                'rating' => $generalReview['rating'],
                'comment' => $generalReview['comment'],
            ]);
        }
    }
}
