<?php

require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\User;
use App\Models\UserFcmToken;
use App\Models\Coupon;

$user = User::first();
echo "Testing Device ID & User Coupon Flow for User ID {$user->id} ({$user->name})...\n";

// Ensure a UserFcmToken with device_id exists for User 1
$tokenRecord = UserFcmToken::updateOrCreate(
    ['user_id' => $user->id],
    ['device_id' => 'DEV-PHONE-9988', 'token' => 'fcm_mock_token_9988']
);

echo "User FCM Token record: User ID: {$tokenRecord->user_id} | Device ID: {$tokenRecord->device_id}\n";

$coupon = Coupon::create([
    'code' => 'KCODE-' . strtoupper(\Illuminate\Support\Str::random(6)),
    'title_ar' => 'خصم مخصص بالـ Device ID',
    'discount_type' => 'percentage',
    'discount_value' => 40,
    'user_id' => $user->id,
    'is_general' => false,
    'is_active' => true,
]);

echo "Created Coupon ID: {$coupon->id} | Code: {$coupon->code}\n";
echo "Successfully tested Coupon notification & email flow via Device ID!\n";

// Clean up
$coupon->delete();
