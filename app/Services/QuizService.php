<?php

namespace App\Services;

use App\Http\Resources\API\PRODUCT\ProductListResource;
use App\Http\Resources\API\QUIZ\QuizQuestionResource;
use App\Models\Assessment;
use App\Models\AssessmentAnswer;
use App\Models\AssessmentConcern;
use App\Models\AssessmentGoal;
use App\Models\Concern;
use App\Models\Product;
use App\Models\QuizOption;
use App\Models\QuizQuestion;
use App\Models\Routine;
use App\Models\RoutineGoal;
use App\Models\RoutineProduct;
use App\Models\RoutineStep;
use App\Models\Setting;
use App\Models\SkinType;
use App\Services\ActivityLogger;

class QuizService
{
    protected R479EngineService $r479Engine;

    public function __construct(?R479EngineService $r479Engine = null)
    {
        $this->r479Engine = $r479Engine ?? new R479EngineService();
    }

    /**
     * Get all questions with options ordered by step_number.
     */
    public function getquestions(): array
    {
        $questions = QuizQuestion::with('options')->orderBy('step_number', 'asc')->get();
        if ($questions->isEmpty()) {
            return [
                'status' => false,
                'message' => __('messages.no_questions_available'),
                'data' => [],
            ];
        }

        return [
            'status' => true,
            'message' => __('messages.questions_fetched_successfully'),
            'data' => QuizQuestionResource::collection($questions),
        ];
    }

