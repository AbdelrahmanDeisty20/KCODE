<?php

use Illuminate\Support\Facades\Schedule;

// هنخلي الـ Worker يشتغل لمدة 55 ثانية عشان يغطي الدقيقة كلها
// ده هيخلي الإرسال أسرع بكتير لأن الـ Worker هيفضل مستني أي جوب جديد
Schedule::call(function () {
    Artisan::call('queue:work', [
        '--stop-when-empty' => true,
        '--max-time' => 50,
    ]);
})->name('queue-worker')->everyMinute()->withoutOverlapping();

// تنظيف التوكنات
Schedule::call(function () {
    Artisan::call('auth:clear-resets');
})->name('clear-resets')->hourly();

Schedule::call(function () {
    Artisan::call('sanctum:prune-expired', ['--hours' => 24]);
})->name('prune-sanctum')->daily();

// فحص وإيقاف العروض والكوبونات المنتهية وتسجيلها في سجل النشاطات
Schedule::call(function () {
    Artisan::call('kcode:check-expired-offers');
})->name('check-expired-offers')->hourly();
