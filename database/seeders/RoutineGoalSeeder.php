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
                'image_url' => 'https://images.unsplash.com/photo-1620916566398-39f1143ab7be?auto=format&fit=crop&q=80&w=400',
            ],
            [
                'name_ar' => 'حبوب وآثارها',
                'name_en' => 'Acne & Scars',
                'image_url' => 'https://images.unsplash.com/photo-1556228720-195a672e8a03?auto=format&fit=crop&q=80&w=400',
            ],
            [
                'name_ar' => 'العناية بالمسام',
                'name_en' => 'Pore Care',
                'image_url' => 'https://images.unsplash.com/photo-1522337360788-8b13dee7a37e?auto=format&fit=crop&q=80&w=400',
            ],
            [
                'name_ar' => 'تفتيح وتوحيد',
                'name_en' => 'Brightening & Evening Tone',
                'image_url' => 'https://images.unsplash.com/photo-1596755094514-f87e34085b2c?auto=format&fit=crop&q=80&w=400',
            ],
            [
                'name_ar' => 'ترطيب وحماية',
                'name_en' => 'Hydration & Protection',
                'image_url' => 'https://images.unsplash.com/photo-1601049676099-e7ed07d825b0?auto=format&fit=crop&q=80&w=400',
            ],
        ];

        foreach ($goals as $goal) {
            $filename = \Illuminate\Support\Str::slug($goal['name_en']) . '.jpg';
            ImageDownloader::downloadAndSave($goal['image_url'], 'routine-goals', $filename);

            RoutineGoal::updateOrCreate(
                ['name_en' => $goal['name_en']],
                [
                    'name_ar' => $goal['name_ar'],
                    'image' => $filename,
                ]
            );
        }
    }
}
