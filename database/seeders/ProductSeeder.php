<?php

namespace Database\Seeders;

use App\Models\Brand;
use App\Models\Category;
use App\Models\SubCategory;
use App\Models\Product;
use App\Models\Concern;
use App\Models\SkinType;
use App\Models\ProductConcern;
use App\Models\ProductSkinType;
use App\Models\ProductRoutine;
use App\Models\RoutineStep;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

class ProductSeeder extends Seeder
{
    /**
     * Seed official Developer Pack products from kcode_products.json.
     */
    public function run(): void
    {
        // 1. Clear existing products and relations
        Schema::disableForeignKeyConstraints();
        ProductConcern::truncate();
        ProductSkinType::truncate();
        \App\Models\ProductGoal::truncate();
        \App\Models\ProductMarketingDetail::truncate();
        \App\Models\ProductRecommendationRule::truncate();
        \App\Models\ProductAudit::truncate();
        ProductRoutine::truncate();
        \App\Models\ProductImage::truncate();
        Product::truncate();
        Schema::enableForeignKeyConstraints();

        $jsonPaths = [
            base_path('exicel/kcode_products.json'),
            base_path('kcode_products.json'),
            'c:/Users/Dell/Downloads/KCODE_Developer_Pack_v16.5 (2)/kcode_modern_v16_5/kcode_products.json',
        ];

        $jsonFile = null;
        foreach ($jsonPaths as $p) {
            if (file_exists($p)) {
                $jsonFile = $p;
                break;
            }
        }

        if (!$jsonFile) {
            $this->command->error("kcode_products.json file not found.");
            return;
        }

        $jsonContent = json_decode(file_get_contents($jsonFile), true);
        $jsonProducts = $jsonContent['products'] ?? [];

        $categoryTranslations = [
            'Cleanser' => 'غسول',
            'Toner' => 'تونر',
            'Essence' => 'إسنس',
            'Serum' => 'سيروم وأمبول',
            'Moisturizer' => 'مرطب',
            'Sunscreen' => 'واقي شمس',
            'Eye Care' => 'العناية بالعين',
            'Exfoliator' => 'مقشر',
            'Mask' => 'ماسكات',
            'Body Care' => 'العناية بالجسم',
        ];

        $stepMapping = [
            'Cleanser' => ['ar' => 'غسول', 'en' => 'Cleanser', 'order' => 1],
            'Toner' => ['ar' => 'تونر', 'en' => 'Toner', 'order' => 2],
            'Essence' => ['ar' => 'إسنس', 'en' => 'Essence', 'order' => 3],
            'Serum' => ['ar' => 'سيروم وأمبول', 'en' => 'Serum', 'order' => 4],
            'Moisturizer' => ['ar' => 'مرطب', 'en' => 'Moisturizer', 'order' => 5],
            'Sunscreen' => ['ar' => 'واقي شمس', 'en' => 'Sunscreen', 'order' => 6],
            'Eye Care' => ['ar' => 'العناية بالعين', 'en' => 'Eye Care', 'order' => 7],
            'Exfoliator' => ['ar' => 'مقشر', 'en' => 'Exfoliator', 'order' => 8],
            'Mask' => ['ar' => 'ماسكات', 'en' => 'Mask', 'order' => 9],
            'Body Care' => ['ar' => 'العناية بالجسم', 'en' => 'Body Care', 'order' => 10],
        ];

        $allSkinTypes = SkinType::all();
        $allConcerns = Concern::all();

        $seededCount = 0;

        foreach ($jsonProducts as $pData) {
            $brandName = trim($pData['brand'] ?? '');
            $prodName = trim($pData['product'] ?? '');
            $sku = trim($pData['sku'] ?? '');
            $size = trim($pData['size'] ?? '');

            if (empty($brandName) || empty($prodName)) continue;

            $fullEnName = "{$brandName} {$prodName}" . ($size ? " ({$size})" : "");
            $slug = Str::slug("{$brandName}-{$prodName}-{$size}");

            $brand = Brand::whereRaw('LOWER(name_en) = ?', [strtolower($brandName)])->first();
            if (!$brand) {
                $brand = Brand::create([
                    'name_en' => $brandName,
                    'name_ar' => $brandName,
                    'image'   => Str::slug($brandName) . '.png',
                ]);
            }

            $categoryName = $pData['category'] ?? 'Serum';
            $category = Category::firstOrCreate(
                ['name_en' => $categoryName],
                ['name_ar' => $categoryTranslations[$categoryName] ?? $categoryName, 'image' => Str::slug($categoryName) . '.webp']
            );

            $subCategoryName = $pData['sub_category'] ?? $categoryName;
            $subCategory = SubCategory::firstOrCreate(
                ['name_en' => $subCategoryName, 'category_id' => $category->id],
                ['name_ar' => $subCategoryName, 'image' => Str::slug($subCategoryName) . '.webp']
            );

            $descAr = $pData['description_ar'] ?? "منتج كوري أصلي مميز من ماركة {$brandName} للعناية بالبشرة.";
            $descEn = "Authentic {$brandName} {$prodName} Korean skincare product.";
            $nameAr = $pData['name_ar'] ?? "{$brandName} {$prodName}" . ($size ? " ({$size})" : "");

            $product = Product::updateOrCreate(
                ['sku' => $sku ?: $slug],
                [
                    'brand_id' => $brand->id,
                    'category_id' => $category->id,
                    'sub_category_id' => $subCategory->id,
                    'name_en' => $fullEnName,
                    'name_ar' => $nameAr,
                    'short_name_en' => "{$brandName} {$prodName}",
                    'short_name_ar' => $nameAr,
                    'sku' => $sku ?: $slug,
                    'price' => rand(65, 185),
                    'stock' => rand(15, 80),
                    'image' => 'https://images.unsplash.com/photo-1620916566398-39f1143ab7be?auto=format&fit=crop&q=80&w=800',
                    'description_ar' => $descAr,
                    'description_en' => $descEn,
                    'ingredients_ar' => $pData['ingredients_ar'] ?? $descAr,
                    'ingredients_en' => $descEn,
                    'how_to_use_ar' => $descAr,
                    'how_to_use_en' => $descEn,
                    'status' => 'active',
                    'is_best_seller' => true,
                ]
            );

            foreach ($allSkinTypes as $st) {
                ProductSkinType::firstOrCreate([
                    'product_id' => $product->id,
                    'skin_type_id' => $st->id,
                ]);
            }

            foreach ($allConcerns as $c) {
                ProductConcern::firstOrCreate([
                    'product_id' => $product->id,
                    'concern_id' => $c->id,
                ]);
            }

            $stepName = $pData['category'] ?? 'Serum';
            $mapping = $stepMapping[$stepName] ?? ['ar' => $stepName, 'en' => $stepName, 'order' => 4];
            $routineStep = RoutineStep::firstOrCreate(
                ['name_en' => $stepName],
                ['name_ar' => $mapping['ar'], 'order' => $mapping['order']]
            );

            ProductRoutine::firstOrCreate([
                'product_id' => $product->id,
                'routine_step_id' => $routineStep->id,
            ], [
                'morning' => true,
                'night' => true,
                'layer_order' => 3,
                'is_core' => true,
                'is_addon' => false,
            ]);

            $seededCount++;
        }

        $this->command->info("Successfully seeded {$seededCount} official products from JSON Developer Pack.");
    }
}
