<?php

require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$goals = App\Models\RoutineGoal::all();
foreach ($goals as $g) {
    echo "ID: {$g->id} | Name: {$g->name_ar}\n";
}
