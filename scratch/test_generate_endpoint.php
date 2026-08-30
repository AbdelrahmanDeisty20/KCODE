<?php

require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$service = new App\Services\RoutineService();
$result = $service->generatePresetRoutines();

echo "Generated Routines Count: " . count($result['data']) . "\n";
foreach ($result['data'] as $r) {
    echo "- ID: {$r['id']} | Title: {$r['title']} | Products Count: {$r['products_count']}\n";
}
