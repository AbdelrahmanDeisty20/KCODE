<?php
require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$expected = [
    'KC0011' => 'مناسب للبشرة الحساسة',
    'KC0023' => 'مناسب للبشرة الحساسة',
    'KC0063' => 'مناسب للبشرة الحساسة',
    'KC0068' => 'مناسب للبشرة الحساسة',
    'KC0084' => 'مناسب للبشرة الحساسة',
    'KC0089' => 'مناسب للبشرة الحساسة',
    'KC0127' => 'مناسب للبشرة الحساسة',
    'KC0129' => 'مناسب للبشرة الحساسة',
    'KC0145' => 'مناسب للبشرة الحساسة',
    'KC0153' => 'مناسب للبشرة الحساسة',
    'KC0158' => 'مناسب للبشرة الحساسة',
    'KC0178' => 'مناسب للبشرة الحساسة',
    'KC0054' => 'مناسب بشرط',
    'KC0122' => 'غير مناسب كخطوة أولى للبشرة الحساسة',
    'KC0124' => 'غير مناسب كخطوة أولى للبشرة الحساسة',
];

$allPassed = true;
foreach ($expected as $sku => $expectedStatus) {
    $p = new App\Models\Product();
    $p->sku = $sku;
    $actual = $p->sensitive_status;
    $match = ($actual === $expectedStatus);
    if (!$match) $allPassed = false;
    echo "SKU {$sku}: {$actual} " . ($match ? "✅" : "❌ (Expected: {$expectedStatus})") . "\n";
}

if ($allPassed) {
    echo "\n🎉 ALL 15 F13 SENSITIVE STATUSES VERIFIED 100%!\n";
} else {
    echo "\n❌ F13 VERIFICATION FAILED!\n";
}
