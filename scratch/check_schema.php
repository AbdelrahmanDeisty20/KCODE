<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

try {
    $schema = Illuminate\Support\Facades\Schema::getColumnListing('products');
    echo 'Products table columns: ' . implode(', ', $schema) . PHP_EOL;

    $reviewCols = Illuminate\Support\Facades\Schema::getColumnListing('reviews');
    echo 'Reviews table columns: ' . implode(', ', $reviewCols) . PHP_EOL;

    $presetCols = Illuminate\Support\Facades\Schema::getColumnListing('preset_routines');
    echo 'Preset routines columns: ' . implode(', ', $presetCols) . PHP_EOL;
} catch (\Throwable $e) {
    echo 'DB connection failed in sandbox: ' . $e->getMessage() . PHP_EOL;
}
