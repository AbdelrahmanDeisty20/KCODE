<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$controller = app(\App\Http\Controllers\API\Genral\QuizController::class);

echo "--- TESTING BEST SELLERS ONLY RESPONSE ---\n";
// Clean DB tables
\App\Models\RoutineProduct::query()->delete();
\App\Models\Routine::query()->delete();
\App\Models\AssessmentConcern::query()->delete();
\App\Models\AssessmentGoal::query()->delete();
\App\Models\Assessment::query()->delete();

$req = \App\Http\Requests\API\QUIZ\EvaluateQuizRequest::create('/api/quiz/submit-answers', 'POST', [
    'skin_type_id' => 1,
    'routine_goal_id' => 5,
    'concern_ids' => [1]
]);
$req->setContainer($app);
$req->setRedirector($app->make(\Illuminate\Routing\Redirector::class));
$req->validateResolved();

$resp = $controller->evaluate($req);
echo "Status: " . $resp->getStatusCode() . "\n";
$data = $resp->getData()->data;
echo "Number of steps returned: " . count($data) . "\n";
foreach ($data as $item) {
    echo "Step: {$item->step_name} -> Product: [{$item->product->id}] {$item->product->name} (Price: {$item->product->price})\n";
}
