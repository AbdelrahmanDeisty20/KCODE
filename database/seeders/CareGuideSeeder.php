<?php

namespace Database\Seeders;

use App\Models\CareGuide;
use App\Models\CareGuideItem;
use Illuminate\Database\Seeder;

class CareGuideSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $careGuide = CareGuide::updateOrCreate(
            ['id' => 1],
            [
                'title_ar'               => 'دليل العناية',
                'title_en'               => 'Care Guide',
                'description_ar'         => 'يراجع فريق KCODE مكونات المنتج ودورها في تركيبته، ويحوّل هذه القراءة إلى إرشادات واضحة تساعدك على الاختيار.',
                'description_en'         => 'The KCODE team reviews product ingredients and their role in its formula, translating this reading into clear guidelines to help you choose.',
                'note_ar'                => 'المنتج الجيد قد لا يناسب الجميع. لذلك نذكر المزايا والملاحظات التي قد تغيّر قرارك.',
                'note_en'                => 'A good product may not suit everyone. Therefore, we mention the pros and considerations that might influence your decision.',
                'button_quiz_text_ar'    => 'ابدأ تحليل بشرتك',
                'button_quiz_text_en'    => 'Start Skin Analysis',
                'button_quiz_action'     => 'quiz',
                'button_routines_text_ar'=> 'اكتشف الروتينات',
                'button_routines_text_en'=> 'Discover Routines',
                'button_routines_action' => 'routines',
                'is_active'              => true,
            ]
        );

        $items = [
            [
                'title_ar'       => 'نفهم تركيبته',
                'title_en'       => 'Understand Its Formulation',
                'description_ar' => 'نشرح لك فائدة مكوناته، وما يمكن أن يقدّمه لبشرتك.',
                'description_en' => 'We explain the benefits of its ingredients, and what it can offer to your skin.',
                'icon'           => 'flask',
                'sort_order'     => 1,
                'is_active'      => true,
            ],
            [
                'title_ar'       => 'نوضّح لمن يناسب',
                'title_en'       => 'Clarify Who It Suits',
                'description_ar' => 'نحدّد أنواع البشرة الأنسب له، وما يجب مراعاته إذا كانت بشرتك حساسة.',
                'description_en' => 'We determine the most suitable skin types for it, and what to consider if your skin is sensitive.',
                'icon'           => 'user-check',
                'sort_order'     => 2,
                'is_active'      => true,
            ],
            [
                'title_ar'       => 'نرشدك لاستخدامه',
                'title_en'       => 'Guide You on How to Use It',
                'description_ar' => 'نوضّح متى تستخدمه، وترتيبه في روتينك، وكيف تبدأ به.',
                'description_en' => 'We clarify when to use it, its order in your routine, and how to get started.',
                'icon'           => 'sparkles',
                'sort_order'     => 3,
                'is_active'      => true,
            ],
        ];

        foreach ($items as $item) {
            CareGuideItem::updateOrCreate(
                [
                    'care_guide_id' => $careGuide->id,
                    'title_ar'      => $item['title_ar'],
                ],
                array_merge($item, ['care_guide_id' => $careGuide->id])
            );
        }
    }
}
