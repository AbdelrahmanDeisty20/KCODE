<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\PresetRoutine;
use App\Models\PresetRoutineProduct;
use App\Models\Product;

class GenerateMenPresetRoutinesCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'routines:generate-men-preset';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generates 5 official Men Simple Preset Routines in the database';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Generating Men Simple Preset Routines in database...');

        $menRoutinesPool = [
            [
                'title_ar' => 'لا مشكلة عندي، أريد الاهتمام بوجهي',
                'title_en' => 'The Simple Daily Essentials Routine for Men',
                'description_ar' => 'ثلاثة منتجات أساسية تساعدك على تنظيف بشرتك وترطيبها وحمايتها من الشمس - من دون خطوات كثيرة أو إحساس ثقيل.',
                'description_en' => 'Three essential products to cleanse, hydrate, and protect your face daily — without heavy feeling or complicated steps.',
                'badge_ar' => 'البداية الأبسط للعناية اليومية',
                'badge_en' => 'Daily Essentials 🌿',
                'skin_type_ar' => 'جميع أنواع البشرة',
                'skin_type_en' => 'All Skin Types',
                'goal_ar' => 'عناية أساسية يومية',
                'goal_en' => 'Daily Essentials',
                'skin_type_id' => 3,
                'goal_id' => 1,
            ],
            [
                'title_ar' => 'وجهي يلمع بسرعة',
                'title_en' => 'Oily & Shine Control Routine for Men',
                'description_ar' => 'تنظيف خفيف، وترطيب من دون إحساس دهني، وواقي شمس غير مرئي — بخطوات واضحة يمكن الالتزام بها كل يوم.',
                'description_en' => 'Lightweight cleansing, non-greasy hydration, and invisible sun protection — clear steps to commit to every day.',
                'badge_ar' => 'للوجه الذي يصبح لامعاً خلال اليوم',
                'badge_en' => 'Shine Control 💧',
                'skin_type_ar' => 'البشرة الدهنية والمختلطة',
                'skin_type_en' => 'Oily & Combination Skin',
                'goal_ar' => 'السيطرة على الدهون واللمعان',
                'goal_en' => 'Oil & Shine Control',
                'skin_type_id' => 1,
                'goal_id' => 3,
            ],
            [
                'title_ar' => 'وجهي يشدّ بعد الغسيل',
                'title_en' => 'Dryness & Tightness Relief Routine for Men',
                'description_ar' => 'روتين لطيف يساعد على تنظيف بشرتك من دون إحساس مزعج بالشد، واستعادة الترطيب، والراحة مع حماية يومية من الشمس.',
                'description_en' => 'Gentle routine to cleanse without uncomfortable tightness, restoring hydration and comfort with daily sun protection.',
                'badge_ar' => 'للجفاف والشعور بعدم الراحة',
                'badge_en' => 'Moisture & Comfort 💧',
                'skin_type_ar' => 'البشرة الجافة والحساسة',
                'skin_type_en' => 'Dry & Sensitive Skin',
                'goal_ar' => 'ترميم واستعادة الترطيب',
                'goal_en' => 'Hydration & Comfort',
                'skin_type_id' => 2,
                'goal_id' => 5,
            ],
            [
                'title_ar' => 'بشرتي تنزعج بعد الحلاقة',
                'title_en' => 'Post-Shave Soothing & Calming Routine for Men',
                'description_ar' => 'عناية خفيفة تساعد على تهدئة الشعور بالحرقان والجفاف بعد الحلاقة، من دون عطر أو قوام دقيق.',
                'description_en' => 'Lightweight care to soothe burning, redness, and dryness after shaving, fragrance-free with light texture.',
                'badge_ar' => 'راحة البشرة بعد مرور الشفرة',
                'badge_en' => 'Post-Shave Comfort 🪒',
                'skin_type_ar' => 'البشرة الحساسة والمتهيجة',
                'skin_type_en' => 'Sensitive & Irritated Skin',
                'goal_ar' => 'تهدئة التهيّج بعد الحلاقة',
                'goal_en' => 'Post-Shave Soothing',
                'skin_type_id' => 4,
                'goal_id' => 5,
            ],
            [
                'title_ar' => 'وجهي باهت ويبدو متعباً',
                'title_en' => 'Dull & Tired Skin Revitalizing Routine for Men',
                'description_ar' => 'روتين خفيف يساعد على ترطيب البشرة واستعادة مظهرها الصحي، لتبدو أكثر نضارة وأقل تعباً من دون قوام دقيق.',
                'description_en' => 'Light routine to hydrate skin and restore a healthy look, appearing fresher and less tired without greasy feel.',
                'badge_ar' => 'لترطيب وإشراقة طبيعية غير دهنية',
                'badge_en' => 'Natural Glow & Vitality 🌟',
                'skin_type_ar' => 'جميع أنواع البشرة',
                'skin_type_en' => 'All Skin Types',
                'goal_ar' => 'نضارة وتجديد الحيوية',
                'goal_en' => 'Glow & Vitality',
                'skin_type_id' => 3,
                'goal_id' => 1,
            ],
        ];

        foreach ($menRoutinesPool as $config) {
            // Check if routine title already exists for men
            $presetRoutine = PresetRoutine::forMen()
                ->where('title_ar', $config['title_ar'])
                ->first();

            if (!$presetRoutine) {
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
            }

            // Always pick 3 to 4 suitable products for each Men Routine
            $presetRoutine->items()->delete();

            $products = Product::where('stock', '>', 0)
                ->whereHas('skinTypes', function ($q) use ($config) {
                    $q->where('skin_type_id', $config['skin_type_id']);
                })
                ->inRandomOrder()
                ->take(4)
                ->get();

            if ($products->count() < 3) {
                $products = Product::where('stock', '>', 0)
                    ->inRandomOrder()
                    ->take(3)
                    ->get();
            }

            $order = 1;
            foreach ($products as $prod) {
                $routineInfo = $prod->routines->first() ?? ($prod->routineSteps ?? collect())->first();

                $stepNameAr = match ($order) {
                    1 => 'غسول تنظيف',
                    2 => 'علاج خفيف / بعد الحلاقة',
                    3 => 'مرطب خفيف',
                    4 => 'واقي شمس غير مرئي',
                    default => "الخطوة {$order}"
                };

                $stepNameEn = match ($order) {
                    1 => 'Cleanser',
                    2 => 'Light Treatment / After Shave',
                    3 => 'Moisturizer',
                    4 => 'Invisible Sunscreen',
                    default => "Step {$order}"
                };

                PresetRoutineProduct::create([
                    'preset_routine_id' => $presetRoutine->id,
                    'product_id' => $prod->id,
                    'display_order' => $order++,
                    'step_name_ar' => $stepNameAr,
                    'step_name_en' => $stepNameEn,
                    'morning' => true,
                    'night' => true,
                    'use_time_ar' => 'صباحاً ومساءً',
                ]);
            }
        }

        $this->info('Successfully generated Men Simple Preset Routines in database!');
        return 0;
    }
}
