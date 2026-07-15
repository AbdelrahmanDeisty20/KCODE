<?php

namespace App\Http\Controllers\API\Genral;

use App\Http\Controllers\Controller;
use App\Models\QuizQuestion;
use App\Http\Resources\API\QUIZ\QuizQuestionResource;
use App\Http\Resources\API\QUIZ\QuizResource;
use App\Http\Requests\API\QUIZ\EvaluateQuizRequest;
use App\Services\QuizService;
use App\Traits\ApiResponse;

class QuizController extends Controller
{
    use ApiResponse;
    protected $quizService;

    public function __construct(QuizService $quizService)
    {
        $this->quizService = $quizService;
    }

    /**
     * Get all quiz questions with options.
     */
    public function getQuestions()
    {
       $questions = $this->quizService->getquestions();
       if(!$questions['status'])
       {
        return $this->error($questions['message']);
       }

       return $this->success($questions['data'], $questions['message']);
    }

    /**
     * Submit quiz answers, save the assessment, and recommend custom skincare routine.
     */
    public function evaluate(EvaluateQuizRequest $request)
    {
        $result = $this->quizService->saveAssessment($request->validated());

        if (!$result['status']) {
            return $this->error($result['message']);
        }

        return $this->success(new QuizResource($result['data']), $result['message']);
    }
}
