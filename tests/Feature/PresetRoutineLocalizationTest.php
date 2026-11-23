<?php

namespace Tests\Feature;

use App\Models\Brand;
use App\Models\Category;
use App\Models\PresetRoutine;
use App\Models\PresetRoutineProduct;
use App\Models\Product;
use App\Models\SubCategory;
use Database\Seeders\BackfillEnglishPresetRoutinesSeeder;
use Database\Seeders\MenPresetRoutineSeeder;
use Database\Seeders\PresetRoutineSeeder;
use Database\Seeders\Support\PresetRoutineEnglishBuilder;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class PresetRoutineLocalizationTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        Schema::dropAllTables();

        $migrations = glob(database_path('migrations/*.php'));
        sort($migrations);

        $skipMigrations = [
            '2026_07_26_154500_change_order_status_to_enum_in_orders_table.php',
            '2026_11_12_000001_fix_product_short_name_ar.php',
            '2026_11_18_115000_prefix_categories_and_brands_image_paths.php',
        ];

        foreach ($migrations as $file) {
            if (in_array(basename($file), $skipMigrations, true)) {
                continue;
            }

            $migration = require $file;
            if (is_object($migration) && method_exists($migration, 'up')) {
                $migration->up();
            }
        }

        \App\Support\StaticCache::flushAll();
    }

    private function seedRoutineCatalogProducts(): void
    {
        $brand = Brand::create([
            'name_ar' => 'سكن 1004',
            'name_en' => 'KCODE LAB',
        ]);

        $category = Category::create([
            'name_ar' => 'العناية بالبشرة',
            'name_en' => 'Skincare',
            'description_ar' => 'منتجات العناية بالبشرة',
            'description_en' => 'Skincare essentials',
        ]);

        $subCategory = SubCategory::create([
            'name_ar' => 'روتين يومي',
            'name_en' => 'Daily Care',
            'category_id' => $category->id,
        ]);

        $skus = [];
        $women = json_decode(file_get_contents(database_path('seeders/data/preset_routines_data.json')), true) ?: [];
        foreach ($women as $r) {
            foreach ($r['items'] ?? [] as $item) {
                if (!empty($item['sku'])) {
                    $skus[$item['sku']] = $item['source_name'] ?? $item['sku'];
                }
            }
            foreach ($r['complementary_products_json'] ?? [] as $comp) {
                if (!empty($comp['sku'])) {
                    $skus[$comp['sku']] = $comp['name'] ?? $comp['sku'];
                }
            }
        }

        $men = json_decode(file_get_contents(database_path('seeders/data/men_routines_data.json')), true) ?: [];
        foreach ($men as $r) {
            foreach ($r['steps'] ?? [] as $step) {
                if (!empty($step['sku']) && !isset($skus[$step['sku']])) {
                    $skus[$step['sku']] = 'KCODE ' . $step['sku'];
                }
            }
        }

        foreach ($skus as $sku => $name) {
            Product::create([
                'sku' => $sku,
                'name_en' => $name,
                'name_ar' => $name,
                'short_name_en' => $name,
                'short_name_ar' => $name,
                'description_en' => 'English product formulation overview.',
                'description_ar' => 'وصف عربي للمنتج.',
                'ingredients_en' => 'Water, Glycerin',
                'ingredients_ar' => 'Water, Glycerin',
                'how_to_use_en' => 'Apply gently onto skin.',
                'how_to_use_ar' => 'يوضع بلطف على البشرة.',
                'category_id' => $category->id,
                'sub_category_id' => $subCategory->id,
                'brand_id' => $brand->id,
                'price' => 10.50,
                'size' => '50 ml',
                'status' => 'active',
                'stock' => 10,
                'role_ar' => 'عناية بالبشرة',
                'role_en' => 'Skincare Step',
                'brief_insight_ar' => 'ملخص عربي للمنتج',
                'brief_insight_en' => 'Balanced English product insight.',
            ]);
        }
    }

    private function assertNoArabic(?string $value, string $context): void
    {
        if ($value === null || $value === '') {
            return;
        }
        $this->assertDoesNotMatchRegularExpression(
            '/[\x{0600}-\x{06FF}\x{0750}-\x{077F}\x{08A0}-\x{08FF}]/u',
            $value,
            "Unexpected Arabic characters in {$context}: {$value}"
        );
    }

    private function assertContainsArabic(?string $value, string $context): void
    {
        $this->assertNotEmpty($value, "Expected non-empty Arabic in {$context}");
        $this->assertMatchesRegularExpression(
            '/[\x{0600}-\x{06FF}]/u',
            (string) $value,
            "Expected Arabic characters in {$context}, got: {$value}"
        );
    }

    public function test_women_preset_routines_and_details_switch_cleanly_between_ar_and_en(): void
    {
        $this->seedRoutineCatalogProducts();
        $this->seed(PresetRoutineSeeder::class);

        $enListRes = $this->withHeaders(['lang' => 'en'])
            ->getJson('/api/preset-routines?per_page=20');
        $enListRes->assertOk();

        $enList = $enListRes->json('data');
        $this->assertCount(8, $enList);

        foreach ($enList as $routine) {
            $slug = $routine['slug'] ?? $routine['id'];
            foreach (['title', 'subtitle', 'description', 'badge', 'skin_type', 'goal', 'card_note', 'audience'] as $field) {
                $this->assertNotEmpty($routine[$field], "Expected {$field} on women routine {$slug} in EN list");
                $this->assertNoArabic($routine[$field], "women.list.{$slug}.{$field}");
            }
            $this->assertNotEmpty($routine['benefits'], "Expected benefits on {$slug}");
            foreach ($routine['benefits'] as $bi => $benefit) {
                $this->assertNoArabic($benefit, "women.list.{$slug}.benefits[{$bi}]");
            }
            if (!empty($routine['cover']['alt'])) {
                $this->assertNoArabic($routine['cover']['alt'], "women.list.{$slug}.cover.alt");
            }

            $enDetailRes = $this->withHeaders(['lang' => 'en'])
                ->getJson('/api/preset-routines/' . $slug);
            $enDetailRes->assertOk();
            $detailEn = $enDetailRes->json('data');

            $this->assertNotEmpty($detailEn['what_it_offers']);
            foreach ($detailEn['what_it_offers'] as $oi => $offer) {
                $this->assertNoArabic($offer, "women.detail.{$slug}.what_it_offers[{$oi}]");
            }

            $this->assertNotEmpty($detailEn['how_to_use']);
            $this->assertNoArabic($detailEn['how_to_use'], "women.detail.{$slug}.how_to_use");

            $guide = $detailEn['usage_guide'];
            $this->assertIsArray($guide);
            $this->assertSame('Morning Steps', $guide['am_title']);
            $this->assertSame('Evening Steps', $guide['pm_title']);
            $this->assertSame('Regular Care Night', $guide['pm_standard_label']);
            $this->assertNoArabic($guide['night_notes'] ?? '', "women.detail.{$slug}.night_notes");

            foreach ($guide['am_steps'] ?? [] as $si => $step) {
                $this->assertNotEmpty($step['role']);
                $this->assertNotEmpty($step['application']);
                $this->assertNotEmpty($step['frequency']);
                $this->assertNoArabic($step['role'], "women.detail.{$slug}.am_steps[{$si}].role");
                $this->assertNoArabic($step['application'], "women.detail.{$slug}.am_steps[{$si}].application");
                $this->assertNoArabic($step['frequency'], "women.detail.{$slug}.am_steps[{$si}].frequency");
                $this->assertNoArabic($step['starter_guide'] ?? '', "women.detail.{$slug}.am_steps[{$si}].starter_guide");
            }

            foreach ($guide['pm_standard_steps'] ?? [] as $si => $step) {
                $this->assertNoArabic($step['role'] ?? '', "women.detail.{$slug}.pm_standard_steps[{$si}].role");
                $this->assertNoArabic($step['application'] ?? '', "women.detail.{$slug}.pm_standard_steps[{$si}].application");
                $this->assertNoArabic($step['frequency'] ?? '', "women.detail.{$slug}.pm_standard_steps[{$si}].frequency");
                $this->assertNoArabic($step['starter_guide'] ?? '', "women.detail.{$slug}.pm_standard_steps[{$si}].starter_guide");
            }

            foreach ($guide['pm_variants'] ?? [] as $vi => $variant) {
                $this->assertNotEmpty($variant['label']);
                $this->assertNoArabic($variant['label'], "women.detail.{$slug}.pm_variants[{$vi}].label");
                $this->assertNoArabic($variant['badge'] ?? '', "women.detail.{$slug}.pm_variants[{$vi}].badge");
                $this->assertNoArabic($variant['note'] ?? '', "women.detail.{$slug}.pm_variants[{$vi}].note");
                foreach ($variant['steps'] ?? [] as $vsi => $vStep) {
                    $this->assertNoArabic($vStep['role'] ?? '', "women.detail.{$slug}.pm_variants[{$vi}].steps[{$vsi}].role");
                    $this->assertNoArabic($vStep['application'] ?? '', "women.detail.{$slug}.pm_variants[{$vi}].steps[{$vsi}].application");
                    $this->assertNoArabic($vStep['frequency'] ?? '', "women.detail.{$slug}.pm_variants[{$vi}].steps[{$vsi}].frequency");
                    $this->assertNoArabic($vStep['starter_guide'] ?? '', "women.detail.{$slug}.pm_variants[{$vi}].steps[{$vsi}].starter_guide");
                }
            }

            $this->assertCount(5, $detailEn['items']);
            foreach ($detailEn['items'] as $ii => $item) {
                $this->assertNotEmpty($item['step_name']);
                $this->assertNotEmpty($item['use_time']);
                $this->assertNotEmpty($item['use_time_ar']);
                $this->assertNotEmpty($item['frequency']);
                $this->assertNotEmpty($item['application']);
                $this->assertNoArabic($item['step_name'], "women.detail.{$slug}.items[{$ii}].step_name");
                $this->assertNoArabic($item['use_time'], "women.detail.{$slug}.items[{$ii}].use_time");
                $this->assertNoArabic($item['use_time_ar'], "women.detail.{$slug}.items[{$ii}].use_time_ar");
                $this->assertNoArabic($item['frequency'], "women.detail.{$slug}.items[{$ii}].frequency");
                $this->assertNoArabic($item['application'], "women.detail.{$slug}.items[{$ii}].application");
                $this->assertNoArabic($item['starter_guide'] ?? '', "women.detail.{$slug}.items[{$ii}].starter_guide");
                $this->assertNoArabic($item['reason'] ?? '', "women.detail.{$slug}.items[{$ii}].reason");
                $this->assertNoArabic($item['product']['name'], "women.detail.{$slug}.items[{$ii}].product.name");
                $this->assertNoArabic($item['product']['short_name'], "women.detail.{$slug}.items[{$ii}].product.short_name");
            }

            $this->assertCount(2, $detailEn['complementary_products']);
            foreach ($detailEn['complementary_products'] as $ci => $comp) {
                $this->assertNotEmpty($comp['why_selected']);
                $this->assertNotEmpty($comp['application']);
                $this->assertNoArabic($comp['name'], "women.detail.{$slug}.comp[{$ci}].name");
                $this->assertNoArabic($comp['why_selected'], "women.detail.{$slug}.comp[{$ci}].why_selected");
                $this->assertNoArabic($comp['step'] ?? '', "women.detail.{$slug}.comp[{$ci}].step");
                $this->assertNoArabic($comp['application'], "women.detail.{$slug}.comp[{$ci}].application");
            }
        }

        // Arabic regression check across ALL 8 women routines list + acne details
        $arListRes = $this->withHeaders(['lang' => 'ar'])
            ->getJson('/api/preset-routines?per_page=20');
        $arListRes->assertOk();
        $arList = $arListRes->json('data');
        $this->assertCount(8, $arList);

        foreach ($arList as $arRoutine) {
            $slug = $arRoutine['slug'] ?? $arRoutine['id'];
            foreach (['title', 'subtitle', 'description', 'badge', 'skin_type', 'goal', 'card_note', 'audience'] as $field) {
                $this->assertNotEmpty($arRoutine[$field], "Expected {$field} on women routine {$slug} in AR list");
                $this->assertContainsArabic($arRoutine[$field], "women.ar.list.{$slug}.{$field}");
            }
        }

        $arDetailRes = $this->withHeaders(['lang' => 'ar'])
            ->getJson('/api/preset-routines/acne');
        $arDetailRes->assertOk();
        $arDetail = $arDetailRes->json('data');

        $this->assertContainsArabic($arDetail['title'], 'acne.ar.title');
        $this->assertContainsArabic($arDetail['description'], 'acne.ar.description');
        $this->assertContainsArabic($arDetail['how_to_use'], 'acne.ar.how_to_use');
        $this->assertSame('خطوات الصباح', $arDetail['usage_guide']['am_title']);
        $this->assertContainsArabic($arDetail['items'][0]['use_time_ar'], 'acne.ar.items[0].use_time_ar');
        $this->assertContainsArabic($arDetail['items'][0]['application'], 'acne.ar.items[0].application');
        $this->assertContainsArabic($arDetail['complementary_products'][0]['application'], 'acne.ar.comp[0].application');
        // Product name remains fixed English even in AR
        $this->assertSame('SKIN1004 Madagascar Centella Ampoule Foam', $arDetail['items'][0]['product']['name']);
    }

    public function test_men_preset_routines_and_details_switch_cleanly_between_ar_and_en(): void
    {
        $this->seedRoutineCatalogProducts();
        $this->seed(MenPresetRoutineSeeder::class);

        $enListRes = $this->withHeaders(['lang' => 'en'])
            ->getJson('/api/men-preset-routines?per_page=10');
        $enListRes->assertOk();

        $enList = $enListRes->json('data');
        $this->assertCount(5, $enList);

        foreach ($enList as $routine) {
            $slug = $routine['slug'];
            foreach (['title', 'subtitle', 'description', 'badge', 'skin_type', 'goal', 'card_note', 'audience'] as $field) {
                $this->assertNotEmpty($routine[$field], "Expected {$field} on men routine {$slug}");
                $this->assertNoArabic($routine[$field], "men.list.{$slug}.{$field}");
            }
            $this->assertNoArabic($routine['timing']['am'] ?? '', "men.list.{$slug}.timing.am");
            $this->assertNoArabic($routine['timing']['pm'] ?? '', "men.list.{$slug}.timing.pm");
            foreach ($routine['benefits'] as $bi => $benefit) {
                $this->assertNoArabic($benefit, "men.list.{$slug}.benefits[{$bi}]");
            }

            $detailRes = $this->getJson('/api/men-preset-routines/' . $routine['id'] . '?lang=en');
            $detailRes->assertOk();
            $detail = $detailRes->json('data');

            $this->assertNotEmpty($detail['how_to_use']);
            $this->assertNoArabic($detail['how_to_use'], "men.detail.{$slug}.how_to_use");
            foreach ($detail['what_it_offers'] as $oi => $offer) {
                $this->assertNoArabic($offer, "men.detail.{$slug}.what_it_offers[{$oi}]");
            }

            $guide = $detail['usage_guide'];
            foreach ($guide['morning_steps'] ?? [] as $si => $ms) {
                $this->assertNotEmpty($ms['role']);
                $this->assertNotEmpty($ms['instruction']);
                $this->assertNoArabic($ms['role'], "men.detail.{$slug}.morning_steps[{$si}].role");
                $this->assertNoArabic($ms['role_ar'], "men.detail.{$slug}.morning_steps[{$si}].role_ar");
                $this->assertNoArabic($ms['instruction'], "men.detail.{$slug}.morning_steps[{$si}].instruction");
                $this->assertNoArabic($ms['instruction_ar'], "men.detail.{$slug}.morning_steps[{$si}].instruction_ar");
            }
            foreach ($guide['evening_variants'] ?? [] as $vi => $ev) {
                $this->assertNoArabic($ev['label'], "men.detail.{$slug}.evening_variants[{$vi}].label");
                $this->assertNoArabic($ev['label_ar'], "men.detail.{$slug}.evening_variants[{$vi}].label_ar");
                foreach ($ev['steps'] ?? [] as $esi => $es) {
                    $this->assertNoArabic($es['role'], "men.detail.{$slug}.evening_variants[{$vi}].steps[{$esi}].role");
                    $this->assertNoArabic($es['instruction'], "men.detail.{$slug}.evening_variants[{$vi}].steps[{$esi}].instruction");
                    $this->assertNoArabic($es['instruction_ar'], "men.detail.{$slug}.evening_variants[{$vi}].steps[{$esi}].instruction_ar");
                }
            }
            foreach ($guide['notes_ar'] ?? [] as $ni => $note) {
                $this->assertNoArabic($note, "men.detail.{$slug}.notes_ar[{$ni}]");
            }

            $this->assertNotEmpty($detail['items']);
            foreach ($detail['items'] as $ii => $item) {
                $this->assertNoArabic($item['step_name'], "men.detail.{$slug}.items[{$ii}].step_name");
                $this->assertNoArabic($item['use_time_ar'], "men.detail.{$slug}.items[{$ii}].use_time_ar");
                $this->assertNoArabic($item['frequency'], "men.detail.{$slug}.items[{$ii}].frequency");
                $this->assertNoArabic($item['application'], "men.detail.{$slug}.items[{$ii}].application");
                $this->assertNoArabic($item['product']['name'], "men.detail.{$slug}.items[{$ii}].product.name");
            }
        }

        // Arabic check for men shaving routine
        $arShavingRes = $this->withHeaders(['lang' => 'ar'])
            ->getJson('/api/men-preset-routines/' . PresetRoutine::where('is_for_men', true)->where('slug', 'shaving')->firstOrFail()->id);
        $arShavingRes->assertOk();
        $arShaving = $arShavingRes->json('data');

        $this->assertSame('عناية بعد الحلاقة', $arShaving['title']);
        $this->assertContainsArabic($arShaving['timing']['am'], 'men.shaving.ar.timing.am');
        $this->assertContainsArabic($arShaving['usage_guide']['morning_steps'][0]['instruction_ar'], 'men.shaving.ar.instruction_ar');
    }

    public function test_legacy_arabic_contaminated_routine_never_leaks_arabic_in_english_mode(): void
    {
        $this->seedRoutineCatalogProducts();
        $prod = Product::where('sku', 'KC0104')->firstOrFail();

        $legacyRoutine = PresetRoutine::create([
            'slug' => 'legacy-test-routine',
            'title_ar' => 'روتين تجريبي قديم',
            'title_en' => 'Legacy Test Routine',
            'subtitle_ar' => 'وصف قصير بالعربي',
            'subtitle_en' => 'وصف قصير بالعربي',
            'description_ar' => 'وصف طويل بالعربي',
            'description_en' => 'وصف طويل بالعربي',
            'badge_ar' => 'شارة عربية',
            'badge_en' => null,
            'skin_type_ar' => 'جميع أنواع البشرة',
            'skin_type_en' => 'جميع أنواع البشرة',
            'goal_ar' => 'هدف عربي',
            'goal_en' => 'هدف عربي',
            'card_note_ar' => 'ملاحظة عربية',
            'card_note_en' => 'ملاحظة عربية',
            'audience_ar' => 'جمهور عربي',
            'audience_en' => 'جمهور عربي',
            'is_for_men' => true,
            'status' => 'active',
            'timing_json' => ['am' => 'غسول ثم واقي شمس', 'pm' => 'غسول ثم مرطب'],
            'benefits_json' => ['فائدة عربية أولى'],
            'benefits_en_json' => null,
            'what_it_offers_json' => ['ميزة عربية مفصلة'],
            'what_it_offers_en_json' => null,
            'how_to_use_ar' => 'استخدمه يومياً',
            'how_to_use_en' => null,
            'usage_guide_json' => [
                'morning_steps' => [
                    ['step_number' => 1, 'sku' => 'KC0104', 'role_ar' => 'غسول', 'instruction_ar' => 'اغسل وجهك بالماء والغسول'],
                ],
                'evening_variants' => [
                    ['id' => 'default', 'label_ar' => 'الليالي العادية', 'steps' => []],
                ],
                'notes_ar' => ['ملاحظة ليلية عربية'],
            ],
            'complementary_products_json' => [
                [
                    'sku' => 'MISSING_SKU',
                    'name' => 'Test Complementary',
                    'reason' => 'سبب عربي فقط',
                    'application' => 'طريقة استخدام عربية فقط',
                ],
            ],
        ]);

        PresetRoutineProduct::create([
            'preset_routine_id' => $legacyRoutine->id,
            'product_id' => $prod->id,
            'display_order' => 1,
            'step_name_ar' => 'غسول',
            'step_name_en' => 'غسول',
            'morning' => true,
            'night' => true,
            'use_time_ar' => 'صباحاً ومساءً',
            'use_time_en' => null,
            'frequency_ar' => 'يوميًا.',
            'frequency_en' => 'يوميًا.',
            'application_ar' => 'تعليمات عربية فقط',
            'application_en' => null,
        ]);

        $res = $this->withHeaders(['lang' => 'en'])
            ->getJson('/api/men-preset-routines/' . $legacyRoutine->id);
        $res->assertOk();
        $data = $res->json('data');

        $this->assertSame('Legacy Test Routine', $data['title']);
        $this->assertSame("Men's Care Routine", $data['badge']);
        $this->assertNull($data['description']);
        $this->assertNull($data['subtitle']);
        $this->assertNull($data['skin_type']);
        $this->assertNull($data['card_note']);
        $this->assertNull($data['timing']['am']);
        $this->assertSame([], $data['benefits']);
        $this->assertSame([], $data['what_it_offers']);
        $this->assertSame('Cleanser', $data['items'][0]['step_name']);
        $this->assertSame('Morning & Evening', $data['items'][0]['use_time_ar']);
        $this->assertSame('Daily.', $data['items'][0]['frequency']);
        $this->assertNull($data['items'][0]['application']);
        $this->assertSame('Cleanser', $data['usage_guide']['morning_steps'][0]['role']);
        $this->assertSame('Cleanser', $data['usage_guide']['morning_steps'][0]['role_ar']);
        $this->assertSame('Regular Evenings', $data['usage_guide']['evening_variants'][0]['label']);
        $this->assertSame([], $data['usage_guide']['notes_ar']);
        $this->assertNull($data['complementary_products'][0]['why_selected']);
        $this->assertNull($data['complementary_products'][0]['application']);
    }

    public function test_backfill_english_preset_routines_seeder_fixes_legacy_rows_and_preserves_custom_edits(): void
    {
        $this->seedRoutineCatalogProducts();
        $this->seed(PresetRoutineSeeder::class);
        $this->seed(MenPresetRoutineSeeder::class);

        $acne = PresetRoutine::forWomen()->where('slug', 'acne')->firstOrFail();
        $originalAcneId = $acne->id;
        $acne->update([
            'subtitle_ar' => null,
            'audience_ar' => null,
            'description_en' => $acne->description_ar,
            'skin_type_en' => $acne->skin_type_ar,
            'goal_en' => 'acne',
            'card_note_en' => $acne->card_note_ar,
            'how_to_use_en' => PresetRoutineEnglishBuilder::LEGACY_GENERIC_WOMEN_HOW_TO_USE,
            'benefits_en_json' => null,
            'what_it_offers_en_json' => null,
        ]);
        $acneItem = $acne->items()->orderBy('display_order')->firstOrFail();
        $acneItem->update([
            'use_time_en' => null,
            'application_en' => null,
            'starter_guide_en' => null,
        ]);

        // Simulate custom Dashboard edit on daily (women) that MUST be preserved
        $daily = PresetRoutine::forWomen()->where('slug', 'daily')->firstOrFail();
        $daily->update([
            'title_en' => 'Custom Admin Daily Glow Plan',
            'subtitle_ar' => 'عنوان فرعي مخصص من الداشبورد',
            'card_note_en' => 'Admin custom morning & night note.',
            'benefits_en_json' => ['Custom dashboard benefit 1', 'Custom dashboard benefit 2'],
        ]);
        $dailyItem = $daily->items()->orderBy('display_order')->firstOrFail();
        $dailyItem->update([
            'application_en' => 'Custom admin cleanser instructions.',
        ]);

        // Run backfill seeder twice to prove strict idempotency
        $this->seed(BackfillEnglishPresetRoutinesSeeder::class);
        $this->seed(BackfillEnglishPresetRoutinesSeeder::class);

        $acne->refresh();
        $acneItem->refresh();
        $daily->refresh();
        $dailyItem->refresh();

        $this->assertSame($originalAcneId, $acne->id);
        $this->assertContainsArabic($acne->subtitle_ar, 'backfilled.acne.subtitle_ar');
        $this->assertContainsArabic($acne->audience_ar, 'backfilled.acne.audience_ar');
        $this->assertNoArabic($acne->description_en, 'backfilled.acne.description_en');
        $this->assertNoArabic($acne->skin_type_en, 'backfilled.acne.skin_type_en');
        $this->assertSame('Breakouts, Blemishes & Post-Acne Marks', $acne->goal_en);
        $this->assertNoArabic($acne->card_note_en, 'backfilled.acne.card_note_en');
        $this->assertStringContainsString('COSRX BHA', $acne->how_to_use_en);
        $this->assertCount(4, $acne->benefits_en_json);
        $this->assertCount(6, $acne->what_it_offers_en_json);
        $this->assertSame('Morning & Evening', $acneItem->use_time_en);
        $this->assertNotEmpty($acneItem->application_en);
        $this->assertNoArabic($acneItem->application_en, 'backfilled.acneItem.application_en');

        // Custom admin Dashboard edits preserved untouched
        $this->assertSame('Custom Admin Daily Glow Plan', $daily->title_en);
        $this->assertSame('عنوان فرعي مخصص من الداشبورد', $daily->subtitle_ar);
        $this->assertSame('Admin custom morning & night note.', $daily->card_note_en);
        $this->assertSame(['Custom dashboard benefit 1', 'Custom dashboard benefit 2'], $daily->benefits_en_json);
        $this->assertSame('Custom admin cleanser instructions.', $dailyItem->application_en);
    }
}
