<?php

namespace Tests\Feature;

use App\Models\Brand;
use App\Models\Category;
use App\Models\Product;
use App\Models\RoutineStep;
use App\Models\SubCategory;
use App\Models\User;
use Database\Seeders\QuizQuestionSeeder;
use Database\Seeders\Support\R479EnglishBuilder;
use Illuminate\Support\Facades\Schema;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class QuizRoutineLocalizationTest extends TestCase
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

    private function seedQuizCatalogAndSteps(): void
    {
        $this->seed(QuizQuestionSeeder::class);

        foreach ([
            ['order' => 3,  'name_en' => 'Cleanser',    'name_ar' => 'الغسول'],
            ['order' => 5,  'name_en' => 'Toner',       'name_ar' => 'التونر'],
            ['order' => 9,  'name_en' => 'Serum',       'name_ar' => 'السيروم'],
            ['order' => 11, 'name_en' => 'Treatment',   'name_ar' => 'العلاج المركّز'],
            ['order' => 15, 'name_en' => 'Moisturizer', 'name_ar' => 'المرطب'],
            ['order' => 18, 'name_en' => 'Sunscreen',   'name_ar' => 'واقي الشمس'],
        ] as $step) {
            RoutineStep::create($step);
        }

        $brand = Brand::create([
            'name_ar' => 'كود',
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

        $catalog = json_decode(file_get_contents(database_path('seeders/data/r479_quiz/catalog.json')), true) ?: [];
        $prodsJson = json_decode(file_get_contents(database_path('seeders/data/r479_products.json')), true) ?: [];

        foreach ($catalog as $entry) {
            $sku = $entry['sku'];
            $p = $prodsJson[$sku] ?? [];
            $name = $p['name'] ?? ($entry['name'] ?? $sku);

            $product = Product::create([
                'sku' => $sku,
                'name_en' => $name,
                'name_ar' => $name,
                'short_name_en' => $name,
                'short_name_ar' => $name,
                'description_en' => 'Clean English formulation overview for ' . $sku,
                'description_ar' => $p['description'] ?? 'وصف عربي للمنتج',
                'brief_insight_en' => 'Targeted English formulation insight for ' . $name,
                'brief_insight_ar' => $p['brief'] ?? 'ملخص عربي',
                'how_to_use_en' => 'Clean English directions for ' . $name,
                'how_to_use_ar' => $p['usage'] ?? 'طريقة استخدام عربية',
                'ingredients_en' => 'Water, Glycerin',
                'ingredients_ar' => 'Water, Glycerin',
                'category_id' => $category->id,
                'sub_category_id' => $subCategory->id,
                'brand_id' => $brand->id,
                'price' => 11.50,
                'size' => $p['size'] ?? '50ml',
                'status' => 'active',
                'stock' => 25,
                'role_ar' => 'عناية بالبشرة',
                'role_en' => 'Skincare Step',
                'usage_instructions_json' => [
                    'timing_label' => 'التوقيت',
                    'timing_label_en' => 'Timing',
                    'timing' => 'صباحًا ومساءً',
                    'timing_en' => 'Morning & evening',
                ],
            ]);

            R479EnglishBuilder::fillMissingEnglishOnModel($product);
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

    public function test_quiz_questions_and_post_quiz_routine_usage_schedule_switch_cleanly_between_ar_and_en(): void
    {
        $this->seedQuizCatalogAndSteps();

        // 1. Questions in English
        $qEnRes = $this->withHeaders(['lang' => 'en'])->getJson('/api/quiz/questions');
        $qEnRes->assertOk();
        foreach ($qEnRes->json('data') as $qi => $q) {
            $this->assertNoArabic($q['step_label'] ?? '', "questions[{$qi}].step_label");
            $this->assertNoArabic($q['title'] ?? '', "questions[{$qi}].title");
            $this->assertNoArabic($q['description'] ?? '', "questions[{$qi}].description");
            foreach ($q['options'] ?? [] as $oi => $opt) {
                $this->assertNoArabic($opt['title'] ?? '', "questions[{$qi}].options[{$oi}].title");
                $this->assertNoArabic($opt['description'] ?? '', "questions[{$qi}].options[{$oi}].description");
            }
        }

        $user = User::create([
            'name' => 'Routine Tester',
            'email' => 'routine-test@kcodeskin.com',
            'password' => bcrypt('secret123'),
        ]);
        Sanctum::actingAs($user);

        // Scenario triggering separate_nights_warning (e.g. acne + lines with non-sensitive oily skin)
        $payload = [
            'primary' => 'acne',
            'secondary' => 'lines',
            'skin' => 'oily',
            'sensitive' => false,
        ];

        $enSubmit = $this->withHeaders(['lang' => 'en'])
            ->postJson('/api/quiz/submit-answers', $payload);
        $enSubmit->assertOk();
        $enData = $enSubmit->json('data');

        // Diagnosis in English
        $diag = $enData['diagnosis'];
        $this->assertSame('Your Suggested Routine', $diag['title']);
        $this->assertStringStartsWith('Based on your answers,', $diag['skin_summary']);
        $this->assertNoArabic($diag['skin_summary'], 'diagnosis.skin_summary');
        $this->assertNoArabic($diag['focus_goal'], 'diagnosis.focus_goal');
        $this->assertNoArabic($diag['extra_concern'], 'diagnosis.extra_concern');
        foreach ($diag['badges'] as $bi => $b) {
            $this->assertNoArabic($b, "diagnosis.badges[{$bi}]");
        }
        foreach ($diag['goals_summary'] as $gi => $gs) {
            $this->assertNoArabic($gs, "diagnosis.goals_summary[{$gi}]");
        }

        // Questions echo in English
        foreach ($enData['questions'] as $qi => $qEcho) {
            $this->assertNoArabic($qEcho['question_title'], "echo.questions[{$qi}].title");
            $this->assertNoArabic($qEcho['selected_options'][0]['option_title'] ?? '', "echo.questions[{$qi}].opt");
            $this->assertNoArabic($qEcho['selected_options'][0]['description'] ?? '', "echo.questions[{$qi}].desc");
        }

        // Primary routine items in English
        $this->assertNotEmpty($enData['primary_routine']);
        foreach ($enData['primary_routine'] as $pi => $item) {
            $this->assertNotEmpty($item['display_role_ar']);
            $this->assertNotEmpty($item['usage_short_ar']);
            $this->assertNoArabic($item['display_role'], "primary[{$pi}].display_role");
            $this->assertNoArabic($item['display_role_ar'], "primary[{$pi}].display_role_ar");
            $this->assertNoArabic($item['routine_step_ar'], "primary[{$pi}].routine_step_ar");
            $this->assertNoArabic($item['use_time_ar'], "primary[{$pi}].use_time_ar");
            $this->assertNoArabic($item['usage_badge_ar'], "primary[{$pi}].usage_badge_ar");
            $this->assertNoArabic($item['chosen_for_ar'], "primary[{$pi}].chosen_for_ar");
            $this->assertNoArabic($item['usage_short_ar'], "primary[{$pi}].usage_short_ar");
            $this->assertNoArabic($item['usage_text_ar'], "primary[{$pi}].usage_text_ar");
            $this->assertNoArabic($item['usage_notice_ar'] ?? '', "primary[{$pi}].usage_notice_ar");
            $this->assertNoArabic($item['usage_policy']['label'] ?? '', "primary[{$pi}].usage_policy.label");
            foreach ($item['usage_tiles'] ?? [] as $ti => $tile) {
                $this->assertNoArabic($tile['label'] ?? '', "primary[{$pi}].tiles[{$ti}].label");
                $this->assertNoArabic($tile['text'] ?? '', "primary[{$pi}].tiles[{$ti}].text");
            }
            $this->assertNoArabic($item['product']['name'], "primary[{$pi}].product.name");
        }

        // Routine support items in English
        foreach ($enData['routine_support'] as $si => $supp) {
            $this->assertNoArabic($supp['display_role_ar'], "support[{$si}].display_role_ar");
            $this->assertNoArabic($supp['reason_ar'], "support[{$si}].reason_ar");
            $this->assertNoArabic($supp['chosen_for_ar'], "support[{$si}].chosen_for_ar");
            $this->assertNoArabic($supp['usage_short_ar'], "support[{$si}].usage_short_ar");
            $this->assertNoArabic($supp['usage_text_ar'], "support[{$si}].usage_text_ar");
            if (!empty($supp['night_variant'])) {
                $this->assertNoArabic($supp['night_variant']['label'], "support[{$si}].nv.label");
                $this->assertNoArabic($supp['night_variant']['badge'], "support[{$si}].nv.badge");
                $this->assertNoArabic($supp['night_variant']['note'], "support[{$si}].nv.note");
            }
            if (!empty($supp['usage_schedule_step'])) {
                $this->assertNoArabic($supp['usage_schedule_step']['display_role_ar'], "support[{$si}].sched.role");
                $this->assertNoArabic($supp['usage_schedule_step']['usage_short_ar'], "support[{$si}].sched.short");
            }
        }

        // Usage schedule in English (exact UI data shown on /routine screenshot!)
        $schedule = $enData['usage_schedule'];
        $this->assertSame('Morning', $schedule['morning']['label']);
        $this->assertSame('Evening', $schedule['evening']['label']);
        $this->assertSame(
            'Exfoliant & Retinoid: Use on separate nights, never together. Do not combine two retinoids.',
            $schedule['separate_nights_warning']
        );
        $this->assertSame($schedule['separate_nights_warning'], $schedule['evening']['note']);

        foreach (['morning', 'evening'] as $slot) {
            $this->assertNotEmpty($schedule[$slot]['steps']);
            foreach ($schedule[$slot]['steps'] as $sti => $st) {
                $this->assertNotEmpty($st['display_role_ar']);
                $this->assertNotEmpty($st['usage_short_ar']);
                $this->assertNoArabic($st['display_role_ar'], "schedule.{$slot}.steps[{$sti}].display_role_ar");
                $this->assertNoArabic($st['usage_short_ar'], "schedule.{$slot}.steps[{$sti}].usage_short_ar");
                $this->assertNoArabic($st['usage_notice_ar'] ?? '', "schedule.{$slot}.steps[{$sti}].usage_notice_ar");
                $this->assertNoArabic($st['name'], "schedule.{$slot}.steps[{$sti}].name");
            }
        }

        // Arabic submit check (regression check)
        $arSubmit = $this->withHeaders(['lang' => 'ar'])
            ->postJson('/api/quiz/submit-answers', $payload);
        $arSubmit->assertOk();
        $arData = $arSubmit->json('data');

        $this->assertSame('صباحًا', $arData['usage_schedule']['morning']['label']);
        $this->assertSame('مساءً', $arData['usage_schedule']['evening']['label']);
        $this->assertContainsArabic($arData['usage_schedule']['separate_nights_warning'], 'ar.separate_nights_warning');
        $this->assertContainsArabic($arData['usage_schedule']['morning']['steps'][0]['display_role_ar'], 'ar.step0.role');
        $this->assertContainsArabic($arData['usage_schedule']['morning']['steps'][0]['usage_short_ar'], 'ar.step0.short');
        // Product names stay fixed English even in AR
        $this->assertNoArabic($arData['usage_schedule']['morning']['steps'][0]['name'], 'ar.step0.product_name');

        // /api/routine/suggested & /api/routine/confirm & /api/routine in EN & AR
        $suggestedEn = $this->withHeaders(['lang' => 'en'])->getJson('/api/routine/suggested');
        $suggestedEn->assertOk();
        foreach ($suggestedEn->json('data.items') as $ii => $rItem) {
            $this->assertNoArabic($rItem['step_name'] ?? '', "suggested.items[{$ii}].step_name");
            $this->assertNoArabic($rItem['product']['name'], "suggested.items[{$ii}].product.name");
        }

        $routineId = $suggestedEn->json('data.id');
        $confirmRes = $this->withHeaders(['lang' => 'en'])
            ->postJson('/api/routine/confirm', ['routine_id' => $routineId]);
        $confirmRes->assertOk();

        $finalEn = $this->getJson('/api/routine?lang=en');
        $finalEn->assertOk();
        foreach ($finalEn->json('data.0.items') as $fi => $fItem) {
            $this->assertNoArabic($fItem['step_name'] ?? '', "final.items[{$fi}].step_name");
            $this->assertNoArabic($fItem['product']['name'], "final.items[{$fi}].product.name");
        }
    }

    public function test_quiz_and_post_quiz_routine_texts_are_dashboard_driven_and_backfill_is_non_destructive(): void
    {
        $this->seedQuizCatalogAndSteps();

        // Capture existing QuizQuestion & QuizOption IDs + customize one option title_en
        $q1 = \App\Models\QuizQuestion::where('key', 'q1')->firstOrFail();
        $origQ1Id = $q1->id;
        $acneOpt = \App\Models\QuizOption::where('quiz_question_id', $q1->id)->where('code', 'acne')->firstOrFail();
        $origAcneOptId = $acneOpt->id;
        $acneOpt->update(['title_en' => 'Custom Admin Acne Option']);

        // Run BackfillQuizAndRoutineSettingsSeeder twice + QuizQuestionSeeder directly (must NOT truncate or reset IDs!)
        $this->seed(\Database\Seeders\BackfillQuizAndRoutineSettingsSeeder::class);
        $this->seed(\Database\Seeders\BackfillQuizAndRoutineSettingsSeeder::class);
        $this->seed(QuizQuestionSeeder::class);

        $q1After = \App\Models\QuizQuestion::where('key', 'q1')->firstOrFail();
        $acneOptAfter = \App\Models\QuizOption::findOrFail($origAcneOptId);
        $this->assertSame($origQ1Id, $q1After->id);
        $this->assertSame('Custom Admin Acne Option', $acneOptAfter->title_en);

        // Verify Setting rows & Product usage_instructions_json were backfilled cleanly
        $warningSetting = \App\Models\Setting::where('key_en', 'routine_separate_nights_warning')->firstOrFail();
        $this->assertNotEmpty($warningSetting->value_en);

        $user = User::create([
            'name' => 'Dashboard Tester',
            'email' => 'dash-tester@kcodeskin.com',
            'password' => bcrypt('secret123'),
        ]);
        Sanctum::actingAs($user);

        // Determine actual cleanser SKU picked for this scenario
        $probeRes = $this->withHeaders(['lang' => 'en'])->postJson('/api/quiz/submit-answers', [
            'primary' => 'acne',
            'secondary' => 'lines',
            'skin' => 'oily',
            'sensitive' => false,
        ]);
        $probeRes->assertOk();
        $cleanserSku = $probeRes->json('data.primary_routine.0.sku');

        $cleanser = Product::where('sku', $cleanserSku)->firstOrFail();
        $this->assertNotEmpty($cleanser->usage_instructions_json['routine_usage_short_en'] ?? null);
        $this->assertNotEmpty($cleanser->usage_instructions_json['routine_usage_short_ar'] ?? null);

        // Customize both Setting and Product in DB (simulating Filament Dashboard edit)
        $warningSetting->update(['value_en' => 'Admin Custom Night Separation Rule']);
        \App\Models\Setting::where('key_en', 'routine_slot_morning_label')->update(['value_en' => 'AM Routine']);
        \App\Models\Setting::where('key_en', 'routine_diagnosis_title')->update(['value_en' => 'Admin Diagnosis Header']);

        $json = $cleanser->usage_instructions_json;
        $json['routine_role_en'] = 'Admin Custom Cleanser';
        $json['routine_usage_short_en'] = 'Admin custom rinse instruction.';
        $json['routine_usage_badge_en'] = 'Admin Custom Badge';
        $cleanser->update(['usage_instructions_json' => $json]);

        // Re-running BackfillQuizAndRoutineSettingsSeeder MUST preserve every Dashboard edit
        $this->seed(\Database\Seeders\BackfillQuizAndRoutineSettingsSeeder::class);
        \App\Support\StaticCache::flushAll();

        $res = $this->withHeaders(['lang' => 'en'])->postJson('/api/quiz/submit-answers', [
            'primary' => 'acne',
            'secondary' => 'lines',
            'skin' => 'oily',
            'sensitive' => false,
        ]);
        $res->assertOk();
        $data = $res->json('data');

        // Verify Settings overrides from Dashboard beat static defaults
        $this->assertSame('Admin Diagnosis Header', $data['diagnosis']['title']);
        $this->assertSame('Custom Admin Acne Option', $data['diagnosis']['focus_goal']);
        $this->assertSame('AM Routine', $data['usage_schedule']['morning']['label']);
        $this->assertSame('Admin Custom Night Separation Rule', $data['usage_schedule']['separate_nights_warning']);

        // Verify Product Dashboard overrides beat static defaults in primary_routine AND usage_schedule
        $primaryStep0 = collect($data['primary_routine'])->firstWhere('sku', $cleanserSku);
        $this->assertNotNull($primaryStep0);
        $this->assertSame('Admin Custom Cleanser', $primaryStep0['display_role_ar']);
        $this->assertSame('Admin custom rinse instruction.', $primaryStep0['usage_short_ar']);
        $this->assertSame('Admin Custom Badge', $primaryStep0['usage_badge_ar']);

        $schedStep0 = collect($data['usage_schedule']['morning']['steps'])->firstWhere('sku', $cleanserSku);
        $this->assertNotNull($schedStep0);
        $this->assertSame('Admin Custom Cleanser', $schedStep0['display_role_ar']);
        $this->assertSame('Admin custom rinse instruction.', $schedStep0['usage_short_ar']);
    }
}
