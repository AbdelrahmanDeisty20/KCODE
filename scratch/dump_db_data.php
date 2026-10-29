<?php

require __DIR__ . '/../vendor/autoload.php';
$app = require __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$quiz = App\Models\QuizQuestion::with('options')->orderBy('step_number')->get();
file_put_contents(__DIR__ . '/db_quiz.json', $quiz->toJson(JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));

$goals = App\Models\RoutineGoal::all();
file_put_contents(__DIR__ . '/db_goals.json', $goals->toJson(JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));

$presets = App\Models\PresetRoutine::with('items.product')->get();
file_put_contents(__DIR__ . '/db_presets.json', $presets->toJson(JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));

$concerns = App\Models\Concern::all();
file_put_contents(__DIR__ . '/db_concerns.json', $concerns->toJson(JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));

$skinTypes = App\Models\SkinType::all();
file_put_contents(__DIR__ . '/db_skin_types.json', $skinTypes->toJson(JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));

echo "DUMP_SUCCESS";
