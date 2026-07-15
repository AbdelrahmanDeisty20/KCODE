<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

echo "Total products: " . \App\Models\Product::count() . "\n";
echo "Best sellers: " . \App\Models\Product::where('is_best_seller', true)->count() . "\n";
