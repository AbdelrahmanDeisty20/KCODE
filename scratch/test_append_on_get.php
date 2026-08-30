<?php

require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$service = new App\Services\RoutineService();

echo "Call 1 - Total routines: ";
$res1 = $service->getPresetRoutines();
echo $res1['pagination']['total'] . "\n";

echo "Call 2 - Total routines: ";
$res2 = $service->getPresetRoutines();
echo $res2['pagination']['total'] . "\n";
