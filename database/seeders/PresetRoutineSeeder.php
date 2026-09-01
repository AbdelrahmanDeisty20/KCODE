<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\PresetRoutine;
use App\Models\PresetRoutineProduct;
use App\Models\Product;

class PresetRoutineSeeder extends Seeder
{
    /**
     * Seed the 4 official KCODE Carefully Selected Preset Routines from Homepage V24 Design.
     */
    public function run(): void
    {
        // Remove existing non-men preset routines
        PresetRoutine::forWomen()->delete();

        $routinesConfig = [
            [
                'title_ar' => 'الحبوب والبثور وآثار الحبوب',
                'title_en' => 'Acne, Blemishes & Post-Acne Marks',
                'description_ar' => 'تنظيف لطيف، وعلاج يومي للحبوب وآثارها ومقشر أسبوعي، مع حماية يومية من الشمس تمنع تعلق الآثار.',
                'description_en' => 'Gentle cleansing, daily acne treatment, weekly exfoliant, and daily SPF protection preventing dark spots.',
                'badge_ar' => 'مختار وفق مراجعة KCODE للمركبات',
                'badge_en' => 'Selected by KCODE Formulation Review',
                'skin_type_ar' => 'مناسب لـ: الدهنية والمختلطة المعرضة للحبوب',
                'skin_type_en' => 'Suitable for: Oily & Combination Acne-Prone Skin',
                'goal_ar' => 'عناية بالحبوب والآثار',
                'goal_en' => 'Acne & Blemish Care',
                'skin_type_id' => 1,
                'goal_id' => 2,
                'steps' => [
                    ['ar' => 'غسول تنظيف', 'en' => 'Cleanser'],
                    ['ar' => 'علاج الحبوب والآثار', 'en' => 'Acne & Blemish Treatment'],
                    ['ar' => 'مرطب خفيف خالي من الزيوت', 'en' => 'Oil-Free Lightweight Moisturizer'],
                    ['ar' => 'واقي شمس مهدئ', 'en' => 'Soothing Sunscreen'],
                ],
            ],
            [
                'title_ar' => 'المسام والرؤوس السوداء واللمعان الزائد',
                'title_en' => 'Pores, Blackheads & Excess Shine',
                'description_ar' => 'نياسيناميد مركز للمسام واللمعان مع مقشر BHA للرؤوس السوداء ومقشر خفيف لا يثقل البشرة.',
                'description_en' => 'Concentrated Niacinamide for pores and shine, BHA exfoliant for blackheads, and lightweight non-clogging formula.',
                'badge_ar' => 'مختار وفق مراجعة KCODE للمركبات',
                'badge_en' => 'Selected by KCODE Formulation Review',
                'skin_type_ar' => 'مناسب لـ: الدهنية والمختلطة',
                'skin_type_en' => 'Suitable for: Oily & Combination Skin',
                'goal_ar' => 'تنقية المسام واللمعان',
                'goal_en' => 'Pore Refining & Oil Control',
                'skin_type_id' => 1,
                'goal_id' => 3,
                'steps' => [
                    ['ar' => 'غسول المسام العميقة', 'en' => 'Deep Pore Cleanser'],
                    ['ar' => 'سيروم نياسيناميد / BHA', 'en' => 'Niacinamide / BHA Serum'],
                    ['ar' => 'مرطب موازن للدهون', 'en' => 'Balancing Gel Moisturizer'],
                    ['ar' => 'واقي شمس مطفي', 'en' => 'Matte Sunscreen'],
                ],
            ],
            [
                'title_ar' => 'العناية بمظهر الكلف والتصبغات وتفاوت اللون',
                'title_en' => 'Hyperpigmentation, Melasma & Uneven Tone',
                'description_ar' => 'سيروم صباحي للتصبغات وأكثر من ريتينول مع مرطب داعم للحاجز وواقي شمس يومي لا غنى عنه.',
                'description_en' => 'Morning brightening serum, advanced Retinol treatment, barrier-supporting moisturizer, and essential daily SPF.',
                'badge_ar' => 'مختار وفق مراجعة KCODE للمركبات',
                'badge_en' => 'Selected by KCODE Formulation Review',
                'skin_type_ar' => 'مناسب لـ: الدهنية والمختلطة والعادية والجافة غير الحساسة',
                'skin_type_en' => 'Suitable for: Oily, Combination, Normal & Non-Sensitive Dry Skin',
                'goal_ar' => 'تفتيح وتوحيد اللون',
                'goal_en' => 'Brightening & Evening Tone',
                'skin_type_id' => 3,
                'goal_id' => 4,
                'steps' => [
                    ['ar' => 'غسول منظم للتفتيح', 'en' => 'Brightening Cleanser'],
                    ['ar' => 'سيروم فيتامين سي للتصبغات', 'en' => 'Vitamin C Serum for Pigmentation'],
                    ['ar' => 'مرطب داعم لحاجز البشرة', 'en' => 'Barrier Support Cream'],
                    ['ar' => 'واقي شمس حماية عالية', 'en' => 'High Protection Sunscreen'],
                ],
            ],
            [
                'title_ar' => 'البشرة الجافة والباهتة للترطيب والإشراقة',
                'title_en' => 'Dry & Dull Skin Hydration & Radiance',
                'description_ar' => 'طبقات ترطيب متدرجة من التونر إلى السيروم إلى الكريم، مع واقي شمس مريح للبشرة الجافة.',
                'description_en' => 'Layered hydration from toner to serum to cream, paired with a comfortable hydrating SPF.',
                'badge_ar' => 'مختار وفق مراجعة KCODE للمركبات',
                'badge_en' => 'Selected by KCODE Formulation Review',
                'skin_type_ar' => 'مناسب لـ: الجافة والعادية المائلة إلى الجفاف',
                'skin_type_en' => 'Suitable for: Dry & Normal to Dry Skin',
                'goal_ar' => 'ترطيب إضافي وإشراقة',
                'goal_en' => 'Deep Hydration & Radiance',
                'skin_type_id' => 2,
                'goal_id' => 5,
                'steps' => [
                    ['ar' => 'غسول مرطب لطيف', 'en' => 'Gentle Hydrating Cleanser'],
                    ['ar' => 'تونر / سيروم ترطيب مكثف', 'en' => 'Intense Hydrating Toner / Serum'],
                    ['ar' => 'كريم ترطيب عميق', 'en' => 'Rich Hydrating Cream'],
                    ['ar' => 'واقي شمس مغذي', 'en' => 'Nourishing Sunscreen'],
                ],
            ],
        ];

        foreach ($routinesConfig as $config) {
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
                'is_for_men' => false,
                'status' => 'active',
            ]);

            // Pick suitable products for each step
            $products = Product::where('stock', '>', 0)
                ->inRandomOrder()
                ->take(count($config['steps']))
                ->get();

            if ($products->isEmpty()) {
                $products = Product::inRandomOrder()->take(count($config['steps']))->get();
            }

            $order = 1;
            foreach ($config['steps'] as $idx => $stepInfo) {
                $prod = $products->get($idx) ?? $products->first();
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

