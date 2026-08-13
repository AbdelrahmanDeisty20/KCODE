<?php

$sourceDir = 'C:/myProjects/alpha-peta-main/public/svgs';
$targetDir = __DIR__ . '/../public/svgs';

if (!file_exists($targetDir)) {
    mkdir($targetDir, 0777, true);
}

$files = glob($sourceDir . '/*.svg');
foreach ($files as $file) {
    $target = $targetDir . '/' . basename($file);
    copy($file, $target);
    echo "Copied icon: " . basename($file) . "\n";
}

echo "Done copying all custom SVG icons from alpha-peta-main!\n";
