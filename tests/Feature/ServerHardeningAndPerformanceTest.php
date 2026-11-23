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
}
