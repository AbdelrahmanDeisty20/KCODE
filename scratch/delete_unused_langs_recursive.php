<?php

function deleteDirectory($dir) {
    if (!file_exists($dir)) return true;
    if (!is_dir($dir)) return unlink($dir);
    foreach (scandir($dir) as $item) {
        if ($item == '.' || $item == '..') continue;
        if (!deleteDirectory($dir . DIRECTORY_SEPARATOR . $item)) return false;
    }
    return rmdir($dir);
}

$vendorDir = __DIR__ . '/../lang/vendor';
$packages = glob($vendorDir . '/*');

foreach ($packages as $pkg) {
    if (!is_dir($pkg)) continue;
    $langFolders = glob($pkg . '/*');
    foreach ($langFolders as $folder) {
        $langName = basename($folder);
        if ($langName !== 'ar' && $langName !== 'en') {
            deleteDirectory($folder);
            echo "Deleted folder: " . basename($pkg) . "/" . $langName . "\n";
        }
    }
}

echo "Successfully removed all non-ar and non-en language files from lang/vendor!\n";
