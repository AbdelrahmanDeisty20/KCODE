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
