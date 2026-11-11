<?php

namespace Database\Seeders;

use App\Models\Concern;
use App\Models\QuizOption;
use App\Models\QuizQuestion;
use App\Models\RoutineGoal;
use App\Models\SkinType;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Schema;

class QuizQuestionSeeder extends Seeder
{
    /**
     * Run the database seeds for the 4 Approved KCODE R479 Quiz Questions
     * (9 Primary Goals × 10 Secondary Concerns + None × 4 Skin Types × 2 Sensitivity = 728 Scenarios).
     */
    public function run(): void
    {
        Schema::disableForeignKeyConstraints();
        QuizOption::truncate();
        QuizQuestion::truncate();
        Schema::enableForeignKeyConstraints();

        // Ensure all 9 R479 Primary Goals exist in routine_goals for 1-to-1 mapped_id resolution
        $primaryGoalsCatalog = [
            'acne' => [
                'name_en' => 'Acne & Scars',
                'name_ar' => 'الحبوب والبثور',
                'title_ar' => 'الحبوب والبثور',
                'title_en' => 'Acne & Blemishes',
                'description_ar' => 'العناية بالحبوب والبثور وتقليل مظهرها والآثار المصاحبة لها',
                'description_en' => 'Target active breakouts, blemishes and associated marks',
                'image' => 'acne.png',
            ],
            'pores' => [
                'name_en' => 'Pore Care',
                'name_ar' => 'مظهر المسام',
                'title_ar' => 'مظهر المسام',
                'title_en' => 'Pore Appearance',
                'description_ar' => 'تحسين مظهر المسام الواسعة وتنعيم ملمس البشرة',
                'description_en' => 'Refine the appearance of enlarged pores and smooth skin texture',
                'image' => 'pores.png',
            ],
            'blackheads' => [
                'name_en' => 'Blackheads & Clogged Pores',
                'name_ar' => 'الرؤوس السوداء وانسداد المسام',
                'title_ar' => 'الرؤوس السوداء وانسداد المسام',
                'title_en' => 'Blackheads & Clogged Pores',
                'description_ar' => 'تقليل انسداد المسام والرؤوس السوداء وتنقية البشرة',
                'description_en' => 'Unclog pores and reduce blackheads with targeted care',
                'image' => 'blackheads.png',
            ],
            'dark_spots' => [
                'name_en' => 'Brightening & Evening Tone',
                'name_ar' => 'البقع الداكنة والتصبغات',
                'title_ar' => 'البقع الداكنة والتصبغات',
                'title_en' => 'Dark Spots & Pigmentation',
                'description_ar' => 'تفتيح البقع الداكنة والتصبغات وآثار الحبوب وتوحيد لون البشرة',
                'description_en' => 'Fade dark spots, post-acne pigmentation and uneven tone',
                'image' => 'brightening.png',
            ],
            'dullness' => [
                'name_en' => 'Radiance & Freshness',
                'name_ar' => 'البهتان',
                'title_ar' => 'البهتان',
                'title_en' => 'Dullness',
                'description_ar' => 'استعادة إشراقة البشرة ونضارتها وتقليل مظهر البهتان',
                'description_en' => 'Restore skin radiance, glow and vitality',
                'image' => 'radiance.png',
            ],
            'redness' => [
                'name_en' => 'Redness & Soothing',
                'name_ar' => 'الاحمرار',
                'title_ar' => 'الاحمرار',
                'title_en' => 'Redness',
                'description_ar' => 'تهدئة الاحمرار والتهيّج ودعم راحة البشرة',
                'description_en' => 'Calm visible redness, soothe irritation and comfort skin',
                'image' => 'redness.png',
            ],
            'dryness' => [
                'name_en' => 'Hydration & Protection',
                'name_ar' => 'الجفاف',
                'title_ar' => 'الجفاف',
                'title_en' => 'Dryness',
                'description_ar' => 'ترطيب البشرة بعمق ودعم حاجزها الواقي وتقليل الشعور بالشد',
                'description_en' => 'Deeply hydrate skin, support the moisture barrier and relieve tightness',
                'image' => 'hydration.png',
            ],
            'lines' => [
                'name_en' => 'Fine Lines & Wrinkles',
                'name_ar' => 'الخطوط الدقيقة والتجاعيد',
                'title_ar' => 'الخطوط الدقيقة والتجاعيد',
                'title_en' => 'Fine Lines & Wrinkles',
                'description_ar' => 'تحسين مظهر الخطوط الدقيقة والتجاعيد ودعم مرونة البشرة',
                'description_en' => 'Improve the look of fine lines and wrinkles and support elasticity',
                'image' => 'anti_aging.png',
            ],
            'maintain' => [
                'name_en' => 'Maintain Healthy Skin',
                'name_ar' => 'الحفاظ على بشرة صحية',
                'title_ar' => 'الحفاظ على بشرة صحية',
                'title_en' => 'Maintain Healthy Skin',
                'description_ar' => 'روتين يومي متوازن للحفاظ على ترطيب البشرة وحاجزها ونضارتها',
                'description_en' => 'Balanced daily care to maintain hydration, barrier health and freshness',
                'image' => 'maintain.png',
            ],
        ];

        $goalModelsByCode = [];
        foreach ($primaryGoalsCatalog as $code => $gData) {
            $goalModelsByCode[$code] = RoutineGoal::firstOrCreate(
                ['name_en' => $gData['name_en']],
                [
                    'name_ar' => $gData['name_ar'],
                    'image' => $gData['image'],
                ]
            );
        }

        // Ensure all 10 R479 Secondary Concerns exist in concerns for 1-to-1 mapped_id resolution
        $secondaryConcernsCatalog = [
            'acne' => [
                'name_en' => 'Acne & Blemishes',
                'name_ar' => 'الحبوب والبثور',
                'title_ar' => 'الحبوب والبثور',
                'title_en' => 'Acne & Blemishes',
                'description_ar' => 'العناية بالحبوب والبثور وتقليل مظهرها',
                'description_en' => 'Target active breakouts and blemishes',
                'image' => 'acne.png',
            ],
            'pores' => [
                'name_en' => 'Pores & Blackheads',
                'name_ar' => 'مظهر المسام',
                'title_ar' => 'مظهر المسام',
                'title_en' => 'Pore Appearance',
                'description_ar' => 'تحسين مظهر المسام الواسعة وتنعيم ملمس البشرة',
                'description_en' => 'Refine the appearance of enlarged pores',
                'image' => 'pores.png',
            ],
            'blackheads' => [
                'name_en' => 'Blackheads & Clogged Pores',
                'name_ar' => 'الرؤوس السوداء وانسداد المسام',
                'title_ar' => 'الرؤوس السوداء وانسداد المسام',
                'title_en' => 'Blackheads & Clogged Pores',
                'description_ar' => 'تقليل انسداد المسام والرؤوس السوداء',
                'description_en' => 'Reduce clogged pores and blackheads',
                'image' => 'blackheads.png',
            ],
            'dark_spots' => [
                'name_en' => 'Pigmentation & Dark Spots',
                'name_ar' => 'البقع الداكنة والتصبغات',
                'title_ar' => 'البقع الداكنة والتصبغات',
                'title_en' => 'Dark Spots & Pigmentation',
                'description_ar' => 'تفتيح البقع الداكنة والتصبغات وتوحيد لون البشرة',
                'description_en' => 'Fade dark spots and uneven pigmentation',
                'image' => 'dark_spots.png',
            ],
            'dullness' => [
                'name_en' => 'Dullness & Lack of Glow',
                'name_ar' => 'البهتان',
                'title_ar' => 'البهتان',
                'title_en' => 'Dullness',
                'description_ar' => 'تحسين إشراقة البشرة وتقليل مظهر البهتان',
                'description_en' => 'Boost skin radiance and reduce dullness',
                'image' => 'dullness.png',
            ],
            'redness' => [
                'name_en' => 'Redness & Irritation',
                'name_ar' => 'الاحمرار',
                'title_ar' => 'الاحمرار',
                'title_en' => 'Redness',
                'description_ar' => 'تهدئة الاحمرار والتهيّج ودعم راحة البشرة',
                'description_en' => 'Calm redness and soothe skin irritation',
                'image' => 'redness.png',
            ],
            'dryness' => [
                'name_en' => 'Dryness & Hydration',
                'name_ar' => 'الجفاف',
                'title_ar' => 'الجفاف',
                'title_en' => 'Dryness',
                'description_ar' => 'ترطيب البشرة ودعم حاجزها وتقليل الشعور بالجفاف',
                'description_en' => 'Hydrate skin and support barrier comfort',
                'image' => 'dryness.png',
            ],
            'lines' => [
                'name_en' => 'Wrinkles & Fine Lines',
                'name_ar' => 'الخطوط الدقيقة والتجاعيد',
                'title_ar' => 'الخطوط الدقيقة والتجاعيد',
                'title_en' => 'Fine Lines & Wrinkles',
                'description_ar' => 'العناية بمظهر الخطوط الدقيقة والتجاعيد ودعم المرونة',
                'description_en' => 'Target fine lines and wrinkles and support firmness',
                'image' => 'fine_lines.png',
            ],
            'oil' => [
                'name_en' => 'Excess Oil & Shine',
                'name_ar' => 'الدهون الزائدة واللمعان',
                'title_ar' => 'الدهون الزائدة واللمعان',
                'title_en' => 'Excess Oil & Shine',
                'description_ar' => 'تنظيم الإفرازات الدهنية وتقليل اللمعان الزائد',
                'description_en' => 'Balance excess sebum and control shine',
                'image' => 'excess_oil.png',
            ],
            'eyes' => [
                'name_en' => 'Eye Contour Concerns',
                'name_ar' => 'الهالات تحت العين',
                'title_ar' => 'الهالات تحت العين',
                'title_en' => 'Under-Eye Dark Circles',
                'description_ar' => 'العناية بمظهر الهالات تحت العين ومنطقة محيط العين',
                'description_en' => 'Care for under-eye dark circles and the delicate eye area',
                'image' => 'eye_concerns.png',
            ],
        ];

        $concernModelsByCode = [];
        foreach ($secondaryConcernsCatalog as $code => $cData) {
            $concernModelsByCode[$code] = Concern::firstOrCreate(
                ['name_en' => $cData['name_en']],
                [
                    'name_ar' => $cData['name_ar'],
                    'description_ar' => $cData['description_ar'],
                    'description_en' => $cData['description_en'],
                    'image' => $cData['image'],
                    'status' => true,
                ]
            );
        }

        // -------------------------------------------------------------
        // Question 1: Primary Goal (هدفك الأساسي - q1)
        // -------------------------------------------------------------
        $q1 = QuizQuestion::create([
            'key' => 'q1',
            'step_label_ar' => 'هدفك الأساسي',
            'step_label_en' => 'Primary Goal',
            'title_ar' => 'ما الذي تريد التركيز عليه أكثر من غيره؟',
            'title_en' => 'What would you like to focus on the most?',
            'description_ar' => 'سنبني روتينك حول هذا الهدف',
            'description_en' => 'We will build your routine around this goal',
            'helper_ar' => null,
            'helper_en' => null,
            'selection_type' => 'single',
            'step_number' => 1,
            'is_optional' => false,
        ]);

        foreach ($primaryGoalsCatalog as $code => $optData) {
            $mapped = $goalModelsByCode[$code] ?? null;
            QuizOption::create([
                'quiz_question_id' => $q1->id,
                'code' => $code,
                'title_ar' => $optData['title_ar'],
                'title_en' => $optData['title_en'],
                'description_ar' => $optData['description_ar'],
                'description_en' => $optData['description_en'],
                'image' => $optData['image'],
                'option_type' => 'goal',
                'mapped_id' => $mapped ? $mapped->id : null,
            ]);
        }

        // -------------------------------------------------------------
        // Question 2: Secondary Concern (مشكلة إضافية - q4)
        // -------------------------------------------------------------
        $q2 = QuizQuestion::create([
            'key' => 'q4',
            'step_label_ar' => 'مشكلة إضافية',
            'step_label_en' => 'Additional Concern',
            'title_ar' => 'هل هناك شيء آخر تودّ تحسينه؟',
            'title_en' => 'Is there anything else you would like to improve?',
            'description_ar' => 'إذا لم يكن لديك اهتمام إضافي، اختر «لا يوجد».',
            'description_en' => 'If you have no additional concern, choose "None".',
            'helper_ar' => null,
            'helper_en' => null,
            'selection_type' => 'single',
            'step_number' => 2,
            'is_optional' => true,
        ]);

        // First option: "none" (لا يوجد) matching R479 presentation.js order
        QuizOption::create([
            'quiz_question_id' => $q2->id,
            'code' => 'none',
            'title_ar' => 'لا يوجد',
            'title_en' => 'None',
            'description_ar' => 'التركيز الكامل على الهدف الأساسي فقط',
            'description_en' => 'Focus solely on the primary goal',
            'image' => 'none.png',
            'option_type' => 'none',
            'mapped_id' => null,
        ]);

        foreach ($secondaryConcernsCatalog as $code => $optData) {
            $mapped = $concernModelsByCode[$code] ?? null;
            QuizOption::create([
                'quiz_question_id' => $q2->id,
                'code' => $code,
                'title_ar' => $optData['title_ar'],
                'title_en' => $optData['title_en'],
                'description_ar' => $optData['description_ar'],
                'description_en' => $optData['description_en'],
                'image' => $optData['image'],
                'option_type' => 'concern',
                'mapped_id' => $mapped ? $mapped->id : null,
            ]);
        }

        // -------------------------------------------------------------
        // Question 3: Skin Type (نوع بشرتك - skin_type)
        // -------------------------------------------------------------
        $q3 = QuizQuestion::create([
            'key' => 'skin_type',
            'step_label_ar' => 'نوع بشرتك',
            'step_label_en' => 'Your Skin Type',
            'title_ar' => 'ما نوع بشرتك؟',
            'title_en' => 'What is your skin type?',
            'description_ar' => 'اختر الوصف الأقرب لطبيعة بشرتك في أغلب الأيام.',
            'description_en' => 'Choose the description closest to your skin nature on most days.',
            'helper_ar' => 'الشدّ بعد الغسل أو اللمعان بعد التعرّق وحده لا يكفي لتحديد نوع بشرتك.',
            'helper_en' => 'Tightness after washing or shine after sweating alone is not enough to determine your skin type.',
            'selection_type' => 'single',
            'step_number' => 3,
            'is_optional' => false,
        ]);

        $q3Options = [
            [
                'code' => 'oily',
                'name_en' => 'Oily',
                'title_ar' => 'دهنية',
                'title_en' => 'Oily',
                'description_ar' => 'دهون ولمعان متكرّر في معظم الوجه',
                'description_en' => 'Recurrent oiliness and shine across most of the face',
                'image' => 'oily_skin.png',
            ],
            [
                'code' => 'combination',
                'name_en' => 'Combination',
                'title_ar' => 'مختلطة',
                'title_en' => 'Combination',
                'description_ar' => 'دهون ولمعان في الجبهة والأنف والذقن، مع خدّين أقل دهنية أو جافّين',
                'description_en' => 'Oiliness and shine on forehead, nose and chin, with less oily or dry cheeks',
                'image' => 'combination_skin.png',
            ],
            [
                'code' => 'normal',
                'name_en' => 'Normal',
                'title_ar' => 'عادية',
                'title_en' => 'Normal',
                'description_ar' => 'متوازنة غالبًا، دون دهون زائدة أو جفاف واضح',
                'description_en' => 'Mostly balanced, without excess oil or noticeable dryness',
                'image' => 'normal_skin.png',
            ],
            [
                'code' => 'dry',
                'name_en' => 'Dry',
                'title_ar' => 'جافة',
                'title_en' => 'Dry',
                'description_ar' => 'جفاف أو خشونة متكرّرة، وقد يصاحبهما شدّ أو تقشّر',
                'description_en' => 'Recurrent dryness or roughness, which may be accompanied by tightness or flaking',
                'image' => 'dry_skin.png',
            ],
        ];

        foreach ($q3Options as $optData) {
            $mapped = SkinType::firstOrCreate(
                ['name_en' => $optData['name_en']],
                [
                    'name_ar' => $optData['title_ar'],
                    'description_ar' => $optData['description_ar'],
                    'description_en' => $optData['description_en'],
                    'image' => $optData['image'],
                    'status' => true,
                ]
            );
            QuizOption::create([
                'quiz_question_id' => $q3->id,
                'code' => $optData['code'],
                'title_ar' => $optData['title_ar'],
                'title_en' => $optData['title_en'],
                'description_ar' => $optData['description_ar'],
                'description_en' => $optData['description_en'],
                'image' => $optData['image'],
                'option_type' => 'skin_type',
                'mapped_id' => $mapped->id,
            ]);
        }

        // -------------------------------------------------------------
        // Question 4: Sensitivity Filter (تحسّس بشرتك - sensitivity)
        // -------------------------------------------------------------
        $q4 = QuizQuestion::create([
            'key' => 'sensitivity',
            'step_label_ar' => 'تحسّس بشرتك',
            'step_label_en' => 'Skin Sensitivity',
            'title_ar' => 'هل بشرتك حساسة؟',
            'title_en' => 'Is your skin sensitive?',
            'description_ar' => 'هل تتهيّج بسهولة مع منتجات العناية، مثل الشعور باللسع أو الحرقة أو ظهور احمرار؟',
            'description_en' => 'Does it irritate easily with skincare products, such as feeling stinging, burning, or redness?',
            'helper_ar' => null,
            'helper_en' => null,
            'selection_type' => 'single',
            'step_number' => 4,
            'is_optional' => false,
        ]);

        QuizOption::create([
            'quiz_question_id' => $q4->id,
            'code' => 'yes',
            'title_ar' => 'نعم، تتهيّج بسهولة',
            'title_en' => 'Yes, irritates easily',
            'description_ar' => 'تتهيّج بسهولة مع بعض المنتجات أو الفعالات القوية',
            'description_en' => 'Irritates easily with certain products or strong actives',
            'image' => 'sensitive_yes.png',
            'option_type' => 'sensitivity',
            'mapped_id' => 1,
        ]);

        QuizOption::create([
            'quiz_question_id' => $q4->id,
            'code' => 'no',
            'title_ar' => 'لا، لا تتهيّج عادةً',
            'title_en' => 'No, usually does not irritate',
            'description_ar' => 'لا تتحسّس بسهولة وتتحمل منتجات العناية المعتادة',
            'description_en' => 'Does not irritate easily and tolerates regular skincare products',
            'image' => 'sensitive_no.png',
            'option_type' => 'sensitivity',
            'mapped_id' => 0,
        ]);
    }
}
