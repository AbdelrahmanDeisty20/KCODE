<?php

require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\User;
use App\Models\Coupon;
use App\Filament\Resources\CouponResource\Pages\CreateCoupon;

echo "Testing Coupon Generation & Scope...\n";

$users = User::take(2)->get();
if ($users->isEmpty()) {
    echo "No users found in DB!\n";
    exit;
}

echo "Found " . $users->count() . " users for test.\n";

// Test multi-user coupon creation
$testData = [
    'code' => 'KCODE-TESTMAIN',
    'title_ar' => 'خصم مخصص تجريبي',
    'title_en' => 'Test Custom Discount',
    'discount_type' => 'percentage',
    'discount_value' => 15,
    'target_type' => 'specific',
    'target_user_ids' => $users->pluck('id')->toArray(),
    'is_active' => true,
];

class TestCreateCouponPage extends CreateCoupon {
    public function testCreate(array $data) {
        return $this->handleRecordCreation($data);
    }
}

$page = new TestCreateCouponPage();
$res = $page->testCreate($testData);

echo "First created coupon code: " . $res->code . " for User ID: " . $res->user_id . "\n";

$createdCoupons = Coupon::whereIn('user_id', $users->pluck('id')->toArray())->latest()->take($users->count())->get();
foreach ($createdCoupons as $c) {
    echo "Created Coupon ID: {$c->id} | Code: {$c->code} | User ID: {$c->user_id} | Is General: " . ($c->is_general ? 'YES' : 'NO') . "\n";
}

// Clean up
Coupon::whereIn('id', $createdCoupons->pluck('id'))->delete();
echo "Cleaned up test coupons!\n";
