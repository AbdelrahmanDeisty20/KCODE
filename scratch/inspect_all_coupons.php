<?php

require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Coupon;
use App\Models\User;
use App\Models\NewsletterSubscription;

echo "=== ALL COUPONS IN DB ===\n";
$coupons = Coupon::with('user')->latest()->get();
echo "Total Coupons count: " . $coupons->count() . "\n";
foreach ($coupons as $c) {
    echo "ID: {$c->id} | Code: {$c->code} | Is General: " . ($c->is_general ? 'YES' : 'NO') . " | User ID: " . ($c->user_id ?? 'NULL') . " | Target User: " . ($c->user?->name ?? 'None') . " | User Email: " . ($c->user?->email ?? 'None') . " | Created At: {$c->created_at}\n";
}

echo "\n=== ALL NEWSLETTER SUBSCRIPTIONS ===\n";
$subs = NewsletterSubscription::all();
echo "Total Subscriptions count: " . $subs->count() . "\n";
foreach ($subs as $s) {
    echo "ID: {$s->id} | Email: {$s->email} | Active: " . ($s->is_active ? 'YES' : 'NO') . "\n";
}
