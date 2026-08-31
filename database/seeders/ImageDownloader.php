<?php

namespace Database\Seeders;

use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Http;

class ImageDownloader
{
    protected static array $downloadedUrlsCache = [];

    /**
     * Download an image from a URL and save it to the target public directory.
     */
    public static function downloadAndSave(string $url, string $targetDirectory, string $filename, bool $forceOverwrite = true): string
    {
        $storageDir = storage_path('app/public/' . $targetDirectory);
        $targetPath = $storageDir . '/' . $filename;

        if (!File::exists($storageDir)) {
            File::makeDirectory($storageDir, 0755, true, true);
        }

        if (!$forceOverwrite && File::exists($targetPath) && File::size($targetPath) > 20000) {
            return $filename;
        }

        if (isset(self::$downloadedUrlsCache[$url]) && !$forceOverwrite) {
            $cachedSourcePath = self::$downloadedUrlsCache[$url];
            if (File::exists($cachedSourcePath) && File::size($cachedSourcePath) > 0) {
                File::copy($cachedSourcePath, $targetPath);
                return $filename;
            }
        }

        try {
            $response = Http::withHeaders([
                'User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120.0.0.0 Safari/537.36',
                'Accept' => 'image/avif,image/webp,image/apng,image/svg+xml,image/*,*/*;q=0.8',
            ])->timeout(12)->get($url);

            if ($response->successful() && strlen($response->body()) > 200) {
                File::put($targetPath, $response->body());
                self::$downloadedUrlsCache[$url] = $targetPath;
                return $filename;
            }
        } catch (\Exception $e) {
            // Silence exception to let fallback handle it
        }

        if (!File::exists($targetPath)) {
            self::createPlaceholder($targetPath);
        }

        return $filename;
    }

    /**
     * Generate a solid color placeholder image if download fails.
     */
    private static function createPlaceholder(string $path)
    {
        if (function_exists('imagecreatetruecolor')) {
            $img = imagecreatetruecolor(400, 400);
            $bg = imagecolorallocate($img, 240, 242, 245);
            imagefill($img, 0, 0, $bg);
            
            $txtColor = imagecolorallocate($img, 140, 140, 140);
            imagestring($img, 5, 120, 190, "KCODE Skincare", $txtColor);
            
            imagejpeg($img, $path);
            imagedestroy($img);
        } else {
            $tinyJpg = base64_decode('/9j/4AAQSkZJRgABAQEASABIAAD/2wBDAP//////////////////////////////////////////////////////////////////////////////////////wgALCAABAAEBAREA/8QAFBABAAAAAAAAAAAAAAAAAAAAAP/aAAgBAQABPxA=');
            File::put($path, $tinyJpg);
        }
    }
}
