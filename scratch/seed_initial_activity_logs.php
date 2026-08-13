<?php

require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\ActivityLog;
use App\Models\User;

$user = User::first();
$adminName = $user?->name ?? 'KCODE Admin';

echo "Seeding initial activity logs for testing...\n";

ActivityLog::truncate();

ActivityLog::create([
    'user_id' => $user?->id,
    'user_name' => $adminName,
    'event' => 'login',
    'subject_type' => 'User',
    'subject_id' => $user?->id,
    'description' => 'قام المشرف ' . $adminName . ' بتسجيل الدخول للوحة التحكم بنجاح',
    'ip_address' => '127.0.0.1',
    'user_agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64)',
    'created_at' => now()->subMinutes(30),
]);

ActivityLog::create([
    'user_id' => $user?->id,
    'user_name' => $adminName,
    'event' => 'created',
    'subject_type' => 'Coupon',
    'subject_id' => '15',
    'description' => 'تم إنشاء كوبون خصم جديد برمز [KCODE-8X9P2Q] ونسبة خصم 20%',
    'old_values' => null,
    'new_values' => ['code' => 'KCODE-8X9P2Q', 'discount_value' => '20', 'discount_type' => 'percentage'],
    'ip_address' => '127.0.0.1',
    'user_agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64)',
    'created_at' => now()->subMinutes(20),
]);

ActivityLog::create([
    'user_id' => $user?->id,
    'user_name' => $adminName,
    'event' => 'notification_sent',
    'subject_type' => 'AppNotification',
    'subject_id' => '8',
    'description' => 'تم إرسال إشعار لحظي (Push Notification) لجميع العملاء عبر الفايربيس بنجاح',
    'ip_address' => '127.0.0.1',
    'user_agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64)',
    'created_at' => now()->subMinutes(15),
]);

ActivityLog::create([
    'user_id' => $user?->id,
    'user_name' => $adminName,
    'event' => 'updated',
    'subject_type' => 'Order',
    'subject_id' => '102',
    'description' => 'تم تحديث حالة الطلب رقم #KCODE-20260813-GBYK5 إلى [قيد التوصيل]',
    'old_values' => ['order_status' => 'pending'],
    'new_values' => ['order_status' => 'processing'],
    'ip_address' => '127.0.0.1',
    'user_agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64)',
    'created_at' => now()->subMinutes(5),
]);

echo "Seeded " . ActivityLog::count() . " activity log records successfully!\n";
