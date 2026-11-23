<?php

use App\Services\SitemapService;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/sitemap.xml', function (SitemapService $sitemapService) {
    return $sitemapService->generateSitemap();
});

Route::get('/robots.txt', function (SitemapService $sitemapService) {
    return $sitemapService->generateRobots();
});

// Universal storage fallback to ensure all images load whether requested via /storage/ or /storage/app/public/
Route::get('/storage/{path}', function (string $path) {
    $cleanPath = ltrim(str_replace(['../', '..\\'], '', $path), '/');
    if (str_starts_with($cleanPath, 'app/public/')) {
        $cleanPath = substr($cleanPath, strlen('app/public/'));
    }

    $fullPath = storage_path('app/public/' . $cleanPath);

    if (file_exists($fullPath) && is_file($fullPath)) {
        return response()->file($fullPath);
    }

    abort(404);
})->where('path', '.*');
