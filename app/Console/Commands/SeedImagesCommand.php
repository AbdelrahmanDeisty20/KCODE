<?php

namespace App\Console\Commands;

use App\Models\Brand;
use App\Models\Category;
use App\Models\Concern;
use App\Models\Product;
use App\Models\SkinType;
use App\Models\SubCategory;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class SeedImagesCommand extends Command
{
    protected $signature = 'kcode:seed-images {sourceDir?}';
    protected $description = 'Populate storage/app/public images from a local assets directory containing brands and product-types';

    public function handle(): void
    {
        $assetsDir = $this->argument('sourceDir') ?: $this->ask('Enter source assets directory (with product-types/ and brands/)');

        if (!$assetsDir || !File::exists($assetsDir)) {
            $this->error('Assets directory not found.');
            return;
        }

        $subDirs = ['categories', 'sub_categories', 'brands', 'skin_types', 'concerns', 'products', 'routine-goals'];
        foreach ($subDirs as $dir) {
            $storagePath = storage_path('app/public/' . $dir);
            File::ensureDirectoryExists($storagePath);
        }

        $productTypeSourceMap = [
            'cleanser'    => "{$assetsDir}/product-types/cleanser.webp",
            'toner'       => "{$assetsDir}/product-types/toner.webp",
            'serum'       => "{$assetsDir}/product-types/serum-ampoule.webp",
            'moisturizer' => "{$assetsDir}/product-types/moisturizer.webp",
            'sunscreen'   => "{$assetsDir}/product-types/sunscreen.webp",
            'essence'     => "{$assetsDir}/product-types/essence.webp",
            'exfoliator'  => "{$assetsDir}/product-types/exfoliator.webp",
            'eye-care'    => "{$assetsDir}/product-types/eye-care.webp",
            'mask'        => "{$assetsDir}/product-types/mask.webp",
            'body-care'   => "{$assetsDir}/product-types/body-care.webp",
        ];

        foreach (Category::all() as $cat) {
            $slug = Str::slug($cat->name_en ?: $cat->name_ar) ?: 'category-' . $cat->id;
            $typeKey = $this->determineCategoryKey($cat->name_en ?: $cat->name_ar);
            $srcFile = $productTypeSourceMap[$typeKey] ?? $productTypeSourceMap['serum'];
            if (!File::exists($srcFile)) continue;

            $filename = $slug . '.' . pathinfo($srcFile, PATHINFO_EXTENSION);
            File::copy($srcFile, storage_path("app/public/categories/{$filename}"));
            $cat->update(['image' => "categories/{$filename}"]);
        }

        foreach (SubCategory::all() as $sub) {
            $slug = Str::slug($sub->name_en ?: $sub->name_ar) ?: 'subcat-' . $sub->id;
            $typeKey = $this->determineCategoryKey($sub->name_en ?: $sub->name_ar);
            $srcFile = $productTypeSourceMap[$typeKey] ?? $productTypeSourceMap['serum'];
            if (!File::exists($srcFile)) continue;

            $filename = $slug . '.' . pathinfo($srcFile, PATHINFO_EXTENSION);
            File::copy($srcFile, storage_path("app/public/sub_categories/{$filename}"));
            $sub->update(['image' => "sub_categories/{$filename}"]);
        }

        $brandLogosDir = "{$assetsDir}/brands";
        $availableBrandLogos = File::exists($brandLogosDir) ? File::files($brandLogosDir) : [];

        foreach (Brand::all() as $brand) {
            $slug = Str::slug($brand->name_en ?: $brand->name_ar) ?: 'brand-' . $brand->id;
            $matchedLogo = null;
            foreach ($availableBrandLogos as $logoFile) {
                $logoName = Str::slug(pathinfo($logoFile->getFilename(), PATHINFO_FILENAME));
                if (str_contains($slug, $logoName) || str_contains($logoName, $slug)) {
                    $matchedLogo = $logoFile->getPathname();
                    break;
                }
            }
            if (!$matchedLogo) {
                $matchedLogo = !empty($availableBrandLogos) ? $availableBrandLogos[0]->getPathname() : $productTypeSourceMap['serum'];
            }
            if (!File::exists($matchedLogo)) continue;

            $filename = $slug . '.' . pathinfo($matchedLogo, PATHINFO_EXTENSION);
            File::copy($matchedLogo, storage_path("app/public/brands/{$filename}"));
            $brand->update(['image' => "brands/{$filename}"]);
        }

        foreach (SkinType::all() as $type) {
            $slug = Str::slug($type->name_en ?: $type->name_ar) ?: 'skintype-' . $type->id;
            $srcFile = $productTypeSourceMap['moisturizer'];
            if (!File::exists($srcFile)) continue;

            $filename = $slug . '.' . pathinfo($srcFile, PATHINFO_EXTENSION);
            File::copy($srcFile, storage_path("app/public/skin_types/{$filename}"));
            $type->update(['image' => "skin_types/{$filename}"]);
        }

        foreach (Concern::all() as $con) {
            $slug = Str::slug($con->name_en ?: $con->name_ar) ?: 'concern-' . $con->id;
            $srcFile = $productTypeSourceMap['serum'];
            if (!File::exists($srcFile)) continue;

            $filename = $slug . '.' . pathinfo($srcFile, PATHINFO_EXTENSION);
            File::copy($srcFile, storage_path("app/public/concerns/{$filename}"));
            $con->update(['image' => "concerns/{$filename}"]);
        }

        foreach (Product::with('category')->get() as $prod) {
            $slug = $prod->final_url_slug ?: Str::slug($prod->name_en) ?: 'product-' . $prod->id;
            $catName = $prod->category ? ($prod->category->name_en ?: $prod->category->name_ar) : '';
            $typeKey = $this->determineCategoryKey($catName . ' ' . $prod->name_en);
            $srcFile = $productTypeSourceMap[$typeKey] ?? $productTypeSourceMap['serum'];
            if (!File::exists($srcFile)) continue;

            $filename = $slug . '.' . pathinfo($srcFile, PATHINFO_EXTENSION);
            File::copy($srcFile, storage_path("app/public/products/{$filename}"));
            $prod->update(['image' => "products/{$filename}"]);
        }

        $this->info('Storage assets populated cleanly.');
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
