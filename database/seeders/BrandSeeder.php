<?php

namespace Database\Seeders;

use App\Models\Brand;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class BrandSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $brands = [
            ['name_en' => 'La Roche-Posay', 'name_ar' => 'لاروش بوزيه', 'image' => 'https://images.unsplash.com/photo-1608248597279-f99d160bfcbc?auto=format&fit=crop&q=80&w=400'],
            ['name_en' => 'CeraVe', 'name_ar' => 'سيرافي', 'image' => 'https://images.unsplash.com/photo-1620916566398-39f1143ab7be?auto=format&fit=crop&q=80&w=400'],
            ['name_en' => 'The Ordinary', 'name_ar' => 'ذا أورديناري', 'image' => 'https://images.unsplash.com/photo-1601049676099-e7ed07d825b0?auto=format&fit=crop&q=80&w=400'],
            ['name_en' => 'Vichy', 'name_ar' => 'فيشي', 'image' => 'https://images.unsplash.com/photo-1598440947619-2c35fc9aa908?auto=format&fit=crop&q=80&w=400'],
            ['name_en' => 'Bioderma', 'name_ar' => 'بيوديرما', 'image' => 'https://images.unsplash.com/photo-1596755094514-f87e34085b2c?auto=format&fit=crop&q=80&w=400'],
            ['name_en' => 'Neutrogena', 'name_ar' => 'نيتروجينا', 'image' => 'https://images.unsplash.com/photo-1556228720-195a672e8a03?auto=format&fit=crop&q=80&w=400'],
            ['name_en' => 'Eucerin', 'name_ar' => 'يوسيرين', 'image' => 'https://images.unsplash.com/photo-1556228578-0d85b1a4d571?auto=format&fit=crop&q=80&w=400'],
            ['name_en' => 'Cetaphil', 'name_ar' => 'سيتافيل', 'image' => 'https://images.unsplash.com/photo-1617897903246-719242758050?auto=format&fit=crop&q=80&w=400'],
            ['name_en' => 'Laneige', 'name_ar' => 'لانيج', 'image' => 'https://images.unsplash.com/photo-1608571423902-eed4a5ad8108?auto=format&fit=crop&q=80&w=400'],
            ['name_en' => 'COSRX', 'name_ar' => 'كوزريكس', 'image' => 'https://images.unsplash.com/photo-1620916566398-39f1143ab7be?auto=format&fit=crop&q=80&w=400'],
            ['name_en' => 'L\'Oréal Paris', 'name_ar' => 'لوريال باريس', 'image' => 'https://images.unsplash.com/photo-1601049676099-e7ed07d825b0?auto=format&fit=crop&q=80&w=400'],
            ['name_en' => 'Clinique', 'name_ar' => 'كلينيك', 'image' => 'https://images.unsplash.com/photo-1617897903246-719242758050?auto=format&fit=crop&q=80&w=400'],
            ['name_en' => 'Kiehl\'s', 'name_ar' => 'كيلز', 'image' => 'https://images.unsplash.com/photo-1556228720-195a672e8a03?auto=format&fit=crop&q=80&w=400'],
            ['name_en' => 'Paula\'s Choice', 'name_ar' => 'بولاز تشويس', 'image' => 'https://images.unsplash.com/photo-1556228578-0d85b1a4d571?auto=format&fit=crop&q=80&w=400'],
            ['name_en' => 'Avène', 'name_ar' => 'أفين', 'image' => 'https://images.unsplash.com/photo-1598440947619-2c35fc9aa908?auto=format&fit=crop&q=80&w=400'],
        ];

        foreach ($brands as $brand) {
            $filename = \Illuminate\Support\Str::slug($brand['name_en']) . '.jpg';
            ImageDownloader::downloadAndSave($brand['image'], 'brands', $filename);

            Brand::updateOrCreate(
                ['name_en' => $brand['name_en']],
                [
                    'name_ar' => $brand['name_ar'],
                    'image' => $filename,
                ]
            );
        }
    }
}
