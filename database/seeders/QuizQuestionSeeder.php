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

        $goals = [
            [
                'name_en' => 'Hydration & Protection',
                'title_ar' => 'ترطيب وحماية',
                'title_en' => 'Hydration & Protection',
                'desc_ar' => 'للبشرة الجافة يحافظ على الترطيب ويعزز حاجز البشرة الطبيعي',
                'desc_en' => 'For dry skin, maintains hydration and boosts the natural skin barrier',
                'image_name' => 'hydration.png',
                'url' => 'https://images.unsplash.com/photo-1601049676099-e7ed07d825b0?auto=format&fit=crop&q=80&w=400',
            ],
            [
                'name_en' => 'Brightening & Evening Tone',
                'title_ar' => 'تفتيح وتوحيد',
                'title_en' => 'Brightening & Evening Tone',
                'desc_ar' => 'للبشرة غير المتجانسة يوحد اللون ويمنح إشراقة',
                'desc_en' => 'For uneven skin tone, evens color and boosts radiance',
                'image_name' => 'brightening.png',
                'url' => 'https://images.unsplash.com/photo-1596755094514-f87e34085b2c?auto=format&fit=crop&q=80&w=400',
            ],
            [
                'name_en' => 'Pore Care',
                'title_ar' => 'العناية بالمسام',
                'title_en' => 'Pore Care',
                'desc_ar' => 'ينظف المسام ويقلل مظهرها ويحسن ملمس البشرة تدريجياً',
                'desc_en' => 'Clears pores, reduces their appearance, and gradually refines texture',
                'image_name' => 'pores.png',
                'url' => 'https://images.unsplash.com/photo-1522337360788-8b13dee7a37e?auto=format&fit=crop&q=80&w=400',
            ],
            [
                'name_en' => 'Acne & Scars',
                'title_ar' => 'حبوب وآثارها',
                'title_en' => 'Acne & Scars',
                'desc_ar' => 'يساعد على تهدئة الحبوب وتقليل الاحمرار وآثارها',
                'desc_en' => 'Helps calm breakouts, reduce redness and acne marks',
                'image_name' => 'acne.png',
                'url' => 'https://images.unsplash.com/photo-1556228720-195a672e8a03?auto=format&fit=crop&q=80&w=400',
            ],
            [
                'name_en' => 'Radiance & Freshness',
                'title_ar' => 'إشراقة ونضارة',
                'title_en' => 'Radiance & Freshness',
                'desc_ar' => 'للبشرة الباهتة يعزز الإشراقة ويمنح نضارة صحية طبيعية',
                'desc_en' => 'For dull skin, enhances glow and gives a healthy natural freshness',
                'image_name' => 'radiance.png',
                'url' => 'https://images.unsplash.com/photo-1620916566398-39f1143ab7be?auto=format&fit=crop&q=80&w=400',
            ],
        ];

        foreach ($goals as $goalInfo) {
            $mappedObj = RoutineGoal::where('name_en', $goalInfo['name_en'])->first();
            ImageDownloader::downloadAndSave($goalInfo['url'], 'quiz', $goalInfo['image_name']);

            QuizOption::create([
                'quiz_question_id' => $q1->id,
                'title_ar' => $goalInfo['title_ar'],
                'title_en' => $goalInfo['title_en'],
                'description_ar' => $goalInfo['desc_ar'],
                'description_en' => $goalInfo['desc_en'],
                'image' => $goalInfo['image_name'],
                'option_type' => 'goal',
                'mapped_id' => $mappedObj ? $mappedObj->id : null,
            ]);
        }

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

        $skinTypes = [
            [
                'name_en' => 'Oily',
                'title_ar' => 'دهنية',
                'desc_ar' => 'تتميز بإفراز زائد للزيوت ولمعان مستمر مع قابلية للمسام الواسعة والحبوب',
                'desc_en' => 'Characterized by excess oil production, shine, and prone to enlarged pores and acne',
                'image_name' => 'oily_skin.png',
                'url' => 'https://images.unsplash.com/photo-1556228720-195a672e8a03?auto=format&fit=crop&q=80&w=400',
            ],
            [
                'name_en' => 'Dry',
                'title_ar' => 'جافة',
                'desc_ar' => 'تعاني من نقص الترطيب والملمس المشدود أو التقشر وتتطلب ترطيبًا عميقًا مكثفًا',
                'desc_en' => 'Lacks moisture, feels tight or flaky, requiring intensive deep hydration',
                'image_name' => 'dry_skin.png',
                'url' => 'https://images.unsplash.com/photo-1601049676099-e7ed07d825b0?auto=format&fit=crop&q=80&w=400',
            ],
            [
                'name_en' => 'Combination',
                'title_ar' => 'مختلطة',
                'desc_ar' => 'تجمع بين منطقة T دهنية (الجبهة والأنف والذقن) وجفاف أو توازن في باقي الوجه',
                'desc_en' => 'Combines an oily T-zone (forehead, nose, chin) with dry or normal cheeks',
                'image_name' => 'combination_skin.png',
                'url' => 'https://images.unsplash.com/photo-1522337360788-8b13dee7a37e?auto=format&fit=crop&q=80&w=400',
            ],
            [
                'name_en' => 'Sensitive',
                'title_ar' => 'حساسة',
                'desc_ar' => 'تتأثر بسهولة بالعوامل الخارجية وتظهر احمرارًا أو حكة وتتطلب منتجات لطيفة مهدئة',
                'desc_en' => 'Easily affected by external factors, showing redness or irritation, needs gentle soothing care',
                'image_name' => 'sensitive_skin.png',
                'url' => 'https://images.unsplash.com/photo-1596755094514-f87e34085b2c?auto=format&fit=crop&q=80&w=400',
            ],
        ];

        foreach ($skinTypes as $stInfo) {
            $mappedObj = SkinType::where('name_en', $stInfo['name_en'])->first();
            ImageDownloader::downloadAndSave($stInfo['url'], 'quiz', $stInfo['image_name']);

            QuizOption::create([
                'quiz_question_id' => $q2->id,
                'title_ar' => $stInfo['title_ar'],
                'title_en' => $stInfo['name_en'],
                'description_ar' => $stInfo['desc_ar'],
                'description_en' => $stInfo['desc_en'],
                'image' => $stInfo['image_name'],
                'option_type' => 'skin_type',
                'mapped_id' => $mappedObj ? $mappedObj->id : null,
            ]);
        }

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
        ImageDownloader::downloadAndSave(
            'https://images.unsplash.com/photo-1620916566398-39f1143ab7be?auto=format&fit=crop&q=80&w=400',
            'quiz',
            'no_concern.png'
        );
        QuizOption::create([
            'quiz_question_id' => $q3->id,
            'title_ar' => 'لا توجد مشكلة',
            'title_en' => 'No concern',
            'description_ar' => 'لا أعاني من مشاكل إضافية محددة وأرغب في روتين عناية يومي أساسي',
            'description_en' => 'I have no specific additional concerns and prefer a basic daily routine',
            'image' => 'no_concern.png',
            'option_type' => 'none',
            'mapped_id' => null,
        ]);

        $concerns = [
            'Acne & Blemishes' => [
                'ar' => 'الحبوب والشوائب',
                'desc_ar' => 'الحد من انتشار الحبوب وتنقية الشوائب وتصفية البشرة',
                'desc_en' => 'Reduce acne breakouts, clear impurities, and refine skin texture',
                'url' => 'https://images.unsplash.com/photo-1556228720-195a672e8a03?auto=format&fit=crop&q=80&w=400',
            ],
            'Pores & Blackheads' => [
                'ar' => 'المسام الرؤوس السوداء',
                'desc_ar' => 'التخلص من الرؤوس السوداء وتنظيف المسام المنسدة العميقة',
                'desc_en' => 'Eliminate blackheads and deeply cleanse clogged pores',
                'url' => 'https://images.unsplash.com/photo-1522337360788-8b13dee7a37e?auto=format&fit=crop&q=80&w=400',
            ],
            'Pigmentation & Dark Spots' => [
                'ar' => 'التصبغات والبقع الداكنة',
                'desc_ar' => 'علاج آثار الشمس وتفتيح البقع الداكنة والتصبغات القديمة',
                'desc_en' => 'Treat sun spots, fade dark marks and old pigmentation',
                'url' => 'https://images.unsplash.com/photo-1596755094514-f87e34085b2c?auto=format&fit=crop&q=80&w=400',
            ],
            'Redness & Irritation' => [
                'ar' => 'الاحمرار والتهيج',
                'desc_ar' => 'تهدئة البشرة المتهيجة والحساسة وتقليل الاحمرار المفاجئ',
                'desc_en' => 'Calm irritated, reactive skin and reduce sudden redness',
                'url' => 'https://images.unsplash.com/photo-1596755094514-f87e34085b2c?auto=format&fit=crop&q=80&w=400',
            ],
            'Skin Barrier' => [
                'ar' => 'حاجز البشرة',
                'desc_ar' => 'تقوية وتدعيم حاجز البشرة المتضرر وحمايته من الجفاف والعوامل البيئية',
                'desc_en' => 'Strengthen and restore damaged skin barrier against dehydration and environmental stress',
                'url' => 'https://images.unsplash.com/photo-1601049676099-e7ed07d825b0?auto=format&fit=crop&q=80&w=400',
            ],
            'Wrinkles & Fine Lines' => [
                'ar' => 'التجاعيد والخطوط التعبيرية',
                'desc_ar' => 'تحسين مرونة البشرة وتقليل ظهور الخطوط الدقيقة والتجاعيد المبكرة',
                'desc_en' => 'Improve skin elasticity and minimize fine lines and early wrinkles',
                'url' => 'https://images.unsplash.com/photo-1620916566398-39f1143ab7be?auto=format&fit=crop&q=80&w=400',
            ],
            'Dryness & Hydration' => [
                'ar' => 'الجفاف والترطيب',
                'desc_ar' => 'علاج الجفاف الشديد ومنح البشرة ترطيبًا مضاعفًا وطويل الأمد',
                'desc_en' => 'Relieve severe dryness and provide long-lasting intense hydration',
                'url' => 'https://images.unsplash.com/photo-1601049676099-e7ed07d825b0?auto=format&fit=crop&q=80&w=400',
            ],
        ];

        foreach ($concerns as $en => $info) {
            $concernObj = Concern::where('name_en', $en)->first();
            $imageName = strtolower(str_replace(' ', '_', $en)) . '.png';
            ImageDownloader::downloadAndSave($info['url'], 'quiz', $imageName);

            QuizOption::create([
                'quiz_question_id' => $q3->id,
                'title_ar' => $info['ar'],
                'title_en' => $en,
                'description_ar' => $info['desc_ar'],
                'description_en' => $info['desc_en'],
                'image' => $imageName,
                'option_type' => 'concern',
                'mapped_id' => $concernObj ? $concernObj->id : null,
            ]);
        }
    }
}
