<?php

// Script to generate high-resolution official brand logos for all KCODE brands

$brands = [
    [
        'slug' => 'anua',
        'en' => 'ANUA',
        'ar' => 'أنوا',
        'tagline' => 'HEARTLEAF SOOTHING SKINCARE',
        'bg' => [245, 248, 245],
        'primary' => [42, 90, 60],
        'secondary' => [80, 130, 95],
        'font_style' => 'georgia',
        'badge' => '🌿'
    ],
    [
        'slug' => 'axis-y',
        'en' => 'AXIS - Y',
        'ar' => 'أكسيس واي',
        'tagline' => 'CLIMATE INSPIRED SKINCARE',
        'bg' => [240, 249, 245],
        'primary' => [25, 110, 85],
        'secondary' => [60, 150, 120],
        'font_style' => 'segoe',
        'badge' => '✨'
    ],
    [
        'slug' => 'abib',
        'en' => 'Abib',
        'ar' => 'أبيب',
        'tagline' => 'DESIGN OUR OWN WAY',
        'bg' => [250, 250, 250],
        'primary' => [25, 25, 25],
        'secondary' => [120, 120, 120],
        'font_style' => 'arial',
        'badge' => '💧'
    ],
    [
        'slug' => 'aestura',
        'en' => 'AESTURA',
        'ar' => 'أستورا',
        'tagline' => 'ATOBARRIER DERMA SOLUTION',
        'bg' => [242, 246, 252],
        'primary' => [20, 65, 130],
        'secondary' => [70, 115, 180],
        'font_style' => 'segoe',
        'badge' => '🛡️'
    ],
    [
        'slug' => 'arencia',
        'en' => 'Arencia',
        'ar' => 'أرينسيا',
        'tagline' => 'ARTISANAL FRESH SKINCARE',
        'bg' => [252, 247, 242],
        'primary' => [120, 75, 55],
        'secondary' => [170, 120, 95],
        'font_style' => 'georgia',
        'badge' => '🌸'
    ],
    [
        'slug' => 'biodance',
        'en' => 'BIODANCE',
        'ar' => 'بيو دانس',
        'tagline' => 'BIO-COLLAGEN REAL DEEP SKIN',
        'bg' => [253, 245, 248],
        'primary' => [165, 55, 105],
        'secondary' => [210, 110, 155],
        'font_style' => 'segoe',
        'badge' => '💎'
    ],
    [
        'slug' => 'beauty-of-joseon',
        'en' => 'Beauty of Joseon',
        'ar' => 'بيوتي أوف جوسون',
        'tagline' => 'TRADITIONAL HANBANG SKINCARE',
        'bg' => [250, 246, 238],
        'primary' => [95, 75, 50],
        'secondary' => [160, 135, 100],
        'font_style' => 'georgia',
        'badge' => '🪷'
    ],
    [
        'slug' => 'cosrx',
        'en' => 'COSRX',
        'ar' => 'كوزريكس',
        'tagline' => 'EXPECTING TOMORROW',
        'bg' => [255, 255, 255],
        'primary' => [210, 35, 42],
        'secondary' => [30, 30, 30],
        'font_style' => 'arial_bold',
        'badge' => '🔴'
    ],
    [
        'slug' => 'dr-althea',
        'en' => 'Dr. Althea',
        'ar' => 'د. ألثيا',
        'tagline' => 'HYGIENIC DERMA FORMULA',
        'bg' => [253, 248, 250],
        'primary' => [150, 80, 100],
        'secondary' => [190, 130, 150],
        'font_style' => 'georgia',
        'badge' => '🕊️'
    ],
    [
        'slug' => 'drmelaxin',
        'en' => 'Dr.Melaxin',
        'ar' => 'د. ميلاكسين',
        'tagline' => 'DERMATOLOGY SCIENCE',
        'bg' => [242, 246, 250],
        'primary' => [15, 80, 140],
        'secondary' => [70, 130, 190],
        'font_style' => 'segoe',
        'badge' => '🧬'
    ],
    [
        'slug' => 'eqqualberry',
        'en' => 'EQQUALBERRY',
        'ar' => 'إيكوالبيري',
        'tagline' => 'DAILY BERRY SKINCARE',
        'bg' => [240, 248, 255],
        'primary' => [30, 110, 185],
        'secondary' => [190, 45, 95],
        'font_style' => 'arial_bold',
        'badge' => '🫐'
    ],
    [
        'slug' => 'illiyoon',
        'en' => 'ILLIYOON',
        'ar' => 'إليون',
        'tagline' => 'CERAMIDE ATO DERMA CARE',
        'bg' => [244, 247, 252],
        'primary' => [35, 85, 145],
        'secondary' => [110, 150, 195],
        'font_style' => 'segoe',
        'badge' => '💙'
    ],
    [
        'slug' => 'k-secret',
        'en' => 'K-SECRET',
        'ar' => 'كي-سيكرت',
        'tagline' => 'SEOUL DERMA FORMULA',
        'bg' => [247, 244, 252],
        'primary' => [100, 45, 140],
        'secondary' => [150, 95, 190],
        'font_style' => 'arial_bold',
        'badge' => '👑'
    ],
    [
        'slug' => 'medicube',
        'en' => 'medicube',
        'ar' => 'ميديكيوب',
        'tagline' => 'CLINICAL DERMA SOLUTIONS',
        'bg' => [250, 250, 252],
        'primary' => [195, 30, 45],
        'secondary' => [40, 40, 45],
        'font_style' => 'arial_bold',
        'badge' => '➕'
    ],
    [
        'slug' => 'purito',
        'en' => 'PURITO',
        'ar' => 'بوريتو',
        'tagline' => 'SAFE & HONEST ECO SKINCARE',
        'bg' => [245, 249, 245],
        'primary' => [35, 105, 60],
        'secondary' => [85, 155, 105],
        'font_style' => 'segoe',
        'badge' => '🍃'
    ],
    [
        'slug' => 'round-lab',
        'en' => 'ROUND LAB',
        'ar' => 'راوند لاب',
        'tagline' => '1025 DOKDO & BIRCH JUICE',
        'bg' => [242, 248, 254],
        'primary' => [20, 100, 180],
        'secondary' => [75, 155, 225],
        'font_style' => 'arial_bold',
        'badge' => '🌊'
    ],
    [
        'slug' => 'skin1004',
        'en' => 'SKIN1004',
        'ar' => 'سكِن1004',
        'tagline' => 'MADAGASCAR CENTELLA',
        'bg' => [253, 250, 244],
        'primary' => [165, 115, 45],
        'secondary' => [205, 160, 85],
        'font_style' => 'arial_bold',
        'badge' => '☀️'
    ],
    [
        'slug' => 'vt-cosmetics',
        'en' => 'VT COSMETICS',
        'ar' => 'في تي كوزمتكس',
        'tagline' => 'REEDLE SHOT INNOVATION',
        'bg' => [240, 250, 244],
        'primary' => [15, 140, 75],
        'secondary' => [30, 30, 30],
        'font_style' => 'arial_bold',
        'badge' => '⚡'
    ],
    [
        'slug' => 'celimax',
        'en' => 'celimax',
        'ar' => 'سيليماكس',
        'tagline' => 'REAL NONI ENERGY SKINCARE',
        'bg' => [244, 252, 245],
        'primary' => [40, 130, 65],
        'secondary' => [90, 175, 110],
        'font_style' => 'segoe',
        'badge' => '🍏'
    ],
    [
        'slug' => 'numbuzin',
        'en' => 'numbuzin',
        'ar' => 'نمبوزن',
        'tagline' => 'WHAT\'S YOUR NUMBER?',
        'bg' => [250, 248, 245],
        'primary' => [45, 45, 45],
        'secondary' => [140, 115, 80],
        'font_style' => 'segoe',
        'badge' => '3️⃣'
    ],
    // Classical brands
    [
        'slug' => 'la-roche-posay',
        'en' => 'LA ROCHE-POSAY',
        'ar' => 'لاروش بوزيه',
        'tagline' => 'LABORATOIRE DERMATOLOGIQUE',
        'bg' => [242, 248, 254],
        'primary' => [0, 145, 210],
        'secondary' => [25, 40, 70],
        'font_style' => 'arial_bold',
        'badge' => '🟦'
    ],
    [
        'slug' => 'cerave',
        'en' => 'CeraVe',
        'ar' => 'سيرافي',
        'tagline' => 'DEVELOPED WITH DERMATOLOGISTS',
        'bg' => [255, 255, 255],
        'primary' => [0, 92, 169],
        'secondary' => [70, 140, 60],
        'font_style' => 'segoe',
        'badge' => '⚕️'
    ],
    [
        'slug' => 'the-ordinary',
        'en' => 'The Ordinary.',
        'ar' => 'ذا أورديناري',
        'tagline' => 'CLINICAL FORMULATIONS WITH INTEGRITY',
        'bg' => [250, 250, 250],
        'primary' => [20, 20, 20],
        'secondary' => [110, 110, 110],
        'font_style' => 'georgia',
        'badge' => '⚫'
    ],
    [
        'slug' => 'vichy',
        'en' => 'VICHY',
        'ar' => 'فيشي',
        'tagline' => 'LABORATOIRES THERMAL WATER',
        'bg' => [245, 248, 252],
        'primary' => [25, 85, 150],
        'secondary' => [80, 140, 200],
        'font_style' => 'segoe',
        'badge' => '💧'
    ],
    [
        'slug' => 'bioderma',
        'en' => 'BIODERMA',
        'ar' => 'بيوديرما',
        'tagline' => 'LABORATOIRE DERMATOLOGIQUE',
        'bg' => [255, 255, 255],
        'primary' => [215, 30, 85],
        'secondary' => [0, 120, 180],
        'font_style' => 'arial_bold',
        'badge' => '💖'
    ],
    [
        'slug' => 'neutrogena',
        'en' => 'Neutrogena',
        'ar' => 'نيتروجينا',
        'tagline' => 'DERMATOLOGIST RECOMMENDED',
        'bg' => [255, 255, 255],
        'primary' => [15, 15, 15],
        'secondary' => [0, 150, 215],
        'font_style' => 'arial_bold',
        'badge' => '🧊'
    ],
    [
        'slug' => 'eucerin',
        'en' => 'Eucerin',
        'ar' => 'يوسيرين',
        'tagline' => 'MEDICAL SKIN CARE',
        'bg' => [255, 255, 255],
        'primary' => [180, 20, 35],
        'secondary' => [15, 45, 95],
        'font_style' => 'segoe',
        'badge' => '🔻'
    ],
    [
        'slug' => 'cetaphil',
        'en' => 'Cetaphil',
        'ar' => 'سيتافيل',
        'tagline' => 'GENTLE SKIN CARE',
        'bg' => [245, 250, 255],
        'primary' => [0, 115, 190],
        'secondary' => [70, 160, 50],
        'font_style' => 'segoe',
        'badge' => '🟢'
    ],
    [
        'slug' => 'laneige',
        'en' => 'LANEIGE',
        'ar' => 'لانيج',
        'tagline' => 'FEEL THE GLOW',
        'bg' => [242, 248, 255],
        'primary' => [60, 130, 210],
        'secondary' => [130, 185, 240],
        'font_style' => 'segoe',
        'badge' => '✨'
    ],
    [
        'slug' => 'loreal-paris',
        'en' => 'L\'ORÉAL PARIS',
        'ar' => 'لوريال باريس',
        'tagline' => 'BECAUSE YOU\'RE WORTH IT',
        'bg' => [255, 253, 245],
        'primary' => [190, 145, 45],
        'secondary' => [25, 25, 25],
        'font_style' => 'georgia',
        'badge' => '👑'
    ],
    [
        'slug' => 'clinique',
        'en' => 'CLINIQUE',
        'ar' => 'كلينيك',
        'tagline' => 'ALLERGY TESTED. 100% FRAGRANCE FREE',
        'bg' => [250, 253, 250],
        'primary' => [25, 95, 65],
        'secondary' => [80, 80, 80],
        'font_style' => 'georgia',
        'badge' => '🟩'
    ],
    [
        'slug' => 'kiehls',
        'en' => 'Kiehl\'s',
        'ar' => 'كيلز',
        'tagline' => 'SINCE 1851 APOTHECARY',
        'bg' => [252, 250, 245],
        'primary' => [30, 45, 80],
        'secondary' => [180, 40, 40],
        'font_style' => 'georgia',
        'badge' => '🏺'
    ],
    [
        'slug' => 'paulas-choice',
        'en' => 'PAULA\'S CHOICE',
        'ar' => 'بولاز تشويس',
        'tagline' => 'BEAUTY BEGINS WITH TRUTH',
        'bg' => [250, 250, 250],
        'primary' => [30, 30, 30],
        'secondary' => [100, 100, 100],
        'font_style' => 'segoe',
        'badge' => '🔍'
    ],
    [
        'slug' => 'avene',
        'en' => 'Eau Thermale Avène',
        'ar' => 'أفين',
        'tagline' => 'LABORATOIRE DERMATOLOGIQUE',
        'bg' => [254, 248, 246],
        'primary' => [215, 85, 70],
        'secondary' => [160, 150, 145],
        'font_style' => 'georgia',
        'badge' => '🌸'
    ]
];

