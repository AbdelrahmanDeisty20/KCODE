<?php

namespace App\Services;

use App\Http\Resources\API\QUIZ\QuizQuestionResource;
use App\Models\QuizOption;
use App\Models\QuizQuestion;
use App\Models\Assessment;
use App\Models\AssessmentGoal;
use App\Models\AssessmentConcern;
use App\Models\SkinType;
use App\Models\Concern;
use App\Models\RoutineGoal;
use App\Models\RoutineStep;
use App\Models\Product;
use App\Models\ProductAlternative;
use App\Models\Routine;
use App\Models\RoutineProduct;
use Illuminate\Support\Facades\Auth;

class QuizService
{
    /**
     * Get all questions with options.
     */
    public function getquestions()
    {
        $questions = QuizQuestion::with('options')->get();
        if ($questions->isEmpty()) {
            return [
                'status' => false,
                'message' => __('messages.no_questions_available'),
                'data' => []
            ];
        }
        
        return [
            'status' => true,
            'message' => __('messages.questions_fetched_successfully'),
            'data' => QuizQuestionResource::collection($questions)
        ];
    }

    /**
     * Save Assessment and recommended Routine products to the database (At most 5 Best Sellers, prioritizing concern treatments).
     */
    public function saveAssessment(array $data)
    { 
        $user = auth('sanctum')->user();
        $lang = request()->header('lang') ?? app()->getLocale();
        
        // 1. Resolve directly from request parameters
        $skinTypeId = $data['skin_type_id'] ?? null;
        $goalId = $data['routine_goal_id'] ?? null;
        $concernIds = $data['concern_ids'] ?? [];

        // Check if skin type is provided
        if (!$skinTypeId) {
            return [
                'status' => false,
                'message' => __('messages.skin_type_required')
            ];
        }

        // Check if routine goal is provided
        if (!$goalId) {
            return [
                'status' => false,
                'message' => __('messages.routine_goal_required')
            ];
        }

        // 1. Process quiz assessment and create a new Routine record for each submission
        if ($user) {
            $user->update(['skin_type_id' => $skinTypeId]);
        }

        $assessment = Assessment::create([
            'user_id'      => $user ? $user->id : null,
            'skin_type_id' => $skinTypeId,
        ]);

        $routine = Routine::create([
            'assessment_id' => $assessment->id,
        ]);

        // 3. Save Goal
        if ($goalId) {
            AssessmentGoal::create([
                'assessment_id' => $assessment->id,
                'goal_id' => $goalId,
            ]);
        }

        // 4. Save Concerns
        foreach ($concernIds as $concernId) {
            AssessmentConcern::create([
                'assessment_id' => $assessment->id,
                'concern_id' => $concernId,
            ]);
        }

        // 6. Resolve Quiz Questions and Answers to return in response & Build Diagnosis
        $questionsAndAnswers = [];
        $badges = [];
        $goalsSummary = [];

        // Skin Type Question (Question ID 2) - Primary Badge
        $skinQuestion = QuizQuestion::find(2);
        if ($skinQuestion) {
            $selectedSkinOption = QuizOption::where('quiz_question_id', 2)
                ->where('option_type', 'skin_type')
                ->where('mapped_id', $skinTypeId)
                ->first();

            if ($selectedSkinOption) {
                $skinTitle = $lang === 'ar' ? $selectedSkinOption->title_ar : $selectedSkinOption->title_en;
                $badges[] = ($lang === 'ar' ? 'بشرة ' : '') . $skinTitle . ($lang === 'en' ? ' Skin' : '');
            }

            $questionsAndAnswers[] = [
                'question_id' => 2,
                'question_title' => $lang === 'ar' ? $skinQuestion->title_ar : $skinQuestion->title_en,
                'selected_options' => $selectedSkinOption ? [
                    [
                        'option_id' => $selectedSkinOption->id,
                        'option_title' => $lang === 'ar' ? $selectedSkinOption->title_ar : $selectedSkinOption->title_en,
                        'description' => $selectedSkinOption->description,
                        'image' => $selectedSkinOption->image,
                        'option_type' => $selectedSkinOption->option_type,
                        'mapped_id' => $selectedSkinOption->mapped_id,
                    ]
                ] : []
            ];
        }

        // Goal Question (Question ID 1)
        $goalQuestion = QuizQuestion::find(1);
        if ($goalQuestion) {
            $selectedGoalOption = QuizOption::where('quiz_question_id', 1)
                ->where('option_type', 'goal')
                ->where('mapped_id', $goalId)
                ->first();

            if ($selectedGoalOption) {
                $goalTitle = $lang === 'ar' ? $selectedGoalOption->title_ar : $selectedGoalOption->title_en;
                $badges[] = $goalTitle;

                if ($selectedGoalOption->description) {
                    $goalsSummary[] = $selectedGoalOption->description;
                }
            }

            $questionsAndAnswers[] = [
                'question_id' => 1,
                'question_title' => $lang === 'ar' ? $goalQuestion->title_ar : $goalQuestion->title_en,
                'selected_options' => $selectedGoalOption ? [
                    [
                        'option_id' => $selectedGoalOption->id,
                        'option_title' => $lang === 'ar' ? $selectedGoalOption->title_ar : $selectedGoalOption->title_en,
                        'description' => $selectedGoalOption->description,
                        'image' => $selectedGoalOption->image,
                        'option_type' => $selectedGoalOption->option_type,
                        'mapped_id' => $selectedGoalOption->mapped_id,
                    ]
                ] : []
            ];
        }

        // Concern Question (Question ID 3)
        $concernQuestion = QuizQuestion::find(3);
        if ($concernQuestion) {
            $selectedConcernOptions = [];
            if (count($concernIds) > 0) {
                $opts = QuizOption::where('quiz_question_id', 3)
                    ->where('option_type', 'concern')
                    ->whereIn('mapped_id', $concernIds)
                    ->get();
                foreach ($opts as $opt) {
                    $cTitle = $lang === 'ar' ? $opt->title_ar : $opt->title_en;
                    $badges[] = $cTitle;

                    if ($opt->description) {
                        $goalsSummary[] = $opt->description;
                    }

                    $selectedConcernOptions[] = [
                        'option_id' => $opt->id,
                        'option_title' => $cTitle,
                        'description' => $opt->description,
                        'image' => $opt->image,
                        'option_type' => $opt->option_type,
                        'mapped_id' => $opt->mapped_id,
                    ];
                }
            } else {
                // "No concern" option
                $noConcernOpt = QuizOption::where('quiz_question_id', 3)
                    ->where('option_type', 'none')
                    ->first();
                if ($noConcernOpt) {
                    $selectedConcernOptions[] = [
                        'option_id' => $noConcernOpt->id,
                        'option_title' => $lang === 'ar' ? $noConcernOpt->title_ar : $noConcernOpt->title_en,
                        'description' => $noConcernOpt->description,
                        'image' => $noConcernOpt->image,
                        'option_type' => $noConcernOpt->option_type,
                        'mapped_id' => $noConcernOpt->mapped_id,
                    ];
                }
            }

            $questionsAndAnswers[] = [
                'question_id' => 3,
                'question_title' => $lang === 'ar' ? $concernQuestion->title_ar : $concernQuestion->title_en,
                'selected_options' => $selectedConcernOptions
            ];
        }

        // Fetch WhatsApp number dynamically from settings table
        $whatsappSetting = \App\Models\Setting::whereIn('key_en', ['whatsapp_number', 'whatsapp', 'phone'])->first();
        $whatsappNumber = $whatsappSetting ? ($whatsappSetting->value_en ?: $whatsappSetting->value_ar) : '966500000000';
        $cleanPhone = preg_replace('/[^0-9]/', '', $whatsappNumber);
        $whatsappLink = str_starts_with($whatsappNumber, 'http') 
            ? $whatsappNumber 
            : 'https://wa.me/' . ($cleanPhone ?: '966500000000');

        $diagnosis = [
            'title' => $lang === 'ar' ? 'تشخيص بشرتك' : 'Your Skin Diagnosis',
            'badges' => array_values(array_unique($badges)),
            'goals_summary' => array_values(array_filter($goalsSummary)),
            'consultation' => [
                'title' => $lang === 'ar' ? 'هل تودين استشارة خبيرة؟' : 'Would you like expert consultation?',
                'description' => $lang === 'ar' ? 'صيدلانيات كود متواجدات للإجابة على استفساراتك وتخصيص الروتين أكثر.' : 'KCODE pharmacists are available to answer your questions and further customize your routine.',
            ]
        ];

        // 7. Run Product Recommendation Logic using Database values
        $steps = RoutineStep::orderBy('order', 'asc')->get();
        $primaryCandidates = [];
        $supportCandidates = [];
        $addonCandidates = [];

        $skinTypeModel = SkinType::find($skinTypeId);
        $skinTypeName = $skinTypeModel ? ($lang === 'ar' ? $skinTypeModel->name_ar : $skinTypeModel->name_en) : ($lang === 'ar' ? 'المختارة' : 'Selected');

        foreach ($steps as $step) {
            $query = Product::query()
                ->where('stock', '>', 0)
                ->bestSeller()
                ->whereHas('routines', function ($q) use ($step) {
                    $q->where('routine_step_id', $step->id);
                });

            if ($skinTypeId) {
                $query->whereHas('skinTypes', function ($q) use ($skinTypeId) {
                    $q->where('skin_type_id', $skinTypeId);
                });
            }

            $eligibleProducts = $query->with([
                'brand',
                'marketingDetail',
                'recommendationRule',
                'reviews',
                'goals',
                'concerns'
            ])->get();

            if ($eligibleProducts->isEmpty()) {
                continue;
            }

            // Score each product
            $scoredProducts = $eligibleProducts->map(function ($product) use ($goalId, $concernIds) {
                $score = 0;

                if ($product->recommendationRule) {
                    $score += $product->recommendationRule->default_priority_score;
                }

                if ($goalId && ($product->goals->contains('id', $goalId) || $product->goals->contains('pivot.goal_id', $goalId))) {
                    $score += 100;
                }

                foreach ($concernIds as $cId) {
                    if ($product->concerns->contains('id', $cId) || $product->concerns->contains('pivot.concern_id', $cId)) {
                        $score += 200;
                    }
                }

                $score += (int)($product->average_rating * 2);

                return [
                    'product' => $product,
                    'score' => $score
                ];
            });

            $sorted = $scoredProducts->sortByDesc('score');
            $bestElement = $sorted->first();
            $bestMatch = $bestElement['product'];
            $highestScore = $bestElement['score'];

            $routineInfo = $bestMatch->routines()->where('routine_step_id', $step->id)->first();
            $isCore = $routineInfo ? (bool)$routineInfo->is_core : in_array($step->order, [3, 9, 11, 15]); // Cleanser, Serum, Treatment, Moisturizer
            $isAddon = $routineInfo ? (bool)$routineInfo->is_addon : ($step->order > 15 || in_array($step->order, [16, 17]));

            $candidateData = [
                'step_id' => $step->id,
                'step_name' => $lang === 'ar' ? ($step->name_ar ?? $step->name) : $step->name,
                'step_order' => $step->order,
                'score' => $highestScore,
                'best_match' => $bestMatch,
                'routine_info' => $routineInfo,
                'is_core' => $isCore,
                'is_addon' => $isAddon,
            ];

            if ($isAddon) {
                $addonCandidates[] = $candidateData;
            } elseif ($isCore && count($primaryCandidates) < 5) {
                $primaryCandidates[] = $candidateData;
            } else {
                $supportCandidates[] = $candidateData;
            }
        }

        // If primary has less than 4, take from support
        if (count($primaryCandidates) < 4 && !empty($supportCandidates)) {
            while (count($primaryCandidates) < 4 && !empty($supportCandidates)) {
                $primaryCandidates[] = array_shift($supportCandidates);
            }
        }

        // If primary has more than 5, move extra to support
        if (count($primaryCandidates) > 5) {
            $extra = array_splice($primaryCandidates, 5);
            $supportCandidates = array_merge($extra, $supportCandidates);
        }

        $supportCandidates = array_slice($supportCandidates, 0, 3);
        $addonCandidates = array_slice($addonCandidates, 0, 2);

        usort($primaryCandidates, fn($a, $b) => $a['step_order'] <=> $b['step_order']);
        usort($supportCandidates, fn($a, $b) => $a['step_order'] <=> $b['step_order']);
        usort($addonCandidates, fn($a, $b) => $a['step_order'] <=> $b['step_order']);

        // 1. Build Primary Routine Array
        $primaryRoutineResponse = [];
        $orderIndex = 1;
        foreach ($primaryCandidates as $cand) {
            $product = $cand['best_match'];
            $routineInfo = $cand['routine_info'];

            RoutineProduct::create([
                'routine_id' => $routine->id,
                'product_id' => $product->id,
                'step' => $cand['step_order'],
                'replaced_with_product_id' => $product->id,
                'accepted' => true,
            ]);

            $morning = $routineInfo ? (bool)$routineInfo->morning : true;
            $night = $routineInfo ? (bool)$routineInfo->night : true;
            $useTimeText = ($morning && $night) ? ($lang === 'ar' ? 'صباحاً ومساءً' : 'Morning & Evening') : ($morning ? ($lang === 'ar' ? 'صباحاً' : 'Morning') : ($lang === 'ar' ? 'مساءً' : 'Evening'));

            $primaryRoutineResponse[] = [
                'display_order' => $orderIndex++,
                'selected_by_default' => true,
                'step_id' => $cand['step_id'],
                'routine_step_ar' => $cand['step_name'],
                'routine_step_code' => 'step_' . $cand['step_order'],
                'use_time_ar' => $useTimeText,
                'usage_badge_ar' => ($lang === 'ar' ? 'يومياً ' : 'Daily ') . $useTimeText,
                'chosen_for_ar' => $lang === 'ar' 
                    ? "تم اختياره خصيصاً للبشرة {$skinTypeName} لتوفير العناية والتوازن وتجهيز البشرة."
                    : "Specifically chosen for {$skinTypeName} skin to deliver optimal care and balance.",
                'product' => $product,
            ];
        }

        // 2. Build Routine Support Array
        $routineSupportResponse = [];
        $orderIndex = 1;
        foreach ($supportCandidates as $cand) {
            $product = $cand['best_match'];
            $routineInfo = $cand['routine_info'];

            $morning = $routineInfo ? (bool)$routineInfo->morning : true;
            $night = $routineInfo ? (bool)$routineInfo->night : true;
            $useTimeText = ($morning && $night) ? ($lang === 'ar' ? 'صباحاً ومساءً' : 'Morning & Evening') : ($morning ? ($lang === 'ar' ? 'صباحاً' : 'Morning') : ($lang === 'ar' ? 'مساءً' : 'Evening'));

            $routineSupportResponse[] = [
                'display_order' => $orderIndex++,
                'selected_by_default' => false,
                'step_id' => $cand['step_id'],
                'routine_step_ar' => $cand['step_name'],
                'routine_step_code' => 'step_' . $cand['step_order'],
                'use_time_ar' => $useTimeText,
                'usage_badge_ar' => ($lang === 'ar' ? '2-3 مرات أسبوعياً ' : '2-3 times weekly ') . $useTimeText,
                'chosen_for_ar' => $lang === 'ar'
                    ? "خطوة تكميلية مثالية لدعم نضارة البشرة {$skinTypeName} وتسريع نتائج الروتين."
                    : "Ideal complementary step to enhance {$skinTypeName} skin radiance.",
                'product' => $product,
            ];
        }

        // 3. Build Cart Addons Array
        $cartAddonsResponse = [];
        $orderIndex = 1;
        foreach ($addonCandidates as $cand) {
            $product = $cand['best_match'];

            $cartAddonsResponse[] = [
                'display_order' => $orderIndex++,
                'selected_by_default' => false,
                'cart_note_ar' => $lang === 'ar'
                    ? "مُقترح إضافي رائع يُوصى به في السلة لإكمال العناية المزدوجة بالبشرة."
                    : "Great extra recommendation suggested for your cart for double care.",
                'product' => $product,
            ];
        }

        return [
            'status' => true,
            'message' => __('messages.routine_recommended_successfully'),
            'data' => [
                'is_routine_added' => true,
                'routine_id'       => $routine->id,
                'diagnosis'        => $diagnosis,
                'questions'        => $questionsAndAnswers,
                'primary_routine'  => $primaryRoutineResponse,
                'routine_support'  => $routineSupportResponse,
                'cart_addons'       => $cartAddonsResponse,
            ]
        ];
    }
}

