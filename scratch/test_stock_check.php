<?php

require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$user = App\Models\User::first();
auth('sanctum')->setUser($user);

// Get a product and set its stock to 0 temporarily to test
$product = App\Models\Product::first();
$originalStock = $product->stock;
$product->update(['stock' => 0]);

$service = new App\Services\CartService();
$res = $service->addProductsToCart([$product->id]);

// Restore stock
$product->update(['stock' => $originalStock]);

echo json_encode($res, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
