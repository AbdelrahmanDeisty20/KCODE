<?php

require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';

$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Product;
use App\Models\FinalRoutine;
use App\Http\Resources\API\QUIZ\FinalRoutineResource;
use Illuminate\Support\Facades\DB;

echo "=== 1. Testing Product Eager Loading ===\n";
DB::enableQueryLog();

$product = Product::with(['skinTypes', 'concerns', 'goals', 'brand'])->first();
if ($product) {
    echo "Product Name: " . $product->name . "\n";
    echo "Skin Types Count: " . $product->skinTypes->count() . "\n";
    echo "Concerns Count: " . $product->concerns->count() . "\n";
    echo "Goals Count: " . $product->goals->count() . "\n";
} else {
    echo "No product found.\n";
}

$queries = DB::getQueryLog();
echo "Queries executed for Product Eager Load: " . count($queries) . "\n";
foreach ($queries as $q) {
    echo " - SQL: " . $q['query'] . "\n";
}

echo "\n=== 2. Testing FinalRoutineResource (N+1 Verification) ===\n";
DB::flushQueryLog();

$finalRoutine = FinalRoutine::with([
    'products.routineStep',
    'products.product.brand',
    'products.product.routines'
])->first();

if ($finalRoutine) {
    $routineProducts = $finalRoutine->products->sortBy('step')->values();
    $routineProducts->each(function ($item, $index) {
        $item->temp_sequence_order = $index + 1;
    });

    $resourceData = FinalRoutineResource::collection($routineProducts)->resolve();
    echo "Routine items resolved count: " . count($resourceData) . "\n";
    
    // Output sample item to verify structure
    if (!empty($resourceData)) {
        echo "Sample Item step_name: " . ($resourceData[0]['step_name'] ?? 'N/A') . "\n";
        echo "Sample Item is_core: " . ($resourceData[0]['is_core'] ? 'true' : 'false') . "\n";
    }
} else {
    echo "No FinalRoutine found in database.\n";
}

$routineQueries = DB::getQueryLog();
echo "Queries executed during FinalRoutine Serialization: " . count($routineQueries) . "\n";
foreach ($routineQueries as $q) {
    echo " - SQL: " . $q['query'] . "\n";
}

echo "\nDone!\n";
