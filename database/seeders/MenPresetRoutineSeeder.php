<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\PresetRoutine;
use App\Models\PresetRoutineProduct;
use App\Models\Product;

class MenPresetRoutineSeeder extends Seeder
{
    /**
     * Seed the 5 official Men's Curated Preset Routines matching R479.
     */
    public function run(): void
    {
        // Clear existing Men routines
        $existing = PresetRoutine::forMen()->get();
        foreach ($existing as $ex) {
            $ex->items()->delete();
            $ex->delete();
        }

        $dataPath = database_path('seeders/data/men_routines_data.json');
        if (!file_exists($dataPath)) {
            return;
        }

        $menRoutines = json_decode(file_get_contents($dataPath), true);

        foreach ($menRoutines as $config) {
            $skinTypeId = $config['skin_type_id'] ?? null;
            if ($skinTypeId && !\App\Models\SkinType::where('id', $skinTypeId)->exists()) {
                $skinTypeId = null;
            }

            $goalId = $config['goal_id'] ?? null;
            if ($goalId && !\App\Models\RoutineGoal::where('id', $goalId)->exists()) {
                $goalId = null;
            }

            $routine = PresetRoutine::create([
                'slug' => $config['slug'] ?? null,
                'title_ar' => $config['title_ar'],
                'title_en' => $config['title_en'],
                'subtitle_ar' => $config['subtitle_ar'] ?? null,
                'subtitle_en' => $config['subtitle_en'] ?? null,
                'description_ar' => $config['description_ar'],
                'description_en' => $config['description_en'],
                'card_note_ar' => $config['card_note_ar'] ?? null,
                'card_note_en' => $config['card_note_en'] ?? null,
                'audience_ar' => $config['audience_ar'] ?? null,
                'audience_en' => $config['audience_en'] ?? null,
                'badge_ar' => $config['badge_ar'] ?? 'عناية مخصصة للرجال',
                'badge_en' => $config['badge_en'] ?? "Men's Care Routine",
                'skin_type_ar' => $config['skin_type_ar'] ?? 'جميع أنواع البشرة',
                'skin_type_en' => $config['skin_type_en'] ?? 'All Skin Types',
                'goal_ar' => $config['goal_ar'] ?? '',
                'goal_en' => $config['goal_en'] ?? '',
                'skin_type_id' => $skinTypeId,
                'goal_id' => $goalId,
                'is_for_men' => true,
                'status' => 'active',
                'timing_json' => $config['timing_json'] ?? null,
                'cover_image' => $config['cover_image'] ?? null,
                'cover_json' => $config['cover_json'] ?? null,
                'benefits_json' => $config['benefits_json'] ?? [],
                'what_it_offers_json' => $config['what_it_offers_json'] ?? [],
            ]);

            foreach ($config['steps'] as $step) {
                $product = Product::where('sku', $step['sku'])->first();
                if (!$product) {
                    continue;
                }

                PresetRoutineProduct::create([
                    'preset_routine_id' => $routine->id,
                    'product_id' => $product->id,
                    'display_order' => $step['order'],
                    'step_name_ar' => $step['role_ar'] ?? 'الخطوة ' . $step['order'],
                    'step_name_en' => $step['role_en'] ?? 'Step ' . $step['order'],
                    'morning' => (bool)$step['morning'],
                    'night' => (bool)$step['night'],
                    'use_time_ar' => $step['use_time_ar'] ?? 'صباحاً ومساءً',
                    'frequency_ar' => $step['frequency_ar'] ?? null,
                    'frequency_en' => $step['frequency_en'] ?? null,
                    'application_ar' => $step['application_ar'] ?? null,
                ]);
            }
        }
    }
}
