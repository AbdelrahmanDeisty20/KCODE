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
        'type' => 'user',
        'status' => 'active',
    ]
);

$role = \Spatie\Permission\Models\Role::findOrCreate('super_admin', 'web');
$user->assignRole($role);

echo "SUCCESS: Admin user created/updated with email: " . $user->email . " and password: password\n";
