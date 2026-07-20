<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class LoyaltyLevelsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    // php artisan db:seed --class=LoyaltyLevelsSeeder
    public function run(): void
    {
        DB::table('loyalty_levels')->truncate();

        $levels = [
            [
                'name_ar'        => 'المبتدئة',
                'name_en'        => 'Beginner',
                'description_ar' => 'مرحباً بك! ابدئي رحلتك مع KCODE واكسبي نقاطك الأولى.',
                'description_en' => 'Welcome! Start your KCODE journey and earn your first points.',
                'policy_ar'      => 'سياسة المستوى: كسب 1 نقطة مقابل كل 10 ريالات يتم إنفاقها. تنتهي صلاحية النقاط بعد 6 أشهر.',
                'policy_en'      => 'Level Policy: Earn 1 point for every 10 SAR spent. Points expire after 6 months.',
                'min_points'     => 0,
                'max_points'     => 199,
                'icon'           => '🌱',
                'sort_order'     => 1,
                'is_active'      => true,
            ],
            [
                'name_ar'        => 'الوردي الجميل',
                'name_en'        => 'Beautiful Pink',
                'description_ar' => 'أنتِ في تقدم رائع! استمري للحصول على عروض أكثر.',
                'description_en' => 'You are making great progress! Keep going for more offers.',
                'policy_ar'      => 'سياسة المستوى: كسب 1.2 نقطة مقابل كل 10 ريالات. الحصول على خصم 5% على منتج واحد شهرياً.',
                'policy_en'      => 'Level Policy: Earn 1.2 points for every 10 SAR. Get 5% discount on one product monthly.',
                'min_points'     => 200,
                'max_points'     => 499,
                'icon'           => '🌸',
                'sort_order'     => 2,
                'is_active'      => true,
            ],
            [
                'name_ar'        => 'الذهبية',
                'name_en'        => 'Golden',
                'description_ar' => 'مستوى رائع! تمتعي بخصومات حصرية وأولوية الشحن.',
                'description_en' => 'Amazing level! Enjoy exclusive discounts and shipping priority.',
                'policy_ar'      => 'سياسة المستوى: كسب 1.5 نقطة مقابل كل 10 ريالات. أولوية في الشحن وهدية خاصة في عيد ميلادك.',
                'policy_en'      => 'Level Policy: Earn 1.5 points for every 10 SAR. Priority shipping and a special birthday gift.',
                'min_points'     => 500,
                'max_points'     => 999,
                'icon'           => '⭐',
                'sort_order'     => 3,
                'is_active'      => true,
            ],
            [
                'name_ar'        => 'البلاتينية',
                'name_en'        => 'Platinum',
                'description_ar' => 'إنجاز استثنائي! خصومات فورية وعروض VIP حصرية لكِ.',
                'description_en' => 'Exceptional achievement! Instant discounts and exclusive VIP offers.',
                'policy_ar'      => 'سياسة المستوى: كسب 2 نقطة مقابل كل 10 ريالات. شحن مجاني لجميع الطلبات وتجربة المنتجات الجديدة مجاناً.',
                'policy_en'      => 'Level Policy: Earn 2 points for every 10 SAR. Free shipping on all orders and try new products for free.',
                'min_points'     => 1000,
                'max_points'     => 1999,
                'icon'           => '💎',
                'sort_order'     => 4,
                'is_active'      => true,
            ],
            [
                'name_ar'        => 'KCODE VIP',
                'name_en'        => 'KCODE VIP',
                'description_ar' => 'أنتِ من نخبة عضوات KCODE! مزايا لا محدودة وعروض حصرية دائماً.',
                'description_en' => 'You are among KCODE elite members! Unlimited perks and exclusive offers.',
                'policy_ar'      => 'سياسة المستوى: كسب 2.5 نقطة مقابل كل 10 ريالات. مستشار بشرة شخصي مجاناً، ودعوات حصرية لفعاليات KCODE.',
                'policy_en'      => 'Level Policy: Earn 2.5 points for every 10 SAR. Free personal skin consultant, and exclusive invitations to KCODE events.',
                'min_points'     => 2000,
                'max_points'     => null,
                'icon'           => '👑',
                'sort_order'     => 5,
                'is_active'      => true,
            ],
        ];

        foreach ($levels as $level) {
            DB::table('loyalty_levels')->insert(array_merge($level, [
                'created_at' => now(),
                'updated_at' => now(),
            ]));
        }

        $this->command->info('✅ تم إضافة ' . count($levels) . ' مستويات نقاط بنجاح!');
    }
}
