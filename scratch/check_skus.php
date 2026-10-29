<?php

require __DIR__ . '/../vendor/autoload.php';
$app = require __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$matrix = json_decode(file_get_contents(database_path('seeders/data/quiz_results_matrix.json')), true);
$skus = collect($matrix['products'])->pluck('sku')->unique();
$missing = $skus->diff(App\Models\Product::pluck('sku'));
echo 'Total SKUs in Matrix: ' . $skus->count() . ', Missing in DB: ' . $missing->count() . PHP_EOL;
if ($missing->count() > 0) {
    echo 'Missing: ' . $missing->implode(', ') . PHP_EOL;
}
