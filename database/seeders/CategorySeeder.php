<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = [
            ['name_en' => 'Cleanser', 'name_ar' => 'غسول', 'image' => 'https://images.unsplash.com/photo-1556228720-195a672e8a03?auto=format&fit=crop&q=80&w=400'],
            ['name_en' => 'Toner', 'name_ar' => 'تونر', 'image' => 'https://images.unsplash.com/photo-1608248597279-f99d160bfcbc?auto=format&fit=crop&q=80&w=400'],
            ['name_en' => 'Serum', 'name_ar' => 'سيروم', 'image' => 'https://images.unsplash.com/photo-1620916566398-39f1143ab7be?auto=format&fit=crop&q=80&w=400'],
            ['name_en' => 'Moisturizer', 'name_ar' => 'مرطب', 'image' => 'https://images.unsplash.com/photo-1601049676099-e7ed07d825b0?auto=format&fit=crop&q=80&w=400'],
            ['name_en' => 'Sunscreen', 'name_ar' => 'واقي شمس', 'image' => 'https://images.unsplash.com/photo-1598440947619-2c35fc9aa908?auto=format&fit=crop&q=80&w=400'],
            ['name_en' => 'Face Mask', 'name_ar' => 'ماسك للوجه', 'image' => 'https://images.unsplash.com/photo-1596755094514-f87e34085b2c?auto=format&fit=crop&q=80&w=400'],
            ['name_en' => 'Eye Cream', 'name_ar' => 'كريم للعين', 'image' => 'https://images.unsplash.com/photo-1608248597279-f99d160bfcbc?auto=format&fit=crop&q=80&w=400'],
            ['name_en' => 'Exfoliator', 'name_ar' => 'مقشر للبشرة', 'image' => 'https://images.unsplash.com/photo-1556228578-0d85b1a4d571?auto=format&fit=crop&q=80&w=400'],
            ['name_en' => 'Lip Balm', 'name_ar' => 'مرطب شفاه', 'image' => 'https://images.unsplash.com/photo-1617897903246-719242758050?auto=format&fit=crop&q=80&w=400'],
            ['name_en' => 'Face Oil', 'name_ar' => 'زيت للوجه', 'image' => 'https://images.unsplash.com/photo-1608571423902-eed4a5ad8108?auto=format&fit=crop&q=80&w=400'],
            ['name_en' => 'Makeup Remover', 'name_ar' => 'مزيل مكياج', 'image' => 'https://images.unsplash.com/photo-1556228578-0d85b1a4d571?auto=format&fit=crop&q=80&w=400'],
            ['name_en' => 'Essence', 'name_ar' => 'إيسنس للبشرة', 'image' => 'https://images.unsplash.com/photo-1620916566398-39f1143ab7be?auto=format&fit=crop&q=80&w=400'],
            ['name_en' => 'Night Cream', 'name_ar' => 'كريم ليلي', 'image' => 'https://images.unsplash.com/photo-1601049676099-e7ed07d825b0?auto=format&fit=crop&q=80&w=400'],
            ['name_en' => 'Micellar Water', 'name_ar' => 'ماء ميسيلار', 'image' => 'https://images.unsplash.com/photo-1617897903246-719242758050?auto=format&fit=crop&q=80&w=400'],
            ['name_en' => 'Acne Treatment', 'name_ar' => 'علاج حب الشباب', 'image' => 'https://images.unsplash.com/photo-1556228720-195a672e8a03?auto=format&fit=crop&q=80&w=400'],
            ['name_en' => 'Body Lotion', 'name_ar' => 'لوشن للجسم', 'image' => 'https://images.unsplash.com/photo-1556228578-0d85b1a4d571?auto=format&fit=crop&q=80&w=400'],
        ];

        foreach ($categories as $category) {
            $filename = \Illuminate\Support\Str::slug($category['name_en']) . '.jpg';
            ImageDownloader::downloadAndSave($category['image'], 'storage/categories', $filename);

            Category::updateOrCreate(
                ['name_en' => $category['name_en']],
                [
                    'name_ar' => $category['name_ar'],
                    'image' => $filename
                ]
            );
        }
    }
}
