<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$p = App\Models\Product::where('sku', 'KC0159')->first();
if ($p) {
    echo "Found KC0159 in DB:\n";
    echo "ID: " . $p->id . "\n";
    echo "Name EN: " . $p->name_en . "\n";
    echo "Name AR: " . $p->name_ar . "\n";
    echo "How to use AR: " . $p->how_to_use_ar . "\n";
    echo "Usage frequency AR: " . $p->usage_frequency_ar . "\n";
    echo "Category: " . ($p->category ? $p->category->name_ar : 'none') . "\n";
} else {
    echo "KC0159 not found in DB\n";
}
