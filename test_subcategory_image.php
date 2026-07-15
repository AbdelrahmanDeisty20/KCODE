<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$subcategories = App\Models\SubCategory::limit(3)->get();
foreach ($subcategories as $sub) {
    echo "Subcategory ID: " . $sub->id . "\n";
    echo "Name: " . $sub->name . "\n";
    echo "Database image field: " . $sub->image . "\n";
    echo "Resolved image_path: " . $sub->image_path . "\n";
    echo "----------------------------------------\n";
}
