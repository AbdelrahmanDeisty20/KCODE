<?php
require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

app()->setLocale('ar');

$user = new App\Models\User();
$user->id = 1;
$user->name = 'Blog Author';
$user->email = 'author@kcode.com';
$user->type = 'blog_author';

$res = (new App\Http\Resources\API\Blog\UserBlogResource($user))->toArray(request());
echo "Author Name (AR): " . $res['name'] . "\n";

app()->setLocale('en');
$resEn = (new App\Http\Resources\API\Blog\UserBlogResource($user))->toArray(request());
echo "Author Name (EN): " . $resEn['name'] . "\n";

$passAr = ($res['name'] === 'فريق KCODE');
$passEn = ($resEn['name'] === 'KCODE Editorial');

if ($passAr && $passEn) {
    echo "🎉 F26 BLOG AUTHOR NAME VERIFIED 100%!\n";
} else {
    echo "❌ F26 TEST FAILED!\n";
}
