<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\PresetRoutine;
use App\Models\PresetRoutineProduct;
use App\Models\Product;
use Illuminate\Support\Facades\Schema;

class GeneratePresetRoutinesCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'routines:generate-preset';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Deletes old preset routines and generates 4 new random preset routines in the database';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Generating 4 new random preset routines...');

        // Clear existing preset routines from DB
        Schema::disableForeignKeyConstraints();
        PresetRoutineProduct::truncate();
        PresetRoutine::truncate();
        Schema::enableForeignKeyConstraints();

        $allPresetsPool = [
            [
                'title_ar' => 'روتين النضارة والتفتيح المضاعف',
                'title_en' => 'Ultimate Radiance & Brightening Routine',
                'description_ar' => 'روتين كوري متكامل يركز على تفتيح البقع الداكنة وتوحيد لون البشرة وإعطاء إشراقة زجاجية فورية.',
                'description_en' => 'A comprehensive Korean routine focused on fading dark spots, evening skin tone, and delivering a glass-skin glow.',
                'skin_type_ar' => 'جميع أنواع البشرة',
                'skin_type_en' => 'All Skin Types',
                'goal_ar' => 'تفتيح وإشراقة',
                'goal_en' => 'Brightening & Radiance',
                'badge_ar' => 'الأكثر مبيعاً ⭐',
                'badge_en' => 'Best Seller ⭐',
                'skin_type_id' => 3,
                'goal_id' => 2,
            ],
            [
                'title_ar' => 'روتين تنقية المسام والسيطرة على الحبوب',
                'title_en' => 'Pore Purifying & Acne Control Routine',
                'description_ar' => 'مخصص للبشرة الدهنية والمختلطة لتنظيف المسام العميقة، السيطرة على الإفرازات الزيتية وتهدئة الحبوب.',
                'description_en' => 'Specially designed for oily and combination skin to deeply clear pores, control sebum, and soothe breakouts.',
                'skin_type_ar' => 'البشرة الدهنية والمختلطة',
                'skin_type_en' => 'Oily & Combination Skin',
                'goal_ar' => 'عناية بالمسام والحبوب',
                'goal_en' => 'Pore & Acne Care',
                'badge_ar' => 'موصى به صيدلانياً 🌿',
                'badge_en' => 'Dermatologist Recommended 🌿',
                'skin_type_id' => 1,
                'goal_id' => 4,
            ],
            [
                'title_ar' => 'روتين الترميم الفائق وتدعيم حاجز البشرة',
                'title_en' => 'Barrier Repair & Intense Moisture Routine',
                'description_ar' => 'تركيبة غنية بالسيراميد والبانثينول لإصلاح حاجز البشرة المتضرر والحد من التقشر والجفاف الشديد.',
                'description_en' => 'Enriched with Ceramides and Panthenol to restore damaged skin barrier and relieve intense dryness.',
                'skin_type_ar' => 'البشرة الجافة والحساسة',
                'skin_type_en' => 'Dry & Sensitive Skin',
                'goal_ar' => 'ترميم وترطيب عميق',
                'goal_en' => 'Barrier Repair & Hydration',
                'badge_ar' => 'ترطيب مكثف 💧',
                'badge_en' => 'Intense Hydration 💧',
                'skin_type_id' => 2,
                'goal_id' => 1,
            ],
            [
                'title_ar' => 'روتين الشباب النضر والكولاجين (Glass Skin)',
                'title_en' => 'Glass Skin & Youth Boosting Routine',
                'description_ar' => 'روتين يعتمد على الببتيدات والكولاجين لمنح البشرة ملمساً حريرياً ناعماً ونضارة شبابية دائمة.',
                'description_en' => 'Infused with Peptides and Collagen to firm the skin, refine texture, and achieve everlasting youthfulness.',
                'skin_type_ar' => 'البشرة العادية والمختلطة',
                'skin_type_en' => 'Normal & Combination Skin',
                'goal_ar' => 'نضارة ومقاومة التجاعيد',
                'goal_en' => 'Youth & Radiance',
                'badge_ar' => 'روتين مميز ✨',
                'badge_en' => 'Featured Routine ✨',
                'skin_type_id' => 3,
                'goal_id' => 5,
            ],
            [
                'title_ar' => 'روتين التهدئة الفورية ومقاومة التهيّج',
                'title_en' => 'Instant Soothing & Anti-Redness Routine',
                'description_ar' => 'تركيبة مهدئة مستخلصة من السينتيلا وشجرة الشاي لتخفيف احمرار وحساسية البشرة.',
                'description_en' => 'Soothing formula extracted from Centella and Tea Tree to relieve skin redness and reactivity.',
                'skin_type_ar' => 'البشرة الحساسة والمتهيجة',
                'skin_type_en' => 'Sensitive & Irritated Skin',
                'goal_ar' => 'تهدئة الاحمرار',
                'goal_en' => 'Soothing & Anti-Redness',
                'badge_ar' => 'تهدئة فائقة 🌿',
                'badge_en' => 'Instant Relief 🌿',
                'skin_type_id' => 4,
                'goal_id' => 1,
            ],
            [
                'title_ar' => 'روتين التوازن والترطيب للبشرة المختلطة',
                'title_en' => 'Combination Skin Balance Routine',
                'description_ar' => 'يوازن الإفرازات الزيتية في منطقة T مع توفير الترطيب الكافي لباقي أجزاء الوجه.',
                'description_en' => 'Balances T-zone oiliness while providing optimal hydration for drier facial zones.',
                'skin_type_ar' => 'البشرة المختلطة',
                'skin_type_en' => 'Combination Skin',
                'goal_ar' => 'توازن وترطيب',
                'goal_en' => 'Balance & Moisture',
                'badge_ar' => 'توازن مثالي ⚖️',
                'badge_en' => 'Perfect Balance ⚖️',
                'skin_type_id' => 3,
                'goal_id' => 1,
            ],
            [
                'title_ar' => 'روتين النضارة السريعة وتجديد البشرة الباهتة',
                'title_en' => 'Instant Glow & Revitalizing Routine',
                'description_ar' => 'يعيد الحيوية للبشرة المجهدة والباهتة بتركيبات غنية بالفيتامينات والتوت الكوري.',
                'description_en' => 'Restores radiance to tired, dull skin with vitamin-rich Berry Korean formulations.',
                'skin_type_ar' => 'جميع أنواع البشرة',
                'skin_type_en' => 'All Skin Types',
                'goal_ar' => 'إشراقة ونضارة',
                'goal_en' => 'Glow & Vitality',
                'badge_ar' => 'نضارة فورية 🌟',
                'badge_en' => 'Instant Glow 🌟',
                'skin_type_id' => 4,
                'goal_id' => 5,
            ],
            [
                'title_ar' => 'روتين التنقية العميقة وتصغير المسام',
                'title_en' => 'Deep Pore Cleansing & Tightening Routine',
                'description_ar' => 'روتين يستهدف تنظيف المسام من الرؤوس السوداء والترسبات مع تحسين ملمس البشرة.',
                'description_en' => 'Deeply cleanses pores from blackheads and impurities while refining skin texture.',
                'skin_type_ar' => 'البشرة الدهنية والمختلطة',
                'skin_type_en' => 'Oily & Combination Skin',
                'goal_ar' => 'عناية بالمسام',
                'goal_en' => 'Pore Minimizing',
                'badge_ar' => 'مسام نقية ✨',
                'badge_en' => 'Pure Pores ✨',
                'skin_type_id' => 1,
                'goal_id' => 3,
            ],
        ];

        // Randomly select exactly 4 routines from the pool
        $selectedPresets = collect($allPresetsPool)->shuffle()->take(4)->values()->all();

        foreach ($selectedPresets as $config) {
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
                'status' => 'active',
            ]);

            // Pick 5 random active best-selling products for this routine
            $products = Product::where('stock', '>', 0)
                ->bestSeller()
                ->whereHas('skinTypes', function ($q) use ($config) {
                    $q->where('skin_type_id', $config['skin_type_id']);
                })
                ->inRandomOrder()
                ->take(5)
                ->get();

            if ($products->count() < 4) {
                $products = Product::where('stock', '>', 0)
                    ->inRandomOrder()
                    ->take(5)
                    ->get();
            }

            $order = 1;
            foreach ($products as $prod) {
                $routineInfo = $prod->routines->first() ?? ($prod->routineSteps ?? collect())->first();

                PresetRoutineProduct::create([
                    'preset_routine_id' => $presetRoutine->id,
                    'product_id' => $prod->id,
                    'display_order' => $order++,
                    'step_name_ar' => $routineInfo ? ($routineInfo->name_ar ?? $routineInfo->name) : "الخطوة {$order}",
                    'step_name_en' => $routineInfo ? ($routineInfo->name_en ?? $routineInfo->name) : "Step {$order}",
                    'morning' => $routineInfo ? (bool)($routineInfo->morning ?? true) : true,
                    'night' => $routineInfo ? (bool)($routineInfo->night ?? true) : true,
                    'use_time_ar' => 'صباحاً ومساءً',
                ]);
            }

            $this->info("Created routine: {$presetRoutine->title_ar} with {$products->count()} products.");
        }

        $this->info('Successfully generated 4 fresh preset routines in the database!');
        return 0;
    }
}
