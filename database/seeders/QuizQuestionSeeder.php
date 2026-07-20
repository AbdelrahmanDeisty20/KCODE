<?php

namespace Database\Seeders;

use App\Models\QuizQuestion;
use App\Models\QuizOption;
use App\Models\RoutineGoal;
use App\Models\SkinType;
use App\Models\Concern;
use Illuminate\Database\Seeder;

class QuizQuestionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. Clear existing questions and options
        \Illuminate\Support\Facades\Schema::disableForeignKeyConstraints();
        QuizOption::truncate();
        QuizQuestion::truncate();
        \Illuminate\Support\Facades\Schema::enableForeignKeyConstraints();

        // 2. Question 1: Goal Selection
        $q1 = QuizQuestion::create([
            'title_ar' => 'روتينات KCODE حسب هدف بشرتك',
            'title_en' => 'KCODE routines based on your skin goal',
            'description_ar' => 'اختاري هدفك.. نختار لك المكونات المناسبة لروتين فعال ونتائج حقيقية.',
            'description_en' => 'Choose your goal.. we choose the appropriate ingredients for an effective routine and real results.',
            'selection_type' => 'single',
            'step_number' => 1,
            'is_optional' => false,
        ]);

        $hydrationGoal = RoutineGoal::where('name_en', 'Hydration & Protection')->first();
        $brighteningGoal = RoutineGoal::where('name_en', 'Brightening & Evening Tone')->first();
        $poresGoal = RoutineGoal::where('name_en', 'Pore Care')->first();
        $acneGoal = RoutineGoal::where('name_en', 'Acne & Scars')->first();
        $radianceGoal = RoutineGoal::where('name_en', 'Radiance & Freshness')->first();

        QuizOption::create([
            'quiz_question_id' => $q1->id,
            'title_ar' => 'ترطيب وحماية',
            'title_en' => 'Hydration & Protection',
            'description_ar' => 'لالبشرة الجافة يحافظ على الترطيب ويعزز حاجز البشرة الطبيعي',
            'description_en' => 'For dry skin, maintains hydration and boosts the natural skin barrier',
            'image' => 'hydration.png',
            'option_type' => 'goal',
            'mapped_id' => $hydrationGoal ? $hydrationGoal->id : null,
        ]);

        QuizOption::create([
            'quiz_question_id' => $q1->id,
            'title_ar' => 'تفتيح وتوحيد',
            'title_en' => 'Brightening & Evening Tone',
            'description_ar' => 'لالبشرة غير المتجانسة يوحد اللون ويمنح إشراقة',
            'description_en' => 'For uneven skin tone, evens color and boosts radiance',
            'image' => 'brightening.png',
            'option_type' => 'goal',
            'mapped_id' => $brighteningGoal ? $brighteningGoal->id : null,
        ]);

        QuizOption::create([
            'quiz_question_id' => $q1->id,
            'title_ar' => 'العناية بالمسام',
            'title_en' => 'Pore Care',
            'description_ar' => 'ينظف المسام ويقلل مظهرها ويحسن ملمس البشرة تدريجياً',
            'description_en' => 'Clears pores, reduces their appearance, and gradually refines texture',
            'image' => 'pores.png',
            'option_type' => 'goal',
            'mapped_id' => $poresGoal ? $poresGoal->id : null,
        ]);

        QuizOption::create([
            'quiz_question_id' => $q1->id,
            'title_ar' => 'حبوب وآثارها',
            'title_en' => 'Acne & Scars',
            'description_ar' => 'يساعد على تهدئة الحبوب وتقليل الاحمرار وآثارها',
            'description_en' => 'Helps calm breakouts, reduce redness and acne marks',
            'image' => 'acne.png',
            'option_type' => 'goal',
            'mapped_id' => $acneGoal ? $acneGoal->id : null,
        ]);

        QuizOption::create([
            'quiz_question_id' => $q1->id,
            'title_ar' => 'إشراقة ونضارة',
            'title_en' => 'Radiance & Freshness',
            'description_ar' => 'للبشرة الباهتة يعزز الإشراقة ويمنح نضارة صحية طبيعية',
            'description_en' => 'For dull skin, enhances glow and gives a healthy natural freshness',
            'image' => 'radiance.png',
            'option_type' => 'goal',
            'mapped_id' => $radianceGoal ? $radianceGoal->id : null,
        ]);


        // 3. Question 2: Skin Type Selection
        $q2 = QuizQuestion::create([
            'title_ar' => 'ما نوع بشرتك؟',
            'title_en' => 'What is your skin type?',
            'description_ar' => 'اختاري نوع بشرتك.. نختار لك الروتين الأنسب لك.',
            'description_en' => 'Choose your skin type.. we choose the most suitable routine for you.',
            'selection_type' => 'single',
            'step_number' => 2,
            'is_optional' => false,
        ]);

        $oilyType = SkinType::where('name_en', 'Oily')->first();
        $dryType = SkinType::where('name_en', 'Dry')->first();
        $combinationType = SkinType::where('name_en', 'Combination')->first();
        $sensitiveType = SkinType::where('name_en', 'Sensitive')->first();

        QuizOption::create([
            'quiz_question_id' => $q2->id,
            'title_ar' => 'دهنية',
            'title_en' => 'Oily',
            'image' => 'oily_skin.png',
            'option_type' => 'skin_type',
            'mapped_id' => $oilyType ? $oilyType->id : null,
        ]);

        QuizOption::create([
            'quiz_question_id' => $q2->id,
            'title_ar' => 'جافة',
            'title_en' => 'Dry',
            'image' => 'dry_skin.png',
            'option_type' => 'skin_type',
            'mapped_id' => $dryType ? $dryType->id : null,
        ]);

        QuizOption::create([
            'quiz_question_id' => $q2->id,
            'title_ar' => 'مختلطة',
            'title_en' => 'Combination',
            'image' => 'combination_skin.png',
            'option_type' => 'skin_type',
            'mapped_id' => $combinationType ? $combinationType->id : null,
        ]);

        QuizOption::create([
            'quiz_question_id' => $q2->id,
            'title_ar' => 'حساسة',
            'title_en' => 'Sensitive',
            'image' => 'sensitive_skin.png',
            'option_type' => 'skin_type',
            'mapped_id' => $sensitiveType ? $sensitiveType->id : null,
        ]);


        // 4. Question 3: Additional Concerns Selection
        $q3 = QuizQuestion::create([
            'title_ar' => 'هل ترغبين بمعالجة مشكلة إضافية؟',
            'title_en' => 'Would you like to treat an additional concern?',
            'description_ar' => '(اختياري) يمكنك اختيار أكثر من مشكلة.',
            'description_en' => '(Optional) You can select more than one concern.',
            'selection_type' => 'multiple',
            'step_number' => 3,
            'is_optional' => true,
        ]);

        // None option
        QuizOption::create([
            'quiz_question_id' => $q3->id,
            'title_ar' => 'لا توجد مشكلة',
            'title_en' => 'No concern',
            'image' => 'no_concern.png',
            'option_type' => 'none',
            'mapped_id' => null,
        ]);

        $concerns = [
            'Acne & Blemishes' => 'الحبوب والشوائب',
            'Pores & Blackheads' => 'المسام الرؤوس السوداء',
            'Pigmentation & Dark Spots' => 'التصبغات والبقع الداكنة',
            'Redness & Irritation' => 'الاحمرار والتهيج',
            'Skin Barrier' => 'حاجز البشرة',
            'Wrinkles & Fine Lines' => 'التجاعيد والخطوط التعبيرية',
            'Dryness & Hydration' => 'الجفاف والترطيب',
        ];

        foreach ($concerns as $en => $ar) {
            $concernObj = Concern::where('name_en', $en)->first();
            QuizOption::create([
                'quiz_question_id' => $q3->id,
                'title_ar' => $ar,
                'title_en' => $en,
                'image' => strtolower(str_replace(' ', '_', $en)) . '.png',
                'option_type' => 'concern',
                'mapped_id' => $concernObj ? $concernObj->id : null,
            ]);
        }
    }
}
