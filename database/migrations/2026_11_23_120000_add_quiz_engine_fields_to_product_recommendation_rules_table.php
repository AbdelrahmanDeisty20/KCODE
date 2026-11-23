<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('product_recommendation_rules')) {
            if (!Schema::hasColumn('product_recommendation_rules', 'sku')) {
                Schema::table('product_recommendation_rules', function (Blueprint $table) {
                    $table->dropForeign(['product_id']);
                });

                Schema::table('product_recommendation_rules', function (Blueprint $table) {
                    $table->unsignedBigInteger('product_id')->nullable()->change();
                    $table->foreign('product_id')->references('id')->on('products')->nullOnDelete();

                    $table->string('sku', 32)->nullable()->unique()->after('product_id');
                    $table->boolean('is_catalog_eligible')->default(false)->index()->after('routine_role');
                    $table->json('roles')->nullable()->after('is_catalog_eligible');
                    $table->json('allowed_goals')->nullable()->after('roles');
                    $table->decimal('fit_oily', 6, 2)->default(0)->after('allowed_goals');
                    $table->decimal('fit_combination', 6, 2)->default(0)->after('fit_oily');
                    $table->decimal('fit_normal', 6, 2)->default(0)->after('fit_combination');
                    $table->decimal('fit_dry', 6, 2)->default(0)->after('fit_normal');
                    $table->decimal('tolerance', 6, 2)->default(0)->after('fit_dry');
                    $table->json('scores')->nullable()->after('tolerance');
                    $table->string('conflict_groups')->nullable()->after('scores');
                    $table->unsignedInteger('supplement_order')->nullable()->after('conflict_groups');
                    $table->string('supplement_kind', 32)->nullable()->index()->after('supplement_order');
                    $table->json('supplement_goals')->nullable()->after('supplement_kind');
                    $table->string('exfoliator_purposes')->nullable()->after('supplement_goals');
                    $table->json('usage_policy')->nullable()->after('exfoliator_purposes');
                    $table->text('usage_short_ar')->nullable()->after('usage_policy');
                    $table->json('display_labels')->nullable()->after('usage_short_ar');
                });
            }
        }

        if (!Schema::hasTable('quiz_engine_configs')) {
            Schema::create('quiz_engine_configs', function (Blueprint $table) {
                $table->id();
                $table->string('key', 64)->unique();
                $table->longText('payload');
                $table->timestamps();
            });
        }

        $this->seedBootstrapData();
    }

    protected function seedBootstrapData(): void
    {
        $fixturePath = database_path('migrations/fixtures/r479_quiz_bootstrap.json.gz');
        if (!file_exists($fixturePath)) {
            return;
        }

        $raw = gzdecode(file_get_contents($fixturePath));
        $bundle = $raw ? json_decode($raw, true) : null;
        if (!is_array($bundle)) {
            return;
        }

        $now = now();
        $productIdsBySku = Schema::hasTable('products')
            ? DB::table('products')->pluck('id', 'sku')->all()
            : [];

        $existingBySku = DB::table('product_recommendation_rules')->whereNotNull('sku')->pluck('id', 'sku')->all();
        $existingByProdId = DB::table('product_recommendation_rules')->whereNotNull('product_id')->pluck('id', 'product_id')->all();

        $inserts = [];
        foreach ($bundle['rules'] ?? [] as $r) {
            $sku = $r['sku'];
            $productId = $productIdsBySku[$sku] ?? null;

            $payload = [
                'product_id' => $productId,
                'sku' => $sku,
                'routine_role' => $r['routine_role'] ?? null,
                'is_catalog_eligible' => !empty($r['is_catalog_eligible']),
                'roles' => isset($r['roles']) ? json_encode($r['roles'], JSON_UNESCAPED_UNICODE) : null,
                'allowed_goals' => isset($r['allowed_goals']) ? json_encode($r['allowed_goals'], JSON_UNESCAPED_UNICODE) : null,
                'fit_oily' => (float) ($r['fit_oily'] ?? 0),
                'fit_combination' => (float) ($r['fit_combination'] ?? 0),
                'fit_normal' => (float) ($r['fit_normal'] ?? 0),
                'fit_dry' => (float) ($r['fit_dry'] ?? 0),
                'tolerance' => (float) ($r['tolerance'] ?? 0),
                'scores' => isset($r['scores']) ? json_encode($r['scores'], JSON_UNESCAPED_UNICODE) : null,
                'conflict_groups' => $r['conflict_groups'] ?? null,
                'supplement_order' => $r['supplement_order'] ?? null,
                'supplement_kind' => $r['supplement_kind'] ?? null,
                'supplement_goals' => isset($r['supplement_goals']) ? json_encode($r['supplement_goals'], JSON_UNESCAPED_UNICODE) : null,
                'exfoliator_purposes' => $r['exfoliator_purposes'] ?? null,
                'usage_policy' => isset($r['usage_policy']) ? json_encode($r['usage_policy'], JSON_UNESCAPED_UNICODE) : null,
                'usage_short_ar' => $r['usage_short_ar'] ?? null,
                'display_labels' => isset($r['display_labels']) ? json_encode($r['display_labels'], JSON_UNESCAPED_UNICODE) : null,
                'updated_at' => $now,
            ];

            $existingId = $existingBySku[$sku] ?? ($productId ? ($existingByProdId[$productId] ?? null) : null);
            if ($existingId) {
                DB::table('product_recommendation_rules')->where('id', $existingId)->update($payload);
            } else {
                $payload['created_at'] = $now;
                $inserts[] = $payload;
            }
        }

        foreach (array_chunk($inserts, 50) as $chunk) {
            DB::table('product_recommendation_rules')->insertOrIgnore($chunk);
        }

        foreach ($bundle['configs'] ?? [] as $key => $payload) {
            DB::table('quiz_engine_configs')->insertOrIgnore([
                'key' => $key,
                'payload' => json_encode($payload, JSON_UNESCAPED_UNICODE),
                'created_at' => $now,
                'updated_at' => $now,
            ]);
        }

        // Non-destructively ensure missing tiles / routine_usage_notice_ar exist on products
        if (!empty($productIdsBySku) && !empty($bundle['product_usage_backfill'])) {
            $backfillMap = $bundle['product_usage_backfill'];
            $products = DB::table('products')
                ->whereIn('sku', array_keys($backfillMap))
                ->select(['id', 'sku', 'usage_instructions_json'])
                ->get();

            foreach ($products as $prod) {
                $defaults = $backfillMap[$prod->sku] ?? [];
                if (empty($defaults)) {
                    continue;
                }
                $json = is_string($prod->usage_instructions_json)
                    ? (json_decode($prod->usage_instructions_json, true) ?: [])
                    : [];
                $dirty = false;
                if (empty($json['tiles']) && !empty($defaults['tiles'])) {
                    $json['tiles'] = $defaults['tiles'];
                    $dirty = true;
                }
                if (empty($json['routine_usage_notice_ar']) && !empty($defaults['routine_usage_notice_ar'])) {
                    $json['routine_usage_notice_ar'] = $defaults['routine_usage_notice_ar'];
                    $dirty = true;
                }
                if ($dirty) {
                    DB::table('products')->where('id', $prod->id)->update([
                        'usage_instructions_json' => json_encode($json, JSON_UNESCAPED_UNICODE),
                        'updated_at' => $now,
                    ]);
                }
            }
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('quiz_engine_configs');

        if (Schema::hasTable('product_recommendation_rules') && Schema::hasColumn('product_recommendation_rules', 'sku')) {
            Schema::table('product_recommendation_rules', function (Blueprint $table) {
                $table->dropColumn([
                    'sku',
                    'is_catalog_eligible',
                    'roles',
                    'allowed_goals',
                    'fit_oily',
                    'fit_combination',
                    'fit_normal',
                    'fit_dry',
                    'tolerance',
                    'scores',
                    'conflict_groups',
                    'supplement_order',
                    'supplement_kind',
                    'supplement_goals',
                    'exfoliator_purposes',
                    'usage_policy',
                    'usage_short_ar',
                    'display_labels',
                ]);
            });
        }
    }
};
