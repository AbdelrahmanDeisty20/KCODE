<?php

namespace Database\Seeders;

use App\Models\Product;
use App\Models\Category;
use App\Models\Brand;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categoryCleanser = Category::where('name_en', 'Cleanser')->first();
        $categoryMoisturizer = Category::where('name_en', 'Moisturizer')->first();
        $categorySunscreen = Category::where('name_en', 'Sunscreen')->first();

        $brandLaRoche = Brand::where('name_en', 'La Roche-Posay')->first();
        $brandCeraVe = Brand::where('name_en', 'CeraVe')->first();
        $brandOrdinary = Brand::where('name_en', 'The Ordinary')->first();

        // Default categories/brands fallback if not found
        $categoryId1 = $categoryCleanser ? $categoryCleanser->id : 1;
        $categoryId2 = $categoryMoisturizer ? $categoryMoisturizer->id : 1;
        $categoryId3 = $categorySunscreen ? $categorySunscreen->id : 1;

        $brandId1 = $brandLaRoche ? $brandLaRoche->id : 1;
        $brandId2 = $brandCeraVe ? $brandCeraVe->id : 1;
        $brandId3 = $brandOrdinary ? $brandOrdinary->id : 1;

        $products = [
            [
                'name_en' => 'CeraVe Hydrating Facial Cleanser',
                'name_ar' => 'سيرافي غسول الوجه المرطب',
                'short_name_en' => 'CeraVe Cleanser',
                'short_name_ar' => 'غسول سيرافي',
                'description_en' => 'A unique formula with three essential ceramides that cleanses, hydrates and helps restore the protective skin barrier.',
                'description_ar' => 'تركيبة فريدة تحتوي على ثلاثة سيراميدات أساسية تنظف وترطب وتساعد على استعادة الحاجز الواقي للبشرة.',
                'category_id' => $categoryId1,
                'brand_id' => $brandId2,
                'sku' => 'CERAVE-CLEANSER-001',
                'price' => 15.99,
                'stock' => 50,
                'ingredients_en' => 'Purified Water, Glycerin, Cetearyl Alcohol, Ceramide 3, Ceramide 6-II, Ceramide 1, Hyaluronic Acid, Cholesterol.',
                'ingredients_ar' => 'مياه نقية، جليسرين، كحول السيتياريل، سيراميد 3، سيراميد 6-II، سيراميد 1، حمض الهيالورونيك، كوليسترول.',
                'how_to_use_en' => 'Wet skin with lukewarm water. Massage cleanser into skin in a gentle, circular motion. Rinse.',
                'how_to_use_ar' => 'بللي البشرة بالماء الفاتر. دلكي الغسول على البشرة بحركة دائرية لطيفة. اشطفيه بالماء.',
                'image' => 'cerave_cleanser.jpg',
                'status' => 'active',
            ],
            [
                'name_en' => 'La Roche-Posay Effaclar Duo+',
                'name_ar' => 'لاروش بوزيه إيفاكلار ثنائي+',
                'short_name_en' => 'Effaclar Duo+',
                'short_name_ar' => 'إيفاكلار ثنائي+',
                'description_en' => 'Corrective unclogging care, anti-imperfections, anti-marks and anti-recurrence for oily and acne-prone skin.',
                'description_ar' => 'علاج مصحح لانسداد المسام، مضاد للشوائب والآثار ومضاد لظهور حب الشباب للبشرة الدهنية والمعرضة لحب الشباب.',
                'category_id' => $categoryId2,
                'brand_id' => $brandId1,
                'sku' => 'LAROCHE-EFFACLAR-002',
                'price' => 22.50,
                'stock' => 30,
                'ingredients_en' => 'Aqua/Water, Glycerin, Dimethicone, Isocetyl Stearate, Niacinamide, Isopropyl Lauroyl Sarcosinate, Silica, Ammonium Polyacryloyldimethyl Taurate.',
                'ingredients_ar' => 'مياه، جليسرين، ثنائي الميثيكون، إيزوسيتيل ستيرات، نياسيناميد، إيزوبروبيل لوريل ساركوسينات، سيليكا.',
                'how_to_use_en' => 'Apply to entire face morning and/or evening after cleansing skin.',
                'how_to_use_ar' => 'يوضع على كامل الوجه صباحاً و/أو مساءً بعد تنظيف البشرة.',
                'image' => 'laroche_effaclar.jpg',
                'status' => 'active',
            ],
            [
                'name_en' => 'The Ordinary Niacinamide 10% + Zinc 1%',
                'name_ar' => 'ذا أورديناري نياسيناميد 10% + زنك 1%',
                'short_name_en' => 'Niacinamide 10% + Zinc 1%',
                'short_name_ar' => 'نياسيناميد 10% + زنك 1%',
                'description_en' => 'High-strength vitamin and mineral blemish formula to reduce the appearance of skin blemishes and congestion.',
                'description_ar' => 'تركيبة فيتامينات ومعادن عالية القوة لتقليل ظهور شوائب البشرة واحتقانها.',
                'category_id' => $categoryId2,
                'brand_id' => $brandId3,
                'sku' => 'ORDINARY-NIACINAMIDE-003',
                'price' => 9.99,
                'stock' => 100,
                'ingredients_en' => 'Aqua (Water), Niacinamide, Pentylene Glycol, Zinc PCA, Dimethyl Isosorbide, Tamarindus Indica Seed Gum, Xanthan Gum.',
                'ingredients_ar' => 'مياه، نياسيناميد، بنتيلين جليكول، زنك PCA، ثنائي ميثيل إيزوسوربيد، صمغ بذور التمر الهندي.',
                'how_to_use_en' => 'Apply to entire face morning and evening before heavier creams.',
                'how_to_use_ar' => 'يوضع على كامل الوجه صباحاً ومساءً قبل الكريمات الأثقل.',
                'image' => 'ordinary_niacinamide.jpg',
                'status' => 'active',
            ],
        ];

        foreach ($products as $product) {
            Product::updateOrCreate(
                ['sku' => $product['sku']],
                $product
            );
        }
    }
}
