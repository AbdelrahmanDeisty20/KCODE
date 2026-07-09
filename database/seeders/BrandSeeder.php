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
            ['name_en' => 'La Roche-Posay', 'name_ar' => 'لاروش بوزيه'],
            ['name_en' => 'CeraVe', 'name_ar' => 'سيرافي'],
            ['name_en' => 'The Ordinary', 'name_ar' => 'ذا أورديناري'],
            ['name_en' => 'Vichy', 'name_ar' => 'فيشي'],
            ['name_en' => 'Bioderma', 'name_ar' => 'بيوديرما'],
            ['name_en' => 'Neutrogena', 'name_ar' => 'نيتروجينا'],
            ['name_en' => 'Eucerin', 'name_ar' => 'يوسيرين'],
            ['name_en' => 'Cetaphil', 'name_ar' => 'سيتافيل'],
            ['name_en' => 'Laneige', 'name_ar' => 'لانيج'],
            ['name_en' => 'COSRX', 'name_ar' => 'كوزريكس'],
            ['name_en' => 'L\'Oréal Paris', 'name_ar' => 'لوريال باريس'],
            ['name_en' => 'Clinique', 'name_ar' => 'كلينيك'],
            ['name_en' => 'Kiehl\'s', 'name_ar' => 'كيلز'],
            ['name_en' => 'Paula\'s Choice', 'name_ar' => 'بولاز تشويس'],
            ['name_en' => 'Avène', 'name_ar' => 'أفين'],
        ];

        foreach ($brands as $brand) {
            Brand::updateOrCreate(
                ['name_en' => $brand['name_en']],
                ['name_ar' => $brand['name_ar']]
            );
        }
    }
}