    /**
     * Normalize incoming request payload (supports R479 codes, Option IDs, or Legacy DB IDs)
     * into the canonical R479 4-answer contract + DB foreign keys.
     */
    public function resolveAnswers(array $data): array
    {
        $nested = is_array($data['answers'] ?? null) ? $data['answers'] : [];

        $primaryOption = null;
        $skinOption = null;
        $sensOption = null;
        $secondaryOption = null;

        // If 'answers' is a sequential list of option IDs or answer objects, e.g. [1, 5, 8, 12] or [['option_id' => 1], ...]
        $extractedOptionIds = [];
        if (!empty($nested) && array_is_list($nested)) {
            foreach ($nested as $item) {
                if (is_numeric($item)) {
                    $extractedOptionIds[] = (int) $item;
                } elseif (is_array($item)) {
                    if (!empty($item['option_id'])) {
                        $extractedOptionIds[] = (int) $item['option_id'];
                    } elseif (!empty($item['id'])) {
                        $extractedOptionIds[] = (int) $item['id'];
                    }
                }
            }
        }

        if (!empty($extractedOptionIds)) {
            $optionsById = QuizOption::with('question')->whereIn('id', $extractedOptionIds)->get();
            foreach ($optionsById as $opt) {
                $step = (int) ($opt->question?->step_number ?? 0);
                $qKey = $opt->question?->key ?? '';
                if ($step === 1 || $qKey === 'q1') {
                    $primaryOption = $primaryOption ?? $opt;
                } elseif ($step === 2 || $qKey === 'skin_type') {
                    $skinOption = $skinOption ?? $opt;
                } elseif ($step === 3 || $qKey === 'sensitivity') {
                    $sensOption = $sensOption ?? $opt;
                } elseif ($step === 4 || $qKey === 'q4') {
                    $secondaryOption = $secondaryOption ?? $opt;
                }
            }
        }

        // 1. Resolve Primary Goal ('acne', 'pores', 'blackheads', 'dark_spots', 'dullness', 'redness', 'dryness', 'lines', 'maintain')
        $primaryCode = $nested['primary'] ?? $data['primary'] ?? $data['q1'] ?? null;
        $goalId = $data['routine_goal_id'] ?? null;

        if (!empty($data['primary_option_id'])) {
            $primaryOption = QuizOption::find($data['primary_option_id']);
        }
        if (!$primaryOption && $primaryCode) {
            $primaryOption = QuizOption::whereHas('question', fn ($q) => $q->where('step_number', 1))
                ->where('code', $primaryCode)
                ->first();
        }
        if (!$primaryOption && $goalId) {
            // Check if $goalId matches mapped_id or option id on Q1
            $primaryOption = QuizOption::whereHas('question', fn ($q) => $q->where('step_number', 1))
                ->where(fn ($q) => $q->where('mapped_id', $goalId)->orWhere('id', $goalId))
                ->first();
        }

        if ($primaryOption) {
            $primaryCode = $primaryCode ?: $primaryOption->code;
            $goalId = $goalId ?: $primaryOption->mapped_id;
        }

        if (!$primaryCode && $goalId) {
            $goalModel = RoutineGoal::find($goalId);
            if ($goalModel) {
                $primaryCode = $this->mapGoalModelToCode($goalModel->name_en);
            }
        }

        // 2. Resolve Skin Type ('oily', 'combination', 'normal', 'dry')
        $skinCode = $nested['skin'] ?? $data['skin'] ?? $data['skin_type'] ?? null;
        $skinTypeId = $data['skin_type_id'] ?? null;

        if (!empty($data['skin_option_id'])) {
            $skinOption = QuizOption::find($data['skin_option_id']);
        }
        if (!$skinOption && $skinCode) {
            $skinOption = QuizOption::whereHas('question', fn ($q) => $q->where('step_number', 2))
                ->where('code', strtolower((string) $skinCode))
                ->first();
        }
        if (!$skinOption && $skinTypeId) {
            $skinOption = QuizOption::whereHas('question', fn ($q) => $q->where('step_number', 2))
                ->where(fn ($q) => $q->where('mapped_id', $skinTypeId)->orWhere('id', $skinTypeId))
                ->first();
        }

        if ($skinOption) {
            $skinCode = $skinCode ?: $skinOption->code;
            $skinTypeId = $skinTypeId ?: $skinOption->mapped_id;
        }

        if (!$skinCode && $skinTypeId) {
            $skinModel = SkinType::find($skinTypeId);
            if ($skinModel) {
                $skinCode = $this->mapSkinModelToCode($skinModel->name_en);
            }
        }
        if ($skinCode && !$skinTypeId) {
            $skinModel = SkinType::whereRaw('LOWER(name_en) = ?', [strtolower($skinCode)])->first();
            $skinTypeId = $skinModel?->id;
        }

        // 3. Resolve Sensitivity (boolean true / false)
        $rawSens = $nested['sensitive'] ?? $data['sensitive'] ?? $data['sensitivity'] ?? $data['is_sensitive'] ?? null;
        if (!empty($data['sensitivity_option_id'])) {
            $sensOption = QuizOption::find($data['sensitivity_option_id']);
        }
        if ($sensOption && $rawSens === null) {
            $rawSens = $sensOption->code === 'yes' || (int) $sensOption->mapped_id === 1;
        }
        $isSensitive = $this->parseSensitivityBoolean($rawSens);
        if (!$sensOption) {
            $sensOption = QuizOption::whereHas('question', fn ($q) => $q->where('step_number', 3))
                ->where('code', $isSensitive ? 'yes' : 'no')
                ->first();
        }

        // 4. Resolve Secondary Concern ('acne', 'pores', 'blackheads', 'dark_spots', 'dullness', 'redness', 'dryness', 'lines', 'oil', 'eyes', or null)
        $rawSecondary = $nested['secondary'] ?? $data['secondary'] ?? $data['q4'] ?? null;
        if (is_array($rawSecondary)) {
            $rawSecondary = $rawSecondary[0] ?? null;
        }
        $concernIds = $data['concern_ids'] ?? [];
        $secondaryCode = $secondaryOption?->code;

        if (!empty($data['secondary_option_id'])) {
            $secondaryOption = QuizOption::find($data['secondary_option_id']);
            $secondaryCode = $secondaryOption?->code;
        }

        if ($rawSecondary !== null && $rawSecondary !== '' && $rawSecondary !== 'none' && $rawSecondary !== 'null') {
            $secondaryCode = strtolower((string) $rawSecondary);
            if (!$secondaryOption) {
                $secondaryOption = QuizOption::whereHas('question', fn ($q) => $q->where('step_number', 4))
                    ->where('code', $secondaryCode)
                    ->first();
            }
        } elseif (!$secondaryOption && !empty($concernIds)) {
            foreach ($concernIds as $cid) {
                if ($cid === null || $cid === '' || $cid === 'none') {
                    continue;
                }
                $opt = QuizOption::whereHas('question', fn ($q) => $q->where('step_number', 4))
                    ->where(function ($q) use ($cid) {
                        if (is_numeric($cid)) {
                            $q->where('mapped_id', (int) $cid)->orWhere('id', (int) $cid);
                        } else {
                            $q->where('code', strtolower((string) $cid));
                        }
                    })
                    ->first();

                if ($opt && $opt->code !== 'none' && $opt->code !== $primaryCode) {
                    $secondaryOption = $opt;
                    $secondaryCode = $opt->code;
                    break;
                }

                if (is_numeric($cid)) {
                    $cModel = Concern::find((int) $cid);
                    if ($cModel) {
                        $mappedCode = $this->mapConcernModelToCode($cModel->name_en);
                        if ($mappedCode && $mappedCode !== $primaryCode) {
                            $secondaryCode = $mappedCode;
                            $secondaryOption = QuizOption::whereHas('question', fn ($q) => $q->where('step_number', 4))
                                ->where('code', $secondaryCode)
                                ->first();
                            break;
                        }
                    }
                }
            }
        }

        if ($secondaryOption && $secondaryOption->code && $secondaryOption->code !== 'none') {
            $secondaryCode = $secondaryOption->code;
            if ($secondaryOption->mapped_id && empty($concernIds)) {
                $concernIds = [$secondaryOption->mapped_id];
            }
        }

        if ($secondaryCode === 'none' || $secondaryCode === '') {
            $secondaryCode = null;
        }

        // Check if secondary equals primary
        $explicitSecondaryConflict = ($rawSecondary !== null && $rawSecondary !== 'none' && $secondaryCode === $primaryCode);
        if (!$explicitSecondaryConflict && $secondaryCode === $primaryCode) {
            // If resolved from legacy concern_ids array, automatically clear duplicate primary
            $secondaryCode = null;
        }

        if (!$secondaryOption) {
            $secondaryOption = QuizOption::whereHas('question', fn ($q) => $q->where('step_number', 4))
                ->where('code', $secondaryCode ?? 'none')
                ->first();
        }

        return [
            'answers' => [
                'primary' => $primaryCode,
                'secondary' => $secondaryCode,
                'skin' => $skinCode ? strtolower((string) $skinCode) : null,
                'sensitive' => $isSensitive,
            ],
            'explicit_secondary_conflict' => $explicitSecondaryConflict,
            'skin_type_id' => $skinTypeId,
            'routine_goal_id' => $goalId,
            'concern_ids' => array_values(array_filter($concernIds, 'is_numeric')),
            'options' => [
                1 => $primaryOption,
                2 => $skinOption,
                3 => $sensOption,
                4 => $secondaryOption,
            ],
        ];
    }

