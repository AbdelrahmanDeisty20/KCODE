<?php

require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$user = App\Models\User::updateOrCreate(
    ['email' => 'admin@admin.com'],
    [
        'name' => 'KCODE Admin',
        'password' => \Illuminate\Support\Facades\Hash::make('password'),
        'type' => 'admin',
        'status' => 'active',
    ]
);

echo "SUCCESS: Admin user created/updated with email: " . $user->email . " and password: password\n";
