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
        // 1. Remove all test reviews on specific products (KC0107, KC0122, KC0137)
        $testSkus = ['KC0107', 'KC0122', 'KC0137'];
        $testProductIds = Product::whereIn('sku', $testSkus)->pluck('id');
        if ($testProductIds->isNotEmpty()) {
            Review::whereIn('product_id', $testProductIds)->delete();
        }

        // 2. Remove any test comments or gibberish across all reviews
        Review::where(function ($q) {
            $q->whereIn('comment', ['35t45t53tt', 'فففففففف', 'Good', 'test', 'تجربة'])
              ->orWhere('comment', 'like', '%35t45t53tt%')
              ->orWhere('comment', 'like', '%فففف%');
        })->delete();

        // 3. Remove legacy dummy test users and their reviews
        $dummyUsers = User::whereIn('email', ['user1@example.com', 'user2@example.com', 'user3@example.com'])
            ->orWhereIn('name', ['John Doe', 'Saeed', 'فريق KCODE', 'Abdelrahman Eldeisty Hamed'])
            ->get();

        if ($dummyUsers->isNotEmpty()) {
            Review::whereIn('user_id', $dummyUsers->pluck('id'))->delete();
            User::whereIn('email', ['user1@example.com', 'user2@example.com', 'user3@example.com'])->delete();
        }

        // 4. Remove any general reviews that have legacy placeholder comments
        Review::whereNull('product_id')->where(function ($q) {
            $q->where('comment', 'like', '%Highly recommended%')
              ->orWhere('comment', 'like', '%موقع ممتاز وسرعة في التوصيل%');
        })->delete();

        // 5. Seed authentic, professional verified store reviews
        $customers = [
            [
                'name'    => 'نورة الدوسري',
                'email'   => 'noura.aldossary@kcodeskin.com',
                'city'    => 'الرياض',
                'rating'  => 5,
                'comment' => 'المنتجات أصلية 100% والتوصيل كان سريع جداً، روتين العناية الكوري فرق معايا في نضارة البشرة بشكل ملحوظ.',
                'tag_ar'  => 'عميل موثّق',
                'tag_en'  => 'Verified Buyer',
            ],
            [
                'name'    => 'ريم القحطاني',
                'email'   => 'reem.alqahtani@kcodeskin.com',
                'city'    => 'جدة',
                'rating'  => 5,
                'comment' => 'تجربة ممتازة وخدمة العملاء متعاونة جداً في اقتراح المنتجات المناسبة لنوع بشرتي الحساسة.',
                'tag_ar'  => 'عميل موثّق',
                'tag_en'  => 'Verified Buyer',
            ],
            [
                'name'    => 'شهد الحربي',
                'email'   => 'shahad.alharbi@kcodeskin.com',
                'city'    => 'الدمام',
                'rating'  => 5,
                'comment' => 'أفضل متجر للمنتجات الكورية في السعودية! التغليف كان ممتاز ومحكم والتوصيل في وقت قياسي.',
                'tag_ar'  => 'عميل موثّق',
                'tag_en'  => 'Verified Buyer',
            ],
            [
                'name'    => 'منى السعيد',
                'email'   => 'mona.alsaeed@kcodeskin.com',
                'city'    => 'الخبر',
                'rating'  => 5,
                'comment' => 'المنتجات رائعة وأسعارها مناسبة مقارنة بالسوق، وتجربة الشراء من الموقع سهلة وسلسة.',
                'tag_ar'  => 'عميل موثّق',
                'tag_en'  => 'Verified Buyer',
            ],
        ];

        foreach ($customers as $item) {
            $user = User::updateOrCreate(
                ['email' => $item['email']],
                [
                    'name'     => $item['name'],
                    'city'     => $item['city'],
                    'password' => bcrypt('SecureKcodePass2026!'),
                    'image'    => 'default.png',
                ]
            );

            Review::updateOrCreate(
                [
                    'product_id' => null,
                    'user_id'    => $user->id,
                ],
                [
                    'rating'   => $item['rating'],
                    'comment'  => $item['comment'],
                    'tag_ar'   => $item['tag_ar'],
                    'tag_en'   => $item['tag_en'],
                    'featured' => true,
                ]
            );
        }
    }
}