    /**
     * Save Assessment and recommend custom skincare routine using the canonical R479 Engine.
     */
    public function saveAssessment(array $data): array
    {
        $user = auth('sanctum')->user();
        $lang = request()->header('lang') ?? app()->getLocale();

        $resolved = $this->resolveAnswers($data);
        $r479Answers = $resolved['answers'];

        if (empty($r479Answers['skin'])) {
            return [
                'status' => false,
                'message' => __('messages.skin_type_required'),
            ];
        }

        if (empty($r479Answers['primary'])) {
            return [
                'status' => false,
                'message' => __('messages.routine_goal_required'),
            ];
        }

        if ($resolved['explicit_secondary_conflict']) {
            return [
                'status' => false,
                'message' => $lang === 'ar'
                    ? 'لا يمكن اختيار نفس الهدف الأساسي كمشكلة إضافية.'
                    : 'Secondary concern cannot be identical to the primary goal.',
            ];
        }

        try {
            $this->r479Engine->validateAnswers($r479Answers);
        } catch (\InvalidArgumentException $e) {
            return [
                'status' => false,
                'message' => $lang === 'ar'
                    ? 'يرجى التأكد من صحة إجابات تحليل البشرة.'
                    : 'Invalid quiz answers provided.',
            ];
        }

        // Ensure skin_type_id exists in DB for foreign key
        $skinTypeId = $resolved['skin_type_id'];
        if (!$skinTypeId) {
            $skinModel = SkinType::whereRaw('LOWER(name_en) = ?', [$r479Answers['skin']])->first() ?? SkinType::first();
            $skinTypeId = $skinModel?->id;
        }

        if ($user && $skinTypeId) {
            $user->update(['skin_type_id' => $skinTypeId]);
        }

        $scenarioKey = implode('|', [
            $r479Answers['primary'],
            $r479Answers['secondary'] ?? 'none',
            $r479Answers['skin'],
            $r479Answers['sensitive'] ? 'yes' : 'no',
        ]);

        // 1. Save Assessment
        $assessment = Assessment::create([
            'user_id'             => $user ? $user->id : null,
            'skin_type_id'        => $skinTypeId,
            'primary_goal_code'   => $r479Answers['primary'],
            'secondary_goal_code' => $r479Answers['secondary'],
            'is_sensitive'        => $r479Answers['sensitive'],
            'scenario_key'        => $scenarioKey,
        ]);

        $routine = Routine::create([
            'assessment_id' => $assessment->id,
        ]);

        if (!empty($resolved['routine_goal_id'])) {
            AssessmentGoal::create([
                'assessment_id' => $assessment->id,
                'goal_id'       => $resolved['routine_goal_id'],
            ]);
        }

        foreach ($resolved['concern_ids'] as $concernId) {
            if (Concern::where('id', $concernId)->exists()) {
                AssessmentConcern::create([
                    'assessment_id' => $assessment->id,
                    'concern_id'    => $concernId,
                ]);
            }
        }

        // Save 4 Question Answers to assessment_answers & format questions array for API response
        $questionsAndAnswers = [];
        $questionsByStep = QuizQuestion::with('options')->orderBy('step_number', 'asc')->get()->keyBy('step_number');

        foreach ([1, 2, 3, 4] as $stepNum) {
            $qModel = $questionsByStep->get($stepNum);
            $optModel = $resolved['options'][$stepNum] ?? null;

            if ($qModel && $optModel) {
                AssessmentAnswer::create([
                    'assessment_id' => $assessment->id,
                    'question_id'   => $qModel->id,
                    'answer_id'     => $optModel->id,
                ]);
            }

            if ($qModel) {
                $questionsAndAnswers[] = [
                    'question_id'      => $qModel->id,
                    'question_key'     => $qModel->key,
                    'question_title'   => $lang === 'ar' ? $qModel->title_ar : $qModel->title_en,
                    'selected_options' => $optModel ? [
                        [
                            'option_id'    => $optModel->id,
                            'code'         => $optModel->code,
                            'option_title' => $lang === 'ar' ? $optModel->title_ar : $optModel->title_en,
                            'description'  => $optModel->description,
                            'image'        => $optModel->image,
                            'option_type'  => $optModel->option_type,
                            'mapped_id'    => $optModel->mapped_id,
                        ],
                    ] : [],
                ];
            }
        }

        // 2. Query live out-of-stock SKUs from DB for R479 dynamic stock fallback
        $unavailableSkus = Product::where('stock', '<=', 0)
            ->whereNotNull('sku')
            ->pluck('sku')
            ->toArray();

        // 3. Run R479 Recommendation Engine
        $engineResult = $this->r479Engine->recommend($r479Answers, $unavailableSkus);

        // Preload Eloquent Product models for all recommended SKUs
        $allSkus = array_values(array_unique(array_merge(
            array_column($engineResult['core'], 'sku'),
            array_column($engineResult['supplements'], 'sku')
        )));

        $productsBySku = Product::with([
            'brand',
            'category',
            'subCategory',
            'marketingDetail',
            'recommendationRule',
            'reviews',
            'goals',
            'concerns',
            'images',
        ])->whereIn('sku', $allSkus)->get()->keyBy('sku');

        $stepsByOrder = RoutineStep::orderBy('order', 'asc')->get()->keyBy('order');

        // 4. Build Primary Routine Response & save RoutineProduct records
        $primaryRoutineResponse = [];
        $orderIndex = 1;
        foreach ($engineResult['core'] as $card) {
            $sku = $card['sku'];
            $product = $productsBySku->get($sku);
            if (!$product) {
                continue;
            }

            $stepOrder = $this->mapRoleToStepOrder($card['routine_step_code'] ?? ($card['roles'][0] ?? 'cleanser'));
            $stepModel = $stepsByOrder->get($stepOrder);

            RoutineProduct::create([
                'routine_id'               => $routine->id,
                'product_id'               => $product->id,
                'step'                     => $stepOrder,
                'replaced_with_product_id' => $product->id,
                'accepted'                 => true,
            ]);

            $useTimeAr = $this->mapPeriodToArabic($card['usage_policy']['period'] ?? 'both', $lang);
            $badgeAr = $this->mapBadgeLabel($card['usage_policy'] ?? [], $lang);

            $primaryRoutineResponse[] = [
                'display_order'       => $orderIndex++,
                'selected_by_default' => true,
                'step_id'             => $stepModel?->id ?? $orderIndex,
                'sku'                 => $sku,
                'roles'               => $card['roles'],
                'display_role_ar'     => $card['display_role_ar'],
                'routine_step_ar'     => $card['display_role_ar'],
                'routine_step_code'   => $card['routine_step_code'],
                'use_time_ar'         => $useTimeAr,
                'usage_badge_ar'      => $badgeAr,
                'chosen_for_ar'       => $card['chosen_for_ar'],
                'usage_short_ar'      => $card['usage_short_ar'] ?? '',
                'usage_text_ar'       => $card['usage_text_ar'] ?? '',
                'usage_tiles'         => $card['usage'] ?? [],
                'usage_policy'        => $card['usage_policy'] ?? null,
                'usage_notice_ar'     => $card['usage_notice_ar'] ?? '',
                'product'             => ProductListResource::make($product),
            ];
        }

        // 5. Build Routine Support Response (up to 3 supplements)
        $routineSupportResponse = [];
        $orderIndex = 1;
        foreach ($engineResult['supplements'] as $card) {
            $sku = $card['sku'];
            $product = $productsBySku->get($sku);
            if (!$product) {
                continue;
            }

            $useTimeAr = $this->mapPeriodToArabic($card['usage_policy']['period'] ?? 'evening', $lang);
            $badgeAr = $this->mapBadgeLabel($card['usage_policy'] ?? [], $lang);

            $routineSupportResponse[] = [
                'display_order'       => $orderIndex++,
                'selected_by_default' => false,
                'step_id'             => null,
                'sku'                 => $sku,
                'kind'                => $card['kind'] ?? 'supplement',
                'roles'               => ['supplement'],
                'display_role_ar'     => 'عناية مكمّلة',
                'routine_step_ar'     => 'عناية مكمّلة',
                'routine_step_code'   => 'supplement',
                'use_time_ar'         => $useTimeAr,
                'usage_badge_ar'      => $badgeAr,
                'chosen_for_ar'       => $card['chosen_for_ar'],
                'reason_ar'           => $card['reason_ar'] ?? '',
                'usage_short_ar'      => $card['usage_short_ar'] ?? '',
                'usage_text_ar'       => $card['usage_text_ar'] ?? '',
                'usage_tiles'         => $card['usage'] ?? [],
                'usage_policy'        => $card['usage_policy'] ?? null,
                'usage_notice_ar'     => $card['usage_notice_ar'] ?? '',
                'product'             => ProductListResource::make($product),
            ];
        }

        // 6. Build Diagnosis Summary matching R479 presentation.js profile()
        $diagnosis = $this->buildDiagnosis($r479Answers, $resolved['options'], $lang);

        $clientName = $user?->name ?? 'زائر المتجر';
        ActivityLogger::log(
            event: 'quiz_completed',
            description: "قام العميل/الزائر [{$clientName}] بإجراء تحليل البشرة والحصول على روتين مخصص (الحالة: {$scenarioKey})",
            subjectType: 'Assessment',
            subjectId: $assessment->id,
            newValues: [
                'scenario_key' => $scenarioKey,
                'routine_id'   => $routine->id,
            ],
            userId: $user?->id,
            userName: $clientName
        );

        return [
            'status' => true,
            'message' => __('messages.routine_recommended_successfully'),
            'data' => [
                'is_routine_added'   => true,
                'routine_id'         => $routine->id,
                'rules_version'      => R479EngineService::VERSION,
                'scenario_key'       => $scenarioKey,
                'selection_complete' => $engineResult['selection_complete'],
                'missing_roles'      => $engineResult['missing_roles'],
                'diagnosis'          => $diagnosis,
                'questions'          => $questionsAndAnswers,
                'primary_routine'    => $primaryRoutineResponse,
                'routine_support'    => $routineSupportResponse,
                'cart_addons'        => [],
                'usage_schedule'     => $engineResult['usage_schedule'],
            ],
        ];
    }

