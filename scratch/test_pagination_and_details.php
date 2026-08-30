<?php

require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$service = new App\Services\RoutineService();

// 1. Test Paginated List
echo "=== TESTING PAGINATED LIST (page 1, per_page 10) ===\n";
$listResult = $service->getPresetRoutines();
echo "Total Routines in DB: " . $listResult['pagination']['total'] . "\n";
echo "Per Page: " . $listResult['pagination']['per_page'] . "\n";
echo "Current Page: " . $listResult['pagination']['current_page'] . "\n";
echo "Last Page: " . $listResult['pagination']['last_page'] . "\n";
echo "Returned Routines Count: " . count($listResult['data']) . "\n";

foreach ($listResult['data'] as $r) {
    echo "- Routine ID: {$r['id']} | Title: {$r['title']} | Items: {$r['products_count']}\n";
}

// 2. Test Single Routine Details
echo "\n=== TESTING SINGLE ROUTINE DETAILS (ID 5) ===\n";
$detailsResult = $service->getPresetRoutineDetails(5);
if ($detailsResult['status']) {
    $r = $detailsResult['data'];
    echo "Routine ID: {$r['id']}\n";
    echo "Title: {$r['title']}\n";
    echo "Badge: {$r['badge']}\n";
    echo "Skin Type: {$r['skin_type']}\n";
    echo "Goal: {$r['goal']}\n";
    echo "Total Price: {$r['total_price']}\n";
    echo "Products Count: {$r['products_count']}\n";
    echo "Items Preview:\n";
    foreach ($r['items'] as $item) {
        echo "  Step {$item['display_order']}: {$item['product']['name']} ({$item['product']['price']} USD)\n";
    }
} else {
    echo "Error: " . $detailsResult['message'] . "\n";
}
