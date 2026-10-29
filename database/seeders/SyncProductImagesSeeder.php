<?php

namespace Database\Seeders;

use App\Models\Product;
use App\Models\ProductImage;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\File;

class SyncProductImagesSeeder extends Seeder
{
    /**
     * Sync and update all products with their local images and fallback placeholders.
     */
    public function run(): void
    {
        $mapFile = database_path('seeders/product_images_map.json');

        if (!File::exists($mapFile)) {
            $this->command->error("product_images_map.json not found!");
            return;
        }

        $imagesMap = json_decode(File::get($mapFile), true);

        // 1. Ensure storage folders exist and copy images from assets
        $sourceDir = database_path('seeders/assets/products');
        $targetProductsDir = storage_path('app/public/products');
        $targetGalleryDir = storage_path('app/public/product_images');

        if (!File::exists($targetProductsDir)) {
            File::makeDirectory($targetProductsDir, 0755, true, true);
        }
        if (!File::exists($targetGalleryDir)) {
            File::makeDirectory($targetGalleryDir, 0755, true, true);
        }

        if (File::exists($sourceDir)) {
            foreach (File::files($sourceDir) as $file) {
                $filename = $file->getFilename();
                if ($filename === 'products_manifest.json') continue;

                $dest1 = $targetProductsDir . '/' . $filename;
                if (!File::exists($dest1)) {
                    File::copy($file->getPathname(), $dest1);
                }

                if (str_contains($filename, '_gallery_') || str_starts_with($filename, 'default')) {
                    $dest2 = $targetGalleryDir . '/' . $filename;
                    if (!File::exists($dest2)) {
                        File::copy($file->getPathname(), $dest2);
                    }
                }
            }
        }

        $defaultImage = 'products/default_bottle.png';
        $updatedCount = 0;
        $realPhotosCount = 0;
        $placeholderCount = 0;
        $galleryImagesCount = 0;

        foreach ($imagesMap as $sku => $data) {
            $mainImg = $data['main'] ?? $defaultImage;
            $subs = $data['subs'] ?? [];
            $hasReal = $data['has_real_photo'] ?? false;

            $product = Product::where('sku', $sku)->first();

            if (!$product) {
                // Try searching with case-insensitive or trimmed SKU
                $product = Product::whereRaw('LOWER(TRIM(sku)) = ?', [strtolower(trim($sku))])->first();
            }

            if ($product) {
                $product->update([
                    'image' => $mainImg,
                ]);

                // Sync gallery images
                ProductImage::where('product_id', $product->id)->delete();
                foreach ($subs as $subImg) {
                    ProductImage::create([
                        'product_id' => $product->id,
                        'images' => $subImg,
                    ]);
                    $galleryImagesCount++;
                }

                $updatedCount++;
                if ($hasReal) {
                    $realPhotosCount++;
                } else {
                    $placeholderCount++;
                }
            }
        }

        // Also check any remaining products in DB that were not in the map
        $remaining = Product::whereNull('image')
            ->orWhere('image', '')
            ->orWhere('image', 'like', 'http%')
            ->get();

        foreach ($remaining as $remProduct) {
            $remProduct->update([
                'image' => $defaultImage,
            ]);
            $placeholderCount++;
        }

        $this->command->info("Product images synchronized successfully!");
        $this->command->info("- Total products updated: {$updatedCount}");
        $this->command->info("- Products with real photos: {$realPhotosCount}");
        $this->command->info("- Products with fallback placeholder: {$placeholderCount}");
        $this->command->info("- Gallery images linked: {$galleryImagesCount}");
    }
}
