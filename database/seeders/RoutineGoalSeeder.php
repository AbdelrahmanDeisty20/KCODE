<?php

namespace Database\Seeders;

use App\Models\RoutineGoal;
use Illuminate\Database\Seeder;

class RoutineGoalSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $goals = [
            [
                'name_ar' => 'إشراقة ونضارة',
                'name_en' => 'Radiance & Freshness',
                'image' => 'radiance.png',
            ],
            [
                'name_ar' => 'حبوب وآثارها',
                'name_en' => 'Acne & Scars',
                'image' => 'acne.png',
            ],
            [
                'name_ar' => 'العناية بالمسام',
                'name_en' => 'Pore Care',
                'image' => 'pores.png',
            ],
            [
                'name_ar' => 'تفتيح وتوحيد',
                'name_en' => 'Brightening & Evening Tone',
                'image' => 'brightening.png',
            ],
            [
                'name_ar' => 'ترطيب وحماية',
                'name_en' => 'Hydration & Protection',
                'image' => 'hydration.png',
            ],
        ];

        foreach ($goals as $goal) {
            RoutineGoal::updateOrCreate(
                ['name_en' => $goal['name_en']],
                [
                    'name_ar' => $goal['name_ar'],
                    'image' => $goal['image'],
                ]
            );
        }
    }
}
