<?php

require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$service = new App\Services\RoutineService();
$res = $service->removeProduct(94, 4);

echo json_encode($res, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
