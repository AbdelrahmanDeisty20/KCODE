<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\PresetRoutine;
use App\Models\PresetRoutineProduct;
use App\Models\Product;

class PresetRoutineSeeder extends Seeder
{
    /**
     * Seed the 8 official KCODE Curated Preset Routines matching the prototype.
     */
    public function run(): void
    {
        // Remove existing non-men preset routines and their items
        $existing = PresetRoutine::forWomen()->get();
        foreach ($existing as $ex) {
            $ex->items()->delete();
            $ex->delete();
        }

        $dataPath = database_path('seeders/data/preset_routines_data.json');
        if (!file_exists($dataPath)) {
            return;
        }

        $routinesConfig = json_decode(file_get_contents($dataPath), true);

        foreach ($routinesConfig as $config) {
            $skinTypeId = $config['skin_type_id'] ?? null;
            if ($skinTypeId && !\App\Models\SkinType::where('id', $skinTypeId)->exists()) {
                $skinTypeId = null;
            }

            $goalId = $config['goal_id'] ?? null;
            if ($goalId && !\App\Models\RoutineGoal::where('id', $goalId)->exists()) {
                $goalId = null;
            }

            $presetRoutine = PresetRoutine::create([
                'title_ar' => $config['title_ar'],
                'title_en' => $config['title_en'],
                'description_ar' => $config['description_ar'],
                'description_en' => $config['description_en'],
                'badge_ar' => $config['badge_ar'],
                'badge_en' => $config['badge_en'],
                'skin_type_ar' => $config['skin_type_ar'],
                'skin_type_en' => $config['skin_type_en'],
                'goal_ar' => $config['goal_ar'],
                'goal_en' => $config['goal_en'],
                'skin_type_id' => $skinTypeId,
                'goal_id' => $goalId,
                'is_for_men' => false,
                'status' => 'active',
                'benefits_json' => $config['benefits_json'] ?? [],
                'what_it_offers_json' => $config['what_it_offers_json'] ?? [],
                'usage_guide_json' => $config['usage_guide_json'] ?? null,
                'how_to_use_ar' => $config['how_to_use_ar'] ?? null,
                'how_to_use_en' => $config['how_to_use_en'] ?? null,
                'complementary_products_json' => $config['complementary_products_json'] ?? [],
            ]);

            foreach ($config['items'] as $itemData) {
                $product = Product::where('sku', $itemData['sku'])->first();
                if (!$product) {
                    continue;
                }

                PresetRoutineProduct::create([
                    'preset_routine_id' => $presetRoutine->id,
                    'product_id' => $product->id,
                    'display_order' => $itemData['order'],
                    'step_name_ar' => $itemData['step_name_ar'],
                    'step_name_en' => $itemData['step_name_en'] ?? null,
                    'morning' => (bool) $itemData['morning'],
                    'night' => (bool) $itemData['night'],
                    'use_time_ar' => $itemData['use_time_ar'],
                    'reason_ar' => $itemData['reason_ar'] ?? null,
                    'frequency_ar' => $itemData['frequency_ar'] ?? null,
                    'application_ar' => $itemData['application_ar'] ?? null,
                    'starter_guide_ar' => $itemData['starter_guide_ar'] ?? null,
                ]);
            }
        }
    }
}
