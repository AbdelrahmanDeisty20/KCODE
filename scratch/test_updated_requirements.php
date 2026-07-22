<?php

require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$user = App\Models\User::first();
auth('sanctum')->setUser($user);

// 1. Test Quiz submission response without routine_id
$quizRes = app(App\Services\QuizService::class)->saveAssessment([
    'skin_type_id' => 1,
    'routine_goal_id' => 1,
    'concern_ids' => [1]
]);
$quizOutput = (new App\Http\Resources\API\QUIZ\QuizResource($quizRes['data']))->toArray(request());

// 2. Test Confirm Routine with routine_id
$confirmRes = app(App\Services\RoutineService::class)->saveFinalRoutine(1);

// 3. Test Remove Product with routine_id
$removeRes = app(App\Services\RoutineService::class)->removeProduct(106, 1);

echo json_encode([
    'quiz_output_has_routine_id' => array_key_exists('routine_id', $quizOutput),
    'confirm_result' => $confirmRes,
    'remove_result' => $removeRes,
], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
