<?php

namespace App\Services;

use App\Models\Blog;
use App\Models\BlogCategory;
use Illuminate\Support\Facades\Response;

class SitemapService
{
    /**
     * Generate dynamic XML sitemap content.
     */
    public function generateSitemap()
    {
        $blogs = Blog::published()->select(['slug', 'updated_at', 'created_at'])->latest('updated_at')->get();
        $categories = BlogCategory::select(['slug', 'updated_at', 'created_at'])->latest('updated_at')->get();
        $baseUrl = url('/');

        $xml = '<?xml version="1.0" encoding="UTF-8"?>' . PHP_EOL;
        $xml .= '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">' . PHP_EOL;

        // Root URL
        $xml .= '  <url>' . PHP_EOL;
        $xml .= '    <loc>' . htmlspecialchars($baseUrl) . '</loc>' . PHP_EOL;
        $xml .= '    <changefreq>daily</changefreq>' . PHP_EOL;
        $xml .= '    <priority>1.0</priority>' . PHP_EOL;
        $xml .= '  </url>' . PHP_EOL;

        // Blogs
        foreach ($blogs as $blog) {
            if (empty($blog->slug)) {
                continue;
            }
            $date = $blog->updated_at ?? $blog->created_at ?? now();
            $lastMod = is_string($date) ? date('c', strtotime($date)) : $date->toAtomString();

            $xml .= '  <url>' . PHP_EOL;
            $xml .= '    <loc>' . htmlspecialchars($baseUrl . '/blogs/' . $blog->slug) . '</loc>' . PHP_EOL;
            $xml .= '    <lastmod>' . $lastMod . '</lastmod>' . PHP_EOL;
            $xml .= '    <changefreq>weekly</changefreq>' . PHP_EOL;
            $xml .= '    <priority>0.8</priority>' . PHP_EOL;
            $xml .= '  </url>' . PHP_EOL;
        }

        // Categories
        foreach ($categories as $category) {
            if (empty($category->slug)) {
                continue;
            }
            $date = $category->updated_at ?? $category->created_at ?? now();
            $lastMod = is_string($date) ? date('c', strtotime($date)) : $date->toAtomString();

            $xml .= '  <url>' . PHP_EOL;
            $xml .= '    <loc>' . htmlspecialchars($baseUrl . '/blog-categories/' . $category->slug) . '</loc>' . PHP_EOL;
            $xml .= '    <lastmod>' . $lastMod . '</lastmod>' . PHP_EOL;
            $xml .= '    <changefreq>monthly</changefreq>' . PHP_EOL;
            $xml .= '    <priority>0.6</priority>' . PHP_EOL;
            $xml .= '  </url>' . PHP_EOL;
        }

        $xml .= '</urlset>';

        return Response::make($xml, 200, [
            'Content-Type' => 'application/xml',
        ]);
    }

    /**
     * Generate robots.txt content.
     */
    public function generateRobots()
    {
        $baseUrl = url('/');

        $content = "User-agent: *" . PHP_EOL;
        $content .= "Allow: /" . PHP_EOL;
        $content .= "Disallow: /admin/" . PHP_EOL;
        $content .= "Disallow: /api/" . PHP_EOL;
        $content .= PHP_EOL;
        $content .= "Sitemap: " . $baseUrl . "/sitemap.xml" . PHP_EOL;

        return Response::make($content, 200, [
            'Content-Type' => 'text/plain',
        ]);
    }

    /**
     * Save sitemap.xml and robots.txt into public directory.
     */
    public function saveToPublic(): void
    {
        $sitemapContent = $this->generateSitemap()->getContent();
        file_put_contents(public_path('sitemap.xml'), $sitemapContent);

        $robotsContent = $this->generateRobots()->getContent();
        file_put_contents(public_path('robots.txt'), $robotsContent);
    }
}
