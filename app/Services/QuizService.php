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

        // Check if user already has an assessment/routine
        if ($user) {
            $existingAssessment = Assessment::where('user_id', $user->id)->first();
            if ($existingAssessment) {
                // تحديث الـ Assessment الحالي
                $existingAssessment->update([
                    'skin_type_id' => $skinTypeId,
                ]);

                // مسح الأهداف والاهتمامات القديمة للبدء من جديد
                AssessmentGoal::where('assessment_id', $existingAssessment->id)->delete();
                AssessmentConcern::where('assessment_id', $existingAssessment->id)->delete();

                // جلب الـ Routine الحالي ومسح منتجاته المؤقتة القديمة
                $routine = Routine::where('assessment_id', $existingAssessment->id)->first();
                if (!$routine) {
                    $routine = Routine::create([
                        'assessment_id' => $existingAssessment->id,
                    ]);
                } else {
                    RoutineProduct::where('routine_id', $routine->id)->delete();
                }

                $assessment = $existingAssessment;
            } else {
                // 2. Create Assessment
                $assessment = Assessment::create([
                    'user_id' => $user->id ?? null,
                    'skin_type_id' => $skinTypeId,
                ]);

                // 5. Create Routine Record in Database
                $routine = Routine::create([
                    'assessment_id' => $assessment->id,
                ]);
            }
        } else {
            // 2. Create Assessment (Guest)
            $assessment = Assessment::create([
                'user_id' => null,
                'skin_type_id' => $skinTypeId,
            ]);

            // 5. Create Routine Record in Database (Guest)
            $routine = Routine::create([
                'assessment_id' => $assessment->id,
            ]);
        }

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

        // 6. Resolve Quiz Questions and Answers to return in response
        $questionsAndAnswers = [];

        // Goal Question (Question ID 1)
        $goalQuestion = QuizQuestion::find(1);
        if ($goalQuestion) {
            $selectedGoalOption = QuizOption::where('quiz_question_id', 1)
                ->where('option_type', 'goal')
                ->where('mapped_id', $goalId)
                ->first();
            
            $questionsAndAnswers[] = [
                'question_id' => 1,
                'question_title' => $lang === 'ar' ? $goalQuestion->title_ar : $goalQuestion->title_en,
                'selected_options' => $selectedGoalOption ? [
                    [
                        'option_id' => $selectedGoalOption->id,
                        'option_title' => $lang === 'ar' ? $selectedGoalOption->title_ar : $selectedGoalOption->title_en,
                    ]
                ] : []
            ];
        }

        // Skin Type Question (Question ID 2)
        $skinQuestion = QuizQuestion::find(2);
        if ($skinQuestion) {
            $selectedSkinOption = QuizOption::where('quiz_question_id', 2)
                ->where('option_type', 'skin_type')
                ->where('mapped_id', $skinTypeId)
                ->first();

            $questionsAndAnswers[] = [
                'question_id' => 2,
                'question_title' => $lang === 'ar' ? $skinQuestion->title_ar : $skinQuestion->title_en,
                'selected_options' => $selectedSkinOption ? [
                    [
                        'option_id' => $selectedSkinOption->id,
                        'option_title' => $lang === 'ar' ? $selectedSkinOption->title_ar : $selectedSkinOption->title_en,
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
                    $selectedConcernOptions[] = [
                        'option_id' => $opt->id,
                        'option_title' => $lang === 'ar' ? $opt->title_ar : $opt->title_en,
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
                    ];
                }
            }

            $questionsAndAnswers[] = [
                'question_id' => 3,
                'question_title' => $lang === 'ar' ? $concernQuestion->title_ar : $concernQuestion->title_en,
                'selected_options' => $selectedConcernOptions
            ];
        }

        // 7. Run Product Recommendation Logic using Database values
        $steps = RoutineStep::orderBy('order', 'asc')->get();
        $candidates = [];

        foreach ($steps as $step) {
            // Query only best-selling products for the current routine step
            $query = Product::query()
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

                // Base priority score
                if ($product->recommendationRule) {
                    $score += $product->recommendationRule->default_priority_score;
                }

                // Goal match bonus
                if ($goalId && $product->goals->contains('goal_id', $goalId)) {
                    $score += 100;
                }

                // Concern match bonus (highly prioritized to treat the concerns)
                foreach ($concernIds as $cId) {
                    if ($product->concerns->contains('concern_id', $cId)) {
                        $score += 200;
                    }
                }

                // Rating bonus
                $score += (int)($product->average_rating * 2);

                return [
                    'product' => $product,
                    'score' => $score
                ];
            });

            // Sort products by score descending
            $sorted = $scoredProducts->sortByDesc('score');
            $bestElement = $sorted->first();
            $bestMatch = $bestElement['product'];
            $highestScore = $bestElement['score'];

            $routineInfo = $bestMatch->routines()->where('routine_step_id', $step->id)->first();

            $candidates[] = [
                'step_id' => $step->id,
                'step_name' => $step->name,
                'step_order' => $step->order,
                'score' => $highestScore,
                'best_match' => $bestMatch,
                'routine_info' => $routineInfo,
            ];
        }

        // Sort candidates by score descending to get the top 5 most relevant best-selling products
        usort($candidates, function ($a, $b) {
            return $b['score'] <=> $a['score'];
        });

        // Limit to at most 5 products
        $topCandidates = array_slice($candidates, 0, 5);

        // Re-sort selected products by step_order ascending to preserve sequence flow
        usort($topCandidates, function ($a, $b) {
            return $a['step_order'] <=> $b['step_order'];
        });

        $recommendedProducts = [];
        $stepIndex = 1;

        foreach ($topCandidates as $cand) {
            $bestMatch = $cand['best_match'];
            $routineInfo = $cand['routine_info'];

            // 8. Save recommended product in the routine_products table
            RoutineProduct::create([
                'routine_id' => $routine->id,
                'product_id' => $bestMatch->id,
                'step' => $cand['step_order'],
                'replaced_with_product_id' => $bestMatch->id,
                'accepted' => true,
            ]);

            $recommendedProducts[] = [
                'step_id' => $cand['step_id'],
                'step_name' => $cand['step_name'],
                'step_order' => $stepIndex++,
                'is_core' => $routineInfo ? (bool)$routineInfo->is_core : true,
                'is_addon' => $routineInfo ? (bool)$routineInfo->is_addon : false,
                'morning' => $routineInfo ? (bool)$routineInfo->morning : true,
                'night' => $routineInfo ? (bool)$routineInfo->night : true,
                'product' => [
                    'id' => $bestMatch->id,
                    'name' => $lang === 'ar' ? $bestMatch->name_ar : $bestMatch->name_en,
                    'sku' => $bestMatch->sku,
                    'price' => (float)$bestMatch->price,
                    'image' => $bestMatch->image ? asset('uploads/products/' . $bestMatch->image) : null,
                    'average_rating' => $bestMatch->average_rating,
                    'num_reviews' => $bestMatch->num_reviews,
                    'brand' => $bestMatch->brand ? [
                        'id' => $bestMatch->brand->id,
                        'name' => $lang === 'ar' ? $bestMatch->brand->name_ar : $bestMatch->brand->name_en,
                    ] : null,
                ],
            ];
        }

        return [
            'status' => true,
            'message' => __('messages.routine_recommended_successfully'),
            'data' => [
                'is_routine_added' => true,
                'questions' => $questionsAndAnswers,
                'routine' => $recommendedProducts
            ]
        ];
    }
}
