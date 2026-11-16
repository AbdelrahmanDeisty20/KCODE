<?php
require 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$p = new App\Models\Product();
$p->sku = 'KC0104';
$p->usage_instructions_json = null;

$ui = $p->usage_instructions;

if (!empty($ui['tiles']) && count($ui['tiles']) === 4) {
    echo "SUCCESS: KC0104 loaded " . count($ui['tiles']) . " tiles from catalog!\n";
    echo "Tile 1 Label: " . $ui['timing_label'] . "\n";
    echo "Tile 1 Text: " . $ui['timing'] . "\n";
    echo "Tile 2 Label: " . $ui['amount_label'] . "\n";
    echo "Tile 2 Text: " . $ui['amount'] . "\n";
    echo "Tile 3 Label: " . $ui['application_label'] . "\n";
    echo "Tile 3 Text: " . $ui['application_method'] . "\n";
    echo "Tile 4 Label: " . $ui['gradual_start_label'] . "\n";
    echo "Tile 4 Text: " . $ui['gradual_start'] . "\n";
} else {
    echo "FAILED: tiles empty or not 4\n";
    var_dump($ui);
}
