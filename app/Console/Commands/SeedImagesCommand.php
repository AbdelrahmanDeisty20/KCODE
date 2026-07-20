<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Database\Seeders\ImageDownloader;
use App\Models\Category;
use App\Models\SubCategory;
use App\Models\Product;
use App\Models\SkinType;
use App\Models\Concern;
use App\Models\RoutineGoal;

class SeedImagesCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'kcode:seed-images';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Download and sync all system images locally (products, categories, subcategories, etc.)';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Starting image synchronization...');

        // 1. Ensure default placeholders exist in all folders
        $directories = [
            'products',
            'skin_types',
            'concerns',
            'routine-goals',
            'pages',
            'product_images',
            'sub_categories',
            'categories',
        ];

        foreach ($directories as $dir) {
            $storageDir = storage_path('app/public/' . $dir);
            if (!File::exists($storageDir)) {
                File::makeDirectory($storageDir, 0755, true, true);
            }

            $defaultPath = $storageDir . '/default.jpg';
            if (!File::exists($defaultPath) || File::size($defaultPath) == 0) {
                $this->info("Creating default placeholder for {$dir}");
                $this->createPlaceholder($defaultPath);
            }
        }

        // 2. Sync Categories
        $this->info('Syncing Category images...');
        $categoryUrls = [
            'Cleanser' => 'https://images.unsplash.com/photo-1556228720-195a672e8a03?auto=format&fit=crop&q=80&w=400',
            'Toner' => 'https://images.unsplash.com/photo-1608248597279-f99d160bfcbc?auto=format&fit=crop&q=80&w=400',
            'Serum' => 'https://images.unsplash.com/photo-1620916566398-39f1143ab7be?auto=format&fit=crop&q=80&w=400',
            'Moisturizer' => 'https://images.unsplash.com/photo-1601049676099-e7ed07d825b0?auto=format&fit=crop&q=80&w=400',
            'Sunscreen' => 'https://images.unsplash.com/photo-1598440947619-2c35fc9aa908?auto=format&fit=crop&q=80&w=400',
            'Face Mask' => 'https://images.unsplash.com/photo-1596755094514-f87e34085b2c?auto=format&fit=crop&q=80&w=400',
            'Eye Cream' => 'https://images.unsplash.com/photo-1608248597279-f99d160bfcbc?auto=format&fit=crop&q=80&w=400',
            'Exfoliator' => 'https://images.unsplash.com/photo-1556228578-0d85b1a4d571?auto=format&fit=crop&q=80&w=400',
            'Lip Balm' => 'https://images.unsplash.com/photo-1617897903246-719242758050?auto=format&fit=crop&q=80&w=400',
            'Face Oil' => 'https://images.unsplash.com/photo-1608571423902-eed4a5ad8108?auto=format&fit=crop&q=80&w=400',
            'Makeup Remover' => 'https://images.unsplash.com/photo-1556228578-0d85b1a4d571?auto=format&fit=crop&q=80&w=400',
            'Essence' => 'https://images.unsplash.com/photo-1620916566398-39f1143ab7be?auto=format&fit=crop&q=80&w=400',
            'Night Cream' => 'https://images.unsplash.com/photo-1601049676099-e7ed07d825b0?auto=format&fit=crop&q=80&w=400',
            'Micellar Water' => 'https://images.unsplash.com/photo-1617897903246-719242758050?auto=format&fit=crop&q=80&w=400',
            'Acne Treatment' => 'https://images.unsplash.com/photo-1556228720-195a672e8a03?auto=format&fit=crop&q=80&w=400',
            'Body Lotion' => 'https://images.unsplash.com/photo-1556228578-0d85b1a4d571?auto=format&fit=crop&q=80&w=400',
        ];
        foreach (Category::all() as $cat) {
            $url = $categoryUrls[$cat->name_en] ?? 'https://images.unsplash.com/photo-1608248597279-f99d160bfcbc?auto=format&fit=crop&q=80&w=400';
            $filename = \Illuminate\Support\Str::slug($cat->name_en) . '.jpg';
            ImageDownloader::downloadAndSave($url, 'categories', $filename);
            $cat->update(['image' => $filename]);
        }

        // 3. Sync SubCategories
        $this->info('Syncing SubCategory images...');
        $subCategoryUrls = [
            'Cleansing Balm' => 'https://images.unsplash.com/photo-1556228720-195a672e8a03?auto=format&fit=crop&q=80&w=400',
            'Oil Cleanser' => 'https://images.unsplash.com/photo-1556228720-195a672e8a03?auto=format&fit=crop&q=80&w=400',
            'Cleanser' => 'https://images.unsplash.com/photo-1556228720-195a672e8a03?auto=format&fit=crop&q=80&w=400',
            'Toner Pads' => 'https://images.unsplash.com/photo-1608248597279-f99d160bfcbc?auto=format&fit=crop&q=80&w=400',
            'Toner' => 'https://images.unsplash.com/photo-1608248597279-f99d160bfcbc?auto=format&fit=crop&q=80&w=400',
            'Mist' => 'https://images.unsplash.com/photo-1608248597279-f99d160bfcbc?auto=format&fit=crop&q=80&w=400',
            'Essence' => 'https://images.unsplash.com/photo-1620916566398-39f1143ab7be?auto=format&fit=crop&q=80&w=400',
            'Ampoule' => 'https://images.unsplash.com/photo-1620916566398-39f1143ab7be?auto=format&fit=crop&q=80&w=400',
            'Serum' => 'https://images.unsplash.com/photo-1620916566398-39f1143ab7be?auto=format&fit=crop&q=80&w=400',
            'Booster Serum' => 'https://images.unsplash.com/photo-1620916566398-39f1143ab7be?auto=format&fit=crop&q=80&w=400',
            'Treatment' => 'https://images.unsplash.com/photo-1556228578-0d85b1a4d571?auto=format&fit=crop&q=80&w=400',
            'Eye Serum' => 'https://images.unsplash.com/photo-1608248597279-f99d160bfcbc?auto=format&fit=crop&q=80&w=400',
            'Eye Cream' => 'https://images.unsplash.com/photo-1608248597279-f99d160bfcbc?auto=format&fit=crop&q=80&w=400',
            'Eye Patch' => 'https://images.unsplash.com/photo-1608248597279-f99d160bfcbc?auto=format&fit=crop&q=80&w=400',
            'Moisturizer' => 'https://images.unsplash.com/photo-1601049676099-e7ed07d825b0?auto=format&fit=crop&q=80&w=400',
            'Balm' => 'https://images.unsplash.com/photo-1617897903246-719242758050?auto=format&fit=crop&q=80&w=400',
            'Spot Treatment' => 'https://images.unsplash.com/photo-1556228720-195a672e8a03?auto=format&fit=crop&q=80&w=400',
            'Mask' => 'https://images.unsplash.com/photo-1596755094514-f87e34085b2c?auto=format&fit=crop&q=80&w=400',
            'Sunscreen' => 'https://images.unsplash.com/photo-1598440947619-2c35fc9aa908?auto=format&fit=crop&q=80&w=400',
            'Sunscreen Serum' => 'https://images.unsplash.com/photo-1598440947619-2c35fc9aa908?auto=format&fit=crop&q=80&w=400',
            'Sunscreen Stick' => 'https://images.unsplash.com/photo-1598440947619-2c35fc9aa908?auto=format&fit=crop&q=80&w=400',
            'Routine Set' => 'https://images.unsplash.com/photo-1612817288484-6f916006741a?auto=format&fit=crop&q=80&w=400',
            'Body Treatment Spray' => 'https://images.unsplash.com/photo-1556228578-0d85b1a4d571?auto=format&fit=crop&q=80&w=400',
        ];
        foreach (SubCategory::all() as $sub) {
            $url = $subCategoryUrls[$sub->name_en] ?? 'https://images.unsplash.com/photo-1608248597279-f99d160bfcbc?auto=format&fit=crop&q=80&w=400';
            $filename = \Illuminate\Support\Str::slug($sub->name_en) . '.jpg';
            ImageDownloader::downloadAndSave($url, 'sub_categories', $filename);
            $sub->update(['image' => $filename]);
        }

        // 4. Sync Skin Types
        $this->info('Syncing Skin Type images...');
        $skinUrls = [
            'Oily' => 'https://images.unsplash.com/photo-1522337360788-8b13dee7a37e?auto=format&fit=crop&q=80&w=400',
            'Dry' => 'https://images.unsplash.com/photo-1616683693504-3ea7e9ad6fec?auto=format&fit=crop&q=80&w=400',
            'Combination' => 'https://images.unsplash.com/photo-1512290923902-8a9f81dc236c?auto=format&fit=crop&q=80&w=400',
            'Sensitive' => 'https://images.unsplash.com/photo-1570172619644-dfd03ed5d881?auto=format&fit=crop&q=80&w=400',
        ];
        foreach (SkinType::all() as $type) {
            $url = $skinUrls[$type->name_en] ?? 'https://images.unsplash.com/photo-1522337360788-8b13dee7a37e?auto=format&fit=crop&q=80&w=400';
            $filename = \Illuminate\Support\Str::slug($type->name_en) . '.jpg';
            ImageDownloader::downloadAndSave($url, 'skin_types', $filename);
            $type->update(['image' => $filename]);
        }

        // 5. Sync Concerns
        $this->info('Syncing Concern images...');
        $concernUrls = [
            'Acne & Blemishes' => 'https://images.unsplash.com/photo-1556228720-195a672e8a03?auto=format&fit=crop&q=80&w=400',
            'Pores & Blackheads' => 'https://images.unsplash.com/photo-1522337360788-8b13dee7a37e?auto=format&fit=crop&q=80&w=400',
            'Pigmentation & Dark Spots' => 'https://images.unsplash.com/photo-1570172619644-dfd03ed5d881?auto=format&fit=crop&q=80&w=400',
            'Redness & Irritation' => 'https://images.unsplash.com/photo-1570172619644-dfd03ed5d881?auto=format&fit=crop&q=80&w=400',
            'Skin Barrier' => 'https://images.unsplash.com/photo-1620916566398-39f1143ab7be?auto=format&fit=crop&q=80&w=400',
            'Wrinkles & Fine Lines' => 'https://images.unsplash.com/photo-1616683693504-3ea7e9ad6fec?auto=format&fit=crop&q=80&w=400',
            'Dryness & Hydration' => 'https://images.unsplash.com/photo-1601049676099-e7ed07d825b0?auto=format&fit=crop&q=80&w=400',
        ];
        foreach (Concern::all() as $con) {
            $url = $concernUrls[$con->name_en] ?? 'https://images.unsplash.com/photo-1556228720-195a672e8a03?auto=format&fit=crop&q=80&w=400';
            $filename = \Illuminate\Support\Str::slug($con->name_en) . '.jpg';
            ImageDownloader::downloadAndSave($url, 'concerns', $filename);
            $con->update(['image' => $filename]);
        }

        // 6. Sync Routine Goals
        $this->info('Syncing Routine Goal images...');
        $goalUrls = [
            'Radiance & Freshness' => 'https://images.unsplash.com/photo-1620916566398-39f1143ab7be?auto=format&fit=crop&q=80&w=400',
            'Acne & Scars' => 'https://images.unsplash.com/photo-1556228720-195a672e8a03?auto=format&fit=crop&q=80&w=400',
            'Pore Care' => 'https://images.unsplash.com/photo-1522337360788-8b13dee7a37e?auto=format&fit=crop&q=80&w=400',
            'Brightening & Evening Tone' => 'https://images.unsplash.com/photo-1596755094514-f87e34085b2c?auto=format&fit=crop&q=80&w=400',
            'Hydration & Protection' => 'https://images.unsplash.com/photo-1601049676099-e7ed07d825b0?auto=format&fit=crop&q=80&w=400',
        ];
        foreach (RoutineGoal::all() as $goal) {
            $url = $goalUrls[$goal->name_en] ?? 'https://images.unsplash.com/photo-1620916566398-39f1143ab7be?auto=format&fit=crop&q=80&w=400';
            $filename = \Illuminate\Support\Str::slug($goal->name_en) . '.jpg';
            ImageDownloader::downloadAndSave($url, 'routine-goals', $filename);
            $goal->update(['image' => $filename]);
        }

        // 7. Sync Products
        $this->info('Syncing Product images...');
        $productCategoryUrls = [
            'Cleanser' => 'https://images.unsplash.com/photo-1556228720-195a672e8a03?auto=format&fit=crop&q=80&w=400',
            'Toner' => 'https://images.unsplash.com/photo-1608248597279-f99d160bfcbc?auto=format&fit=crop&q=80&w=400',
            'Serum' => 'https://images.unsplash.com/photo-1620916566398-39f1143ab7be?auto=format&fit=crop&q=80&w=400',
            'Ampoule' => 'https://images.unsplash.com/photo-1620916566398-39f1143ab7be?auto=format&fit=crop&q=80&w=400',
            'Moisturizer' => 'https://images.unsplash.com/photo-1601049676099-e7ed07d825b0?auto=format&fit=crop&q=80&w=400',
            'Sunscreen' => 'https://images.unsplash.com/photo-1598440947619-2c35fc9aa908?auto=format&fit=crop&q=80&w=400',
            'Face Mask' => 'https://images.unsplash.com/photo-1596755094514-f87e34085b2c?auto=format&fit=crop&q=80&w=400',
            'Eye Cream' => 'https://images.unsplash.com/photo-1608248597279-f99d160bfcbc?auto=format&fit=crop&q=80&w=400',
            'Exfoliator' => 'https://images.unsplash.com/photo-1556228578-0d85b1a4d571?auto=format&fit=crop&q=80&w=400',
            'Lip Balm' => 'https://images.unsplash.com/photo-1617897903246-719242758050?auto=format&fit=crop&q=80&w=400',
            'Face Oil' => 'https://images.unsplash.com/photo-1608571423902-eed4a5ad8108?auto=format&fit=crop&q=80&w=400',
            'Makeup Remover' => 'https://images.unsplash.com/photo-1556228578-0d85b1a4d571?auto=format&fit=crop&q=80&w=400',
            'Essence' => 'https://images.unsplash.com/photo-1620916566398-39f1143ab7be?auto=format&fit=crop&q=80&w=400',
            'Night Cream' => 'https://images.unsplash.com/photo-1601049676099-e7ed07d825b0?auto=format&fit=crop&q=80&w=400',
            'Micellar Water' => 'https://images.unsplash.com/photo-1617897903246-719242758050?auto=format&fit=crop&q=80&w=400',
            'Acne Treatment' => 'https://images.unsplash.com/photo-1556228720-195a672e8a03?auto=format&fit=crop&q=80&w=400',
            'Body Lotion' => 'https://images.unsplash.com/photo-1556228578-0d85b1a4d571?auto=format&fit=crop&q=80&w=400',
        ];

        foreach (Product::all() as $prod) {
            $catName = $prod->category ? $prod->category->name_en : '';
            $url = $productCategoryUrls[$catName] ?? 'https://images.unsplash.com/photo-1608248597279-f99d160bfcbc?auto=format&fit=crop&q=80&w=400';
            $slug = $prod->final_url_slug ?: \Illuminate\Support\Str::slug($prod->name_en);
            $filename = $slug . '.jpg';
            ImageDownloader::downloadAndSave($url, 'products', $filename);
            $prod->update(['image' => $filename]);
        }

        $this->info('Image synchronization completed successfully!');
    }

    /**
     * Create a simple placeholder image.
     */
    private function createPlaceholder(string $path)
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