    protected function buildDiagnosis(array $answers, array $options, string $lang): array
    {
        $skinArMap = [
            'oily'        => 'دهنية',
            'combination' => 'مختلطة',
            'normal'      => 'عادية',
            'dry'         => 'جافة',
        ];
        $skinAr = $skinArMap[$answers['skin']] ?? 'متوازنة';
        $sensPhraseAr = $answers['sensitive'] ? 'وتميل إلى التحسّس.' : 'ولا تتحسّس بسهولة.';

        $primaryLabelAr = $options[1]?->title_ar ?? (R479EngineService::GOAL_LABELS_AR[$answers['primary']] ?? $answers['primary']);
        $secondaryLabelAr = ($answers['secondary'] && isset($options[4]))
            ? $options[4]->title_ar
            : '';

        $skinSummaryAr = "بحسب إجاباتك، بشرتك {$skinAr}، {$sensPhraseAr}";

        $badges = [];
        if ($options[1]) {
            $badges[] = $lang === 'ar' ? $options[1]->title_ar : $options[1]->title_en;
        }
        if ($options[2]) {
            $skinTitle = $lang === 'ar' ? $options[2]->title_ar : $options[2]->title_en;
            $badges[] = ($lang === 'ar' ? 'بشرة ' : '') . $skinTitle . ($lang === 'en' ? ' Skin' : '');
        }
        if ($answers['sensitive']) {
            $badges[] = $lang === 'ar' ? 'تتهيّج بسهولة' : 'Sensitive Skin';
        }
        if ($answers['secondary'] && $options[4] && $options[4]->code !== 'none') {
            $badges[] = $lang === 'ar' ? $options[4]->title_ar : $options[4]->title_en;
        }

        $goalsSummary = array_values(array_filter([
            $skinSummaryAr,
            $options[1]?->description_ar,
            ($answers['secondary'] && $options[4] && $options[4]->code !== 'none') ? $options[4]->description_ar : null,
        ]));

        return [
            'title'          => $lang === 'ar' ? 'روتينك المقترح' : 'Your Suggested Routine',
            'skin_summary'   => $skinSummaryAr,
            'focus_goal'     => $primaryLabelAr,
            'extra_concern'  => $secondaryLabelAr ?: null,
            'badges'         => array_values(array_unique($badges)),
            'goals_summary'  => $goalsSummary,
            'consultation'   => [
                'title'       => $lang === 'ar' ? 'هل تودين استشارة خبيرة؟' : 'Would you like expert consultation?',
                'description' => $lang === 'ar'
                    ? 'مستشارو KCODE متواجدون للإجابة على استفساراتك وتخصيص الروتين أكثر.'
                    : 'KCODE advisors are available to answer your questions and further customize your routine.',
            ],
        ];
    }

