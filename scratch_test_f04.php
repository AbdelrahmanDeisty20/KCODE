<?php
require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$engine = app(App\Services\R479EngineService::class);

echo "========================================================\n";
echo "TEST F04: Dynamic Supplement Schedule Recalculation\n";
echo "========================================================\n";

$answers = [
    'primary' => 'acne',
    'secondary' => null,
    'skin' => 'oily',
    'sensitive' => false,
];

// 1. Recommend without supplements (default)
$resDefault = $engine->recommend($answers, [], []);
$coreSkus = array_column($resDefault['core'], 'sku');
$eveningDefault = array_column($resDefault['usage_schedule']['evening']['steps'], 'sku');
echo "Core SKUs: " . implode(', ', $coreSkus) . "\n";
echo "Default Evening steps: " . implode(', ', $eveningDefault) . "\n";

$pass1 = !in_array('KC0185', $eveningDefault, true);
echo "Default excludes KC0185: " . ($pass1 ? "YES ✅" : "NO ❌") . "\n\n";

// 2. Recommend WITH KC0185 supplement selected
$resWithSupp = $engine->recommend($answers, [], ['KC0185']);
$eveningWithSupp = array_column($resWithSupp['usage_schedule']['evening']['steps'], 'sku');
echo "With KC0185 selected Evening steps: " . implode(', ', $eveningWithSupp) . "\n";

$pass2 = in_array('KC0185', $eveningWithSupp, true);
echo "Recalculated schedule includes KC0185: " . ($pass2 ? "YES ✅" : "NO ❌") . "\n";

// Verify step details for KC0185
$kc185Step = null;
foreach ($resWithSupp['usage_schedule']['evening']['steps'] as $s) {
    if ($s['sku'] === 'KC0185') {
        $kc185Step = $s;
        break;
    }
}
echo "KC0185 Display Role: " . ($kc185Step['display_role_ar'] ?? 'none') . "\n";
echo "KC0185 Usage text: " . ($kc185Step['usage_short_ar'] ?? 'none') . "\n";

$pass3 = $kc185Step && !empty($kc185Step['usage_short_ar']);
echo "KC0185 step has instructions: " . ($pass3 ? "YES ✅" : "NO ❌") . "\n\n";

// 3. Recalculate directly via buildScheduleFromSkus
$skusWith = ['KC0003', 'KC0259', 'KC0002', 'KC0088', 'KC0159', 'KC0185'];
$schedWith = $engine->buildScheduleFromSkus($skusWith);
$evWith = array_column($schedWith['evening']['steps'], 'sku');

$skusWithout = ['KC0003', 'KC0259', 'KC0002', 'KC0088', 'KC0159'];
$schedWithout = $engine->buildScheduleFromSkus($skusWithout);
$evWithout = array_column($schedWithout['evening']['steps'], 'sku');

$pass4 = in_array('KC0185', $evWith, true) && !in_array('KC0185', $evWithout, true);
echo "Direct recalculation (add & remove): " . ($pass4 ? "PASSED ✅" : "FAILED ❌") . "\n\n";

if ($pass1 && $pass2 && $pass3 && $pass4) {
    echo "🎉 ALL F04 ACCEPTANCE CRITERIA MET 100%!\n";
} else {
    echo "❌ SOME TESTS FAILED!\n";
}
