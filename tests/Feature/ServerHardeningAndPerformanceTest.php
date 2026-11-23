<?php

namespace Tests\Feature;

use App\Models\ActivityLog;
use App\Models\AppNotification;
use App\Models\Brand;
use App\Models\Category;
use App\Models\Coupon;
use App\Models\PresetRoutine;
use App\Models\PresetRoutineProduct;
use App\Models\Product;
use App\Models\SubCategory;
use App\Models\User;
use App\Services\AppNotificationService;
use App\Services\ProductService;
use App\Services\RoutineService;
use App\Support\StaticCache;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class ServerHardeningAndPerformanceTest extends TestCase
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

        StaticCache::flushAll();
    }

    private function seedSampleProducts(): array
    {
        $category = Category::create([
            'name_ar' => 'العناية',
            'name_en' => 'Skincare',
            'slug'    => 'skincare',
        ]);

        $subCategory = SubCategory::create([
            'category_id' => $category->id,
            'name_ar'     => 'سيروم',
            'name_en'     => 'Serum',
            'slug'        => 'serum',
        ]);

        $brand = Brand::create([
            'name_ar' => 'انوا',
            'name_en' => 'ANUA',
            'slug'    => 'anua',
        ]);

        $created = [];
        foreach ([
            ['sku' => 'KC0001', 'name' => 'ANUA Serum', 'best' => false, 'sales' => 10],
            ['sku' => 'KC0185', 'name' => 'BIODANCE Mask', 'best' => true, 'sales' => 5],
            ['sku' => 'KC9999', 'name' => 'Other Best Seller', 'best' => true, 'sales' => 500],
        ] as $row) {
            $created[] = Product::create([
                'name_ar'        => $row['name'],
                'name_en'        => $row['name'],
                'short_name_ar'  => $row['name'],
                'short_name_en'  => $row['name'],
                'description_ar' => 'وصف المنتج',
                'description_en' => 'Product description',
                'ingredients_ar' => 'Water, Glycerin',
                'ingredients_en' => 'Water, Glycerin',
                'how_to_use_ar'  => 'يستخدم يومياً',
                'how_to_use_en'  => 'Use daily.',
                'category_id'    => $category->id,
                'sub_category_id'=> $subCategory->id,
                'brand_id'       => $brand->id,
                'price'          => 12.50,
                'size'           => '50ml',
                'stock'          => 10,
                'status'         => 'active',
                'is_best_seller' => $row['best'],
                'sales_count'    => $row['sales'],
                'sku'            => $row['sku'],
            ]);
        }

        return $created;
    }

    public function test_product_service_skus_filter_blocks_sql_injection_and_preserves_order(): void
    {
        $this->seedSampleProducts();

        $this->app->instance('request', Request::create('/api/products', 'GET', [
            'skus' => 'KC0185, KC0001',
        ]));

        $service = new ProductService();
        $result = $service->index();

        $this->assertTrue($result['status']);
        $this->assertSame(['KC0185', 'KC0001'], collect($result['data']->items())->pluck('sku')->all());

        // Malicious quote/SQL payload in ?skus= must not throw SQL errors
        $this->app->instance('request', Request::create('/api/products', 'GET', [
            'skus' => "KC0001', OR 1=1--),KC0001",
        ]));

        $maliciousResult = $service->index();
        $this->assertTrue($maliciousResult['status']);
        $this->assertSame(['KC0001'], collect($maliciousResult['data']->items())->pluck('sku')->all());
    }

    public function test_promo_coupon_uses_static_cache_and_invalidates_on_coupon_change(): void
    {
        [$p1, $p2] = $this->seedSampleProducts();

        Coupon::create([
            'code'           => 'KCODE15',
            'title_ar'       => 'خصم 15%',
            'title_en'       => '15% OFF',
            'discount_type'  => 'percentage',
            'discount_value' => 15,
            'is_general'     => true,
            'is_active'      => true,
            'start_date'     => now()->subDay(),
            'end_date'       => now()->addDay(),
        ]);

        // Warm StaticCache once
        $first = $p1->promo_coupon;
        $this->assertSame('KCODE15', $first['code']);

        // Second lookup across another Product must execute 0 DB queries
        DB::enableQueryLog();
        DB::flushQueryLog();
        $second = $p2->promo_coupon;
        $queries = DB::getQueryLog();
        DB::disableQueryLog();

        $this->assertSame('KCODE15', $second['code']);
        $this->assertCount(0, $queries);
    }

    public function test_preset_routine_refresh_flag_cannot_wipe_db_and_routes_are_protected(): void
    {
        [$p1, $p2, $p3] = $this->seedSampleProducts();

        $routine = PresetRoutine::create([
            'slug'                        => 'custom-edited-routine',
            'title_ar'                    => 'روتين معدّل من الداشبورد',
            'title_en'                    => 'Dashboard Edited Routine',
            'description_ar'              => 'وصف',
            'description_en'              => 'Description',
            'badge_ar'                    => 'مخصص',
            'badge_en'                    => 'Custom',
            'skin_type_ar'                => 'مختلطة',
            'skin_type_en'                => 'Combination',
            'goal_ar'                     => 'الترطيب',
            'goal_en'                     => 'Hydration',
            'is_for_men'                  => false,
            'status'                      => 'active',
            'complementary_products_json' => [
                ['product_id' => $p2->id, 'step' => 'الماسك', 'why' => 'ترطيب'],
                ['sku' => $p3->sku, 'step' => 'دعم', 'why' => 'توازن'],
            ],
        ]);

        PresetRoutineProduct::create([
            'preset_routine_id' => $routine->id,
            'product_id'        => $p1->id,
            'display_order'     => 1,
            'step_name_ar'      => 'السيروم',
            'step_name_en'      => 'Serum',
            'morning'           => true,
            'night'             => true,
        ]);

        // Even with ?refresh=1&generate=1, populated DB must NOT be wiped by public GET endpoint
        $res = $this->getJson('/api/preset-routines?refresh=1&generate=1');
        $res->assertOk();
        $this->assertDatabaseHas('preset_routines', ['slug' => 'custom-edited-routine']);

        // Routine detail query should eagerly load review stats & batch complementary products without N+1 queries
        DB::enableQueryLog();
        DB::flushQueryLog();
        $detailRes = $this->getJson('/api/preset-routines/custom-edited-routine');
        $queryCount = count(DB::getQueryLog());
        DB::disableQueryLog();

        $detailRes->assertOk();
        $this->assertLessThan(12, $queryCount);

        // Public GET /generate cannot trigger seeding (hits {id} -> 404) and POST /generate denies non-admins
        $this->getJson('/api/preset-routines/generate')->assertStatus(404);
        $this->assertDatabaseHas('preset_routines', ['slug' => 'custom-edited-routine']);
        $this->postJson('/api/preset-routines/generate')->assertStatus(401);

        $regularUser = User::create([
            'name'     => 'Regular Shopper',
            'email'    => 'user@kcode.com',
            'password' => bcrypt('secret123'),
            'type'     => 'user',
        ]);
        $this->actingAs($regularUser, 'sanctum')
            ->postJson('/api/preset-routines/generate')
            ->assertStatus(403);
    }

    public function test_bulk_notification_status_operations_and_api_throttle_and_visit_exclusion(): void
    {
        $user = User::create([
            'name'     => 'Notif User',
            'email'    => 'bulk-notif@kcode.com',
            'password' => bcrypt('secret123'),
        ]);

        $n1 = AppNotification::create([
            'user_id'    => $user->id,
            'title_ar'   => 'تنبيه 1',
            'title_en'   => 'Notice 1',
            'message_ar' => 'محتوى 1',
            'message_en' => 'Body 1',
            'is_read'    => false,
        ]);
        $n2 = AppNotification::create([
            'user_id'    => null,
            'title_ar'   => 'تنبيه عام 2',
            'title_en'   => 'General 2',
            'message_ar' => 'محتوى 2',
            'message_en' => 'Body 2',
            'is_read'    => false,
        ]);

        $notifService = new AppNotificationService();

        // Mark 1 as read first (creates existing row), then markAllAsRead (must update n1 + insert n2)
        $this->assertTrue($notifService->markAsRead($user->id, $n1->id)['status']);
        $this->assertTrue($notifService->markAllAsRead($user->id)['status']);
        $this->assertTrue((bool) $n1->fresh()->is_read);
        $this->assertDatabaseHas('app_notification_user_statuses', [
            'user_id'             => $user->id,
            'app_notification_id' => $n2->id,
            'is_read'             => 1,
        ]);

        $this->assertTrue($notifService->clearAllNotifications($user->id)['status']);
        $this->assertDatabaseHas('app_notification_user_statuses', [
            'user_id'             => $user->id,
            'app_notification_id' => $n1->id,
            'is_deleted'          => 1,
        ]);

        // Guest device bulk operations
        $this->assertTrue($notifService->markAllGeneralAsReadByDeviceId('dev-1')['status']);
        $this->assertTrue($notifService->clearAllGeneralNotificationsByDeviceId('dev-1')['status']);

        // TrackSiteVisitMiddleware must NOT log site_visit rows on GET API calls
        $this->seedSampleProducts();
        $this->get('/api/products')->assertOk();
        $this->assertSame(0, ActivityLog::where('event', 'site_visit')->count());

        // Sensitive API route must return 429 once rate limit (20/min) is exceeded
        for ($i = 0; $i < 20; $i++) {
            $resp = $this->postJson('/api/coupons/apply', ['code' => 'NOPE', 'order_amount' => 10]);
            $this->assertNotSame(429, $resp->getStatusCode());
        }
        $throttled = $this->postJson('/api/coupons/apply', ['code' => 'NOPE', 'order_amount' => 10]);
        $this->assertSame(429, $throttled->getStatusCode());
    }

    public function test_preset_routines_lists_and_details_use_static_cache_and_invalidate_on_model_updates(): void
    {
        [$p1, $p2] = $this->seedSampleProducts();

        $women = PresetRoutine::create([
            'slug'           => 'women-glow',
            'title_ar'       => 'روتين الإشراقة',
            'title_en'       => 'Glow Routine',
            'description_ar' => 'وصف',
            'description_en' => 'Glow description',
            'skin_type_ar'   => 'جافة',
            'skin_type_en'   => 'Dry',
            'goal_ar'        => 'الإشراقة',
            'goal_en'        => 'Glow',
            'is_for_men'     => false,
            'status'         => 'active',
        ]);
        $item = PresetRoutineProduct::create([
            'preset_routine_id' => $women->id,
            'product_id'        => $p1->id,
            'display_order'     => 1,
            'step_name_ar'      => 'السيروم',
            'step_name_en'      => 'Serum',
            'morning'           => true,
            'night'             => true,
        ]);

        $men = PresetRoutine::create([
            'slug'           => 'men-simple',
            'title_ar'       => 'روتين الرجال',
            'title_en'       => 'Men Routine',
            'description_ar' => 'وصف رجالي',
            'description_en' => 'Men description',
            'skin_type_ar'   => 'عادي',
            'skin_type_en'   => 'Normal',
            'goal_ar'        => 'الترطيب',
            'goal_en'        => 'Hydration',
            'is_for_men'     => true,
            'status'         => 'active',
        ]);
        PresetRoutineProduct::create([
            'preset_routine_id' => $men->id,
            'product_id'        => $p2->id,
            'display_order'     => 1,
            'step_name_ar'      => 'المرطب',
            'step_name_en'      => 'Moisturizer',
            'morning'           => true,
            'night'             => true,
        ]);

        // Warm caches once for women list, men list, and routine detail (by slug)
        $this->withHeaders(['lang' => 'ar'])->getJson('/api/preset-routines')->assertOk();
        $this->withHeaders(['lang' => 'ar'])->getJson('/api/men-preset-routines')->assertOk();
        $this->withHeaders(['lang' => 'ar'])->getJson('/api/preset-routines/women-glow')->assertOk();

        // Subsequent reads (including ?random=true, pagination, & access by numeric ID) must execute 0 SQL queries
        DB::enableQueryLog();
        DB::flushQueryLog();
        $this->withHeaders(['lang' => 'ar'])->getJson('/api/preset-routines?random=true&per_page=5')->assertOk();
        $this->withHeaders(['lang' => 'ar'])->getJson('/api/men-preset-routines?random=true&page=1')->assertOk();
        $this->withHeaders(['lang' => 'ar'])->getJson('/api/preset-routines/' . $women->id)->assertOk();
        $this->withHeaders(['lang' => 'ar'])->getJson('/api/preset-routines/non-existent-slug')->assertStatus(404);
        $cachedQueries = DB::getQueryLog();
        DB::disableQueryLog();

        $this->assertCount(0, $cachedQueries);

        // Editing PresetRoutine automatically invalidates list & detail caches
        $women->update(['title_ar' => 'روتين الإشراقة المحدّث']);
        $updatedList = $this->withHeaders(['lang' => 'ar'])->getJson('/api/preset-routines')->assertOk();
        $this->assertSame('روتين الإشراقة المحدّث', $updatedList->json('data.0.title'));

        // Editing PresetRoutineProduct automatically invalidates detail cache
        $item->update(['step_name_ar' => 'سيروم محدّث']);
        $updatedDetail = $this->withHeaders(['lang' => 'ar'])->getJson('/api/preset-routines/women-glow')->assertOk();
        $this->assertSame('سيروم محدّث', $updatedDetail->json('data.items.0.step_name'));

        // Editing Product price automatically invalidates routine caches
        $p1->update(['price' => 99.50]);
        $priceUpdatedDetail = $this->withHeaders(['lang' => 'ar'])->getJson('/api/preset-routines/women-glow')->assertOk();
        $this->assertEquals(99.50, $priceUpdatedDetail->json('data.total_price'));

        // Creating a new Review invalidates preset_routines so average_rating updates immediately
        \App\Models\Review::create([
            'user_id' => User::create([
                'name' => 'Reviewer',
                'email' => 'rev@kcode.com',
                'password' => bcrypt('secret123'),
            ])->id,
            'product_id' => $p1->id,
            'rating' => 5,
            'comment' => 'Great serum!',
        ]);
        $ratedDetail = $this->withHeaders(['lang' => 'ar'])->getJson('/api/preset-routines/women-glow')->assertOk();
        $this->assertSame(1, $ratedDetail->json('data.items.0.product.num_reviews'));

        // Deleting a PresetRoutine removes it from cached lists and details immediately
        $women->delete();
        $afterDeleteList = $this->withHeaders(['lang' => 'ar'])->getJson('/api/preset-routines')->assertOk();
        $this->assertCount(0, $afterDeleteList->json('data'));
        $this->withHeaders(['lang' => 'ar'])->getJson('/api/preset-routines/women-glow')->assertStatus(404);
    }

    public function test_every_cached_dashboard_model_invalidates_caches_on_create_update_and_delete(): void
    {
        [$p1] = $this->seedSampleProducts();

        $checks = [
            ['settings', fn () => \App\Models\Setting::create(['key_en' => 'site_tagline', 'key_ar' => 'شعار', 'value_en' => 'v1', 'value_ar' => 'ع1']), fn ($m) => $m->update(['value_en' => 'v2'])],
            ['faqs', fn () => \App\Models\Faq::create(['question_ar' => 'س', 'question_en' => 'Q', 'answer_ar' => 'ج', 'answer_en' => 'A', 'is_active' => true, 'sort_order' => 1]), fn ($m) => $m->update(['answer_en' => 'A2'])],
            ['categories', fn () => Category::create(['name_ar' => 'جديد', 'name_en' => 'New Cat', 'slug' => 'new-cat']), fn ($m) => $m->update(['name_en' => 'New Cat 2'])],
            ['brands', fn () => Brand::create(['name_ar' => 'براند', 'name_en' => 'Brand X', 'slug' => 'brand-x']), fn ($m) => $m->update(['name_en' => 'Brand X2'])],
            ['skin_types', fn () => \App\Models\SkinType::create(['name_ar' => 'دهنية', 'name_en' => 'Oily', 'slug' => 'oily-x']), fn ($m) => $m->update(['name_en' => 'Oily 2'])],
            ['concerns', fn () => \App\Models\Concern::create(['name_ar' => 'حبوب', 'name_en' => 'Acne', 'slug' => 'acne-x']), fn ($m) => $m->update(['name_en' => 'Acne 2'])],
            ['routine_goals', fn () => \App\Models\RoutineGoal::create(['name_ar' => 'نضارة', 'name_en' => 'Glow', 'slug' => 'glow-x']), fn ($m) => $m->update(['name_en' => 'Glow 2'])],
            ['quiz_questions', fn () => \App\Models\QuizQuestion::create(['step_number' => 9, 'key' => 'qx', 'title_ar' => 'سؤال', 'title_en' => 'Question', 'selection_type' => 'single']), fn ($m) => $m->update(['title_en' => 'Question 2'])],
            ['care_guide', fn () => \App\Models\CareGuide::create(['title_ar' => 'دليل', 'title_en' => 'Guide', 'description_ar' => 'وصف', 'is_active' => true]), fn ($m) => $m->update(['title_en' => 'Guide 2'])],
            ['quiz_engine', fn () => \App\Models\QuizEngineConfig::create(['key' => 'test_cfg', 'payload' => ['a' => 1]]), fn ($m) => $m->update(['payload' => ['a' => 2]])],
        ];

        foreach ($checks as [$group, $createFn, $updateFn]) {
            StaticCache::remember($group, 'probe', fn () => 'stale');
            $this->assertSame('stale', StaticCache::remember($group, 'probe', fn () => 'fresh'));

            $model = $createFn();
            $this->assertSame('after_create', StaticCache::remember($group, 'probe', fn () => 'after_create'), "Cache group [{$group}] did not invalidate on create");

            $updateFn($model);
            $this->assertSame('after_update', StaticCache::remember($group, 'probe', fn () => 'after_update'), "Cache group [{$group}] did not invalidate on update");

            $model->delete();
            $this->assertSame('after_delete', StaticCache::remember($group, 'probe', fn () => 'after_delete'), "Cache group [{$group}] did not invalidate on delete");
        }

        // Adding/updating Product also invalidates AI chatbot store context cache
        \Illuminate\Support\Facades\Cache::put('groq_store_context_ar', 'stale_catalog', 180);
        $p1->update(['name_en' => 'Updated Product Title']);
        $this->assertFalse(\Illuminate\Support\Facades\Cache::has('groq_store_context_ar'));

        // Deleting Product flushes quiz_engine cache
        StaticCache::remember('quiz_engine', 'probe', fn () => 'stale_engine');
        $p1->delete();
        $this->assertSame('fresh_engine', StaticCache::remember('quiz_engine', 'probe', fn () => 'fresh_engine'));
    }

    public function test_filament_dashboard_forms_save_and_invalidate_caches(): void
    {
        [$p1] = $this->seedSampleProducts();

        $admin = User::create([
            'name' => 'Panel Admin',
            'email' => 'panel-admin@kcode.com',
            'password' => bcrypt('secret123'),
            'type' => 'admin',
        ]);
        $this->actingAs($admin);

        \Filament\Facades\Filament::setCurrentPanel(
            \Filament\Facades\Filament::getPanel('admin')
        );

        // Warm quiz_engine cache
        StaticCache::remember('quiz_engine', 'probe', fn () => 'stale_quiz');

        // 1. Save Product via Filament EditProduct including recommendationRule tab fields
        \Livewire\Livewire::test(\App\Filament\Resources\ProductResource\Pages\EditProduct::class, [
            'record' => $p1->getRouteKey(),
        ])
            ->fillForm([
                'name_en' => 'ANUA Serum Filament Updated',
                'recommendationRule' => [
                    'is_catalog_eligible' => true,
                    'routine_role' => 'Serum',
                    'roles' => ['treatment1'],
                    'fit_oily' => 1.0,
                    'fit_combination' => 0.9,
                    'fit_normal' => 0.8,
                    'fit_dry' => 0.7,
                    'tolerance' => 0.9,
                    'scores' => ['Acne' => '4.5', 'Pores' => '3.0'],
                    'usage_short_ar' => 'توجيه محفوظ من الداشبورد',
                    'usage_policy' => [
                        'period' => 'both',
                        'family' => 'daily',
                        'label' => 'يومي',
                        'order' => 30,
                    ],
                ],
            ])
            ->call('save')
            ->assertHasNoFormErrors();

        $this->assertDatabaseHas('product_recommendation_rules', [
            'product_id' => $p1->id,
            'sku' => 'KC0001',
            'is_catalog_eligible' => 1,
            'usage_short_ar' => 'توجيه محفوظ من الداشبورد',
        ]);
        $this->assertSame('fresh_quiz', StaticCache::remember('quiz_engine', 'probe', fn () => 'fresh_quiz'));

        // 2. Edit QuizEngineConfig via Filament EditQuizEngineConfig & verify quiz_engine cache flush
        $cfg = \App\Models\QuizEngineConfig::where('key', 'quiz_contract')->firstOrFail();
        StaticCache::remember('quiz_engine', 'probe', fn () => 'stale_cfg');

        \Livewire\Livewire::test(\App\Filament\Resources\QuizEngineConfigResource\Pages\EditQuizEngineConfig::class, [
            'record' => $cfg->getRouteKey(),
        ])
            ->call('save')
            ->assertHasNoFormErrors();

        $this->assertSame('fresh_cfg', StaticCache::remember('quiz_engine', 'probe', fn () => 'fresh_cfg'));

        // 3. Create & Edit PresetRoutine via Filament CreatePresetRoutine & verify preset_routines cache flush
        StaticCache::remember('preset_routines', 'probe', fn () => 'stale_preset');

        \Livewire\Livewire::test(\App\Filament\Resources\PresetRoutineResource\Pages\CreatePresetRoutine::class)
            ->fillForm([
                'slug' => 'filament-new-routine',
                'title_ar' => 'روتين جديد من الداشبورد',
                'title_en' => 'Dashboard New Routine',
                'is_for_men' => false,
                'status' => 'active',
                'items' => [
                    [
                        'product_id' => $p1->id,
                        'display_order' => 1,
                        'step_name_ar' => 'سيروم',
                        'step_name_en' => 'Serum',
                        'morning' => true,
                        'night' => true,
                    ],
                ],
            ])
            ->call('create')
            ->assertHasNoFormErrors();

        $this->assertDatabaseHas('preset_routines', ['slug' => 'filament-new-routine']);
        $this->assertSame('fresh_preset', StaticCache::remember('preset_routines', 'probe', fn () => 'fresh_preset'));

        $createdRoutine = PresetRoutine::where('slug', 'filament-new-routine')->firstOrFail();

        // 4. Deleting PresetRoutine via Filament EditPresetRoutine header DeleteAction flushes cache too
        \Livewire\Livewire::test(\App\Filament\Resources\PresetRoutineResource\Pages\EditPresetRoutine::class, [
            'record' => $createdRoutine->getRouteKey(),
        ])
            ->callAction(\Filament\Actions\DeleteAction::class);

        $this->assertDatabaseMissing('preset_routines', ['slug' => 'filament-new-routine']);
        $this->assertSame('after_preset_delete', StaticCache::remember('preset_routines', 'probe', fn () => 'after_preset_delete'));

        // 5. Creating a brand new Product via Filament CreateProduct with Quiz Rule creates rule & clears caches
        $cat = Category::firstOrFail();
        $sub = SubCategory::firstOrFail();
        $brand = Brand::firstOrFail();

        StaticCache::remember('quiz_engine', 'probe', fn () => 'stale_create_prod');
        \Livewire\Livewire::test(\App\Filament\Resources\ProductResource\Pages\CreateProduct::class)
            ->fillForm([
                'sku' => 'KC-NEW-DASH',
                'name_en' => 'New Dashboard Quiz Cleanser',
                'name_ar' => 'غسول جديد من الداشبورد',
                'short_name_en' => 'New Cleanser',
                'short_name_ar' => 'غسول جديد',
                'category_id' => $cat->id,
                'sub_category_id' => $sub->id,
                'brand_id' => $brand->id,
                'price' => 14.0,
                'stock' => 20,
                'description_ar' => 'وصف عربي',
                'description_en' => 'English overview',
                'ingredients_ar' => 'ماء',
                'ingredients_en' => 'Water',
                'how_to_use_ar' => 'يغسل يوميا',
                'how_to_use_en' => 'Wash daily.',
                'status' => 'active',
                'recommendationRule' => [
                    'is_catalog_eligible' => true,
                    'routine_role' => 'Cleanser',
                    'roles' => ['cleanser'],
                    'fit_oily' => 1.0,
                    'fit_combination' => 1.0,
                    'fit_normal' => 1.0,
                    'fit_dry' => 0.8,
                    'tolerance' => 1.0,
                    'usage_short_ar' => 'غسول مضاف من الداشبورد',
                ],
            ])
            ->call('create')
            ->assertHasNoFormErrors();

        $this->assertDatabaseHas('product_recommendation_rules', [
            'sku' => 'KC-NEW-DASH',
            'is_catalog_eligible' => 1,
            'usage_short_ar' => 'غسول مضاف من الداشبورد',
        ]);
        $this->assertSame('after_create_prod', StaticCache::remember('quiz_engine', 'probe', fn () => 'after_create_prod'));

        // 6. ProductReports Filament Page reads live DB products cleanly (zero exicel CSV dependency)
        \Livewire\Livewire::test(\App\Filament\Pages\ProductReports::class)
            ->assertOk()
            ->assertSee('KC-NEW-DASH')
            ->assertSee('New Dashboard Quiz Cleanser');

        $this->assertSame('http://localhost:5173', config('app.frontend_url'));
        $this->assertArrayHasKey('credentials', config('services.firebase'));
        $this->assertArrayHasKey('project_id', config('services.firebase'));
    }
}