    protected function parseSensitivityBoolean(mixed $value): bool
    {
        if (is_bool($value)) {
            return $value;
        }
        if (is_numeric($value)) {
            return (int) $value === 1;
        }
        if (is_string($value)) {
            return in_array(strtolower(trim($value)), ['1', 'true', 'yes', 'نعم'], true);
        }
        return false;
    }

    protected function mapGoalModelToCode(string $nameEn): string
    {
        $lower = strtolower($nameEn);
        return match (true) {
            str_contains($lower, 'acne') => 'acne',
            str_contains($lower, 'blackhead') => 'blackheads',
            str_contains($lower, 'pore') => 'pores',
            str_contains($lower, 'brighten') || str_contains($lower, 'dark') || str_contains($lower, 'spot') => 'dark_spots',
            str_contains($lower, 'redness') || str_contains($lower, 'sooth') => 'redness',
            str_contains($lower, 'hydrat') || str_contains($lower, 'dry') => 'dryness',
            str_contains($lower, 'line') || str_contains($lower, 'wrinkle') => 'lines',
            str_contains($lower, 'maintain') => 'maintain',
            default => 'dullness',
        };
    }

    protected function mapSkinModelToCode(string $nameEn): string
    {
        $lower = strtolower($nameEn);
        return match (true) {
            str_contains($lower, 'oily') => 'oily',
            str_contains($lower, 'comb') => 'combination',
            str_contains($lower, 'dry') => 'dry',
            default => 'normal',
        };
    }

