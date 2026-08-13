<?php

$vendorDirs = glob(__DIR__ . '/../vendor/filament/*/resources/lang');

foreach ($vendorDirs as $dir) {
    $pkg = basename(dirname(dirname($dir)));
    $targetDir = __DIR__ . '/../lang/vendor/' . $pkg;
    
    if (!file_exists($targetDir)) {
        mkdir($targetDir, 0777, true);
    }
    
    // Copy all subdirectories (ar, en, etc.)
    $langFolders = glob($dir . '/*');
    foreach ($langFolders as $folder) {
        $langName = basename($folder);
        $dest = $targetDir . '/' . $langName;
        if (!file_exists($dest)) {
            mkdir($dest, 0777, true);
        }
        $files = glob($folder . '/*.php');
        foreach ($files as $f) {
            copy($f, $dest . '/' . basename($f));
            echo "Copied translation file: {$pkg}/{$langName}/" . basename($f) . "\n";
        }
    }
}

echo "All Filament package translation files published to lang/vendor!\n";
