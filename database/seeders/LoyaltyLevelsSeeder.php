<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class LoyaltyLevelsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('loyalty_levels')->truncate();

        $levels = [
            [
                'name_ar'         => 'المبتدئة',
                'name_en'         => 'Beginner',
                'description_ar'  => 'مرحباً بك! ابدئي رحلتك مع KCODE واكسبي نقاطك الأولى.',
                'description_en'  => 'Welcome! Start your KCODE journey and earn your first points.',
                'min_points'      => 0,
                'max_points'      => 199,
                'color'           => '#A8A8A8',
                'icon'            => '🌱',
                'sort_order'      => 1,
                'is_active'       => true,
            ],
            [
                'name_ar'         => 'الوردي الجميل',
                'name_en'         => 'Beautiful Pink',
                'description_ar'  => 'أنتِ في تقدم رائع! استمري للحصول على عروض أكثر.',
                'description_en'  => 'You are making great progress! Keep going for more offers.',
                'min_points'      => 200,
                'max_points'      => 499,
                'color'           => '#E91E8C',
                'icon'            => '🌸',
                'sort_order'      => 2,
                'is_active'       => true,
            ],
            [
                'name_ar'         => 'الذهبية',
                'name_en'         => 'Golden',
                'description_ar'  => 'مستوى رائع! تمتعي بخصومات حصرية وأولوية الشحن.',
                'description_en'  => 'Amazing level! Enjoy exclusive discounts and shipping priority.',
                'min_points'      => 500,
                'max_points'      => 999,
                'color'           => '#F4C430',
                'icon'            => '⭐',
                'sort_order'      => 3,
                'is_active'       => true,
            ],
            [
                'name_ar'         => 'البلاتينية',
                'name_en'         => 'Platinum',
                'description_ar'  => 'إنجاز استثنائي! خصومات فورية وعروض VIP حصرية لكِ.',
                'description_en'  => 'Exceptional achievement! Instant discounts and exclusive VIP offers.',
                'min_points'      => 1000,
                'max_points'      => 1999,
                'color'           => '#B0C4DE',
                'icon'            => '💎',
                'sort_order'      => 4,
                'is_active'       => true,
            ],
            [
                'name_ar'         => 'KCODE VIP',
                'name_en'         => 'KCODE VIP',
                'description_ar'  => 'أنتِ من نخبة عضوات KCODE! مزايا لا محدودة وعروض حصرية دائماً.',
                'description_en'  => 'You are among KCODE elite members! Unlimited perks and exclusive offers.',
                'min_points'      => 2000,
                'max_points'      => null,   // لا سقف — أعلى مستوى
                'color'           => '#9B59B6',
                'icon'            => '👑',
                'sort_order'      => 5,
                'is_active'       => true,
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
