<?php
/**
 * ⚠️  TEMPORARY FILE - DELETE AFTER USE  ⚠️
 * Upload to: public_html/init-images.php
 * Open in browser: https://kcodeskin.com/init-images.php
 * DELETE immediately after running!
 */

// Bootstrap Laravel
define('LARAVEL_START', microtime(true));
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\File;

set_time_limit(300);
ini_set('memory_limit', '256M');

echo '<pre style="font-family:monospace;background:#111;color:#0f0;padding:20px;font-size:13px;">';
echo "🚀 KCODE Image Initialization\n";
echo str_repeat("─", 50) . "\n\n";

// Step 1: storage:link
echo "▶ Step 1: Creating storage symlink...\n";
try {
    $publicStoragePath = public_path('storage');
    $storagePath = storage_path('app/public');
    
    if (!File::exists($publicStoragePath)) {
        Artisan::call('storage:link');
        echo "  ✅ Symlink created: public/storage → storage/app/public\n\n";
    } else {
        echo "  ✅ Symlink already exists.\n\n";
    }
} catch (Exception $e) {
    echo "  ⚠️  Symlink: " . $e->getMessage() . "\n\n";
}

// Step 2: seed-images
echo "▶ Step 2: Downloading & syncing images...\n";
try {
    Artisan::call('kcode:seed-images');
    $output = Artisan::output();
    foreach (explode("\n", trim($output)) as $line) {
        echo "  " . $line . "\n";
    }
    echo "\n  ✅ Done!\n\n";
} catch (Exception $e) {
    echo "  ❌ Error: " . $e->getMessage() . "\n\n";
}

// Step 3: Verify folders
echo "▶ Step 3: Verifying image folders...\n";
$dirs = ['categories', 'sub_categories', 'products', 'skin_types', 'concerns', 'routine-goals'];
foreach ($dirs as $dir) {
    $path = storage_path("app/public/{$dir}");
    $count = File::exists($path) ? count(File::files($path)) : 0;
    $status = $count > 0 ? "✅" : "❌";
    echo "  {$status} storage/app/public/{$dir}/ → {$count} files\n";
}

echo "\n" . str_repeat("─", 50) . "\n";
echo "✅ All done! DELETE this file from your server now!\n";
echo '</pre>';
