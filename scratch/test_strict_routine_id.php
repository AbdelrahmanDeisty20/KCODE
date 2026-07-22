<?php

require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$user = App\Models\User::first();
auth('sanctum')->setUser($user);

$service = new App\Services\RoutineService();

// Try deleting product 94 from routine 9999 (where it does NOT belong)
$resWrongRoutine = $service->removeProduct(94, 9999);

echo json_encode([
    'result_when_deleting_from_wrong_routine_id' => $resWrongRoutine,
], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
