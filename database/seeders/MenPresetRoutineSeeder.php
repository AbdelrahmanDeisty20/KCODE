<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\PresetRoutine;
use App\Models\PresetRoutineProduct;
use App\Models\Product;

class MenPresetRoutineSeeder extends Seeder
{
    /**
     * Seed the 5 official Men's Simple Preset Routines from Homepage V24 Design (Image 4).
     */
    public function run(): void
    {
        // Clear existing Men routines
        PresetRoutine::forMen()->delete();

        $menRoutinesPool = [
            [
                'title_ar' => 'لا مشكلة عندي، أريد الاهتمام بوجهي',
                'title_en' => 'Simple Daily Essentials Routine for Men',
                'description_ar' => 'ثلاثة منتجات أساسية تساهم في تنظيف بشرتك وترطيبها وحمايتها من الشمس - من دون خطوات كثيرة أو إحساس ثقيل.',
                'description_en' => 'Three essential products to cleanse, hydrate, and protect your face daily — without heavy feeling or complicated steps.',
                'badge_ar' => 'البداية الأبسط للعناية اليومية.',
                'badge_en' => 'Simplest Start for Daily Care.',
                'skin_type_ar' => 'جميع أنواع البشرة',
                'skin_type_en' => 'All Skin Types',
                'goal_ar' => 'عناية أساسية يومية',
                'goal_en' => 'Daily Essentials',
                'skin_type_id' => 3,
                'goal_id' => 1,
                'steps' => [
                    ['ar' => 'غسول', 'en' => 'Cleanser', 'sku' => 'KC0104'],
                    ['ar' => 'مرطب', 'en' => 'Moisturizer', 'sku' => 'KC0223'],
                    ['ar' => 'واقي شمس', 'en' => 'Sunscreen', 'sku' => 'KC0076'],
                ],
            ],
            [
                'title_ar' => 'وجهي يلمع بسرعة',
                'title_en' => 'Oily & Shine Control Routine for Men',
                'description_ar' => 'تنظيف خفيف، وترطيب من دون إحساس دهني، وواقي شمس غير مرئي — بخطوات واضحة يمكن الالتزام بها كل يوم.',
                'description_en' => 'Lightweight cleansing, non-greasy hydration, and invisible sun protection — clear steps to commit to every day.',
                'badge_ar' => 'الوجه الذي يصبح لامعاً خلال اليوم.',
                'badge_en' => 'For Face That Gets Oily During Day.',
                'skin_type_ar' => 'البشرة الدهنية والمختلطة',
                'skin_type_en' => 'Oily & Combination Skin',
                'goal_ar' => 'السيطرة على الدهون واللمعان',
                'goal_en' => 'Oil & Shine Control',
                'skin_type_id' => 1,
                'goal_id' => 3,
                'steps' => [
                    ['ar' => 'غسول', 'en' => 'Cleanser', 'sku' => 'KC0163'],
                    ['ar' => 'مرطب', 'en' => 'Moisturizer', 'sku' => 'KC0088'],
                    ['ar' => 'واقي شمس', 'en' => 'Sunscreen', 'sku' => 'KC0159'],
                ],
            ],
            [
                'title_ar' => 'وجهي يشدّ بعد الغسيل',
                'title_en' => 'Dryness & Tightness Relief Routine for Men',
                'description_ar' => 'روتين لطيف يساعد على تنظيف بشرتك من دون إحساس مزعج بالشد، واستعادة الترطيب، والراحة مع حماية يومية من الشمس.',
                'description_en' => 'Gentle routine to cleanse without uncomfortable tightness, restoring hydration and comfort with daily sun protection.',
                'badge_ar' => 'الجفاف، والشد والشعور بعدم الراحة.',
                'badge_en' => 'For Dryness, Tightness & Discomfort.',
                'skin_type_ar' => 'البشرة الجافة والحساسة',
                'skin_type_en' => 'Dry & Sensitive Skin',
                'goal_ar' => 'ترميم واستعادة الترطيب',
                'goal_en' => 'Hydration & Comfort',
                'skin_type_id' => 2,
                'goal_id' => 5,
                'steps' => [
                    ['ar' => 'غسول', 'en' => 'Cleanser', 'sku' => 'KC0140'],
                    ['ar' => 'مرطب', 'en' => 'Moisturizer', 'sku' => 'KC0009'],
                    ['ar' => 'واقي شمس', 'en' => 'Sunscreen', 'sku' => 'KC0121'],
                ],
            ],
            [
                'title_ar' => 'وجهي باهت ويبدو متعباً',
                'title_en' => 'Dull & Tired Skin Revitalizing Routine for Men',
                'description_ar' => 'روتين خفيف يساعد على ترطيب البشرة واستعادة مظهرها الصحي، لتبدو أكثر نضارة وأقل تعباً من دون قوام دقيق.',
                'description_en' => 'Light routine to hydrate skin and restore a healthy look, appearing fresher and less tired without greasy feel.',
                'badge_ar' => 'الترطيب، وإشراقة طبيعية غير دهنية.',
                'badge_en' => 'Hydration & Non-Greasy Natural Glow.',
                'skin_type_ar' => 'جميع أنواع البشرة',
                'skin_type_en' => 'All Skin Types',
                'goal_ar' => 'نضارة وتجديد الحيوية',
                'goal_en' => 'Glow & Vitality',
                'skin_type_id' => 3,
                'goal_id' => 1,
                'steps' => [
                    ['ar' => 'غسول', 'en' => 'Cleanser', 'sku' => 'KC0104'],
                    ['ar' => 'علاج خفيف', 'en' => 'Light Treatment', 'sku' => 'KC0095'],
                    ['ar' => 'مرطب', 'en' => 'Moisturizer', 'sku' => 'KC0223'],
                    ['ar' => 'واقي شمس', 'en' => 'Sunscreen', 'sku' => 'KC0076'],
                ],
            ],
            [
                'title_ar' => 'بشرتي تتهيج بعد الحلاقة',
                'title_en' => 'Post-Shave Soothing & Calming Routine for Men',
                'description_ar' => 'عناية خفيفة تساعد على تهدئة الشعور بالحكة والشد بعد الحلاقة من دون عطر أو قوام دقيق.',
                'description_en' => 'Lightweight care to soothe burning, redness, and dryness after shaving, fragrance-free with light texture.',
                'badge_ar' => 'إعادة البشرة بعد مرور الشفرة.',
                'badge_en' => 'Post-Shave Razor Relief.',
                'skin_type_ar' => 'البشرة الحساسة والمتهيجة',
                'skin_type_en' => 'Sensitive & Irritated Skin',
                'goal_ar' => 'تهدئة التهيّج بعد الحلاقة',
                'goal_en' => 'Post-Shave Soothing',
                'skin_type_id' => 4,
                'goal_id' => 5,
                'steps' => [
                    ['ar' => 'غسول', 'en' => 'Cleanser', 'sku' => 'KC0104'],
                    ['ar' => 'بعد الحلاقة', 'en' => 'After Shave Care', 'sku' => 'KC0144'],
                    ['ar' => 'مرطب', 'en' => 'Moisturizer', 'sku' => 'KC0223'],
                    ['ar' => 'واقي شمس', 'en' => 'Sunscreen', 'sku' => 'KC0076'],
                ],
            ],
        ];

        foreach ($menRoutinesPool as $config) {
            $presetRoutine = PresetRoutine::create([
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
                'skin_type_id' => $config['skin_type_id'],
                'goal_id' => $config['goal_id'],
                'is_for_men' => true,
                'status' => 'active',
            ]);

            $order = 1;
            foreach ($config['steps'] as $idx => $stepInfo) {
                $sku = $stepInfo['sku'] ?? null;
                $prod = $sku ? Product::where('sku', $sku)->first() : null;

                if (!$prod) {
                    $prod = Product::where('stock', '>', 0)->inRandomOrder()->first();
                }

                if (!$prod) break;

                PresetRoutineProduct::create([
                    'preset_routine_id' => $presetRoutine->id,
                    'product_id' => $prod->id,
                    'display_order' => $order++,
                    'step_name_ar' => $stepInfo['ar'],
                    'step_name_en' => $stepInfo['en'],
                    'morning' => true,
                    'night' => true,
                    'use_time_ar' => 'صباحاً ومساءً',
                ]);
            }
        }
    }
}
