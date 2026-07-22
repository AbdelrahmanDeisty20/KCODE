<?php

require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$req = new App\Http\Requests\API\ROUTINES\RemoveProductRequest([
    'product_id' => 94,
    'routine_id' => 4,
]);

$controller = app(App\Http\Controllers\API\Genral\RoutineController::class);
$res = $controller->removeProduct($req);

echo json_encode($res->getData(), JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
