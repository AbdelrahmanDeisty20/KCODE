<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

// Link fine lines products (anti-aging, wrinkle, peptide, retinol) to Concern 8
$fineLineProducts = App\Models\Product::where('name_ar', 'LIKE', '%تجاعيد%')
    ->orWhere('name_ar', 'LIKE', '%خطوط%')
    ->orWhere('name_ar', 'LIKE', '%شد%')
    ->orWhere('name_en', 'LIKE', '%wrinkle%')
    ->orWhere('name_en', 'LIKE', '%aging%')
    ->orWhere('name_en', 'LIKE', '%retinol%')
    ->orWhere('name_en', 'LIKE', '%peptide%')
    ->pluck('id');

foreach ($fineLineProducts as $pId) {
    Illuminate\Support\Facades\DB::table('product_concerns')->insertOrIgnore([
        'product_id' => $pId,
        'concern_id' => 8,
        'created_at' => now(),
        'updated_at' => now(),
    ]);
}

// Link eye products to Concern 9
$eyeProducts = App\Models\Product::where('name_ar', 'LIKE', '%عين%')
    ->orWhere('name_ar', 'LIKE', '%هالات%')
    ->orWhere('name_en', 'LIKE', '%eye%')
    ->pluck('id');

foreach ($eyeProducts as $pId) {
    Illuminate\Support\Facades\DB::table('product_concerns')->insertOrIgnore([
        'product_id' => $pId,
        'concern_id' => 9,
        'created_at' => now(),
        'updated_at' => now(),
    ]);
}

$c8 = App\Models\Concern::find(8);
$c9 = App\Models\Concern::find(9);
echo "Concern 8 (Fine Lines) now has: " . $c8->products()->count() . " products" . PHP_EOL;
echo "Concern 9 (Eye Contour Concerns) now has: " . $c9->products()->count() . " products" . PHP_EOL;
