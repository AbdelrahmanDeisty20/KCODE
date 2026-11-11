<?php

namespace Database\Seeders;

use App\Models\Brand;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;

class SeedMissingR479ProductsSeeder extends Seeder
{
    /**
     * Seeds the 20 missing canonical R479 products (KC0228 to KC0259) into the products table.
     */
    public function run(): void
    {
        $productsJsonPath = base_path('database/seeders/data/r479_products.json');
        $pagesJsonPath    = base_path('database/seeders/data/r479_compact_pages.json');
        $snapshotJsonPath = base_path('database/seeders/data/r479_master_snapshot.json');
        $catalogJsonPath  = base_path('database/seeders/data/r479_quiz/catalog.json');

        if (!File::exists($catalogJsonPath) || !File::exists($productsJsonPath)) {
            $this->command->error("R479 data files missing.");
            return;
        }

        $catalog = json_decode(File::get($catalogJsonPath), true);
        $productsData = json_decode(File::get($productsJsonPath), true) ?? [];
        $pagesData = File::exists($pagesJsonPath) ? (json_decode(File::get($pagesJsonPath), true) ?? []) : [];
        $snapshotData = File::exists($snapshotJsonPath) ? (json_decode(File::get($snapshotJsonPath), true) ?? []) : [];

        $snapBySku = [];
        foreach ($snapshotData as $item) {
            if (!empty($item['SKU'])) {
                $snapBySku[$item['SKU']] = $item;
            }
        }

        $allCatalogSkus = array_column($catalog, 'sku');
        $existingSkus = Product::whereIn('sku', $allCatalogSkus)->pluck('sku')->toArray();
        $missingSkus = array_values(array_diff($allCatalogSkus, $existingSkus));

        $this->command->info("Missing SKUs to seed: " . count($missingSkus));

        if (empty($missingSkus)) {
            $this->command->info("All catalog SKUs already exist in products table.");
            return;
        }

        $insertedCount = 0;

        DB::transaction(function () use ($missingSkus, $productsData, $pagesData, $snapBySku, &$insertedCount) {
            foreach ($missingSkus as $sku) {
                $p = $productsData[$sku] ?? [];
                $snap = $snapBySku[$sku] ?? [];
                $html = $pagesData[$sku] ?? '';

                // Brand matching
                $brandName = trim($p['brand'] ?? ($snap['Brand'] ?? ''));
                $brand = null;
                if (!empty($brandName)) {
                    $brand = Brand::where('name_en', 'LIKE', "%$brandName%")
                        ->orWhere('name_ar', 'LIKE', "%$brandName%")
                        ->first();
                }
                if (!$brand) {
                    $brand = Brand::first();
                }

                // Category matching
                $catName = trim($snap['Category'] ?? '');
                $category = null;
                if (!empty($catName)) {
                    $category = Category::where('name_en', 'LIKE', "%$catName%")
                        ->orWhere('name_ar', 'LIKE', "%$catName%")
                        ->first();
                }
                if (!$category) {
                    $category = Category::first();
                }

                $nameEn = trim($p['name'] ?? ($snap['Product'] ?? "Product {$sku}"));
                $nameAr = trim($p['brief'] ?? ($p['name'] ?? $nameEn));
                $descAr = trim($p['description'] ?? ($snap['Description AR'] ?? $nameAr));
                $price  = $sku === 'KC0259' ? 12.0 : 9.9;

                $product = new Product();
                $product->sku = $sku;
                $product->name_en = $nameEn;
                $product->name_ar = $nameAr;
                $product->short_name_en = $nameEn;
                $product->short_name_ar = $nameAr;
                $product->description_en = $descAr;
                $product->description_ar = $descAr;
                $product->brand_id = $brand?->id ?? 1;
                $product->category_id = $category?->id ?? 1;
                $product->price = $price;
                $product->stock = 50;
                $product->size = trim($p['size'] ?? ($snap['Size'] ?? '150ml'));
                $product->barcode = trim($p['barcode'] ?? ($snap['Barcode'] ?? ''));
                $product->status = 'active';
                $product->sensitive_eligible = true;
                $product->role_ar = $catName ?: 'عناية بالبشرة';
                $product->texture_ar = $snap['Texture'] ?? 'سائل';
                $product->how_to_use_ar = $p['usage'] ?? 'ضع كمية مناسبة على البشرة.';
                $product->how_to_use_en = 'Apply an appropriate amount to the skin.';

                // Image handling
                $imageName = $p['image'] ?? ($snap['Image'] ?? "{$sku}.png");
                $product->image = $imageName;

                $product->save();
                $insertedCount++;
            }
        });

        $this->command->info("Successfully inserted {$insertedCount} missing products into database.");
    }
}

