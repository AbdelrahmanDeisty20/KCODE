<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$concerns = App\Models\Concern::all();
echo "Total Concerns in DB: " . $concerns->count() . PHP_EOL;
foreach ($concerns as $c) {
    echo "ID {$c->id}: {$c->name_ar} | {$c->name_en} (Products: " . $c->products()->count() . ")" . PHP_EOL;
}
