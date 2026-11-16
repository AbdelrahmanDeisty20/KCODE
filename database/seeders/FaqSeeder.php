<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class FaqSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('faqs')->truncate();

        $faqs = [
            [
                'question_ar' => 'هل منتجات KCODE أصلية؟',
                'question_en' => 'Are KCODE products authentic?',
                'answer_ar'   => 'نعم. منتجاتنا من العلامات التجارية مباشرةً أو مورّديها الرسميين في كوريا.',
                'answer_en'   => 'Yes. Our products are sourced directly from the brands or their official authorized distributors in South Korea.',
                'sort_order'  => 1,
                'is_active'   => true,
            ],
            [
                'question_ar' => 'ما الذي يميّز اختيار المنتجات في KCODE؟',
                'question_en' => 'What makes KCODE product selection unique?',
                'answer_ar'   => 'يراجع فريق KCODE المكوّنات وفوائدها المتوقعة، وملاءمة المنتج لأنواع البشرة واحتياجاتها، وطريقة استخدامه وتوافقه مع بقية الروتين.',
                'answer_en'   => 'The KCODE team thoroughly reviews ingredients, expected benefits, skin type compatibility, and how each formula layers within a complete routine.',
                'sort_order'  => 2,
                'is_active'   => true,
            ],
            [
                'question_ar' => 'كيف يساعدني تحليل البشرة؟',
                'question_en' => 'How does the skin analysis help me?',
                'answer_ar'   => 'يعتمد التحليل على ما تريد تحسينه، ونوع بشرتك، واحتياجاتها الإضافية، ومدى حساسيتها. وبناءً على إجاباتك، يقترح روتينًا من المنتجات التي راجعها فريق KCODE، مع ترتيبها وطريقة استخدامها.',
                'answer_en'   => 'The analysis assesses your goals, skin type, specific concerns, and sensitivity level to curate a personalized routine from KCODE-vetted products, complete with step order and usage instructions.',
                'sort_order'  => 3,
                'is_active'   => true,
            ],
            [
                'question_ar' => 'ماذا لو كانت بشرتي حساسة؟',
                'question_en' => 'What if I have sensitive skin?',
                'answer_ar'   => 'نأخذ حساسية بشرتك في الحسبان عند اقتراح الروتين، ونركّز على ملاءمة المنتجات لنوع بشرتك واحتياجاتها.',
                'answer_en'   => 'We specifically account for skin sensitivity when recommending routines, prioritizing gentle and compatible formulations suited to reactive skin.',
                'sort_order'  => 4,
                'is_active'   => true,
            ],
            [
                'question_ar' => 'هل يجب أن أشتري الروتين كاملًا؟',
                'question_en' => 'Do I have to purchase the entire routine?',
                'answer_ar'   => 'لا. يمكنك اختيار ما تحتاجه من المنتجات الأساسية والمكمّلة، ومراجعة دور كل خطوة قبل إضافتها إلى السلة.',
                'answer_en'   => 'No. You can freely choose only the core or complementary products you need, reviewing each step\'s role before adding to your cart.',
                'sort_order'  => 5,
                'is_active'   => true,
            ],
            [
                'question_ar' => 'متى يصل طلبي؟',
                'question_en' => 'When will my order arrive?',
                'answer_ar'   => 'نوصّل إلى جميع محافظات سلطنة عُمان. يستغرق التوصيل عادةً من 1 إلى 3 أيام عمل حسب موقعك.',
                'answer_en'   => 'We deliver across all governorates of the Sultanate of Oman. Delivery typically takes 1 to 3 business days depending on your location.',
                'sort_order'  => 6,
                'is_active'   => true,
            ],
        ];

        foreach ($faqs as $faq) {
            DB::table('faqs')->insert(array_merge($faq, [
                'created_at' => now(),
                'updated_at' => now(),
            ]));
        }
    }
}
