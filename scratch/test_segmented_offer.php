<?php

require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Offer;
use App\Models\Product;
use App\Observers\OfferObserver;

$product = Product::first();
if (!$product) {
    echo "No product found\n";
    exit;
}

$offer = new Offer([
    'product_id' => $product->id,
    'discount_percentage' => 20,
    'is_active' => true,
]);

echo "Testing segmented OfferObserver...\n";
$observer = new OfferObserver();

$reflection = new ReflectionClass($observer);
$method = $reflection->getMethod('sendOfferNotification');
$method->setAccessible(true);
$method->invoke($observer, $offer);

echo "Completed segmented notification test successfully!\n";
