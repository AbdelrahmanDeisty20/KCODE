<?php

require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$faMap = [
    'OrderResource.php'                  => 'fas-cart-shopping',
    'UserResource.php'                   => 'fas-user-gear',
    'ProductResource.php'                => 'fas-boxes-stacked',
    'CategoryResource.php'               => 'fas-layer-group',
    'SubCategoryResource.php'            => 'fas-sitemap',
    'BrandResource.php'                  => 'fas-award',
    'OfferResource.php'                  => 'fas-bolt',
    'CouponResource.php'                 => 'fas-ticket-simple',
    'AppNotificationResource.php'        => 'fas-paper-plane',
    'AssessmentResource.php'             => 'fas-clipboard-check',
    'ConcernResource.php'                => 'fas-triangle-exclamation',
    'QuizQuestionResource.php'           => 'fas-circle-question',
    'SkinTypeResource.php'               => 'far-face-smile-beam',
    'BlogResource.php'                   => 'fas-newspaper',
    'BlogCategoryResource.php'           => 'fas-folder-tree',
    'BlogTagResource.php'                => 'fas-hashtag',
    'ChatbotMessageResource.php'         => 'fas-headset',
    'ChatbotSuggestionResource.php'      => 'fas-wand-magic-sparkles',
    'NewsletterSubscriptionResource.php' => 'fas-envelope-open-text',
    'LoyaltyLevelResource.php'           => 'fas-crown',
    'FaqResource.php'                    => 'fas-circle-info',
    'PageResource.php'                   => 'fas-file-lines',
    'ActivityLogResource.php'            => 'fas-clock-rotate-left',
    'SettingResource.php'                => 'fas-sliders',
    'RoleResource.php'                   => 'fas-user-shield',
];

echo "=== TESTING ALL 25 FONTAWESOME ICONS ===\n";

$allPassed = true;
foreach ($faMap as $resource => $icon) {
    try {
        $svg = svg($icon)->toHtml();
        echo "PASS: {$resource} -> {$icon} (len: " . strlen($svg) . ")\n";
    } catch (\Exception $e) {
        echo "FAIL: {$resource} -> {$icon} Error: " . $e->getMessage() . "\n";
        $allPassed = false;
    }
}

if ($allPassed) {
    echo "\nALL 25 FONTAWESOME ICONS TESTED 100% OK!\n";
} else {
    echo "\nSOME ICONS FAILED!\n";
}