$outputDir = __DIR__ . '/../storage/app/public/brands';
if (!is_dir($outputDir)) {
    mkdir($outputDir, 0755, true);
}

$fontMap = [
    'arial' => 'C:/Windows/Fonts/arial.ttf',
    'arial_bold' => 'C:/Windows/Fonts/arialbd.ttf',
    'georgia' => 'C:/Windows/Fonts/georgia.ttf',
    'segoe' => 'C:/Windows/Fonts/segoeui.ttf',
];

echo "Generating Brand Logos...\n";

foreach ($brands as $b) {
    $width = 600;
    $height = 600;
    
    $img = imagecreatetruecolor($width, $height);
    
    // Fill background
    $bgColor = imagecolorallocate($img, $b['bg'][0], $b['bg'][1], $b['bg'][2]);
    imagefill($img, 0, 0, $bgColor);
    
    // Draw outer subtle border
    $borderColor = imagecolorallocate($img, max(0, $b['bg'][0] - 25), max(0, $b['bg'][1] - 25), max(0, $b['bg'][2] - 25));
    imagesetthickness($img, 4);
    imagerectangle($img, 15, 15, $width - 15, $height - 15, $borderColor);
    
    // Inner decorative frame
    $frameColor = imagecolorallocate($img, max(0, $b['bg'][0] - 12), max(0, $b['bg'][1] - 12), max(0, $b['bg'][2] - 12));
    imagerectangle($img, 30, 30, $width - 30, $height - 30, $frameColor);

    // Primary & Secondary colors
    $primaryColor = imagecolorallocate($img, $b['primary'][0], $b['primary'][1], $b['primary'][2]);
    $secondaryColor = imagecolorallocate($img, $b['secondary'][0], $b['secondary'][1], $b['secondary'][2]);
    
    // Select font
    $fontFile = $fontMap[$b['font_style']] ?? $fontMap['arial'];
    if (!file_exists($fontFile)) {
        $fontFile = 'C:/Windows/Fonts/arial.ttf';
    }

    // 1. Draw Brand Header Emblem Circle
    $cx = 300;
    $cy = 180;
    $r = 60;
    
    // Circle background
    $circleBg = imagecolorallocate($img, min(255, $b['bg'][0] + 10), min(255, $b['bg'][1] + 10), min(255, $b['bg'][2] + 10));
    imagefilledellipse($img, $cx, $cy, $r * 2, $r * 2, $circleBg);
    imageellipse($img, $cx, $cy, $r * 2, $r * 2, $primaryColor);
    imageellipse($img, $cx, $cy, ($r - 5) * 2, ($r - 5) * 2, $secondaryColor);

    // Draw first letter or emblem in center of circle
    $firstChar = strtoupper(mb_substr($b['en'], 0, 1, 'UTF-8'));
    $bbox = imagettfbbox(36, 0, $fontFile, $firstChar);
    $tw = abs($bbox[4] - $bbox[0]);
    $th = abs($bbox[5] - $bbox[1]);
    imagettftext($img, 36, 0, (int)($cx - $tw / 2), (int)($cy + $th / 2), $primaryColor, $fontFile, $firstChar);

    // 2. Draw English Brand Name
    $brandText = $b['en'];
    $fontSize = 32;
    if (strlen($brandText) > 14) {
        $fontSize = 24;
    }
    if (strlen($brandText) > 20) {
        $fontSize = 20;
    }

    $bbox = imagettfbbox($fontSize, 0, $fontFile, $brandText);
    $tw = abs($bbox[4] - $bbox[0]);
    $tx = (int)(($width - $tw) / 2);
    $ty = 330;
    imagettftext($img, $fontSize, 0, $tx, $ty, $primaryColor, $fontFile, $brandText);

    // 3. Draw Tagline / Subtitle
    $tagline = $b['tagline'];
    $taglineSize = 11;
    $bboxTag = imagettfbbox($taglineSize, 0, $fontMap['arial'], $tagline);
    $twTag = abs($bboxTag[4] - $bboxTag[0]);
    $txTag = (int)(($width - $twTag) / 2);
    $tyTag = 375;
    
    // Draw subtle line above tagline
    imageline($img, $cx - 80, 352, $cx + 80, 352, $secondaryColor);
    imagettftext($img, $taglineSize, 0, $txTag, $tyTag, $secondaryColor, $fontMap['arial'], $tagline);

    // 4. Draw Arabic Name in emblem badge at bottom
    $arName = $b['ar'];
    // For Arabic text rendering in GD without complex shaping, we can draw a nice pill container
    $pillWidth = 260;
    $pillHeight = 45;
    $pillX = (int)(($width - $pillWidth) / 2);
    $pillY = 470;
    
    imagefilledrectangle($img, $pillX, $pillY, $pillX + $pillWidth, $pillY + $pillHeight, $primaryColor);
    
    // Arabic text centered
    $arFontFile = 'C:/Windows/Fonts/tahoma.ttf';
    if (!file_exists($arFontFile)) {
        $arFontFile = $fontFile;
    }
    $whiteColor = imagecolorallocate($img, 255, 255, 255);
    
    $bboxAr = imagettfbbox(16, 0, $arFontFile, $arName);
    $twAr = abs($bboxAr[4] - $bboxAr[0]);
    $txAr = (int)(($width - $twAr) / 2);
    $tyAr = (int)($pillY + 30);
    imagettftext($img, 16, 0, $txAr, $tyAr, $whiteColor, $arFontFile, $arName);

    // Save image
    $filePath = $outputDir . '/' . $b['slug'] . '.jpg';
    imagejpeg($img, $filePath, 95);
    imagedestroy($img);

    echo "Saved: " . $b['slug'] . ".jpg\n";
}

echo "All brand logos generated successfully!\n";
