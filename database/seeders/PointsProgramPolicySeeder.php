<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PointsProgramPolicySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('points_program_policies')->truncate();

        $contentAr = [
            'redemption' => [
                'title'        => 'معادلة استبدال النقاط',
                'rate'         => 'كل 100 نقطة = 1 ريال عُماني خصم',
                'description'  => 'يمكنك استبدال نقاطك التراكمية مباشرة أثناء خطوة إتمام الطلب (Checkout) للحصول على خصم فوري على إجمالي السلة.',
                'badge_points' => '100 ن',
                'badge_value'  => '1 ر.ع',
                'badge_label'  => 'خصم مباشر'
            ],
            'earning_heading' => 'كيف تكسبين النقاط؟',
            'cards' => [
                [
                    'icon'         => 'shopping-bag',
                    'title'        => 'التسوق للشراء',
                    'subtitle'     => 'احصلي على 10 نقاط مقابل كل 1 ريال عُماني تنفقينه على أي منتج من منتجات العناية بالبشرة.',
                    'points_badge' => '10+ نقاط / ريال'
                ],
                [
                    'icon'         => 'user-plus',
                    'title'        => 'إنشاء حساب جديد',
                    'subtitle'     => 'احصلي على 100 نقطة ترحيبية فورية بمجرد تسجيل حسابك الجديد في منصة KCODE.',
                    'points_badge' => '100+ نقطة'
                ],
                [
                    'icon'         => 'star',
                    'title'        => 'كتابة تقييم موثق',
                    'subtitle'     => 'شاركينا تجربتك وانطباعك عن المنتجات واحصلي على 25 نقطة إضافية لكل تقييم موثق.',
                    'points_badge' => '25+ نقطة'
                ],
                [
                    'icon'         => 'cake',
                    'title'        => 'هدية شهر الميلاد',
                    'subtitle'     => 'نحتفل معكِ في شهر ميلادكِ بهدية خاصة بقيمة 150 نقطة تضاف تلقائياً لحسابكِ.',
                    'points_badge' => '150+ نقطة'
                ]
            ]
        ];

        $contentEn = [
            'redemption' => [
                'title'        => 'Points Redemption Formula',
                'rate'         => 'Every 100 points = 1 Omani Rial discount',
                'description'  => 'You can redeem your accumulated points directly during the Checkout step to get an instant discount on the total cart.',
                'badge_points' => '100 Pts',
                'badge_value'  => '1 OMR',
                'badge_label'  => 'Direct Discount'
            ],
            'earning_heading' => 'How to Earn Points?',
            'cards' => [
                [
                    'icon'         => 'shopping-bag',
                    'title'        => 'Shopping & Purchasing',
                    'subtitle'     => 'Get 10 points for every 1 Omani Rial you spend on any skin care products.',
                    'points_badge' => '+10 Points / Rial'
                ],
                [
                    'icon'         => 'user-plus',
                    'title'        => 'Create a New Account',
                    'subtitle'     => 'Get 100 welcome points instantly when you register your new account on the KCODE platform.',
                    'points_badge' => '+100 Points'
                ],
                [
                    'icon'         => 'star',
                    'title'        => 'Write a Verified Review',
                    'subtitle'     => 'Share your experience and impression of the products and get 25 additional points for each verified review.',
                    'points_badge' => '+25 Points'
                ],
                [
                    'icon'         => 'cake',
                    'title'        => 'Birthday Month Gift',
                    'subtitle'     => 'We celebrate with you in your birthday month with a special gift of 150 points added automatically to your account.',
                    'points_badge' => '+150 Points'
                ]
            ]
        ];

        DB::table('points_program_policies')->insert([
            'title_ar'   => 'برنامج النقاط',
            'title_en'   => 'Points Program',
            'content_ar' => json_encode($contentAr, JSON_UNESCAPED_UNICODE),
            'content_en' => json_encode($contentEn, JSON_UNESCAPED_UNICODE),
            'is_active'  => true,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }
}
