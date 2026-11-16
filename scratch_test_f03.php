<?php
require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$service = app(App\Services\GroqChatService::class);

echo "========================================================\n";
echo "TEST 1: Querying KC0159 (COSRX Sunscreen)\n";
echo "========================================================\n";

$res1 = $service->ask("ما هو منتج KC0159 وكيف أستخدمه لبشرتي الدهنية؟", [], 'ar');
echo "Reply:\n" . $res1['reply'] . "\n\n";

$pass1 = (
    str_contains($res1['reply'], 'KC0159') &&
    (str_contains($res1['reply'], 'COSRX') || str_contains($res1['reply'], 'واقي شمس')) &&
    str_contains($res1['reply'], 'صباح') &&
    str_contains($res1['reply'], 'ساعتين') &&
    !str_contains($res1['reply'], 'غسول رغوي يُشطف')
);
echo "Test 1 Passed: " . ($pass1 ? "YES ✅" : "NO ❌") . "\n\n";

echo "========================================================\n";
echo "TEST 2: Querying non-existent SKU (KC9999)\n";
echo "========================================================\n";

$res2 = $service->ask("عايز أعرف طريقة استخدام منتج KC9999", [], 'ar');
echo "Reply:\n" . $res2['reply'] . "\n\n";

$pass2 = (
    str_contains($res2['reply'], 'KC9999') &&
    (str_contains($res2['reply'], 'غير مسجل') || str_contains($res2['reply'], 'غير موجود')) &&
    !str_contains($res2['reply'], 'اغسل وجهك')
);
echo "Test 2 Passed: " . ($pass2 ? "YES ✅" : "NO ❌") . "\n\n";

echo "========================================================\n";
echo "TEST 3: General Sunscreen inquiry\n";
echo "========================================================\n";

$res3 = $service->ask("إزاي أستخدم واقي الشمس؟", [], 'ar');
echo "Reply:\n" . $res3['reply'] . "\n\n";

$pass3 = (
    str_contains($res3['reply'], 'صباح') &&
    str_contains($res3['reply'], 'ساعتين')
);
echo "Test 3 Passed: " . ($pass3 ? "YES ✅" : "NO ❌") . "\n\n";

if ($pass1 && $pass2 && $pass3) {
    echo "🎉 ALL F03 ACCEPTANCE TESTS PASSED 100%!\n";
} else {
    echo "❌ SOME TESTS FAILED!\n";
}
