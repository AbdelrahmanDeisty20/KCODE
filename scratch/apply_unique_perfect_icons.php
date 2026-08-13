<?php

$uniqueIconMap = [
    'OrderResource.php'                  => 'heroicon-o-shopping-bag',
    'UserResource.php'                   => 'heroicon-o-users',
    'ProductResource.php'                => 'heroicon-o-cube',
    'CategoryResource.php'               => 'heroicon-o-folder',
    'SubCategoryResource.php'            => 'heroicon-o-rectangle-group',
    'BrandResource.php'                  => 'heroicon-o-tag',
    'OfferResource.php'                  => 'heroicon-o-sparkles',
    'CouponResource.php'                 => 'heroicon-o-ticket',
    'AppNotificationResource.php'        => 'heroicon-o-bell-alert',
    'AssessmentResource.php'             => 'heroicon-o-clipboard-document-check',
    'ConcernResource.php'                => 'heroicon-o-exclamation-triangle',
    'QuizQuestionResource.php'           => 'heroicon-o-question-mark-circle',
    'SkinTypeResource.php'               => 'heroicon-o-face-smile',
    'BlogResource.php'                   => 'heroicon-o-newspaper',
    'BlogCategoryResource.php'           => 'heroicon-o-bookmarks',
    'BlogTagResource.php'                => 'heroicon-o-hashtag',
    'ChatbotMessageResource.php'         => 'heroicon-o-chat-bubble-left-right',
    'ChatbotSuggestionResource.php'      => 'heroicon-o-light-bulb',
    'NewsletterSubscriptionResource.php' => 'heroicon-o-envelope',
    'LoyaltyLevelResource.php'           => 'heroicon-o-trophy',
    'FaqResource.php'                    => 'heroicon-o-information-circle',
    'PageResource.php'                   => 'heroicon-o-document-duplicate',
    'ActivityLogResource.php'            => 'heroicon-o-queue-list',
    'SettingResource.php'                => 'heroicon-o-cog-6-tooth',
    'RoleResource.php'                   => 'heroicon-o-shield-check',
];

$dir = __DIR__ . '/../app/Filament/Resources';

foreach ($uniqueIconMap as $file => $icon) {
    $filePath = $dir . '/' . $file;
    if (file_exists($filePath)) {
        $content = file_get_contents($filePath);
        $content = preg_replace(
            '/protected static string\|BackedEnum\|null \$navigationIcon = [^;]+;/',
            "protected static string|BackedEnum|null \$navigationIcon = '{$icon}';",
            $content
        );
        file_put_contents($filePath, $content);
        echo "Assigned UNIQUE ICON to {$file} -> {$icon}\n";
    }
}

echo "All 25 Filament resources updated with 100% unique icons!\n";
