<?php

require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Services\NewsletterService;
use App\Models\NewsletterSubscription;

$service = new NewsletterService();

$email = 'visitor_test@example.com';
$deviceId = 'DEV-VISITOR-7711';

echo "Testing Newsletter Subscription with device_id: {$deviceId}...\n";

// Cleanup existing test record if any
NewsletterSubscription::where('email', $email)->delete();

$res = $service->subscribe($email, $deviceId);

echo "Status: " . ($res['status'] ? 'SUCCESS' : 'FAILED') . " | Message: {$res['message']}\n";

$sub = NewsletterSubscription::where('email', $email)->first();
echo "Saved Subscription ID: {$sub->id} | Email: {$sub->email} | Device ID: {$sub->device_id}\n";

// Cleanup
$sub->delete();
echo "Test completed cleanly!\n";
