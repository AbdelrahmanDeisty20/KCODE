<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

use App\Models\SkinType;

class SkinTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $skinTypes = [
            [
                'name_ar' => 'دهنية',
                'name_en' => 'Oily',
            ],
            [
                'name_ar' => 'جافة',
                'name_en' => 'Dry',
            ],
            [
                'name_ar' => 'مختلطة',
                'name_en' => 'Combination',
            ],
            [
                'name_ar' => 'حساسة',
                'name_en' => 'Sensitive',
            ],
        ];

        foreach ($skinTypes as $type) {
            SkinType::updateOrCreate(['name_en' => $type['name_en']], $type);
        }
    }
}
