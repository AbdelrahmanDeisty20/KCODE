<?php

namespace Database\Seeders;

use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Http;

class ImageDownloader
{
    /**
     * Download an image from a URL and save it to the target public directory.
     */
    public static function downloadAndSave(string $url, string $targetDirectory, string $filename): string
    {
        $publicDir = public_path($targetDirectory);
        $targetPath = $publicDir . '/' . $filename;

        // Ensure directory exists
        if (!File::exists($publicDir)) {
            File::makeDirectory($publicDir, 0755, true, true);
        }

        // If file already exists, skip to save network time
        if (File::exists($targetPath) && File::size($targetPath) > 0) {
            return $filename;
        }

        try {
            // Attempt to download the image with a timeout
            $response = Http::timeout(6)->get($url);
            if ($response->successful()) {
                File::put($targetPath, $response->body());
                return $filename;
            }
        } catch (\Exception $e) {
            // Silence exception to let fallback handle it
        }

        // Fallback: Create a clean mock placeholder image so system doesn't have broken links
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
            
            // Try to add some subtle design element
            $txtColor = imagecolorallocate($img, 140, 140, 140);
            imagestring($img, 5, 120, 190, "KCODE Skincare", $txtColor);
            
            imagejpeg($img, $path);
            imagedestroy($img);
        } else {
            // Write a tiny 1x1 pixel white jpeg
            $tinyJpg = base64_decode('/9j/4AAQSkZJRgABAQEASABIAAD/2wBDAP//////////////////////////////////////////////////////////////////////////////////////wgALCAABAAEBAREA/8QAFBABAAAAAAAAAAAAAAAAAAAAAP/aAAgBAQABPxA=');
            File::put($path, $tinyJpg);
        }
    }
}
