<?php

require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';

$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$user = \App\Models\User::first();
if (!$user) {
    echo "No user found.\n";
    exit;
}

auth('sanctum')->setUser($user);

// Add some test points to the user's ledger if empty
if ($user->loyaltyLedger()->count() === 0) {
    \App\Models\LoyaltyPointsLedger::create([
        'user_id'        => $user->id,
        'points'         => 780,
        'source_type'    => 'test',
        'source_id'      => 1,
        'description_ar' => 'نقاط ترحيبية وتجريبية',
        'description_en' => 'Welcome and test points',
    ]);
}

$loyaltyController = app(\App\Http\Controllers\API\Genral\LoyaltyController::class);

echo "1. Testing getLoyaltyProfile...\n";
$resp1 = $loyaltyController->getLoyaltyProfile();
echo "Response status: " . $resp1->getStatusCode() . "\n";
echo "Content: " . json_encode($resp1->getData(), JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT) . "\n";

echo "\n2. Testing getLoyaltyLevels...\n";
$resp2 = $loyaltyController->getLoyaltyLevels();
echo "Response status: " . $resp2->getStatusCode() . "\n";
echo "Content: " . json_encode($resp2->getData(), JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT) . "\n";
