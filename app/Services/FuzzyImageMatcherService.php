<?php

namespace App\Services;

use App\Models\Brand;
use App\Models\Category;
use App\Models\Concern;
use App\Models\Product;
use App\Models\SkinType;
use App\Models\SubCategory;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class FuzzyImageMatcherService
{
    /**
     * Match a given filename or path against all DB entities and assign the image.
     */
    public static function matchAndAssignFile(string $filePath, bool $syncStorage = true): array
    {
        $originalFilename = basename($filePath);
        $cleanName = pathinfo($originalFilename, PATHINFO_FILENAME);
        $cleanTokens = self::tokenize($cleanName);

        $candidates = [];

        // 1. Check Products
        foreach (Product::with('brand', 'category')->get() as $product) {
            $nameStr = $product->name_en . ' ' . $product->name_ar . ' ' . $product->sku . ' ' . ($product->brand?->name_en ?? '');
            $score = self::calculateSimilarityScore($cleanTokens, $nameStr);
            if ($score > 30) {
                $candidates[] = [
                    'score' => $score,
                    'model' => $product,
                    'type' => 'products',
                    'name' => $product->name_en ?: $product->name_ar,
                ];
            }
        }

        // 2. Check Brands
        foreach (Brand::all() as $brand) {
            $nameStr = $brand->name_en . ' ' . $brand->name_ar;
            $score = self::calculateSimilarityScore($cleanTokens, $nameStr);
            if ($score > 30) {
                $candidates[] = [
                    'score' => $score + 10,
                    'model' => $brand,
                    'type' => 'brands',
                    'name' => $brand->name_en ?: $brand->name_ar,
                ];
            }
        }

        // 3. Check Categories
        foreach (Category::all() as $category) {
            $nameStr = $category->name_en . ' ' . $category->name_ar;
            $score = self::calculateSimilarityScore($cleanTokens, $nameStr);
            if ($score > 30) {
                $candidates[] = [
                    'score' => $score,
                    'model' => $category,
                    'type' => 'categories',
                    'name' => $category->name_en ?: $category->name_ar,
                ];
            }
        }

        // 4. Check SubCategories
        foreach (SubCategory::all() as $subCategory) {
            $nameStr = $subCategory->name_en . ' ' . $subCategory->name_ar;
            $score = self::calculateSimilarityScore($cleanTokens, $nameStr);
            if ($score > 30) {
                $candidates[] = [
                    'score' => $score,
                    'model' => $subCategory,
                    'type' => 'sub_categories',
                    'name' => $subCategory->name_en ?: $subCategory->name_ar,
                ];
            }
        }

        // Sort by highest similarity score
        usort($candidates, fn ($a, $b) => $b['score'] <=> $a['score']);

        if (!empty($candidates)) {
            $best = $candidates[0];
            $model = $best['model'];
            $type = $best['type'];

            $slug = Str::slug($best['name']) ?: 'item-' . $model->id;
            $ext = pathinfo($filePath, PATHINFO_EXTENSION) ?: 'jpg';
            $targetFilename = "{$slug}.{$ext}";

            if ($syncStorage) {
                $storageDir = storage_path("app/public/{$type}");
                $downloadsDir = "C:/Users/Dell/Downloads/kcode-images/{$type}";

                File::ensureDirectoryExists($storageDir);
                File::ensureDirectoryExists($downloadsDir);

                File::copy($filePath, "{$storageDir}/{$targetFilename}");
                File::copy($filePath, "{$downloadsDir}/{$targetFilename}");

                $model->update(['image' => "{$type}/{$targetFilename}"]);
            }

            return [
                'matched' => true,
                'score' => round($best['score'], 1),
                'type' => $type,
                'target' => $best['name'],
                'file' => $originalFilename,
            ];
        }

        return [
            'matched' => false,
            'score' => 0,
            'file' => $originalFilename,
        ];
    }

    /**
     * Process an entire directory path containing image files.
     */
    public static function processFolder(string $folderPath): array
    {
        if (!File::exists($folderPath)) {
            return ['status' => false, 'message' => "Folder not found: {$folderPath}"];
        }

        $files = File::allFiles($folderPath);
        $results = [];
        $matchedCount = 0;

        foreach ($files as $file) {
            $ext = strtolower($file->getExtension());
            if (in_array($ext, ['png', 'jpg', 'jpeg', 'webp', 'avif', 'svg', 'gif'])) {
                $res = self::matchAndAssignFile($file->getPathname());
                $results[] = $res;
                if ($res['matched']) {
                    $matchedCount++;
                }
            }
        }

        return [
            'status' => true,
            'total_files' => count($results),
            'matched_files' => $matchedCount,
            'details' => $results,
        ];
    }

    private static function tokenize(string $str): array
    {
        $clean = preg_replace('/[^a-zA-Z0-9\x{0600}-\x{06FF}]+/u', ' ', strtolower($str));
        $words = array_filter(explode(' ', $clean), fn ($w) => strlen($w) >= 2);
        return array_unique($words);
    }

    private static function calculateSimilarityScore(array $fileTokens, string $targetText): float
    {
        $targetTokens = self::tokenize($targetText);
        if (empty($fileTokens) || empty($targetTokens)) {
            return 0;
        }

        $matchingTokens = 0;
        foreach ($fileTokens as $fToken) {
            foreach ($targetTokens as $tToken) {
                if ($fToken === $tToken) {
                    $matchingTokens += 2;
                } elseif (str_contains($tToken, $fToken) || str_contains($fToken, $tToken)) {
                    $matchingTokens += 1;
                }
            }
        }

        similar_text(implode(' ', $fileTokens), implode(' ', $targetTokens), $percentText);

        $tokenScore = ($matchingTokens / count($fileTokens)) * 50;
        return ($tokenScore * 0.6) + ($percentText * 0.4);
    }
}
