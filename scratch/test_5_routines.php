<?php

require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$service = new App\Services\RoutineService();
$countBefore = App\Models\PresetRoutine::count();

$res = $service->generatePresetRoutines();
$countAfter = App\Models\PresetRoutine::count();

echo "Count Before: {$countBefore}\n";
echo "Count After: {$countAfter}\n";
echo "Diff Added: " . ($countAfter - $countBefore) . "\n";
