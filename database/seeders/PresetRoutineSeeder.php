<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\PresetRoutine;
use App\Models\PresetRoutineProduct;
use Database\Seeders\Support\PresetRoutineEnglishBuilder;
use App\Models\Product;

class PresetRoutineSeeder extends Seeder
{
    /**
     * Seed the 8 official KCODE Curated Preset Routines matching R479.
     */
    public function run(): void
    {
        // 1. Ensure storage directory exists and copy images from tracked assets
        $sourceDir = database_path('seeders/assets/routines');
        $targetDir = storage_path('app/public/routines');
        if (!\Illuminate\Support\Facades\File::exists($targetDir)) {
            \Illuminate\Support\Facades\File::makeDirectory($targetDir, 0755, true, true);
        }
        if (\Illuminate\Support\Facades\File::exists($sourceDir)) {
            \Illuminate\Support\Facades\File::copyDirectory($sourceDir, $targetDir);
        }

        // Clear existing non-men routines
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
            $config = PresetRoutineEnglishBuilder::enrichWomenConfig($config);
            $skinTypeId = $config['skin_type_id'] ?? null;
            if ($skinTypeId && !\App\Models\SkinType::where('id', $skinTypeId)->exists()) {
                $skinTypeId = null;
            }

            $goalId = $config['goal_id'] ?? null;
            if ($goalId && !\App\Models\RoutineGoal::where('id', $goalId)->exists()) {
                $goalId = null;
            }

            $presetRoutine = PresetRoutine::create([
                'slug' => $config['slug'] ?? null,
                'title_ar' => $config['title_ar'],
                'title_en' => $config['title_en'],
                'subtitle_ar' => $config['subtitle_ar'] ?? null,
                'subtitle_en' => $config['subtitle_en'] ?? null,
                'description_ar' => $config['description_ar'],
                'description_en' => $config['description_en'],
                'badge_ar' => $config['badge_ar'],
                'badge_en' => $config['badge_en'],
                'skin_type_ar' => $config['skin_type_ar'],
                'skin_type_en' => $config['skin_type_en'],
                'goal_ar' => $config['goal_ar'],
                'goal_en' => $config['goal_en'],
                'card_note_ar' => $config['note_ar'] ?? null,
                'card_note_en' => $config['card_note_en'] ?? null,
                'audience_ar' => $config['audience_ar'] ?? null,
                'audience_en' => $config['audience_en'] ?? null,
                'skin_type_id' => $skinTypeId,
                'goal_id' => $goalId,
                'is_for_men' => false,
                'status' => 'active',
                'timing_json' => $config['timing_json'] ?? null,
                'cover_image' => $config['cover_image'] ?? null,
                'cover_json' => $config['cover_json'] ?? null,
                'benefits_json' => $config['benefits_json'] ?? [],
                'benefits_en_json' => $config['benefits_en_json'] ?? [],
                'what_it_offers_json' => $config['what_it_offers_json'] ?? [],
                'what_it_offers_en_json' => $config['what_it_offers_en_json'] ?? [],
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

                $stepNameAr = 'الخطوة ' . $itemData['order'];
                if ($product->category) {
                    $stepNameAr = $product->category->name_ar ?: $stepNameAr;
                }

                PresetRoutineProduct::create([
                    'preset_routine_id' => $presetRoutine->id,
                    'product_id' => $product->id,
                    'display_order' => $itemData['order'],
                    'step_name_ar' => $itemData['step_name_ar'] ?? $stepNameAr,
                    'step_name_en' => $itemData['step_name_en'] ?? ('Step ' . $itemData['order']),
                    'morning' => $itemData['morning'] ?? true,
                    'night' => $itemData['night'] ?? true,
                    'use_time_ar' => $itemData['use_time_ar'] ?? 'صباحاً ومساءً',
                    'use_time_en' => $itemData['use_time_en'] ?? 'Morning & Evening',
                    'reason_ar' => $itemData['reason_ar'] ?? ($itemData['support_copy'] ?? null),
                    'reason_en' => $itemData['reason_en'] ?? null,
                    'frequency_ar' => $itemData['frequency_ar'] ?? 'يومياً',
                    'frequency_en' => $itemData['frequency_en'] ?? 'Daily.',
                    'application_ar' => $itemData['application_ar'] ?? null,
                    'application_en' => $itemData['application_en'] ?? null,
                    'starter_guide_ar' => $itemData['starter_guide_ar'] ?? null,
                    'starter_guide_en' => $itemData['starter_guide_en'] ?? null,
                ]);
            }
        }
    }
}
