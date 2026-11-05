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

// Fallback image streamer for routine assets if storage symlink or copy is pending on server
Route::get('/storage/routines/{filename}', function ($filename) {
    $storagePath = storage_path('app/public/routines/' . $filename);
    $assetPath = database_path('seeders/assets/routines/' . $filename);

    if (file_exists($storagePath)) {
        return response()->file($storagePath);
    }

    if (file_exists($assetPath)) {
        $storageDir = storage_path('app/public/routines');
        if (!file_exists($storageDir)) {
            @mkdir($storageDir, 0755, true);
        }
        @copy($assetPath, $storagePath);
        return response()->file($assetPath);
    }

    abort(404);
})->where('filename', '.*');
