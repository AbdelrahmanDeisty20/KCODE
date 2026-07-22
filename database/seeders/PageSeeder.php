<?php

namespace Database\Seeders;

use App\Models\Page;
use Illuminate\Database\Seeder;

class PageSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Download & save about us page image
        $imageName = 'about_us.jpg';
        ImageDownloader::downloadAndSave(
            'https://images.unsplash.com/photo-1556228720-195a672e8a03?auto=format&fit=crop&q=80&w=800',
            'pages',
            $imageName
        );

        $aboutUsData = [
            // Hero Section
            [
                'key_ar' => 'hero_badge',
                'key_en' => 'hero_badge',
                'value_ar' => 'من نحن',
                'value_en' => 'About Us',
                'type' => 'about_us',
            ],
            [
                'key_ar' => 'hero_title',
                'key_en' => 'hero_title',
                'value_ar' => 'قصة KCODE وراء الجمال الكوري',
                'value_en' => 'The Story of KCODE Behind Korean Beauty',
                'type' => 'about_us',
            ],
            [
                'key_ar' => 'hero_subtitle',
                'key_en' => 'hero_subtitle',
                'value_ar' => 'نحن هنا لمساعدتك على فك شفرة جمال بشرتك الخاصة تقديم رعاية كورية أصيلة مخصصة لمتطلباتك.',
                'value_en' => 'We are here to help you decode your unique skin beauty code and provide authentic Korean care tailored to your needs.',
                'type' => 'about_us',
            ],

            // Our Story Section
            [
                'key_ar' => 'story_title',
                'key_en' => 'story_title',
                'value_ar' => 'بدايتنا وحكايتنا',
                'value_en' => 'Our Beginning & Story',
                'type' => 'about_us',
            ],
            [
                'key_ar' => 'story_paragraph_1',
                'key_en' => 'story_paragraph_1',
                'value_ar' => 'تأسست KCODE برؤية واضحة: وهي جعل أسرار العناية بالبشرة الكورية الفعالة والراقية في متناول الجميع في العالم العربي. نحن نؤمن بأن لكل بشرة شفرة فريدة ومميزة (Code) تحتاج إلى روتين مخصص لها لتشرق بأفضل حالاتها.',
                'value_en' => 'KCODE was founded with a clear vision: to make the secrets of effective, premium Korean skincare accessible to everyone in the Arab world. We believe every skin has a unique code that requires a tailored routine to glow at its best.',
                'type' => 'about_us',
            ],
            [
                'key_ar' => 'story_paragraph_2',
                'key_en' => 'story_paragraph_2',
                'value_ar' => 'نقوم بالتعاون مع أفضل المعامل الطبية ومصانع مستحضرات التجميل الكورية في سيول، لنضمن لك منتجات كورية أصلية 100% تم اختبارها بدقة فائقة لتلائم ظروف الطقس ونوع بشرتك لتمنحك ذلك المظهر الزجاجي النضر والجميل.',
                'value_en' => 'We collaborate with top medical labs and cosmetic manufacturers in Seoul to guarantee 100% authentic Korean products, rigorously tested to suit weather conditions and skin types, giving you that fresh, beautiful glass skin look.',
                'type' => 'about_us',
            ],
            [
                'key_ar' => 'team_signature',
                'key_en' => 'team_signature',
                'value_ar' => '— KCODE TEAM',
                'value_en' => '— KCODE TEAM',
                'type' => 'about_us',
            ],
            [
                'key_ar' => 'image_badge',
                'key_en' => 'image_badge',
                'value_ar' => 'منتجات كورية أصلية 100%',
                'value_en' => '100% Authentic Korean Products',
                'type' => 'about_us',
            ],
            [
                'key_ar' => 'image',
                'key_en' => 'image',
                'value_ar' => $imageName,
                'value_en' => $imageName,
                'type' => 'about_us',
            ],

            // Core Values Section
            [
                'key_ar' => 'values_title',
                'key_en' => 'values_title',
                'value_ar' => 'قيمنا الأساسية',
                'value_en' => 'Our Core Values',
                'type' => 'about_us',
            ],
            [
                'key_ar' => 'values_subtitle',
                'key_en' => 'values_subtitle',
                'value_ar' => 'المبادئ التي تقود كل خطوة نخطوها لتقديم أفضل تجربة عناية لبشرتك.',
                'value_en' => 'The principles guiding every step we take to provide the best skincare experience for your skin.',
                'type' => 'about_us',
            ],
            [
                'key_ar' => 'value_1_title',
                'key_en' => 'value_1_title',
                'value_ar' => 'الجمال الطبيعي',
                'value_en' => 'Natural Beauty',
                'type' => 'about_us',
            ],
            [
                'key_ar' => 'value_1_description',
                'key_en' => 'value_1_description',
                'value_ar' => 'نؤمن بتعزيز صحة بشرتك الطبيعية بدلاً من تغطيتها بمستحضرات التجميل الثقيلة.',
                'value_en' => 'We believe in enhancing your skin\'s natural health rather than covering it with heavy makeup.',
                'type' => 'about_us',
            ],
            [
                'key_ar' => 'value_2_title',
                'key_en' => 'value_2_title',
                'value_ar' => 'الأصالة الكورية',
                'value_en' => 'Korean Authenticity',
                'type' => 'about_us',
            ],
            [
                'key_ar' => 'value_2_description',
                'key_en' => 'value_2_description',
                'value_ar' => 'جميع منتجاتنا مستوردة مباشرة من أرقى المعامل المعتمدة في كوريا الجنوبية.',
                'value_en' => 'All our products are imported directly from top certified laboratories in South Korea.',
                'type' => 'about_us',
            ],
            [
                'key_ar' => 'value_3_title',
                'key_en' => 'value_3_title',
                'value_ar' => 'العناية المخصصة',
                'value_en' => 'Customized Care',
                'type' => 'about_us',
            ],
            [
                'key_ar' => 'value_3_description',
                'key_en' => 'value_3_description',
                'value_ar' => 'تصميم روتين عناية مخصص لكل عميلة بناءً على تحليل علمي دقيق لنوع البشرة.',
                'value_en' => 'Designing a customized care routine for every client based on precise scientific skin type analysis.',
                'type' => 'about_us',
            ],
            [
                'key_ar' => 'value_4_title',
                'key_en' => 'value_4_title',
                'value_ar' => 'الشفافية الكاملة',
                'value_en' => 'Full Transparency',
                'type' => 'about_us',
            ],
            [
                'key_ar' => 'value_4_description',
                'key_en' => 'value_4_description',
                'value_ar' => 'مكونات آمنة ونظيفة ومفصلة بالكامل بدون أي وعود وهمية أو مواد ضارة.',
                'value_en' => 'Safe, clean, and fully detailed ingredients without false promises or harmful substances.',
                'type' => 'about_us',
            ],

            // Mission & Vision Section
            [
                'key_ar' => 'mission_title',
                'key_en' => 'mission_title',
                'value_ar' => 'رسالتنا',
                'value_en' => 'Our Mission',
                'type' => 'about_us',
            ],
            [
                'key_ar' => 'mission_description',
                'key_en' => 'mission_description',
                'value_ar' => 'تمكين كل فرد من فهم احتياجات بشرته الفريدة وتوفير الحلول والمنتجات الكورية الأكثر فاعلية وأماناً لبناء روتين صحي مستدام.',
                'value_en' => 'Empowering every individual to understand their unique skin needs, providing the most effective and safe Korean solutions for a sustainable healthy routine.',
                'type' => 'about_us',
            ],
            [
                'key_ar' => 'vision_title',
                'key_en' => 'vision_title',
                'value_ar' => 'رؤيتنا',
                'value_en' => 'Our Vision',
                'type' => 'about_us',
            ],
            [
                'key_ar' => 'vision_description',
                'key_en' => 'vision_description',
                'value_ar' => 'أن نصبح المنصة الرائدة والأولى في الشرق الأوسط لتقديم الحلول والمنتجات الكورية الذكية والمخصصة بالكامل للعناية بالبشرة.',
                'value_en' => 'To become the leading premier platform in the Middle East providing smart, fully customized Korean skincare solutions.',
                'type' => 'about_us',
            ],
        ];

        foreach ($aboutUsData as $item) {
            Page::updateOrCreate(
                [
                    'type' => $item['type'],
                    'key_en' => $item['key_en'],
                ],
                [
                    'key_ar' => $item['key_ar'],
                    'value_ar' => $item['value_ar'],
                    'value_en' => $item['value_en'],
                ]
            );
        }
    }
}
