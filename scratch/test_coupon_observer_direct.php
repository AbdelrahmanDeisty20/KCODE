<?php

require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\User;
use App\Models\Coupon;

$user = User::where('email', 'abdeisty33@gmail.com')->first();
if (!$user) {
    $user = User::first();
}

echo "Testing CouponObserver direct email for User ID {$user->id} ({$user->email})...\n";

$coupon = Coupon::create([
    'code' => 'KCODE-' . strtoupper(\Illuminate\Support\Str::random(6)),
    'title_ar' => 'خصم مباشر تجريبي',
    'discount_type' => 'percentage',
    'discount_value' => 35,
    'user_id' => $user->id,
    'is_general' => false,
    'is_active' => true,
]);

echo "Created Coupon ID: {$coupon->id} | Code: {$coupon->code}\n";
echo "Email sent directly to {$user->email}!\n";

// Clean up
$coupon->delete();
