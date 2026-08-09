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

        // 2. Create Blog Categories with downloadable images
        $categoriesData = [
            [
                'name_ar' => 'العناية بالبشرة',
                'name_en' => 'Skincare',
                'slug' => 'skincare',
                'image_url' => 'https://images.unsplash.com/photo-1556228720-195a672e8a03?auto=format&fit=crop&q=80&w=800',
                'filename' => 'skincare.jpg',
            ],
            [
                'name_ar' => 'روتين الجمال',
                'name_en' => 'Beauty Routine',
                'slug' => 'beauty-routine',
                'image_url' => 'https://images.unsplash.com/photo-1570172619644-dfd03ed5d881?auto=format&fit=crop&q=80&w=800',
                'filename' => 'beauty-routine.jpg',
            ],
            [
                'name_ar' => 'التغذية والنضارة',
                'name_en' => 'Health & Glow',
                'slug' => 'health-glow',
                'image_url' => 'https://images.unsplash.com/photo-1512290900673-70024fe74923?auto=format&fit=crop&q=80&w=800',
                'filename' => 'health-glow.jpg',
            ],
            [
                'name_ar' => 'نصائح وإرشادات',
                'name_en' => 'Tips & Guides',
                'slug' => 'tips-guides',
                'image_url' => 'https://images.unsplash.com/photo-1522337360788-8b13dee7a37e?auto=format&fit=crop&q=80&w=800',
                'filename' => 'tips-guides.jpg',
            ],
            [
                'name_ar' => 'مكافحة الشيخوخة',
                'name_en' => 'Anti-Aging',
                'slug' => 'anti-aging-cat',
                'image_url' => 'https://images.unsplash.com/photo-1598440947619-2c35fc9aa908?auto=format&fit=crop&q=80&w=800',
                'filename' => 'anti-aging.jpg',
            ],
        ];

        $categories = [];
        foreach ($categoriesData as $catData) {
            $savedFilename = ImageDownloader::downloadAndSave(
                $catData['image_url'],
                'blogs/categories',
                $catData['filename']
            );

            $categories[$catData['slug']] = BlogCategory::updateOrCreate(
                ['slug' => $catData['slug']],
                [
                    'name_ar' => $catData['name_ar'],
                    'name_en' => $catData['name_en'],
                    'slug' => $catData['slug'],
                    'image' => 'blogs/categories/' . $savedFilename,
                ]
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
            ['name_ar' => 'بشرة دهنية', 'name_en' => 'Oily Skin', 'slug' => 'oily-skin'],
            ['name_ar' => 'حب الشباب', 'name_en' => 'Acne Control', 'slug' => 'acne-control'],
            ['name_ar' => 'تنظيف مزدوج', 'name_en' => 'Double Cleansing', 'slug' => 'double-cleansing'],
        ];

        $tags = [];
        foreach ($tagsData as $tagData) {
            $tags[$tagData['slug']] = BlogTag::updateOrCreate(
                ['slug' => $tagData['slug']],
                $tagData
            );
        }

        // 4. Create Blogs with downloaded images & full SEO details
        $blogsData = [
            [
                'name_ar' => 'روتين البشرة الجافة في الشتاء',
                'name_en' => 'Winter Dry Skin Care Routine',
                'name' => 'روتين البشرة الجافة في الشتاء',
                'title_ar' => 'أفضل 5 خطوات لروتين العناية بالبشرة الجافة في الشتاء',
                'title_en' => 'Top 5 Steps for Dry Skin Care Routine in Winter',
                'slug' => 'top-5-steps-for-dry-skin-care-routine-in-winter',
                'excerpt_ar' => 'تعرفي على أهم الخطوات الأساسية لحماية بشرتك من الجفاف والتشققات خلال فصل الشتاء والحفاظ على رطوبتها العميقة.',
                'excerpt_en' => 'Discover essential steps to protect your skin from winter dryness and keep it intensely hydrated.',
                'content_ar' => '<p>يعاني الكثيرون خلال فصل الشتاء من جفاف البشرة الشديد والتهيج الناتج عن انخفاض درجات الحرارة واستخدام الماء الساخن بكثرة.</p><h3>1. اختيار المنظف اللطيف</h3><p>تجنبي الغسولات القاسية التي تجرد البشرة من زيوتها الطبيعية، واعتمدي على غسول زيتي أو كريمي مرطب يحافظ على حاجز البشرة الوقائي.</p><h3>2. استخدام سيروم الهيالورونيك أسيد</h3><p>يعمل الهيالورونيك على حبس الرطوبة داخل طبقات الجلد ليبقى مشرقاً وناعماً طوال اليوم. يُفضل وضعه على بشرة رطبة قليلاً.</p><h3>3. استخدام مرطب غني بالسيراميد</h3><p>السيراميد يعيد بناء حاجز الجلد التالف ويحبس الرطوبة داخل طبقات البشرة لمنع التبخر والتشققات.</p><h3>4. لا تتخلي عن واقي الشمس</h3><p>حتى في الأيام الغائمة أو الباردة، تؤثر الأشعة فوق البنفسجية UVA و UVB على صحة بشرتك وتسبب الشيخوخة المبكرة والتصبغات.</p><h3>5. شرب كميات كافية من الماء</h3><p>الترطيب الداخلي لا يقل أهمية عن الترطيب الخارجي. احرصي على شرب 2-3 لتر ماء يومياً للحفاظ على مرونة الجلد.</p>',
                'content_en' => '<p>Many people suffer from extreme dry skin and irritation during winter due to cold weather and hot showers.</p><h3>1. Choose a Gentle Cleanser</h3><p>Avoid harsh foaming washes that strip natural lipids. Opt for cream or oil cleansers.</p><h3>2. Apply Hyaluronic Acid Serum</h3><p>Hyaluronic acid locks moisture into skin layers. Apply it on damp skin for best results.</p><h3>3. Use a Ceramide-Rich Moisturizer</h3><p>Ceramides rebuild the skin barrier and prevent moisture loss throughout the day.</p><h3>4. Never Skip Sunscreen</h3><p>UV rays penetrate even on overcast winter days causing premature aging.</p><h3>5. Stay Hydrated Internally</h3><p>Drink 2-3 liters of water daily to maintain skin elasticity from within.</p>',
                'image_url' => 'https://images.unsplash.com/photo-1512290900673-70024fe74923?auto=format&fit=crop&q=80&w=1000',
                'filename' => 'dry-skin-winter.jpg',
                'category_slug' => 'skincare',
                'tag_slugs' => ['dry-skin', 'moisturizing', 'hyaluronic-serum', 'sunscreen'],
                'is_featured' => true,
                'reading_time' => 4,
                'views' => 1450,
                'status' => 'published',
                'published_at' => now()->subDays(2),
                'seo' => [
                    'meta_title_ar' => 'روتين العناية بالبشرة الجافة في الشتاء | دليل شامل وحلول جفاف الجلد',
                    'meta_title_en' => 'Winter Dry Skin Care Routine | Complete Hydration Guide',
                    'meta_description_ar' => 'دليل خطوة بخطوة للتعامل مع جفاف البشرة في الشتاء باستخدام سيروم الهيالورونيك والسيراميد وواقي الشمس.',
                    'meta_description_en' => 'Step-by-step guide to treat winter dry skin using top hyaluronic serums, ceramides, and moisturizers.',
                    'meta_keywords_ar' => 'بشرة جافة, ترطيب الشتاء, هيالورونيك اسيد, سيراميد, واقي شمس, روتين البشرة',
                    'meta_keywords_en' => 'dry skin, winter moisturizer, hyaluronic acid, ceramides, sunscreen, skincare routine',
                    'canonical_url' => url('/blogs/top-5-steps-for-dry-skin-care-routine-in-winter'),
                    'og_title_ar' => 'أفضل 5 خطوات لروتين العناية بالبشرة الجافة في الشتاء',
                    'og_title_en' => 'Top 5 Steps for Dry Skin Care Routine in Winter',
                    'og_description_ar' => 'احمي بشرتك من الجفاف والتشققات هذا الشتاء مع أفضل نصائح التجميل والعناية بالبشرة.',
                    'og_description_en' => 'Protect your skin from winter dryness with expert hydration & beauty tips.',
                ]
            ],
            [
                'name_ar' => 'دليل سيروم فيتامين سي',
                'name_en' => 'Vitamin C Serum Guide',
                'name' => 'دليل سيروم فيتامين سي',
                'title_ar' => 'فوائد سيروم فيتامين سي وكيفية استخدامه بالشكل الصحيح للنضارة',
                'title_en' => 'Benefits of Vitamin C Serum & How to Use it Correctly for Radiant Skin',
                'slug' => 'benefits-of-vitamin-c-serum-and-how-to-use-it',
                'excerpt_ar' => 'فيتامين سي هو سر النضارة وإشراقة البشرة والتخلص من التصبغات. تعرفي على طريقة دمجه في روتينك اليومي.',
                'excerpt_en' => 'Vitamin C is the key to radiant skin and even tone. Learn how to integrate it safely into your daily skincare routine.',
                'content_ar' => '<p>يعتبر سيروم فيتامين سي من أقوى مضادات الأكسدة التي تعالج التصبغات وتحارب الشوارد حرة الناتجة عن التلوث والشمس.</p><h3>فوائد سيروم فيتامين سي الرئيسية:</h3><ul><li>تحفيز إنتاج الكولاجين وتحسين مرونة الجلد.</li><li>تفتيح التصبغات والبقع الداكنة وآثار حب الشباب.</li><li>حماية البشرة من الأكسدة وأشعة الشمس الضارة عند دمجه مع واقي الشمس.</li></ul><h3>متى وكيف يوضع فيتامين سي؟</h3><p>يُفضل استخدامه صباحاً بعد غسل الوجه وتجفيفه، وتُوضع بضع قطرات على الوجه والرقبة قبل المرطب وواقي الشمس.</p>',
                'content_en' => '<p>Vitamin C serum is a powerhouse antioxidant that brightens skin, reduces hyperpigmentation, and boosts collagen.</p><h3>Key Benefits of Vitamin C:</h3><ul><li>Boosts natural collagen synthesis for firmer skin.</li><li>Fades dark spots, acne scars, and uneven hyperpigmentation.</li><li>Protects against environmental oxidation when paired with sunscreen.</li></ul><h3>How and When to Apply?</h3><p>Best applied in the morning after cleansing. Apply a few drops to face and neck before moisturizer and SPF.</p>',
                'image_url' => 'https://images.unsplash.com/photo-1620916566398-39f1143ab7be?auto=format&fit=crop&q=80&w=1000',
                'filename' => 'vitamin-c-guide.jpg',
                'category_slug' => 'health-glow',
                'tag_slugs' => ['vitamin-c', 'anti-aging', 'sunscreen'],
                'is_featured' => true,
                'reading_time' => 3,
                'views' => 1120,
                'status' => 'published',
                'published_at' => now()->subDays(5),
                'seo' => [
                    'meta_title_ar' => 'فوائد سيروم فيتامين سي للبشرة والطريقة الصحيحة لاستخدامه | دليل النضارة',
                    'meta_title_en' => 'Vitamin C Serum Benefits & Usage Guide for Glowing Skin',
                    'meta_description_ar' => 'اكتشفي فوائد سيروم فيتامين سي في تفتيح وتوحيد لون البشرة وحمايتها من التصبغات وآثار الشمس.',
                    'meta_description_en' => 'Discover how Vitamin C serum brightens skin, reduces hyperpigmentation, and enhances collagen production.',
                    'meta_keywords_ar' => 'فيتامين سي, سيروم تفتيح, نضارة البشرة, تصبغات الوجه, كولاجين, واقي الشمس',
                    'meta_keywords_en' => 'vitamin c, brightening serum, skin glow, dark spots, collagen, sunscreen',
                    'canonical_url' => url('/blogs/benefits-of-vitamin-c-serum-and-how-to-use-it'),
                    'og_title_ar' => 'فوائد سيروم فيتامين سي وكيفية استخدامه بالشكل الصحيح',
                    'og_title_en' => 'Benefits of Vitamin C Serum & How to Use It',
                    'og_description_ar' => 'احصلي على بشرة مشرقة وموحدة اللون مع سيروم فيتامين سي والنصائح الطبية.',
                    'og_description_en' => 'Achieve radiant, even-toned skin with expert Vitamin C skincare tips.',
                ]
            ],
            [
                'name_ar' => 'الروتين المسائي قبل النوم',
                'name_en' => 'Night Skincare Routine Guide',
                'name' => 'الروتين المسائي قبل النوم',
                'title_ar' => 'دليلك الشامل لروتين العناية المسائي قبل النوم لتجديد خلايا البشرة',
                'title_en' => 'Your Ultimate Night Skincare Routine Guide for Skin Regeneration',
                'slug' => 'your-ultimate-night-skincare-routine-guide',
                'excerpt_ar' => 'الليل هو الوقت الذهبي لتجدد خلايا البشرة وإصلاح التلف. اكتشفي الترتيب الصحيح لمنتجات روتينك المسائي.',
                'excerpt_en' => 'Night is the prime time for cell renewal and repair. Discover the correct order to apply your evening skincare steps.',
                'content_ar' => '<p>أثناء النوم، يعمل الجسم والجلد على إصلاح الخلايا التالفة، مما يجعل الليل الوقت المثالي لاستخدام المكونات المغذية والمركزة.</p><h3>ترتيب خطوات الروتين المسائي:</h3><ol><li><strong>التنظيف المزدوج (Double Cleansing):</strong> استخدام غسول زيتي لإزالة المكياج وواقي الشمس، متبوعاً بغسول مائي لتنظيف المسام.</li><li><strong>التونر والمرطب التمهيدي:</strong> لإعادة توازن حموضة البشرة وتجهيزها لإمتصاص السيروم.</li><li><strong>السيروم العلاجي:</strong> مثل الريتينول أو الهيالورونيك أو النياسيناميد بحسب احتياج بشرتك.</li><li><strong>كريم العين:</strong> لترطيب منطقة المحيط بالعين ومنع الهالات والتجاعيد الدقيقة.</li><li><strong>الكريم الليلي الغني:</strong> لحبس الرطوبة والمكونات الفعالة داخل الجلد طوال الليل.</li></ol>',
                'content_en' => '<p>While you sleep, your skin enters high-repair mode, absorbing active ingredients faster and more effectively.</p><h3>Step-by-Step Night Routine Order:</h3><ol><li><strong>Double Cleansing:</strong> Start with an oil cleanser to break down SPF and makeup, followed by a gentle water cleanser.</li><li><strong>Toner & Prep Hydration:</strong> Restores skin pH balance and prepares layers for serums.</li><li><strong>Targeted Serums:</strong> Apply Retinol, Hyaluronic Acid, or Niacinamide depending on your skin goals.</li><li><strong>Eye Cream:</strong> Hydrates delicate under-eye areas to diminish fine lines and darkness.</li><li><strong>Nourishing Night Moisturizer:</strong> Seals in all nutrients and locks moisture overnight.</li></ol>',
                'image_url' => 'https://images.unsplash.com/photo-1570172619644-dfd03ed5d881?auto=format&fit=crop&q=80&w=1000',
                'filename' => 'night-routine.jpg',
                'category_slug' => 'beauty-routine',
                'tag_slugs' => ['night-routine', 'moisturizing', 'anti-aging', 'double-cleansing'],
                'is_featured' => false,
                'reading_time' => 5,
                'views' => 980,
                'status' => 'published',
                'published_at' => now()->subDays(8),
                'seo' => [
                    'meta_title_ar' => 'الروتين المسائي المثالي قبل النوم لترطيب وتجديد البشرة | خطوات مرتبة',
                    'meta_title_en' => 'Ultimate Night Skincare Routine Guide | Nightly Regeneration Steps',
                    'meta_description_ar' => 'أفضل ترتيب لاستخدام كريم الليل والسيروم والتنظيف المزدوج قبل النوم للحصول على بشرة ناعمة ونضرة صباحاً.',
                    'meta_description_en' => 'Best sequence for applying night creams, serums, and double cleansing before sleep for radiant skin.',
                    'meta_keywords_ar' => 'روتين مسائي, كريم الليل, تنظيف مزدوج, تجديد الخلايا, سيروم مسائي, ترطيب العين',
                    'meta_keywords_en' => 'night routine, night cream, double cleansing, skin renewal, night serum, eye cream',
                    'canonical_url' => url('/blogs/your-ultimate-night-skincare-routine-guide'),
                    'og_title_ar' => 'دليلك الشامل لروتين العناية المسائي قبل النوم',
                    'og_title_en' => 'Your Ultimate Night Skincare Routine Guide',
                    'og_description_ar' => 'جددي بشرتك أثناء النوم مع هذا الروتين المسائي التخصصي والخطوات المرتبة.',
                    'og_description_en' => 'Renew your skin overnight with expert evening skincare steps.',
                ]
            ],
            [
                'name_ar' => 'أخطاء واقي الشمس',
                'name_en' => 'Sunscreen Mistakes',
                'name' => 'أخطاء واقي الشمس',
                'title_ar' => 'أخطاء شائعة عند استخدام واقي الشمس تسبب ظهور التصبغات والتجاعيد',
                'title_en' => 'Common Sunscreen Mistakes That Cause Pigmentation and Early Aging',
                'slug' => 'common-sunscreen-mistakes-that-cause-pigmentation',
                'excerpt_ar' => 'هل تضعين واقي الشمس ولكن لا تحصلين على الحماية الكافية؟ اكتشفي الأخطاء الشائعة وكيفية تجنبها للحصول على أقصى حماية.',
                'excerpt_en' => 'Are you applying sunscreen but still suffering from dark spots or tan? Discover common mistakes and solutions.',
                'content_ar' => '<p>الوقاية من الشمس هي الخطوة الأولى والأهم في أي روتين عناية بالبشرة، إلا أن الكثيرين يرتكبون أخطاءً تفقد واقي الشمس فاعليته.</p><h3>أبرز الأخطاء الشائعة:</h3><ul><li><strong>وضع كمية غير كافية:</strong> الكمية الموصى بها طبياً هي مقدار إصبعين كاملين للوجه والرقبة.</li><li><strong>عدم إعادات التطبيق:</strong> يفقد واقي الشمس مفعوله بعد ساعتين من التعرض للشمس ويجب إعادة تطبيقه.</li><li><strong>تجاهل استخدامه داخل المنزل أو في الشتاء:</strong> الأشعة البنفسجية UVA تخترق زجاج النوافذ والغيوم.</li><li><strong>عدم انتماء واقي الشمس لنوع بشرتك:</strong> اختيار تركيبة غير مناسبة يسبب انسداد المسام أو الجفاف.</li></ul>',
                'content_en' => '<p>Sun protection is the single most effective anti-aging product, yet improper application undermines its benefits.</p><h3>Most Common Sunscreen Errors:</h3><ul><li><strong>Using Insufficient Amount:</strong> Apply the two-finger rule to cover face and neck adequately.</li><li><strong>Not Reapplying Every 2 Hours:</strong> Sunscreen filters break down after continuous UV exposure.</li><li><strong>Ignoring Indoors & Cloud Cover:</strong> UVA rays penetrate window glass and cloud layers easily.</li><li><strong>Choosing Wrong Formulation:</strong> Match sunscreen texture (fluid, gel, cream) with your skin type.</li></ul>',
                'image_url' => 'https://images.unsplash.com/photo-1598440947619-2c35fc9aa908?auto=format&fit=crop&q=80&w=1000',
                'filename' => 'sunscreen-mistakes.jpg',
                'category_slug' => 'tips-guides',
                'tag_slugs' => ['sunscreen', 'anti-aging', 'dry-skin'],
                'is_featured' => false,
                'reading_time' => 3,
                'views' => 750,
                'status' => 'published',
                'published_at' => now()->subDays(12),
                'seo' => [
                    'meta_title_ar' => 'أخطاء تجنبيها عند استخدام واقي الشمس لحماية بشرتك من التصبغات',
                    'meta_title_en' => 'Sunscreen Mistakes to Avoid for Full Protection & Even Tone',
                    'meta_description_ar' => 'تعرفي على الكمية المناسبة وكيفية إعادة تطبيق واقي الشمس لحماية كاملة من الشمس والتصبغات.',
                    'meta_description_en' => 'Learn the proper sunscreen application techniques, reapplication rules, and dosage for max UV protection.',
                    'meta_keywords_ar' => 'واقي الشمس, تصبغات الشمس, حماية البشرة, تجاعيد الشمس, اشعة فوق بنفسجية',
                    'meta_keywords_en' => 'sunscreen mistakes, pigmentation, skin protection, uv rays, spf guide',
                    'canonical_url' => url('/blogs/common-sunscreen-mistakes-that-cause-pigmentation'),
                    'og_title_ar' => 'أخطاء شائعة عند استخدام واقي الشمس تسبب التصبغات',
                    'og_title_en' => 'Common Sunscreen Mistakes That Cause Pigmentation',
                    'og_description_ar' => 'تجنبي التصبغات والبقع بحماية شمسية صحيحة وقواعد واقي الشمس.',
                    'og_description_en' => 'Avoid pigmentation and sun spots with correct sunscreen usage.',
                ]
            ],
            [
                'name_ar' => 'العناية بالبشرة الدهنية وحب الشباب',
                'name_en' => 'Oily Skin & Acne Control',
                'name' => 'العناية بالبشرة الدهنية وحب الشباب',
                'title_ar' => 'طرق العناية بالبشرة الدهنية والسيطرة على حب الشباب واللمعان',
                'title_en' => 'How to Manage Oily Skin and Prevent Acne Breakouts Effectively',
                'slug' => 'how-to-manage-oily-skin-and-prevent-acne',
                'excerpt_ar' => 'البشرة الدهنية تتطلب توازناً دقيقاً بين تقليل الافرازات الزهمية والترطيب الخفيف لمنع انسداد المسام.',
                'excerpt_en' => 'Oily skin requires a smart balance between oil control and light hydration to avoid clogged pores and acne.',
                'content_ar' => '<p>إفراز الزهم المفرط يسبب لمعان البشرة وانسداد المسام وظهور الرؤوس السوداء وحب الشباب. إليك الحلول الطبية الفعالة.</p><h3>أهم المكونات الفعالة للبشرة الدهنية:</h3><ul><li><strong>حمض الساليسيليك (BHA):</strong> يخترق الدهون داخل المسام وينظفها بفيزياء عميقة.</li><li><strong>النياسيناميد (Niacinamide):</strong> ينظم إفراز الدهون ويقلل حجم المسام الواسعة ويهدئ الاحمرار.</li><li><strong>المرطبات المائية خفيفة الوزن (Gel-based):</strong> تمنح البشرة الترطيب بدون إثقال المسام.</li></ul>',
                'content_en' => '<p>Excess sebum causes shininess, enlarged pores, blackheads, and breakouts. Here is the dermatologist-recommended guide.</p><h3>Best Ingredients for Oily Skin:</h3><ul><li><strong>Salicylic Acid (BHA):</strong> Soluble in oil, penetrates deep into pores to dissolve debris.</li><li><strong>Niacinamide (Vitamin B3):</strong> Regulates sebum production, minimizes pores, and reduces redness.</li><li><strong>Oil-Free Gel Moisturizers:</strong> Provides hydration without clogging pores or adding grease.</li></ul>',
                'image_url' => 'https://images.unsplash.com/photo-1556228720-195a672e8a03?auto=format&fit=crop&q=80&w=1000',
                'filename' => 'oily-skin-acne.jpg',
                'category_slug' => 'skincare',
                'tag_slugs' => ['oily-skin', 'acne-control', 'moisturizing'],
                'is_featured' => false,
                'reading_time' => 4,
                'views' => 860,
                'status' => 'published',
                'published_at' => now()->subDays(15),
                'seo' => [
                    'meta_title_ar' => 'دليل العناية بالبشرة الدهنية والتخلص من حب الشباب واللمعان الزائد',
                    'meta_title_en' => 'Oily Skin Care Guide | Prevent Acne & Control Sebum Shine',
                    'meta_description_ar' => 'استراتيجية شاملة للتعامل مع البشرة الدهنية وحب الشباب باستخدام حامض الساليسيليك والنياسيناميد.',
                    'meta_description_en' => 'Comprehensive strategy to care for oily skin and cure acne using BHA and Niacinamide.',
                    'meta_keywords_ar' => 'بشرة دهنية, حب الشباب, ساليسيليك اسيد, نياسيناميد, مسام واسعة, افراز الدهون',
                    'meta_keywords_en' => 'oily skin, acne control, salicylic acid, niacinamide, enlarged pores, oil control',
                    'canonical_url' => url('/blogs/how-to-manage-oily-skin-and-prevent-acne'),
                    'og_title_ar' => 'طرق العناية بالبشرة الدهنية والسيطرة على حب الشباب',
                    'og_title_en' => 'How to Manage Oily Skin and Prevent Acne',
                    'og_description_ar' => 'تخلصي من اللمعان وحب الشباب مع المكونات الطبية المناسبة للبشرة الدهنية.',
                    'og_description_en' => 'Control excess oil and prevent breakouts with top dermatological solutions.',
                ]
            ],
            [
                'name_ar' => 'دليل الكولاجين ومكافحة التجاعيد',
                'name_en' => 'Collagen & Anti-Aging Guide',
                'name' => 'دليل الكولاجين ومكافحة التجاعيد',
                'title_ar' => 'سر النضارة الشابة: دليل تحفيز الكولاجين ومكافحة علامات التقدم بالسن',
                'title_en' => 'The Ultimate Guide to Collagen Boosting & Anti-Aging Skincare',
                'slug' => 'the-ultimate-guide-to-collagen-and-anti-aging',
                'excerpt_ar' => 'الكولاجين هو البروتين المسؤول عن شباب البشرة ومرونتها. اكتشفي الطرق الطبيعية والمستحضرات التي تحفز إنتاجه.',
                'excerpt_en' => 'Collagen maintains skin firmness and elasticity. Discover effective topicals and habits to boost collagen synthesis.',
                'content_ar' => '<p>مع تقدم العمر ينخفض إنتاج الكولاجين الطبيعي في البشرة بنسبة 1% سنوياً بعد سن 25، مما يؤدي لظهور الخطوط الدقيقة والمرونة المنخفضة.</p><h3>أفضل الطرق لتحفيز الكولاجين:</h3><ul><li><strong>مشتقات فيتامين أ (الريتينول):</strong> المكون الذهبي المثبت علمياً لتسريع تجدد الخلايا وتحفيز بناء الكولاجين.</li><li><strong>الببتيدات (Peptides):</strong> سلاسل أحماض أمينية ترسل إشارات للجلد لإنتاج المزيد من الكولاجين.</li><li><strong>مضادات الأكسدة:</strong> تحمي الكولاجين الموجود من التكسر بفعل أشعة الشمس والتلوث.</li></ul>',
                'content_en' => '<p>After age 25, collagen production drops by 1% per year, leading to fine lines and loss of firmness.</p><h3>Proven Ways to Boost Collagen:</h3><ul><li><strong>Retinoids & Retinol:</strong> The gold standard scientifically proven to stimulate collagen production.</li><li><strong>Peptides:</strong> Amino acid chains that signal skin cells to build new collagen fibers.</li><li><strong>Antioxidants:</strong> Protect existing collagen from degradation caused by UV and pollution.</li></ul>',
                'image_url' => 'https://images.unsplash.com/photo-1522337360788-8b13dee7a37e?auto=format&fit=crop&q=80&w=1000',
                'filename' => 'collagen-anti-aging.jpg',
                'category_slug' => 'anti-aging-cat',
                'tag_slugs' => ['anti-aging', 'vitamin-c', 'night-routine'],
                'is_featured' => true,
                'reading_time' => 5,
                'views' => 1340,
                'status' => 'published',
                'published_at' => now()->subDays(18),
                'seo' => [
                    'meta_title_ar' => 'دليل تحفيز الكولاجين ومكافحة تجاعيد البشرة والخطوط الدقيقة',
                    'meta_title_en' => 'Collagen Boosting & Anti-Aging Skincare Guide | Younger Skin',
                    'meta_description_ar' => 'تعرفي على أهم المكونات كالريتينول والببتيدات التي تحفز الكولاجين وتحافظ على مرونة وشباب البشرة.',
                    'meta_description_en' => 'Learn how Retinol, Peptides, and Antioxidants boost natural skin collagen and firm fine lines.',
                    'meta_keywords_ar' => 'كولاجين, ريتينول, ببتيدات, مكافحة التجاعيد, مرونة البشرة, علامات التقدم بالسن',
                    'meta_keywords_en' => 'collagen, retinol, peptides, anti aging, skin firmness, fine lines',
                    'canonical_url' => url('/blogs/the-ultimate-guide-to-collagen-and-anti-aging'),
                    'og_title_ar' => 'سر النضارة الشابة: دليل تحفيز الكولاجين ومكافحة التجاعيد',
                    'og_title_en' => 'The Ultimate Guide to Collagen Boosting & Anti-Aging',
                    'og_description_ar' => 'حافظي على شباب بشرتك ومرونتها مع نصائح تحفيز الكولاجين المثبتة علمياً.',
                    'og_description_en' => 'Maintain youthful and firm skin with science-backed collagen tips.',
                ]
            ],
            [
                'name_ar' => 'أهمية التنظيف المزدوج',
                'name_en' => 'Importance of Double Cleansing',
                'name' => 'أهمية التنظيف المزدوج',
                'title_ar' => 'لماذا يعتبر التنظيف المزدوج سر البشرة الصافية الخالية من الشوائب؟',
                'title_en' => 'Why Double Cleansing is Essential for Clear, Glowing Skin',
                'slug' => 'why-double-cleansing-is-essential-for-glowing-skin',
                'excerpt_ar' => 'الغسول العادي لا يكفي لإزالة المكياج الثقيل وواقي الشمس المقاوم للماء. اكتشفي فوائد التنظيف المزدوج اليومي.',
                'excerpt_en' => 'Standard cleansers often leave behind water-resistant sunscreen and makeup. Learn why double cleansing changes your skin.',
                'content_ar' => '<p>التنظيف المزدوج هو تقنية كورية تعتمد على مرحلتين لتنظيف الوجه بعمق دون التسبب في جفاف البشرة.</p><h3>المرحلة الأولى: منظف زيتي أو بلسم</h3><p>الزيوت تذيب الزيوت والمكياج وواقي الشمس المقاوم للماء والرواسب الدهنية المتراكمة داخل المسام.</p><h3>المرحلة الثانية: غسول مائي</h3><p>ينظف الجلد من بقايا العرق والأتربة ويرطب البشرة ويجهزها لاستقبال باقي المنتجات.</p>',
                'content_en' => '<p>Double cleansing is a two-step cleansing routine that removes both oil-based and water-based impurities thoroughly.</p><h3>Step 1: Oil Cleanser or Balm</h3><p>Oil dissolves oil, effortlessly removing waterproof SPF, long-wear makeup, and excess sebum.</p><h3>Step 2: Water-Based Cleanser</h3><p>Washes away sweat, urban dust, and readies skin to absorb serums and moisturizers effectively.</p>',
                'image_url' => 'https://images.unsplash.com/photo-1608248597279-f99d160bfcbc?auto=format&fit=crop&q=80&w=1000',
                'filename' => 'double-cleansing.jpg',
                'category_slug' => 'beauty-routine',
                'tag_slugs' => ['double-cleansing', 'night-routine', 'oily-skin', 'dry-skin'],
                'is_featured' => false,
                'reading_time' => 3,
                'views' => 690,
                'status' => 'published',
                'published_at' => now()->subDays(22),
                'seo' => [
                    'meta_title_ar' => 'فوائد التنظيف المزدوج للبشرة والطريقة الصحيحة لتطبيقه يومياً',
                    'meta_title_en' => 'Benefits of Double Cleansing | Step-by-Step Guide for Clear Skin',
                    'meta_description_ar' => 'اكتشفي كيف يزيل التنظيف المزدوج المكياج وواقي الشمس والشوائب لمنع انسداد المسام وحب الشباب.',
                    'meta_description_en' => 'Discover how double cleansing removes stubborn sunscreen, makeup, and impurities without stripping skin.',
                    'meta_keywords_ar' => 'تنظيف مزدوج, غسول زيتي, غسول مائي, ازالة المكياج, مسام نظيفة, روتين كوري',
                    'meta_keywords_en' => 'double cleansing, oil cleanser, cleansing balm, makeup removal, clean pores, korean skincare',
                    'canonical_url' => url('/blogs/why-double-cleansing-is-essential-for-glowing-skin'),
                    'og_title_ar' => 'لماذا يعتبر التنظيف المزدوج سر البشرة الصافية؟',
                    'og_title_en' => 'Why Double Cleansing is Essential for Glowing Skin',
                    'og_description_ar' => 'نظفي مسام بشرتك بعمق دون جفاف مع تقنية التنظيف المزدوج.',
                    'og_description_en' => 'Cleanse your pores deeply without dryness using double cleansing.',
                ]
            ],
            [
                'name_ar' => 'كل ما تودين معرفته عن الهيالورونيك أسيد',
                'name_en' => 'Hyaluronic Acid Complete Guide',
                'name' => 'كل ما تودين معرفته عن الهيالورونيك أسيد',
                'title_ar' => 'كل ما تودين معرفته عن سيروم الهيالورونيك أسيد وحبس الرطوبة',
                'title_en' => 'Everything You Need to Know About Hyaluronic Acid & Moisture Lock',
                'slug' => 'everything-you-need-to-know-about-hyaluronic-acid',
                'excerpt_ar' => 'جزيء واحد من الهيالورونيك يستطيع جذب 1000 ضعف وزنه من الماء! تعرفي على كيفية استخدامه بالشكل الصحيح.',
                'excerpt_en' => 'A single molecule of hyaluronic acid can hold up to 1,000 times its weight in water. Learn how to maximize its benefits.',
                'content_ar' => '<p>الهيالورونيك أسيد ليس حمضاً مقشراً، بل هو مرطب ومحتجز رطوبة طبيعي يعطي البشرة مظهر الممتلئ المشدود (Plump Skin).</p><h3>السر لاستخدام الهيالورونيك بنجاح:</h3><p>يجب وضعه دائماً على بشرة نديّة أو رطبة قليلاً، وتغطيته فوراً بمرطب كريمي لثبيت الماء داخل الجلد وعدم سحبه من الطبقات العميقة.</p>',
                'content_en' => '<p>Hyaluronic acid is not an exfoliating acid; it is a humectant that attracts and holds water in the skin.</p><h3>The Golden Rule for Hyaluronic Acid:</h3><p>Always apply it on damp skin, then immediately lock it in with a moisturizer to trap hydration inside.</p>',
                'image_url' => 'https://images.unsplash.com/photo-1601049676099-e7ed07d825b0?auto=format&fit=crop&q=80&w=1000',
                'filename' => 'hyaluronic-acid-guide.jpg',
                'category_slug' => 'skincare',
                'tag_slugs' => ['hyaluronic-serum', 'moisturizing', 'dry-skin'],
                'is_featured' => true,
                'reading_time' => 3,
                'views' => 1210,
                'status' => 'published',
                'published_at' => now()->subDays(25),
                'seo' => [
                    'meta_title_ar' => 'دليل سيروم الهيالورونيك أسيد وترطيب البشرة العميق | امتلاء الجلد',
                    'meta_title_en' => 'Hyaluronic Acid Serum Guide | Ultimate Deep Skin Hydration',
                    'meta_description_ar' => 'تعرفي على فوائد الهيالورونيك أسيد والطريقة الصحيحة لاستخدامه لمنع جفاف البشرة وإعطائها امتلاء ونضارة.',
                    'meta_description_en' => 'Discover the science of hyaluronic acid humectant and how to apply it on damp skin for max hydration.',
                    'meta_keywords_ar' => 'هيالورونيك اسيد, ترطيب عميق, امتلاء البشرة, مرطب وجه, جفاف الجلد, سيروم هيدرا',
                    'meta_keywords_en' => 'hyaluronic acid, deep hydration, plump skin, face moisturizer, humectant, hydration serum',
                    'canonical_url' => url('/blogs/everything-you-need-to-know-about-hyaluronic-acid'),
                    'og_title_ar' => 'كل ما تودين معرفته عن سيروم الهيالورونيك أسيد',
                    'og_title_en' => 'Everything You Need to Know About Hyaluronic Acid',
                    'og_description_ar' => 'احصلي على رطوبة مضاعفة وبشرة مشدودة وممتلئة مع سيروم الهيالورونيك.',
                    'og_description_en' => 'Achieve plump, hydrated skin with proper hyaluronic acid application.',
                ]
            ],
        ];

        foreach ($blogsData as $bData) {
            $catSlug = $bData['category_slug'];
            $tagSlugs = $bData['tag_slugs'];
            $seoData = $bData['seo'];
            $imageUrl = $bData['image_url'];
            $filename = $bData['filename'];

            unset($bData['category_slug'], $bData['tag_slugs'], $bData['seo'], $bData['image_url'], $bData['filename']);

            // Download & save featured image locally inside storage/app/public/blogs
            $savedFilename = ImageDownloader::downloadAndSave(
                $imageUrl,
                'blogs',
                $filename
            );

            $bData['featured_image'] = 'blogs/' . $savedFilename;
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

            // Attach SEO Record with downloaded OG Image path
            $seoData['og_image'] = 'blogs/' . $savedFilename;
            $blog->seo()->updateOrCreate(
                ['blog_id' => $blog->id],
                $seoData
            );
        }

        $this->command->info('✅ Blog categories, tags, author, blogs (with downloaded images & full SEO) seeded successfully!');
    }
}
