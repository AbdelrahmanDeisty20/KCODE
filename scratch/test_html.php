<?php

require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$response = $kernel->handle($request = Illuminate\Http\Request::create('/admin/login', 'GET'));
file_put_contents(__DIR__ . '/login_output.html', $response->getContent());
echo "Saved HTML output to login_output.html\n";
