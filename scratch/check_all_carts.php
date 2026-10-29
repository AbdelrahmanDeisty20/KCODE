<?php

require __DIR__ . '/../vendor/autoload.php';
$app = require __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo 'Total Carts in DB: ' . App\Models\Cart::count() . PHP_EOL;
foreach (App\Models\Cart::with('items', 'user')->get() as $c) {
    echo "Cart ID: {$c->id}, User ID: {$c->user_id} ({$c->user?->name}), Session: {$c->session_id}, Coupon: {$c->coupon_code}, Items Count: " . $c->items->count() . PHP_EOL;
    foreach ($c->items as $it) {
        echo "   - Product ID: {$it->product_id}, Qty: {$it->quantity}, Price: {$it->total_price}" . PHP_EOL;
    }
}

echo PHP_EOL . 'Recent Orders for User 42:' . PHP_EOL;
foreach (App\Models\Order::where('user_id', 42)->latest()->take(5)->get() as $ord) {
    echo "Order #{$ord->id}: {$ord->order_number}, User: {$ord->user_id}, Total: {$ord->total}, Status: {$ord->order_status}, Created: {$ord->created_at}" . PHP_EOL;
}

