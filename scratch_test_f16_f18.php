<?php
require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

app()->setLocale('ar');

$p = new App\Models\Product();
$p->name_ar = 'واقي شمس كوري';
$p->role_ar = 'Sunscreen';

$specs = $p->specifications;
echo "Specifications Type: " . $specs['type'] . "\n";
echo "Specifications Texture: " . $specs['texture'] . "\n";
echo "Specifications Fragrance: " . $specs['fragrance'] . "\n";
echo "SEO Image Alt: " . $p->image_alt . "\n";

$passSpecs = ($specs['type'] === 'واقي شمس') && !empty($specs['texture']) && !empty($specs['fragrance']);
$passAlt = ($p->image_alt === 'واقي شمس كوري');

if ($passSpecs && $passAlt) {
    echo "🎉 F16 & F17/F18 VERIFIED 100%!\n";
} else {
    echo "❌ F16/F18 TEST FAILED!\n";
}
