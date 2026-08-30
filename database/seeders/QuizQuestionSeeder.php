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
     * Run the database seeds for the 4 Approved KCODE Quiz Questions (v6.0 Spec).
     */
    public function run(): void
    {
        // Clear existing questions and options
        \Illuminate\Support\Facades\Schema::disableForeignKeyConstraints();
        QuizOption::truncate();
        QuizQuestion::truncate();
        \Illuminate\Support\Facades\Schema::enableForeignKeyConstraints();

        // -------------------------------------------------------------
        // Question 1: Primary Goal (الهدف الأساسي)
        // -------------------------------------------------------------
        $q1 = QuizQuestion::create([
            'title_ar' => 'ما الذي تريد التركيز عليه أكثر من غيره؟',
            'title_en' => 'What would you like to focus on the most?',
            'description_ar' => 'سنبني روتينك حول هذا الهدف',
            'description_en' => 'We will build your routine around this primary goal',
            'selection_type' => 'single',
            'step_number' => 1,
            'is_optional' => false,
        ]);

        $q1_options = [
            [
                'title_ar' => 'الحبوب والبثور',
                'title_en' => 'Acne & Blemishes',
                'description_ar' => 'علاج الحبوب النشطة وتقليل الالتهاب وتصفية البشرة',
                'description_en' => 'Treat active acne, reduce inflammation and clarify skin',
                'image' => 'acne.png',
                'option_type' => 'goal',
                'mapped_name' => 'Acne & Scars',
            ],
            [
                'title_ar' => 'المسام والرؤوس السوداء والدهون الزائدة',
                'title_en' => 'Pores, Blackheads & Excess Oil',
                'description_ar' => 'تنقية المسام وضبط اللمعة وتحسين ملمس البشرة',
                'description_en' => 'Purify pores, control shine and refine skin texture',
                'image' => 'pores.png',
                'option_type' => 'goal',
                'mapped_name' => 'Pore Care',
            ],
            [
                'title_ar' => 'البقع الداكنة والبهتان وتفاوت لون البشرة',
                'title_en' => 'Dark Spots, Dullness & Uneven Tone',
                'description_ar' => 'تفتيح البقع وآثار الحبوب وتوحيد لون البشرة وإعطاء إشراقة',
                'description_en' => 'Fade dark spots, even skin tone and boost skin glow',
                'image' => 'brightening.png',
                'option_type' => 'goal',
                'mapped_name' => 'Brightening & Evening Tone',
            ],
            [
                'title_ar' => 'الاحمرار والتهيّج',
                'title_en' => 'Redness & Irritation',
                'description_ar' => 'تهدئة الاحمرار والالتهاب وتسكين البشرة سريعة الانفعال',
                'description_en' => 'Calm redness, irritation and soothe reactive skin',
                'image' => 'redness.png',
                'option_type' => 'goal',
                'mapped_name' => 'Hydration & Protection',
            ],
            [
                'title_ar' => 'الجفاف وضعف حاجز البشرة',
                'title_en' => 'Dryness & Damaged Barrier',
                'description_ar' => 'ترطيب عميق وتقوية حاجز البشرة الواقي من الجفاف',
                'description_en' => 'Deep hydration and barrier restoration against dryness',
                'image' => 'hydration.png',
                'option_type' => 'goal',
                'mapped_name' => 'Hydration & Protection',
            ],
            [
                'title_ar' => 'الخطوط الدقيقة والتجاعيد',
                'title_en' => 'Fine Lines & Wrinkles',
                'description_ar' => 'تحسين مرونة البشرة ودعم الشد وتقليل علامات تقدم السن',
                'description_en' => 'Improve skin elasticity, firming and anti-aging care',
                'image' => 'anti_aging.png',
                'option_type' => 'goal',
                'mapped_name' => 'Radiance & Freshness',
            ],
            [
                'title_ar' => 'الحفاظ على بشرة صحية ومتوازنة',
                'title_en' => 'Maintain Healthy & Balanced Skin',
                'description_ar' => 'روتين وقائي يومي خفيف للحفاظ على نضارة وتوازن البشرة',
                'description_en' => 'Preventative light daily routine for healthy balanced skin',
                'image' => 'radiance.png',
                'option_type' => 'goal',
                'mapped_name' => 'Radiance & Freshness',
            ],
        ];

        foreach ($q1_options as $optData) {
            $mapped = RoutineGoal::where('name_en', $optData['mapped_name'])->first();
            QuizOption::create([
                'quiz_question_id' => $q1->id,
                'title_ar' => $optData['title_ar'],
                'title_en' => $optData['title_en'],
                'description_ar' => $optData['description_ar'],
                'description_en' => $optData['description_en'],
                'image' => $optData['image'],
                'option_type' => $optData['option_type'],
                'mapped_id' => $mapped ? $mapped->id : null,
            ]);
        }

        // -------------------------------------------------------------
        // Question 2: Skin Type (نوع البشرة)
        // -------------------------------------------------------------
        $q2 = QuizQuestion::create([
            'title_ar' => 'كيف تصف بشرتك في نهاية اليوم؟',
            'title_en' => 'How does your skin feel at the end of the day?',
            'description_ar' => 'حدد إحساس بشرتك في نهاية اليوم لنتعرف على نوعها بدقة',
            'description_en' => 'Identify your skin feeling at day end for accurate skin typing',
            'selection_type' => 'single',
            'step_number' => 2,
            'is_optional' => false,
        ]);

        $q2_options = [
            [
                'name_en' => 'Oily',
                'title_ar' => 'دهنية',
                'description_ar' => 'لمعان في معظم الوجه مع إفراز زيوت مستمر',
                'description_en' => 'Shine across most of the face with excess sebum',
                'image' => 'oily_skin.png',
            ],
            [
                'name_en' => 'Combination',
                'title_ar' => 'مختلطة',
                'description_ar' => 'لمعان في منطقة T فقط (الجبهة والأنف والذقن)',
                'description_en' => 'Shine in T-zone only (forehead, nose, chin)',
                'image' => 'combination_skin.png',
            ],
            [
                'name_en' => 'Normal',
                'title_ar' => 'عادية',
                'description_ar' => 'متوازنة، دون لمعان أو شدّ زائد',
                'description_en' => 'Balanced, without excessive shine or tightness',
                'image' => 'normal_skin.png',
            ],
            [
                'name_en' => 'Dry',
                'title_ar' => 'جافة',
                'description_ar' => 'إحساس بالشدّ أو التقشّر مع نقص الترطيب',
                'description_en' => 'Feeling of tightness or flakiness with moisture deficit',
                'image' => 'dry_skin.png',
            ],
        ];

        foreach ($q2_options as $optData) {
            $mapped = SkinType::where('name_en', $optData['name_en'])->first();
            QuizOption::create([
                'quiz_question_id' => $q2->id,
                'title_ar' => $optData['title_ar'],
                'title_en' => $optData['name_en'],
                'description_ar' => $optData['description_ar'],
                'description_en' => $optData['description_en'],
                'image' => $optData['image'],
                'option_type' => 'skin_type',
                'mapped_id' => $mapped ? $mapped->id : null,
            ]);
        }

        // -------------------------------------------------------------
        // Question 3: Sensitivity Filter (الحساسية)
        // -------------------------------------------------------------
        $q3 = QuizQuestion::create([
            'title_ar' => 'هل تتهيّج بشرتك بسهولة؟',
            'title_en' => 'Does your skin irritate easily?',
            'description_ar' => 'احمرار أو حرقة أو شدّ عند استخدام منتج جديد',
            'description_en' => 'Redness, burning or tightness when trying new products',
            'selection_type' => 'single',
            'step_number' => 3,
            'is_optional' => false,
        ]);

        QuizOption::create([
            'quiz_question_id' => $q3->id,
            'title_ar' => 'نعم',
            'title_en' => 'Yes',
            'description_ar' => 'تطبيق أعلى مستوى حماية واستبعاد الفعالات القوية والعطور',
            'description_en' => 'Apply maximum safety filter: exclude harsh actives & perfumes',
            'image' => 'sensitive_yes.png',
            'option_type' => 'sensitivity',
            'mapped_id' => 1,
        ]);

        QuizOption::create([
            'quiz_question_id' => $q3->id,
            'title_ar' => 'لا',
            'title_en' => 'No',
            'description_ar' => 'بشرة تتحمل المنتجات والتركيزات المختلفة بحرية',
            'description_en' => 'Skin tolerates various active ingredients normally',
            'image' => 'sensitive_no.png',
            'option_type' => 'sensitivity',
            'mapped_id' => 0,
        ]);

        // -------------------------------------------------------------
        // Question 4: Secondary Concern (المشاكل الإضافية)
        // -------------------------------------------------------------
        $q4 = QuizQuestion::create([
            'title_ar' => 'هل هناك شيء آخر تودّ تحسينه؟',
            'title_en' => 'Is there anything else you would like to improve?',
            'description_ar' => 'اختر مشكلة واحدة فقط — أو اختر «لا يوجد»',
            'description_en' => 'Select one additional concern — or select "None"',
            'selection_type' => 'single',
            'step_number' => 4,
            'is_optional' => true,
        ]);

        $q4_options = [
            [
                'title_ar' => 'رؤوس سوداء ومسام واضحة',
                'title_en' => 'Blackheads & Visible Pores',
                'desc_ar' => 'تنقية المسام المسدودة وتقليل الرؤوس السوداء',
                'desc_en' => 'Cleanse clogged pores and reduce blackheads',
                'image' => 'blackheads.png',
                'concern_name' => 'Pores & Blackheads',
            ],
            [
                'title_ar' => 'دهون زائدة ولمعان',
                'title_en' => 'Excess Oil & Shine',
                'desc_ar' => 'السيطرة على الإفراز الدهني وضبط اللمعة',
                'desc_en' => 'Control sebum secretion and balance shine',
                'image' => 'excess_oil.png',
                'concern_name' => 'Pores & Blackheads',
            ],
            [
                'title_ar' => 'آثار حبوب حمراء',
                'title_en' => 'Red Acne Marks (PIE)',
                'desc_ar' => 'بقع وردية أو حمراء تبقى بعد زوال الحبة',
                'desc_en' => 'Pink or red marks remaining after acne heals',
                'image' => 'red_marks.png',
                'concern_name' => 'Redness & Irritation',
            ],
            [
                'title_ar' => 'بقع داكنة وتصبغات',
                'title_en' => 'Dark Spots & Pigmentation (PIH)',
                'desc_ar' => 'بقع بنية أو آثار داكنة تبقى بعد الحبوب أو الشمس',
                'desc_en' => 'Brown spots or dark marks left after breakouts or sun exposure',
                'image' => 'dark_spots.png',
                'concern_name' => 'Pigmentation & Dark Spots',
            ],
            [
                'title_ar' => 'جفاف وشدّ',
                'title_en' => 'Dryness & Tightness',
                'desc_ar' => 'تعويض نقص الترطيب وإلغاء إحساس الانكماش',
                'desc_en' => 'Replenish moisture deficit and eliminate tightness',
                'image' => 'dryness.png',
                'concern_name' => 'Dryness & Hydration',
            ],
            [
                'title_ar' => 'احمرار مستمر',
                'title_en' => 'Persistent Redness',
                'desc_ar' => 'تهدئة وتخفيف الاحمرار الدائم',
                'desc_en' => 'Calm and soothe continuous facial redness',
                'image' => 'persistent_redness.png',
                'concern_name' => 'Redness & Irritation',
            ],
            [
                'title_ar' => 'بهتان وشحوب',
                'title_en' => 'Dullness & Lack of Glow',
                'desc_ar' => 'تنشيط حيوية البشرة واستعادة الإشراقة الصحية',
                'desc_en' => 'Revitalize skin vitality and restore healthy glow',
                'image' => 'dullness.png',
                'concern_name' => 'Pigmentation & Dark Spots',
            ],
            [
                'title_ar' => 'خطوط دقيقة',
                'title_en' => 'Fine Lines',
                'desc_ar' => 'تنعيم مظهر الخطوط التعبيرية المبكرة',
                'desc_en' => 'Smooth the appearance of early fine lines',
                'image' => 'fine_lines.png',
                'concern_name' => 'Wrinkles & Fine Lines',
            ],
            [
                'title_ar' => 'مشاكل حول العين',
                'title_en' => 'Eye Contour Concerns',
                'desc_ar' => 'العناية بالهالات والانتفاخ والخطوط حول العين',
                'desc_en' => 'Care for dark circles, puffiness and fine lines around eyes',
                'image' => 'eye_care.png',
                'concern_name' => 'Wrinkles & Fine Lines',
            ],
            [
                'title_ar' => 'لا يوجد',
                'title_en' => 'None',
                'desc_ar' => 'لا أعاني من مشاكل إضافية وأرغب في الروتين الأساسي فقط',
                'desc_en' => 'I have no additional concerns and want only the core routine',
                'image' => 'none.png',
                'concern_name' => null,
            ],
        ];

        foreach ($q4_options as $optData) {
            $mapped = $optData['concern_name'] ? Concern::where('name_en', $optData['concern_name'])->first() : null;
            QuizOption::create([
                'quiz_question_id' => $q4->id,
                'title_ar' => $optData['title_ar'],
                'title_en' => $optData['title_en'],
                'description_ar' => $optData['desc_ar'],
                'description_en' => $optData['desc_en'],
                'image' => $optData['image'],
                'option_type' => $optData['concern_name'] ? 'concern' : 'none',
                'mapped_id' => $mapped ? $mapped->id : null,
            ]);
        }
    }
}
