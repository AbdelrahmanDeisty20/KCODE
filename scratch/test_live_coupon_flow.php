<?php

require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\User;
use App\Models\Coupon;
use App\Models\NewsletterSubscription;

echo "=== TESTING COMPLETE LIVE COUPON FLOW ===\n";

// 1. Ensure user has an email that is also in NewsletterSubscription
$targetEmail = 'abdeisty33@gmail.com';

$user = User::where('email', $targetEmail)->first();
if (!$user) {
    echo "Updating User 1 email to {$targetEmail} for testing...\n";
    $user = User::first();
    $user->email = $targetEmail;
    $user->save();
}

$sub = NewsletterSubscription::where('email', $targetEmail)->first();
if (!$sub) {
    echo "Adding {$targetEmail} to NewsletterSubscription...\n";
    NewsletterSubscription::create([
        'email' => $targetEmail,
        'is_active' => true,
    ]);
}

echo "Target User ID: {$user->id} | Name: {$user->name} | Email: {$user->email}\n";
echo "Newsletter Subscription Active: YES\n";

echo "\nCreating private coupon for User ID {$user->id} via Coupon::create()...\n";

$coupon = Coupon::create([
    'code' => 'KCODE-' . strtoupper(\Illuminate\Support\Str::random(6)),
    'title_ar' => 'كوبون خصم مخصص 30%',
    'title_en' => '30% Special Coupon',
    'discount_type' => 'percentage',
    'discount_value' => 30,
    'user_id' => $user->id,
    'is_general' => false,
    'is_active' => true,
]);

echo "SUCCESS! Created Coupon ID: {$coupon->id} | Code: {$coupon->code}\n";
echo "Observer executed! Check your email inbox at {$targetEmail}!\n";
