<?php

require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$service = new App\Services\RoutineService();

// Try deleting product 9999 which does NOT exist in routine 1
$res1 = $service->removeProduct(9999, 1);

echo json_encode([
    'attempt_deleting_non_existing_product' => $res1,
], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
