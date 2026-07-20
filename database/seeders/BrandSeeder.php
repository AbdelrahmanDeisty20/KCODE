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
            ['name_en' => 'Medicube', 'name_ar' => 'ميديكيوب', 'image' => 'https://images.unsplash.com/photo-1571781926291-c477ebfd024b?auto=format&fit=crop&q=80&w=400'],
            ['name_en' => 'ANUA', 'name_ar' => 'أنوا', 'image' => 'https://images.unsplash.com/photo-1512290923902-8a9f81dc236c?auto=format&fit=crop&q=80&w=400'],
            ['name_en' => 'Beauty of Joseon', 'name_ar' => 'بيوتي أوف جوسون', 'image' => 'https://images.unsplash.com/photo-1526947425960-945c6e72858f?auto=format&fit=crop&q=80&w=400'],
            ['name_en' => 'SKIN1004', 'name_ar' => 'سكِن1004', 'image' => 'https://images.unsplash.com/photo-1535585209827-a15fcdbc4c2d?auto=format&fit=crop&q=80&w=400'],
            ['name_en' => 'AXIS-Y', 'name_ar' => 'أكسيس واي', 'image' => 'https://images.unsplash.com/photo-1570172619644-dfd03ed5d881?auto=format&fit=crop&q=80&w=400'],
            ['name_en' => 'Dr.Melaxin', 'name_ar' => 'د. ميلاكسين', 'image' => 'https://images.unsplash.com/photo-1556227702-d1e4e7b5c232?auto=format&fit=crop&q=80&w=400'],
            ['name_en' => 'K-SECRET', 'name_ar' => 'كي-سيكرت', 'image' => 'https://images.unsplash.com/photo-1615397349754-cfa2066a298e?auto=format&fit=crop&q=80&w=400'],
            ['name_en' => 'VT Cosmetics', 'name_ar' => 'في تي كوزمتكس', 'image' => 'https://images.unsplash.com/photo-1616683693504-3ea7e9ad6fec?auto=format&fit=crop&q=80&w=400'],
            ['name_en' => 'celimax', 'name_ar' => 'سيليماكس', 'image' => 'https://images.unsplash.com/photo-1611080626919-7cf5a9dbab5b?auto=format&fit=crop&q=80&w=400'],
            ['name_en' => 'Dr. Althea', 'name_ar' => 'د. ألثيا', 'image' => 'https://images.unsplash.com/photo-1608248597279-f99d160bfcbc?auto=format&fit=crop&q=80&w=400'],
            ['name_en' => 'Arencia', 'name_ar' => 'أرينسيا', 'image' => 'https://images.unsplash.com/photo-1620916566398-39f1143ab7be?auto=format&fit=crop&q=80&w=400'],
            ['name_en' => 'ROUND LAB', 'name_ar' => 'راوند لاب', 'image' => 'https://images.unsplash.com/photo-1601049676099-e7ed07d825b0?auto=format&fit=crop&q=80&w=400'],
            ['name_en' => 'numbuzin', 'name_ar' => 'نمبوزن', 'image' => 'https://images.unsplash.com/photo-1598440947619-2c35fc9aa908?auto=format&fit=crop&q=80&w=400'],
            ['name_en' => 'Abib', 'name_ar' => 'أبيب', 'image' => 'https://images.unsplash.com/photo-1596755094514-f87e34085b2c?auto=format&fit=crop&q=80&w=400'],
            ['name_en' => 'EQQUALBERRY', 'name_ar' => 'إيكوالبيري', 'image' => 'https://images.unsplash.com/photo-1556228720-195a672e8a03?auto=format&fit=crop&q=80&w=400'],
            ['name_en' => 'PURITO', 'name_ar' => 'بوريتو', 'image' => 'https://images.unsplash.com/photo-1556228578-0d85b1a4d571?auto=format&fit=crop&q=80&w=400'],
            ['name_en' => 'BIODANCE', 'name_ar' => 'بيو دانس', 'image' => 'https://images.unsplash.com/photo-1617897903246-719242758050?auto=format&fit=crop&q=80&w=400'],
            ['name_en' => 'Aestura', 'name_ar' => 'أستورا', 'image' => 'https://images.unsplash.com/photo-1608571423902-eed4a5ad8108?auto=format&fit=crop&q=80&w=400'],
            ['name_en' => 'Illiyoon', 'name_ar' => 'إليون', 'image' => 'https://images.unsplash.com/photo-1612817288484-6f916006741a?auto=format&fit=crop&q=80&w=400'],
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
