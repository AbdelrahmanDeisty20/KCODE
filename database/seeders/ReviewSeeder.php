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

        // 2. Fetch products
        $products = Product::all();

        // 3. Seed product-specific reviews if products exist
        if ($products->isNotEmpty()) {
            $productReviews = [
                [
                    'user_id' => $user1->id,
                    'rating' => 5,
                    'comment' => 'منتج رائع جداً، أنصح باستخدامه بشكل يومي!',
                ],
                [
                    'user_id' => $user2->id,
                    'rating' => 4,
                    'comment' => 'جميل ومناسب للبشرة الحساسة، لكن يستغرق بعض الوقت لتظهر النتائج.',
                ],
                [
                    'user_id' => $user3->id,
                    'rating' => 3,
                    'comment' => 'It is okay, but I expected better hydration for my dry skin.',
                ],
            ];

            foreach ($products as $product) {
                foreach ($productReviews as $reviewData) {
                    Review::create([
                        'product_id' => $product->id,
                        'user_id' => $reviewData['user_id'],
                        'rating' => $reviewData['rating'],
                        'comment' => $reviewData['comment'],
                    ]);
                }
            }
        }

        // 4. Seed general store/website reviews (product_id = null)
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
            Review::create($generalReview);
        }
    }
}
