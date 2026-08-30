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
    protected $description = 'Deletes old preset routines and generates 16 new preset routines in the database';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Generating and appending new preset routines in database...');

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
                'goal_id' => 4,
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
                'goal_id' => 2,
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
                'goal_id' => 5,
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
                'goal_id' => 1,
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
                'goal_id' => 5,
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
                'goal_id' => 5,
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
                'goal_id' => 1,
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
            [
                'title_ar' => 'روتين مقاومة التجاعيد وشد البشرة الفائق',
                'title_en' => 'Anti-Aging & Firming Care Routine',
                'description_ar' => 'يعتمد على الببتيدات ومستخلص الأرز لشد البشرة وتحفيز إنتاج الكولاجين لمقاومة التجاعيد.',
                'description_en' => 'Infused with Peptides and Rice extracts to firm skin and stimulate collagen for anti-aging.',
                'skin_type_ar' => 'جميع أنواع البشرة',
                'skin_type_en' => 'All Skin Types',
                'goal_ar' => 'مقاومة التجاعيد',
                'goal_en' => 'Firming & Anti-Aging',
                'badge_ar' => 'شد ونضارة 💎',
                'badge_en' => 'Firming & Elasticity 💎',
                'skin_type_id' => 3,
                'goal_id' => 1,
            ],
            [
                'title_ar' => 'روتين العناية بالبشرة الجافة وتغذيتها',
                'title_en' => 'Dry Skin Deep Nourishing Routine',
                'description_ar' => 'ترطيب مكثف بالزيوت الخفيفة والهيالورونيك للتخلص من الجفاف والانكماش.',
                'description_en' => 'Deep nourishment with hyaluronic acid and mild oils to eliminate dryness and flakiness.',
                'skin_type_ar' => 'البشرة الجافة',
                'skin_type_en' => 'Dry Skin',
                'goal_ar' => 'ترطيب وتغذية',
                'goal_en' => 'Hydration & Moisture',
                'badge_ar' => 'غني بالترطيب 💦',
                'badge_en' => 'Rich Moisture 💦',
                'skin_type_id' => 2,
                'goal_id' => 5,
            ],
            [
                'title_ar' => 'روتين الحماية من الشمس والوقاية اليومية',
                'title_en' => 'Daily Sun Protection & Defense Routine',
                'description_ar' => 'حماية متكاملة من أشعة الشمس مع ترطيب خفيف وتفتيح للبشرة المعرضة للشمس.',
                'description_en' => 'Comprehensive sun protection with lightweight hydration and brightening for sun-exposed skin.',
                'skin_type_ar' => 'جميع أنواع البشرة',
                'skin_type_en' => 'All Skin Types',
                'goal_ar' => 'وقاية وحماية',
                'goal_en' => 'Sun Protection & Shield',
                'badge_ar' => 'حماية قصوى ☀️',
                'badge_en' => 'Ultimate Shield ☀️',
                'skin_type_id' => 4,
                'goal_id' => 5,
            ],
            [
                'title_ar' => 'روتين الفيتامينات والسي بستر لإشراقة سريعة',
                'title_en' => 'Vitamin C Radiance & Booster Routine',
                'description_ar' => 'تركيبة غنية بالفيتامينات المركزة لتفتيح آثار الشمس والبهتان وتنشيط خلايا البشرة.',
                'description_en' => 'Concentrated vitamin C formulations to brighten sun spots, overcome dullness and boost vitality.',
                'skin_type_ar' => 'البشرة العادية والمختلطة',
                'skin_type_en' => 'Normal & Combination Skin',
                'goal_ar' => 'تفتيح ونضارة',
                'goal_en' => 'Vitamin Brightening',
                'badge_ar' => 'إشراقة الفيتامينات 🍊',
                'badge_en' => 'Vitamin C Power 🍊',
                'skin_type_id' => 3,
                'goal_id' => 4,
            ],
            [
                'title_ar' => 'روتين السيطرة على اللمعان والدهون الزائدة',
                'title_en' => 'Oil Control & Mattifying Routine',
                'description_ar' => 'يقلل الإفرازات الزيتية ويمنح ملمساً مطفياً خفيفاً بدون إنسداد المسام.',
                'description_en' => 'Controls excess sebum and leaves a light matte finish without clogging pores.',
                'skin_type_ar' => 'البشرة الدهنية',
                'skin_type_en' => 'Oily Skin',
                'goal_ar' => 'السيطرة على الدهون',
                'goal_en' => 'Seating & Shine Control',
                'badge_ar' => 'مظهر مطفي ✨',
                'badge_en' => 'Matte Finish ✨',
                'skin_type_id' => 1,
                'goal_id' => 3,
            ],
            [
                'title_ar' => 'روتين العناية الليلية المكثفة وتجديد الخلايا',
                'title_en' => 'Overnight Cellular Repair Routine',
                'description_ar' => 'يعمل أثناء النوم على إصلاح خلايا البشرة، تغذيتها، واستعادة التوازن الطبيعي.',
                'description_en' => 'Works overnight to repair skin cells, nourish deeply and restore natural balance.',
                'skin_type_ar' => 'جميع أنواع البشرة',
                'skin_type_en' => 'All Skin Types',
                'goal_ar' => 'ترميم ليلي',
                'goal_en' => 'Overnight Repair',
                'badge_ar' => 'ترميم ليلي 🌙',
                'badge_en' => 'Overnight Care 🌙',
                'skin_type_id' => 3,
                'goal_id' => 5,
            ],
            [
                'title_ar' => 'روتين العناية بمحيط العين وتقليل الهالات',
                'title_en' => 'Eye Contour & Dark Circle Care Routine',
                'description_ar' => 'يركز على تفتيح الهالات وتخفيف خطوط العين وانتفاخ الجفون.',
                'description_en' => 'Targeted care to brighten dark circles, smooth fine lines and reduce under-eye puffiness.',
                'skin_type_ar' => 'جميع أنواع البشرة',
                'skin_type_en' => 'All Skin Types',
                'goal_ar' => 'عناية بالعين',
                'goal_en' => 'Eye Care',
                'badge_ar' => 'عناية بالعين 👁️',
                'badge_en' => 'Eye Care Special 👁️',
                'skin_type_id' => 4,
                'goal_id' => 1,
            ],
            [
                'title_ar' => 'روتين السيراميد والسينتيلا للبشرة الحساسة جداً',
                'title_en' => 'Centella & Ceramide Sensitive Care Routine',
                'description_ar' => 'عناية فائقة للبشرة سريعة التفاعل بمركبات مهدئة 100% خالٍ من العطور.',
                'description_en' => 'Ultra-gentle care for highly reactive skin with 100% fragrance-free soothing actives.',
                'skin_type_ar' => 'البشرة الحساسة جداً',
                'skin_type_en' => 'Highly Sensitive Skin',
                'goal_ar' => 'تهدئة وترميم',
                'goal_en' => 'Sensitive Soothing',
                'badge_ar' => 'آمن ومريح 🛡️',
                'badge_en' => 'Ultra Safe 🛡️',
                'skin_type_id' => 4,
                'goal_id' => 5,
            ],
        ];

        $selectedPresets = collect($allPresetsPool)->shuffle()->take(5);

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
        }

        $this->info('Successfully generated 16 fresh preset routines in the database!');
        return 0;
    }
}
