<?php

namespace Database\Seeders;

use App\Models\Blog;
use App\Models\BlogCategory;
use App\Models\BlogSeo;
use App\Models\BlogTag;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class BlogSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. Ensure Blog Author exists
        $author = User::where('type', 'blog_author')->first();
        if (!$author) {
            $author = User::create([
                'name' => 'د. سارة أحمد - خبيرة العناية بالبشرة',
                'email' => 'author@kcode.com',
                'password' => bcrypt('A11223344'),
                'phone' => '01000000000',
                'type' => 'blog_author',
                'status' => 'active',
                'email_verified_at' => now(),
            ]);
        }

        // 2. Create Blog Categories
        $categoriesData = [
            [
                'name_ar' => 'العناية بالبشرة',
                'name_en' => 'Skincare',
                'slug' => 'skincare',
                'image' => 'blogs/categories/skincare.jpg',
            ],
            [
                'name_ar' => 'روتين الجمال',
                'name_en' => 'Beauty Routine',
                'slug' => 'beauty-routine',
                'image' => 'blogs/categories/beauty-routine.jpg',
            ],
            [
                'name_ar' => 'التغذية والنضارة',
                'name_en' => 'Health & Glow',
                'slug' => 'health-glow',
                'image' => 'blogs/categories/health-glow.jpg',
            ],
            [
                'name_ar' => 'نصائح وإرشادات',
                'name_en' => 'Tips & Guides',
                'slug' => 'tips-guides',
                'image' => 'blogs/categories/tips-guides.jpg',
            ],
        ];

        $categories = [];
        foreach ($categoriesData as $catData) {
            $categories[$catData['slug']] = BlogCategory::updateOrCreate(
                ['slug' => $catData['slug']],
                $catData
            );
        }

        // 3. Create Blog Tags
        $tagsData = [
            ['name_ar' => 'بشرة جافة', 'name_en' => 'Dry Skin', 'slug' => 'dry-skin'],
            ['name_ar' => 'ترطيب', 'name_en' => 'Moisturizing', 'slug' => 'moisturizing'],
            ['name_ar' => 'سيروم الهيالورونيك', 'name_en' => 'Hyaluronic Serum', 'slug' => 'hyaluronic-serum'],
            ['name_ar' => 'واقي الشمس', 'name_en' => 'Sunscreen', 'slug' => 'sunscreen'],
            ['name_ar' => 'مكافحة التجاعيد', 'name_en' => 'Anti-Aging', 'slug' => 'anti-aging'],
            ['name_ar' => 'فيتامين سي', 'name_en' => 'Vitamin C', 'slug' => 'vitamin-c'],
            ['name_ar' => 'روتين مسائي', 'name_en' => 'Night Routine', 'slug' => 'night-routine'],
        ];

        $tags = [];
        foreach ($tagsData as $tagData) {
            $tags[$tagData['slug']] = BlogTag::updateOrCreate(
                ['slug' => $tagData['slug']],
                $tagData
            );
        }

        // 4. Create Blogs with detailed content & SEO
        $blogsData = [
            [
                'title_ar' => 'أفضل 5 خطوات لروتين العناية بالبشرة الجافة في الشتاء',
                'title_en' => 'Top 5 Steps for Dry Skin Care Routine in Winter',
                'slug' => 'top-5-steps-for-dry-skin-care-routine-in-winter',
                'excerpt_ar' => 'تعرفي على أهم الخطوات الأساسية لحماية بشرتك من الجفاف والتشققات خلال فصل الشتاء والحفاظ على رطوبتها.',
                'excerpt_en' => 'Discover essential steps to protect your skin from dryness during winter and maintain maximum hydration.',
                'content_ar' => '<p>يعاني الكثيرون خلال فصل الشتاء من جفاف البشرة الشديد والتهيج الناتج عن انخفاض درجات الحرارة واستخدام الماء الساخن.</p><h3>1. اختيار المنظف اللطيف</h3><p>تجنبي الغسولات القاسية التي تجرد البشرة من زيوتها الطبيعية، واعتمدي على غسول زيتي أو كريمي مرطب.</p><h3>2. استخدام سيروم الهيالورونيك أسيد</h3><p>يعمل الهيالورونيك على حبس الرطوبة داخل طبقات الجلد ليبقى مشرقاً وناعماً طوال اليوم.</p><h3>3. لا تتخلي عن واقي الشمس</h3><p>حتى في الأيام الغائمة، تؤثر الأشعة فوق البنفسجية على صحة بشرتك وتسبب الشيخوخة المبكرة.</p>',
                'content_en' => '<p>Many people suffer from extreme dry skin during winter. Here are 5 essential steps to keep your skin glowing and hydrated.</p><h3>1. Use a Gentle Cleanser</h3><p>Avoid harsh soaps that strip your natural oils.</p><h3>2. Apply Hyaluronic Acid Serum</h3><p>Locks in moisture deep inside skin layers.</p><h3>3. Never Skip Sunscreen</h3><p>UV rays penetrate even on cloudy days.</p>',
                'featured_image' => 'blogs/dry-skin-winter.jpg',
                'category_slug' => 'skincare',
                'tag_slugs' => ['dry-skin', 'moisturizing', 'hyaluronic-serum', 'sunscreen'],
                'is_featured' => true,
                'reading_time' => 4,
                'views' => 1250,
                'status' => 'published',
                'published_at' => now()->subDays(2),
                'seo' => [
                    'meta_title_ar' => 'روتين العناية بالبشرة الجافة في الشتاء | دليل شامل',
                    'meta_title_en' => 'Winter Dry Skin Care Routine | Complete Guide',
                    'meta_description_ar' => 'دليل خطوة بخطوة للتعامل مع جفاف البشرة في الشتاء باستخدام أفضل الترطيبات والسيرومات الصحية.',
                    'meta_description_en' => 'Step-by-step guide to treat winter dry skin using top serums and moisturizers.',
                    'meta_keywords_ar' => 'بشرة جافة, ترطيب الشتاء, هيالورونيك اسيد, واقي شمس',
                    'meta_keywords_en' => 'dry skin, winter moisturizer, hyaluronic acid, sunscreen',
                    'canonical_url' => url('/blogs/top-5-steps-for-dry-skin-care-routine-in-winter'),
                    'og_title_ar' => 'أفضل 5 خطوات لروتين العناية بالبشرة الجافة في الشتاء',
                    'og_title_en' => 'Top 5 Steps for Dry Skin Care Routine in Winter',
                    'og_description_ar' => 'احمي بشرتك من الجفاف هذا الشتاء مع أفضل نصائح التجميل والعناية.',
                    'og_description_en' => 'Protect your skin from winter dryness with expert beauty tips.',
                    'og_image' => 'blogs/dry-skin-winter.jpg',
                ]
            ],
            [
                'title_ar' => 'فوائد سيروم فيتامين سي وكيفية استخدامه بالشكل الصحيح',
                'title_en' => 'Benefits of Vitamin C Serum & How to Use it Correctly',
                'slug' => 'benefits-of-vitamin-c-serum-and-how-to-use-it',
                'excerpt_ar' => 'فيتامين سي هو سر النضارة وإشراقة البشرة، تعرفي على طريقة دمجه في روتينك اليومي بدون تهيج.',
                'excerpt_en' => 'Vitamin C is the secret to glowing skin. Learn how to integrate it into your daily skincare routine safely.',
                'content_ar' => '<p>يعتبر فيتامين سي مضاد أكسدة قوي يساعد في توحيد لون البشرة وتقليل التصبغات وتحفيز إنتاج الكولاجين.</p><h3>متى يوضع فيتامين سي؟</h3><p>يُفضل استخدامه صباحاً قبل مرطب البشرة وواقي الشمس لحماية البشرة من التلوث والأكسدة.</p>',
                'content_en' => '<p>Vitamin C is a powerful antioxidant that evens skin tone and boosts collagen production.</p><h3>When to apply Vitamin C?</h3><p>Best applied in the morning before moisturizer and sunscreen.</p>',
                'featured_image' => 'blogs/vitamin-c-guide.jpg',
                'category_slug' => 'health-glow',
                'tag_slugs' => ['vitamin-c', 'anti-aging', 'sunscreen'],
                'is_featured' => true,
                'reading_time' => 3,
                'views' => 840,
                'status' => 'published',
                'published_at' => now()->subDays(5),
                'seo' => [
                    'meta_title_ar' => 'فوائد سيروم فيتامين سي للبشرة والطريقة الصحيحة لاستخدامه',
                    'meta_title_en' => 'Vitamin C Serum Benefits for Skin & Usage Guide',
                    'meta_description_ar' => 'اكتشفي فوائد سيروم فيتامين سي في تفتيح وتوحيد لون البشرة وحمايتها من التصبغات.',
                    'meta_description_en' => 'Discover how Vitamin C serum brightens skin and reduces hyperpigmentation.',
                    'meta_keywords_ar' => 'فيتامين سي, سيروم تفتيح, نضارة البشرة, التصبغات',
                    'meta_keywords_en' => 'vitamin c, brightening serum, skin glow, pigmentation',
                    'canonical_url' => url('/blogs/benefits-of-vitamin-c-serum-and-how-to-use-it'),
                    'og_title_ar' => 'فوائد سيروم فيتامين سي وكيفية استخدامه',
                    'og_title_en' => 'Benefits of Vitamin C Serum',
                    'og_description_ar' => 'احصلي على بشرة مشرقة وموحدة مع سيروم فيتامين سي.',
                    'og_description_en' => 'Achieve glowing and even-toned skin with Vitamin C serum.',
                    'og_image' => 'blogs/vitamin-c-guide.jpg',
                ]
            ],
            [
                'title_ar' => 'دليلك الشامل لروتين العناية المسائي قبل النوم',
                'title_en' => 'Your Ultimate Night Skincare Routine Guide',
                'slug' => 'your-ultimate-night-skincare-routine-guide',
                'excerpt_ar' => 'الليل هو الوقت الأمثل لتجدد خلايا البشرة. اكتشفي الترتيب الصحيح لمنتجات روتينك المسائي.',
                'excerpt_en' => 'Night is the perfect time for skin renewal. Discover the correct order to apply your evening skincare products.',
                'content_ar' => '<p>أثناء النوم تجدد البشرة خلاياها وتستفيد القصوى من المستحضرات المغذية والغنية بالمرطبات العميقة.</p><h3>خطوات الروتين المسائي:</h3><ul><li>إزالة المكياج وتنظيف البشرة مزدوجاً (Double Cleansing).</li><li>وضع التونر والسيروم المغذي.</li><li>استخدام كريم العين وكريم الليل الغني.</li></ul>',
                'content_en' => '<p>During sleep, your skin repairs itself. Make the most out of your night products with this step-by-step order.</p>',
                'featured_image' => 'blogs/night-routine.jpg',
                'category_slug' => 'beauty-routine',
                'tag_slugs' => ['night-routine', 'moisturizing', 'anti-aging'],
                'is_featured' => false,
                'reading_time' => 5,
                'views' => 620,
                'status' => 'published',
                'published_at' => now()->subDays(10),
                'seo' => [
                    'meta_title_ar' => 'الروتين المسائي المثالي قبل النوم لترطيب وتجديد البشرة',
                    'meta_title_en' => 'Ultimate Night Skincare Routine for Skin Regeneration',
                    'meta_description_ar' => 'أفضل ترتيب لاستخدام كريم الليل والسيروم قبل النوم للحصول على بشرة ناعمة ونضرة صباحاً.',
                    'meta_description_en' => 'Best sequence for applying night cream and serums before sleep for smooth morning skin.',
                    'meta_keywords_ar' => 'روتين مسائي, كريم الليل, تنظيف مزدوج, تجديد الخلايا',
                    'meta_keywords_en' => 'night routine, night cream, double cleansing, skin renewal',
                    'canonical_url' => url('/blogs/your-ultimate-night-skincare-routine-guide'),
                    'og_title_ar' => 'دليلك لروتين العناية المسائي قبل النوم',
                    'og_title_en' => 'Your Ultimate Night Skincare Routine Guide',
                    'og_description_ar' => 'جددي بشرتك أثناء النوم مع هذا الروتين المسائي التخصصي.',
                    'og_description_en' => 'Renew your skin overnight with an expert evening skincare steps.',
                    'og_image' => 'blogs/night-routine.jpg',
                ]
            ],
            [
                'title_ar' => 'أخطاء شائعة عند استخدام واقي الشمس تسبب ظهور التصبغات',
                'title_en' => 'Common Sunscreen Mistakes That Cause Pigmentation',
                'slug' => 'common-sunscreen-mistakes-that-cause-pigmentation',
                'excerpt_ar' => 'هل تضعين واقي الشمس ولكن لا تحصلين على الحماية الكافية؟ اكتشفي الأخطاء الشائعة وكيف تتجنبيها.',
                'excerpt_en' => 'Are you applying sunscreen but still getting tanned or pigmented? Discover common mistakes and solutions.',
                'content_ar' => '<p>عدم تجديد واقي الشمس كل ساعتين واستخدام كميات غير كافية من أكثر الأسباب التي تؤدي لظهور البقع والتجاعيد المبكرة.</p>',
                'content_en' => '<p>Not reapplying sunscreen every 2 hours and using insufficient quantity are main causes of early skin damage.</p>',
                'featured_image' => 'blogs/sunscreen-mistakes.jpg',
                'category_slug' => 'tips-guides',
                'tag_slugs' => ['sunscreen', 'anti-aging'],
                'is_featured' => false,
                'reading_time' => 3,
                'views' => 410,
                'status' => 'published',
                'published_at' => now()->subDays(15),
                'seo' => [
                    'meta_title_ar' => 'أخطاء تجنبيها عند استخدام واقي الشمس لحماية بشرتك',
                    'meta_title_en' => 'Sunscreen Mistakes to Avoid for Maximum Protection',
                    'meta_description_ar' => 'تعرفي على الكمية المناسبة وكيفية إعادات تطبيق واقي الشمس لحماية كاملة من الشمس.',
                    'meta_description_en' => 'Learn the proper sunscreen amount and reapplication rules for full protection.',
                    'meta_keywords_ar' => 'واقي الشمس, تصبغات الشمس, حماية البشرة, تجاعيد الشمس',
                    'meta_keywords_en' => 'sunscreen mistakes, pigmentation, skin protection, uv rays',
                    'canonical_url' => url('/blogs/common-sunscreen-mistakes-that-cause-pigmentation'),
                    'og_title_ar' => 'أخطاء شائعة عند استخدام واقي الشمس',
                    'og_title_en' => 'Common Sunscreen Mistakes',
                    'og_description_ar' => 'تجنبي التصبغات والبقع بحماية شمسية صحيحة.',
                    'og_description_en' => 'Avoid pigmentation with proper sunscreen usage.',
                    'og_image' => 'blogs/sunscreen-mistakes.jpg',
                ]
            ],
        ];

        foreach ($blogsData as $bData) {
            $catSlug = $bData['category_slug'];
            $tagSlugs = $bData['tag_slugs'];
            $seoData = $bData['seo'];

            unset($bData['category_slug'], $bData['tag_slugs'], $bData['seo']);

            $bData['category_id'] = $categories[$catSlug]->id ?? null;
            $bData['author_id'] = $author->id;

            $blog = Blog::updateOrCreate(
                ['slug' => $bData['slug']],
                $bData
            );

            // Sync Tags
            $tagIds = [];
            foreach ($tagSlugs as $tSlug) {
                if (isset($tags[$tSlug])) {
                    $tagIds[] = $tags[$tSlug]->id;
                }
            }
            $blog->tags()->sync($tagIds);

            // Create SEO
            $blog->seo()->updateOrCreate(
                ['blog_id' => $blog->id],
                $seoData
            );
        }

        $this->command->info('✅ Blog categories, tags, author, blogs, and SEO records seeded successfully!');
    }
}
