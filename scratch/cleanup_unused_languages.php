<?php

$vendorDir = __DIR__ . '/../lang/vendor';

if (!file_exists($vendorDir)) {
    echo "lang/vendor does not exist.\n";
    exit;
}

$packages = glob($vendorDir . '/*');
$deletedCount = 0;

foreach ($packages as $pkg) {
    if (!is_dir($pkg)) continue;
    
    $langFolders = glob($pkg . '/*');
    foreach ($langFolders as $folder) {
        if (!is_dir($folder)) continue;
        
        $langName = basename($folder);
        // Keep ONLY 'ar' and 'en'
        if ($langName !== 'ar' && $langName !== 'en') {
            array_map('unlink', glob("$folder/*.*"));
            rmdir($folder);
            $deletedCount++;
        }
    }
}

echo "Cleaned up {$deletedCount} unused language folders! Only 'ar' and 'en' preserved in lang/vendor.\n";
