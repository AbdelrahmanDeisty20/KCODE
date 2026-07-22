<?php

require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$res = app(App\Services\QuizService::class)->saveAssessment([
    'skin_type_id' => 1,
    'routine_goal_id' => 1,
    'concern_ids' => [1]
]);

$resource = new App\Http\Resources\API\QUIZ\QuizResource($res['data']);
echo json_encode($resource->toArray(request()), JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
