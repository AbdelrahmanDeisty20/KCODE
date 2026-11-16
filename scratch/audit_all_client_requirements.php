<?php
require 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "=== AUDIT OF ALL CLIENT REQUIREMENTS ===" . PHP_EOL;

// 1. Check men_routines_data.json
$menData = json_decode(file_get_contents('database/seeders/data/men_routines_data.json'), true);
echo "Men routines count: " . count($menData) . PHP_EOL;
foreach ($menData as $m) {
    echo "Men Routine [{$m['slug']}]: " . $m['title_ar'] . " | Steps: " . count($m['steps']) . " | Has usage_guide_json: " . (isset($m['usage_guide_json']) ? 'YES' : 'NO') . PHP_EOL;
}

// 2. Check preset_routines_data.json
$presetData = json_decode(file_get_contents('database/seeders/data/preset_routines_data.json'), true);
echo PHP_EOL . "Preset routines count: " . count($presetData) . PHP_EOL;
foreach ($presetData as $p) {
    echo "Preset Routine [{$p['slug']}]: " . $p['title_ar'] . " | Steps: " . count($p['items']) . " | Has usage_guide_json: " . (isset($p['usage_guide_json']) ? 'YES' : 'NO') . PHP_EOL;
}

// 3. Check DB Preset Routines (both general and men)
echo PHP_EOL . "DB Preset Routines:" . PHP_EOL;
$dbPresets = \App\Models\PresetRoutine::all();
foreach ($dbPresets as $pr) {
    $isMen = $pr->is_for_men ? 'MEN' : 'GENERAL';
    $hasUg = !empty($pr->usage_guide_json) ? 'YES' : 'NO';
    echo "ID {$pr->id} | Slug: {$pr->slug} | [{$isMen}] | Title: {$pr->title_ar} | usage_guide: {$hasUg}" . PHP_EOL;
}

// 4. Check Product Sensitive Status for the 15 products
echo PHP_EOL . "Checking 15 sensitive products:" . PHP_EOL;
$checkSkus = [
    'KC0011', 'KC0023', 'KC0063', 'KC0068', 'KC0084', 'KC0089',
    'KC0127', 'KC0129', 'KC0145', 'KC0153', 'KC0158', 'KC0178',
    'KC0054', 'KC0122', 'KC0124'
];
foreach ($checkSkus as $sku) {
    $prod = \App\Models\Product::where('sku', $sku)->first();
    if ($prod) {
        echo "{$sku}: status='{$prod->sensitive_status}' | eligible=" . ($prod->sensitive_eligible ? 'true' : 'false') . PHP_EOL;
    } else {
        echo "{$sku}: NOT FOUND IN DB" . PHP_EOL;
    }
}

// 5. Check Product Usage Instructions
echo PHP_EOL . "Checking sample product usage instructions:" . PHP_EOL;
$sampleProd = \App\Models\Product::where('sku', 'KC0001')->first();
if ($sampleProd) {
    $ui = $sampleProd->usage_instructions;
    echo "KC0001 usage_instructions: " . (is_array($ui) ? 'Array with ' . count($ui) . ' keys' : 'NULL') . PHP_EOL;
    if (is_array($ui)) {
        echo "  timing: " . ($ui['timing'] ?? 'null') . PHP_EOL;
        echo "  amount: " . ($ui['amount'] ?? 'null') . PHP_EOL;
        echo "  application_method: " . ($ui['application_method'] ?? 'null') . PHP_EOL;
        echo "  gradual_start: " . ($ui['gradual_start'] ?? 'null') . PHP_EOL;
        echo "  tiles count: " . count($ui['tiles'] ?? []) . PHP_EOL;
    }
}
