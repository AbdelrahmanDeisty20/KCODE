<?php

require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "=== TESTING BOTH HEROICONS AND CUSTOM SVG ICONS ===\n";

$testIcons = [
    'heroicon-o-shopping-bag',
    'heroicon-o-users',
    'icon-orders',
    'icon-customer',
    'icon-category',
    'icon-blogger',
    'icon-setting',
    'icon-policy',
    'icon-service',
    'icon-partner',
    'icon-project',
    'icon-contactus',
    'icon-media',
];

foreach ($testIcons as $icon) {
    try {
        $html = svg($icon)->toHtml();
        echo "SUCCESS: {$icon} rendered correctly! Length: " . strlen($html) . "\n";
    } catch (\Exception $e) {
        echo "ERROR: {$icon} -> " . $e->getMessage() . "\n";
    }
}

echo "\nTEST COMPLETED!\n";
