<?php

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

$dir = __DIR__ . '/../app/Filament/Resources';

foreach ($faMap as $file => $icon) {
    $filePath = $dir . '/' . $file;
    if (file_exists($filePath)) {
        $content = file_get_contents($filePath);
        $content = preg_replace(
            '/protected static string\|BackedEnum\|null \$navigationIcon = [^;]+;/',
            "protected static string|BackedEnum|null \$navigationIcon = '{$icon}';",
            $content
        );
        file_put_contents($filePath, $content);
        echo "Assigned FA Icon to {$file} -> {$icon}\n";
    }
}

echo "Successfully updated all 25 resources to FontAwesome icons!\n";
