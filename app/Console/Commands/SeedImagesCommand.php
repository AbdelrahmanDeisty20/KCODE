<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use App\Models\Category;
use App\Models\SubCategory;
use App\Models\Brand;
use App\Models\Product;
use App\Models\SkinType;
use App\Models\Concern;
use App\Models\RoutineGoal;

class SeedImagesCommand extends Command
{
    protected $signature = 'kcode:seed-images';
    protected $description = 'Populate system and Downloads folder using official developer pack assets (assets/brands and assets/product-types)';

    public function handle()
    {
        $this->info('1. Initializing KCODE Developer Pack Official Assets Importer...');

        $developerPackAssets = 'C:/Users/Dell/Downloads/KCODE_Homepage_Developer_V24_FINAL/KCODE_Homepage_Developer_V24_FINAL/assets';
        $downloadsImagesDir = 'C:/Users/Dell/Downloads/kcode-images';

        if (!File::exists($developerPackAssets)) {
            $this->error("Developer Pack Assets directory not found at {$developerPackAssets}");
            return;
        }

        $subDirs = ['categories', 'sub_categories', 'brands', 'skin_types', 'concerns', 'products', 'routine-goals'];

        // Clean & recreate folders
        foreach ($subDirs as $dir) {
            $storagePath = storage_path('app/public/' . $dir);
            $downloadPath = $downloadsImagesDir . '/' . $dir;

            File::ensureDirectoryExists($storagePath);
            File::ensureDirectoryExists($downloadPath);
            File::cleanDirectory($storagePath);
            File::cleanDirectory($downloadPath);
        }

        // Product Types Source Files Map
        $productTypeSourceMap = [
            'cleanser'    => "{$developerPackAssets}/product-types/cleanser.webp",
            'toner'       => "{$developerPackAssets}/product-types/toner.webp",
            'serum'       => "{$developerPackAssets}/product-types/serum-ampoule.webp",
            'moisturizer' => "{$developerPackAssets}/product-types/moisturizer.webp",
            'sunscreen'   => "{$developerPackAssets}/product-types/sunscreen.webp",
            'essence'     => "{$developerPackAssets}/product-types/essence.webp",
            'exfoliator'  => "{$developerPackAssets}/product-types/exfoliator.webp",
            'eye-care'    => "{$developerPackAssets}/product-types/eye-care.webp",
            'mask'        => "{$developerPackAssets}/product-types/mask.webp",
            'body-care'   => "{$developerPackAssets}/product-types/body-care.webp",
        ];

        // 1. Categories
        $this->info('Populating Main Categories using official product-type assets...');
        foreach (Category::all() as $cat) {
            $slug = Str::slug($cat->name_en ?: $cat->name_ar) ?: 'category-' . $cat->id;
            $typeKey = $this->determineCategoryKey($cat->name_en ?: $cat->name_ar);
            $srcFile = $productTypeSourceMap[$typeKey] ?? $productTypeSourceMap['serum'];
            
            $ext = pathinfo($srcFile, PATHINFO_EXTENSION);
            $filename = "{$slug}.{$ext}";

            File::copy($srcFile, storage_path("app/public/categories/{$filename}"));
            File::copy($srcFile, "{$downloadsImagesDir}/categories/{$filename}");

            $cat->update(['image' => "categories/{$filename}"]);
        }

        // 2. SubCategories
        $this->info('Populating SubCategories using official product-type assets...');
        foreach (SubCategory::all() as $sub) {
            $slug = Str::slug($sub->name_en ?: $sub->name_ar) ?: 'subcat-' . $sub->id;
            $typeKey = $this->determineCategoryKey($sub->name_en ?: $sub->name_ar);
            $srcFile = $productTypeSourceMap[$typeKey] ?? $productTypeSourceMap['serum'];
            
            $ext = pathinfo($srcFile, PATHINFO_EXTENSION);
            $filename = "{$slug}.{$ext}";

            File::copy($srcFile, storage_path("app/public/sub_categories/{$filename}"));
            File::copy($srcFile, "{$downloadsImagesDir}/sub_categories/{$filename}");

            $sub->update(['image' => "sub_categories/{$filename}"]);
        }

        // 3. Brands (Official Brand Logos from assets/brands)
        $this->info('Populating 34 Brands using official assets/brands logos...');
        $brandLogosDir = "{$developerPackAssets}/brands";
        $availableBrandLogos = File::exists($brandLogosDir) ? File::files($brandLogosDir) : [];

        foreach (Brand::all() as $brand) {
            $slug = Str::slug($brand->name_en ?: $brand->name_ar) ?: 'brand-' . $brand->id;
            
            // Search for matching logo file
            $matchedLogo = null;
            foreach ($availableBrandLogos as $logoFile) {
                $logoName = Str::slug(pathinfo($logoFile->getFilename(), PATHINFO_FILENAME));
                if (str_contains($slug, $logoName) || str_contains($logoName, $slug)) {
                    $matchedLogo = $logoFile->getPathname();
                    break;
                }
            }

            if (!$matchedLogo) {
                // Default fallback to first brand logo or serum asset
                $matchedLogo = !empty($availableBrandLogos) ? $availableBrandLogos[0]->getPathname() : $productTypeSourceMap['serum'];
            }

            $ext = pathinfo($matchedLogo, PATHINFO_EXTENSION);
            $filename = "{$slug}.{$ext}";

            File::copy($matchedLogo, storage_path("app/public/brands/{$filename}"));
            File::copy($matchedLogo, "{$downloadsImagesDir}/brands/{$filename}");

            $brand->update(['image' => "brands/{$filename}"]);
        }

        // 4. Skin Types & Concerns
        $this->info('Populating Skin Types and Concerns...');
        foreach (SkinType::all() as $type) {
            $slug = Str::slug($type->name_en ?: $type->name_ar) ?: 'skintype-' . $type->id;
            $srcFile = $productTypeSourceMap['moisturizer'];
            $ext = pathinfo($srcFile, PATHINFO_EXTENSION);
            $filename = "{$slug}.{$ext}";

            File::copy($srcFile, storage_path("app/public/skin_types/{$filename}"));
            File::copy($srcFile, "{$downloadsImagesDir}/skin_types/{$filename}");

            $type->update(['image' => "skin_types/{$filename}"]);
        }

        foreach (Concern::all() as $con) {
            $slug = Str::slug($con->name_en ?: $con->name_ar) ?: 'concern-' . $con->id;
            $srcFile = $productTypeSourceMap['serum'];
            $ext = pathinfo($srcFile, PATHINFO_EXTENSION);
            $filename = "{$slug}.{$ext}";

            File::copy($srcFile, storage_path("app/public/concerns/{$filename}"));
            File::copy($srcFile, "{$downloadsImagesDir}/concerns/{$filename}");

            $con->update(['image' => "concerns/{$filename}"]);
        }

        // 5. Products (All 123 Products)
        $this->info('Populating ALL 123 Products using official product-type assets...');
        foreach (Product::all() as $prod) {
            $slug = $prod->final_url_slug ?: Str::slug($prod->name_en);
            if (!$slug) {
                $slug = 'product-' . $prod->id;
            }

            $catName = $prod->category ? ($prod->category->name_en ?: $prod->category->name_ar) : '';
            $typeKey = $this->determineCategoryKey($catName . ' ' . $prod->name_en);
            $srcFile = $productTypeSourceMap[$typeKey] ?? $productTypeSourceMap['serum'];

            $ext = pathinfo($srcFile, PATHINFO_EXTENSION);
            $filename = "{$slug}.{$ext}";

            File::copy($srcFile, storage_path("app/public/products/{$filename}"));
            File::copy($srcFile, "{$downloadsImagesDir}/products/{$filename}");

            $prod->update(['image' => "products/{$filename}"]);
        }

        $this->info('SUCCESSFULLY POPULATED SYSTEM AND DOWNLOADS WITH ALL OFFICIAL DEVELOPER PACK BRAND LOGOS AND PRODUCT TYPE ASSETS!');
    }

    private function determineCategoryKey(string $name): string
    {
        $nameLower = strtolower($name);
        if (str_contains($nameLower, 'cleans') || str_contains($nameLower, 'غسول')) return 'cleanser';
        if (str_contains($nameLower, 'toner') || str_contains($nameLower, 'تونر')) return 'toner';
        if (str_contains($nameLower, 'moistur') || str_contains($nameLower, 'cream') || str_contains($nameLower, 'مرطب') || str_contains($nameLower, 'كريم')) return 'moisturizer';
        if (str_contains($nameLower, 'sun') || str_contains($nameLower, 'شمس')) return 'sunscreen';
        if (str_contains($nameLower, 'essence') || str_contains($nameLower, 'إيسنس')) return 'essence';
        if (str_contains($nameLower, 'exfoliat') || str_contains($nameLower, 'مقشر')) return 'exfoliator';
        if (str_contains($nameLower, 'eye') || str_contains($nameLower, 'عين')) return 'eye-care';
        if (str_contains($nameLower, 'mask') || str_contains($nameLower, 'ماسك')) return 'mask';
        if (str_contains($nameLower, 'body') || str_contains($nameLower, 'جسم')) return 'body-care';
        return 'serum';
    }
}
