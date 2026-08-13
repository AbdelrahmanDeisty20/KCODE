<?php

require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "=== TESTING FONTAWESOME & HEROICONS ===\n";

$testIcons = [
    'far-face-smile',
    'fas-user-gear',
    'fas-cart-shopping',
    'fas-newspaper',
    'fas-bell',
    'fas-tag',
    'fas-ticket',
    'fas-shield-halved',
    'heroicon-o-shopping-bag',
    'heroicon-o-users',
];

foreach ($testIcons as $icon) {
    try {
        $html = svg($icon)->toHtml();
        echo "SUCCESS: {$icon} rendered! Length: " . strlen($html) . "\n";
    } catch (\Exception $e) {
        echo "ERROR: {$icon} -> " . $e->getMessage() . "\n";
    }
}

echo "\nTEST COMPLETED!\n";
