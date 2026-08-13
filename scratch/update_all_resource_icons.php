<?php

$resourceIconMap = [
    'OrderResource.php'                  => 'icon-orders',
    'UserResource.php'                   => 'icon-customer',
    'CategoryResource.php'               => 'icon-category',
    'SubCategoryResource.php'            => 'icon-category',
    'BrandResource.php'                  => 'icon-partner',
    'BlogResource.php'                   => 'icon-blogger',
    'BlogCategoryResource.php'           => 'icon-category',
    'BlogTagResource.php'                => 'icon-media',
    'SettingResource.php'                => 'icon-setting',
    'PageResource.php'                   => 'icon-policy',
    'ProductResource.php'                => 'icon-service',
    'OfferResource.php'                  => 'icon-project',
    'CouponResource.php'                 => 'icon-project',
    'AssessmentResource.php'             => 'icon-service',
    'ConcernResource.php'                => 'icon-policy',
    'QuizQuestionResource.php'           => 'icon-contactus',
    'SkinTypeResource.php'               => 'icon-partner',
    'ChatbotMessageResource.php'         => 'icon-contactus',
    'ChatbotSuggestionResource.php'      => 'icon-contactus',
    'NewsletterSubscriptionResource.php' => 'icon-contactus',
    'LoyaltyLevelResource.php'           => 'icon-partner',
    'FaqResource.php'                    => 'icon-contactus',
    'ActivityLogResource.php'            => 'icon-policy',
    'AppNotificationResource.php'        => 'icon-media',
];

$dir = __DIR__ . '/../app/Filament/Resources';

foreach ($resourceIconMap as $file => $icon) {
    $filePath = $dir . '/' . $file;
    if (file_exists($filePath)) {
        $content = file_get_contents($filePath);
        $content = preg_replace(
            '/protected static string\|BackedEnum\|null \$navigationIcon = [^;]+;/',
            "protected static string|BackedEnum|null \$navigationIcon = '{$icon}';",
            $content
        );
        file_put_contents($filePath, $content);
        echo "Updated {$file} -> {$icon}\n";
    }
}

echo "All 24 resources updated to custom icon-* icons!\n";
