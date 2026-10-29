<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\PresetRoutine;
use App\Models\PresetRoutineProduct;
use App\Models\Product;

class MenPresetRoutineSeeder extends Seeder
{
    /**
     * Seed the 5 official Men's Curated Preset Routines matching KCODE_R422_I_DESKTOP (2).html with full Arabic & English support.
     */
    public function run(): void
    {
        // Clear existing Men routines
        $existing = PresetRoutine::forMen()->get();
        foreach ($existing as $ex) {
            $ex->items()->delete();
            $ex->delete();
        }

        $menRoutinesPool = [
            [
                'title_ar' => 'العناية اليومية الأساسية',
                'title_en' => 'Essential Daily Care',
                'description_ar' => 'ثلاث خطوات واضحة ومباشرة للعناية اليومية بالوجه. تنظيف لطيف، وترطيب خفيف، وحماية شمسية أساسية، دون خطوات إضافية أو ملمس ثقيل.',
                'description_en' => 'Three simple and direct steps for daily facial care. Gentle cleansing, lightweight hydration, and essential sun protection without extra steps or heavy feel.',
                'badge_ar' => 'لا مشكلة عندي، أريد الاهتمام بوجهي',
                'badge_en' => 'I have no issues, I want to care for my face',
                'skin_type_ar' => 'جميع أنواع البشرة',
                'skin_type_en' => 'All Skin Types',
                'goal_ar' => 'عناية أساسية يومية',
                'goal_en' => 'Daily Essentials',
                'skin_type_id' => 3,
                'goal_id' => 1,
                'benefits_json' => [
                    'تنظيف الأوساخ والدهون اليومية المتراكمة بلطف.',
                    'ترطيب خفيف وغير لامع يدعم راحة البشرة طوال اليوم.',
                    'حماية يومية من أشعة الشمس تحافظ على صحة وتوازن الوجه.',
                ],
                'what_it_offers_json' => [
                    'روتين سريع وسهل الالتزام به صباحاً ومساءً.',
                    'قوام مريح وخفيف يمتص سريعاً دون لمعان.',
                ],
                'how_to_use_ar' => 'صباحاً: غسول ثم واقي شمس. مساءً: غسول ثم مرطب.',
                'how_to_use_en' => 'Morning: Cleanser then Sunscreen. Evening: Cleanser then Moisturizer.',
                'steps' => [
                    [
                        'ar' => 'غسول',
                        'en' => 'Cleanser',
                        'sku' => 'KC0104',
                        'frequency_ar' => 'صباحًا ومساءً.',
                        'frequency_en' => 'Morning and Evening.',
                        'morning' => true,
                        'night' => true,
                        'use_time_ar' => 'صباحاً ومساءً',
                        'application_ar' => 'اغسل وجهك بالماء، كوّن رغوة ودلّك الوجه بلطف ثم اشطف جيداً.',
                    ],
                    [
                        'ar' => 'مرطب',
                        'en' => 'Moisturizer',
                        'sku' => 'KC0223',
                        'frequency_ar' => 'مساءً؛ ويمكن استخدامه صباحًا عند الحاجة.',
                        'frequency_en' => 'Evening; and can be used in morning if needed before sunscreen.',
                        'morning' => false,
                        'night' => true,
                        'use_time_ar' => 'مساءً',
                        'application_ar' => 'ضع كمية مناسبة على الوجه بعد التنظيف ودلّك حتى الامتصاص.',
                    ],
                    [
                        'ar' => 'واقي شمس',
                        'en' => 'Sunscreen',
                        'sku' => 'KC0076',
                        'frequency_ar' => 'صباحًا.',
                        'frequency_en' => 'Morning.',
                        'morning' => true,
                        'night' => false,
                        'use_time_ar' => 'صباحاً',
                        'application_ar' => 'ضع طبقة متساوية من واقي الشمس كل صباح قبل الخروج.',
                    ],
                ],
            ],
            [
                'title_ar' => 'عناية باللمعان الزائد',
                'title_en' => 'Excess Shine Care',
                'description_ar' => 'عناية متوازنة بالزيوت الزائدة واللمعان خلال اليوم. تنظيف خفيف وترطيب لا يترك أثراً دهنياً، مع حماية غير مرئية من الشمس تناسب البشرة التي تفرز الزيوت بسرعة.',
                'description_en' => 'Balanced care for excess oil and daytime shine. Lightweight cleansing, non-greasy hydration, and invisible sun protection suitable for skin that gets oily quickly.',
                'badge_ar' => 'وجهي يلمع بسرعة',
                'badge_en' => 'My face gets oily and shiny quickly',
                'skin_type_ar' => 'البشرة الدهنية والمختلطة',
                'skin_type_en' => 'Oily & Combination Skin',
                'goal_ar' => 'السيطرة على الدهون واللمعان',
                'goal_en' => 'Oil & Shine Control',
                'skin_type_id' => 1,
                'goal_id' => 3,
                'benefits_json' => [
                    'يساعد على تنقية المسام من الإفرازات والزيوت.',
                    'يقلل اللمعان المزعج في الجبهة والأنف.',
                    'ترطيب منعش بصيغة جل مائي لا تسد المسام.',
                    'حماية شمسية غير مرئية وبملمس جاف.',
                ],
                'what_it_offers_json' => [
                    'مظهر غير لامع ومريح طوال ساعات العمل واليوم.',
                    'تنظيم إفراز الزيوت دون التسبب في جفاف الجلد.',
                ],
                'how_to_use_ar' => 'صباحاً: غسول ثم واقي شمس غير مرئي. مساءً: غسول ثم مرطب مائي خفيف.',
                'how_to_use_en' => 'Morning: Cleanser then Invisible Sunscreen. Evening: Cleanser then Lightweight Gel Moisturizer.',
                'steps' => [
                    [
                        'ar' => 'غسول',
                        'en' => 'Cleanser',
                        'sku' => 'KC0163',
                        'frequency_ar' => 'صباحًا ومساءً.',
                        'frequency_en' => 'Morning and Evening.',
                        'morning' => true,
                        'night' => true,
                        'use_time_ar' => 'صباحاً ومساءً',
                        'application_ar' => 'دلّك الرغوة على بشرة مبللة مع التركيز على منطقة T ثم اشطف.',
                    ],
                    [
                        'ar' => 'مرطب',
                        'en' => 'Moisturizer',
                        'sku' => 'KC0088',
                        'frequency_ar' => 'مساءً؛ ويمكن استخدامه صباحًا قبل واقي الشمس.',
                        'frequency_en' => 'Evening; can be used in morning before sunscreen if needed.',
                        'morning' => false,
                        'night' => true,
                        'use_time_ar' => 'مساءً',
                        'application_ar' => 'وزّع كمية خفيفة من المرطب المائي لتهدئة وضبط رطوبة البشرة.',
                    ],
                    [
                        'ar' => 'واقي شمس',
                        'en' => 'Sunscreen',
                        'sku' => 'KC0159',
                        'frequency_ar' => 'صباحًا.',
                        'frequency_en' => 'Morning.',
                        'morning' => true,
                        'night' => false,
                        'use_time_ar' => 'صباحاً',
                        'application_ar' => 'ضع طبقة متساوية من الواقي الشمسي الخفيف كل صباح.',
                    ],
                ],
            ],
            [
                'title_ar' => 'ترطيب وراحة بعد الغسل',
                'title_en' => 'Hydration & Comfort After Cleansing',
                'description_ar' => 'عناية بالشد والانزعاج بعد غسل الوجه. غسول لطيف لا يجرّد البشرة من رطوبتها، مع ترطيب غني ومريح يدوم طوال اليوم وحماية شمسية ناعمة.',
                'description_en' => 'Care for tightness and discomfort after washing. Gentle cleanser that does not strip moisture, with rich comfortable hydration lasting all day and soft sun protection.',
                'badge_ar' => 'وجهي يشدّ بعد الغسيل',
                'badge_en' => 'My face feels tight after washing',
                'skin_type_ar' => 'البشرة الجافة والحساسة',
                'skin_type_en' => 'Dry & Sensitive Skin',
                'goal_ar' => 'ترميم واستعادة الترطيب',
                'goal_en' => 'Hydration & Comfort',
                'skin_type_id' => 2,
                'goal_id' => 5,
                'benefits_json' => [
                    'ينظف الوجه بلطف شديد دون التسبب في جفاف أو حكة.',
                    'يغذي حاجز البشرة المنهك ويعيد مرونة الجلد.',
                    'يلغي إحساس الشد المزعج بعد الاستحمام أو الوضوء.',
                ],
                'what_it_offers_json' => [
                    'راحة فورية وانتعاش للبشرة المشدودة.',
                    'حماية متواصلة من الجفاف والتقلبات الجوية.',
                ],
                'how_to_use_ar' => 'صباحاً: غسول لطيف ثم واقي شمس مرطب. مساءً: غسول لطيف ثم مرطب ترميمي غني.',
                'how_to_use_en' => 'Morning: Gentle Cleanser then Hydrating Sunscreen. Evening: Gentle Cleanser then Restorative Cream.',
                'steps' => [
                    [
                        'ar' => 'غسول',
                        'en' => 'Cleanser',
                        'sku' => 'KC0140',
                        'frequency_ar' => 'صباحًا ومساءً.',
                        'frequency_en' => 'Morning and Evening.',
                        'morning' => true,
                        'night' => true,
                        'use_time_ar' => 'صباحاً ومساءً',
                        'application_ar' => 'اغسل الوجه بلطف بالماء الفاتر ودلّك الغسول بنعومة دون فرك قوي.',
                    ],
                    [
                        'ar' => 'مرطب',
                        'en' => 'Moisturizer',
                        'sku' => 'KC0009',
                        'frequency_ar' => 'مساءً؛ ويمكن استخدامه صباحًا عند جفاف البشرة.',
                        'frequency_en' => 'Evening; and in the morning if skin feels dry.',
                        'morning' => false,
                        'night' => true,
                        'use_time_ar' => 'مساءً',
                        'application_ar' => 'ضع طبقة كافية من الكريم المرطب على الوجه والرقبة.',
                    ],
                    [
                        'ar' => 'واقي شمس',
                        'en' => 'Sunscreen',
                        'sku' => 'KC0121',
                        'frequency_ar' => 'صباحًا.',
                        'frequency_en' => 'Morning.',
                        'morning' => true,
                        'night' => false,
                        'use_time_ar' => 'صباحاً',
                        'application_ar' => 'وزّع كمية مناسبة من واقي الشمس المرطب صباح كل يوم.',
                    ],
                ],
            ],
            [
                'title_ar' => 'نضارة للبشرة الباهتة',
                'title_en' => 'Radiance for Dull Skin',
                'description_ar' => 'خطوات مدروسة للمساعدة على استعادة حيوية الوجه ومظهره المتجدّد. تنظيف يومي وسيروم لطيف للنضارة مع ترطيب خفيف وحماية شمسية تحافظ على صحة البشرة.',
                'description_en' => 'Carefully selected steps to help restore face vitality and refreshed look. Daily cleansing, gentle radiance serum, lightweight hydration, and sun protection maintaining skin health.',
                'badge_ar' => 'وجهي باهت ويبدو متعباً',
                'badge_en' => 'My face looks dull and tired',
                'skin_type_ar' => 'جميع أنواع البشرة',
                'skin_type_en' => 'All Skin Types',
                'goal_ar' => 'نضارة وتجديد الحيوية',
                'goal_en' => 'Glow & Vitality',
                'skin_type_id' => 3,
                'goal_id' => 1,
                'benefits_json' => [
                    'يساعد على تنشيط البشرة الباهتة وتوحيد مظهرها.',
                    'يقلل من علامات الإرهاق وقلة النوم.',
                    'يمنح الوجه مظهراً صحياً وأكثر إشراقاً.',
                ],
                'what_it_offers_json' => [
                    'بشرة أكثر حيوية وتوهجاً طبيعياً.',
                    'حماية فعالة ضد أضرار الشمس المسببة للتصبغ والشحوب.',
                ],
                'how_to_use_ar' => 'صباحاً: غسول ثم واقي شمس. مساءً: غسول، ثم 2–3 قطرات سيروم، ثم مرطب.',
                'how_to_use_en' => 'Morning: Cleanser then Sunscreen. Evening: Cleanser, 2-3 drops Serum, then Moisturizer.',
                'steps' => [
                    [
                        'ar' => 'غسول',
                        'en' => 'Cleanser',
                        'sku' => 'KC0104',
                        'frequency_ar' => 'صباحًا ومساءً.',
                        'frequency_en' => 'Morning and Evening.',
                        'morning' => true,
                        'night' => true,
                        'use_time_ar' => 'صباحاً ومساءً',
                        'application_ar' => 'اغسل وجهك بالغسول بالماء الفاتر.',
                    ],
                    [
                        'ar' => 'علاج خفيف',
                        'en' => 'Light Treatment',
                        'sku' => 'KC0095',
                        'frequency_ar' => 'مساءً: كل يومين في الأسبوع الأول، ثم يومياً.',
                        'frequency_en' => 'Evening: every other day for the first week, then daily.',
                        'morning' => false,
                        'night' => true,
                        'use_time_ar' => 'مساءً',
                        'application_ar' => 'ضع 2–3 قطرات من السيروم ودلّك بلطف حتى تمتصه البشرة تماماً.',
                    ],
                    [
                        'ar' => 'مرطب',
                        'en' => 'Moisturizer',
                        'sku' => 'KC0223',
                        'frequency_ar' => 'مساءً؛ ويمكن استخدامه صباحًا عند الحاجة.',
                        'frequency_en' => 'Evening; can be used in morning before sunscreen if needed.',
                        'morning' => false,
                        'night' => true,
                        'use_time_ar' => 'مساءً',
                        'application_ar' => 'ضع المرطب بعد امتصاص السيروم لحبس الرطوبة.',
                    ],
                    [
                        'ar' => 'واقي شمس',
                        'en' => 'Sunscreen',
                        'sku' => 'KC0076',
                        'frequency_ar' => 'صباحًا.',
                        'frequency_en' => 'Morning.',
                        'morning' => true,
                        'night' => false,
                        'use_time_ar' => 'صباحاً',
                        'application_ar' => 'ضع طبقة متساوية من واقي الشمس لحماية نضارة البشرة.',
                    ],
                ],
            ],
            [
                'title_ar' => 'عناية بعد الحلاقة',
                'title_en' => 'Post-Shave Care',
                'description_ar' => 'عناية لطيفة بالجفاف والانزعاج بعد الحلاقة. تنظيف لطيف وأمبول مهدئ للتهيج والاحمرار مع ترطيب وحماية شمسية للحفاظ على وجه ناعم ومرتاح.',
                'description_en' => 'Gentle care for dryness and post-shave discomfort. Gentle cleanser, soothing ampoule for redness and irritation, with hydration and sun protection for a calm, comfortable face.',
                'badge_ar' => 'بشرتي تتهيج بعد الحلاقة',
                'badge_en' => 'My skin gets irritated after shaving',
                'skin_type_ar' => 'البشرة الحساسة والمتهيجة',
                'skin_type_en' => 'Sensitive & Irritated Skin',
                'goal_ar' => 'تهدئة التهيّج بعد الحلاقة',
                'goal_en' => 'Post-Shave Soothing',
                'skin_type_id' => 4,
                'goal_id' => 5,
                'benefits_json' => [
                    'يهدئ الحرقة والاحمرار فوراً بعد مرور شفرة الحلاقة.',
                    'يرطب المناطق الحساسة ويمنع تكون الحبوب أو التهيج.',
                    'يمنح الوجه ملمساً ناعماً ومريحاً طوال اليوم.',
                ],
                'what_it_offers_json' => [
                    'راحة وانتعاش سريع بعد الحلاقة دون أي لسع أو عطور.',
                    'حماية للجلد المتجدد من أشعة الشمس والعوامل البيئية.',
                ],
                'how_to_use_ar' => 'بعد الحلاقة صباحاً: غسول، أمبول بعد الحلاقة، ثم واقي شمس. مساءً: غسول ثم مرطب.',
                'how_to_use_en' => 'Morning Post-Shave: Cleanser, After-Shave Ampoule, then Sunscreen. Evening: Cleanser then Moisturizer.',
                'steps' => [
                    [
                        'ar' => 'غسول',
                        'en' => 'Cleanser',
                        'sku' => 'KC0104',
                        'frequency_ar' => 'صباحًا ومساءً.',
                        'frequency_en' => 'Morning and Evening.',
                        'morning' => true,
                        'night' => true,
                        'use_time_ar' => 'صباحاً ومساءً',
                        'application_ar' => 'اغسل وجهك قبل الحلاقة بالماء الفاتر لتليين الشعر وتهيئة الجلد.',
                    ],
                    [
                        'ar' => 'بعد الحلاقة',
                        'en' => 'After Shave Care',
                        'sku' => 'KC0144',
                        'frequency_ar' => 'بعد الحلاقة فقط.',
                        'frequency_en' => 'After shaving only.',
                        'morning' => true,
                        'night' => false,
                        'use_time_ar' => 'بعد الحلاقة',
                        'application_ar' => 'ضع بضع قطرات من الأمبول على المنطقة المحلوقة لتهدئة الجلد فوراً.',
                    ],
                    [
                        'ar' => 'مرطب',
                        'en' => 'Moisturizer',
                        'sku' => 'KC0223',
                        'frequency_ar' => 'مساءً؛ ويمكن استخدامه صباحًا عند الحاجة.',
                        'frequency_en' => 'Evening; can be used in morning if skin feels dry.',
                        'morning' => false,
                        'night' => true,
                        'use_time_ar' => 'مساءً',
                        'application_ar' => 'ضع المرطب لترميم واستعادة نعومة الجلد.',
                    ],
                    [
                        'ar' => 'واقي شمس',
                        'en' => 'Sunscreen',
                        'sku' => 'KC0076',
                        'frequency_ar' => 'صباحًا.',
                        'frequency_en' => 'Morning.',
                        'morning' => true,
                        'night' => false,
                        'use_time_ar' => 'صباحاً',
                        'application_ar' => 'ضع واقي الشمس بعد امتصاص الأمبول لحماية البشرة الحساسة.',
                    ],
                ],
            ],
        ];

        foreach ($menRoutinesPool as $config) {
            $skinTypeId = $config['skin_type_id'] ?? null;
            if ($skinTypeId && !\App\Models\SkinType::where('id', $skinTypeId)->exists()) {
                $skinTypeId = null;
            }

            $goalId = $config['goal_id'] ?? null;
            if ($goalId && !\App\Models\RoutineGoal::where('id', $goalId)->exists()) {
                $goalId = null;
            }

            $routine = PresetRoutine::create([
                'title_ar' => $config['title_ar'],
                'title_en' => $config['title_en'],
                'description_ar' => $config['description_ar'],
                'description_en' => $config['description_en'],
                'badge_ar' => $config['badge_ar'],
                'badge_en' => $config['badge_en'],
                'skin_type_ar' => $config['skin_type_ar'],
                'skin_type_en' => $config['skin_type_en'],
                'goal_ar' => $config['goal_ar'],
                'goal_en' => $config['goal_en'],
                'skin_type_id' => $skinTypeId,
                'goal_id' => $goalId,
                'is_for_men' => true,
                'status' => 'active',
                'benefits_json' => $config['benefits_json'],
                'what_it_offers_json' => $config['what_it_offers_json'],
                'how_to_use_ar' => $config['how_to_use_ar'],
                'how_to_use_en' => $config['how_to_use_en'],
            ]);

            $order = 1;
            foreach ($config['steps'] as $step) {
                $product = Product::where('sku', $step['sku'])->first();
                if (!$product) {
                    continue;
                }

                PresetRoutineProduct::create([
                    'preset_routine_id' => $routine->id,
                    'product_id' => $product->id,
                    'display_order' => $order++,
                    'step_name_ar' => $step['ar'],
                    'step_name_en' => $step['en'],
                    'morning' => (bool)$step['morning'],
                    'night' => (bool)$step['night'],
                    'use_time_ar' => $step['use_time_ar'],
                    'frequency_ar' => $step['frequency_ar'],
                    'frequency_en' => $step['frequency_en'],
                    'application_ar' => $step['application_ar'],
                ]);
            }
        }
    }
}