    protected function mapConcernModelToCode(string $nameEn): ?string
    {
        $lower = strtolower($nameEn);
        return match (true) {
            str_contains($lower, 'acne') => 'acne',
            str_contains($lower, 'blackhead') => 'blackheads',
            str_contains($lower, 'oil') && !str_contains($lower, 'pore') => 'oil',
            str_contains($lower, 'pore') => 'pores',
            str_contains($lower, 'pigment') || str_contains($lower, 'dark') => 'dark_spots',
            str_contains($lower, 'redness') || str_contains($lower, 'irritat') => 'redness',
            str_contains($lower, 'dry') || str_contains($lower, 'barrier') || str_contains($lower, 'hydrat') => 'dryness',
            str_contains($lower, 'dull') || str_contains($lower, 'glow') => 'dullness',
            str_contains($lower, 'wrinkle') || str_contains($lower, 'line') => 'lines',
            str_contains($lower, 'eye') => 'eyes',
            default => null,
        };
    }

    protected function mapRoleToStepOrder(string $roleCode): int
    {
        return match ($roleCode) {
            'cleanser'   => 3,
            'toner'      => 5,
            'treatment1' => 9,
            'treatment2' => 11,
            'moisturizer'=> 15,
            'sunscreen'  => 18,
            default      => 10,
        };
    }

    protected function mapPeriodToArabic(string $period, string $lang): string
    {
        if ($lang === 'en') {
            return match ($period) {
                'morning' => 'Morning',
                'evening' => 'Evening',
                default   => 'Morning & Evening',
            };
        }
        return match ($period) {
            'morning' => 'صباحًا',
            'evening' => 'مساءً',
            default   => 'صباحًا ومساءً',
        };
    }

    protected function mapBadgeLabel(array $policy, string $lang): string
    {
        $original = $policy['label'] ?? '';
        $family = $policy['family'] ?? 'daily';
        $gradual = in_array($family, ['retinoid', 'exfoliant', 'gradual'], true);

        if ($lang === 'en') {
            return $gradual ? 'Start gradually' : 'Daily use';
        }

        if ($gradual) {
            return 'يُبدأ بالتدرّج';
        }
        if ($original === 'يومي بحسب التعليمات' || empty($original)) {
            return 'استخدام يومي';
        }
        return $original;
    }
}
