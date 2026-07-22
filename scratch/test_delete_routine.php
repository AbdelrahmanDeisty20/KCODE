<?php

require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$user = App\Models\User::first();
auth('sanctum')->setUser($user);

// Test deleting routine 9999 which does NOT exist
$req1 = new App\Http\Requests\API\ROUTINES\DeleteRoutineRequest(['routine_id' => 9999]);
$controller = app(App\Http\Controllers\API\Genral\RoutineController::class);
$res1 = $controller->deleteRoutine($req1);

echo json_encode([
    'deleting_non_existing_routine' => $res1->getData(),
], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
