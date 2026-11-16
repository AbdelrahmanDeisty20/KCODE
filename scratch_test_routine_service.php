<?php
require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$routineService = app(App\Services\RoutineService::class);

echo "1. Testing getPresetRoutines():\n";
try {
    $res = $routineService->getPresetRoutines();
    echo "Result status: " . ($res['status'] ? 'true' : 'false') . "\n";
    echo "Data count: " . count($res['data']) . "\n";
} catch (\Throwable $e) {
    echo "Exception: " . $e->getMessage() . " in " . $e->getFile() . ":" . $e->getLine() . "\n";
    echo $e->getTraceAsString() . "\n";
}

echo "\n2. Testing getPresetRoutineDetails(1):\n";
try {
    $res = $routineService->getPresetRoutineDetails(1);
    echo "Result status: " . ($res['status'] ? 'true' : 'false') . "\n";
    if ($res['status']) {
        echo "Title: " . ($res['data']['title'] ?? 'none') . "\n";
        echo "Items count: " . count($res['data']['items'] ?? []) . "\n";
        echo "Usage guide present: " . (!empty($res['data']['usage_guide']) ? 'yes' : 'no') . "\n";
    }
} catch (\Throwable $e) {
    echo "Exception: " . $e->getMessage() . " in " . $e->getFile() . ":" . $e->getLine() . "\n";
    echo $e->getTraceAsString() . "\n";
}
