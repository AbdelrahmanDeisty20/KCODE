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
                'question_ar' => 'كيف يمكنني معرفة نوع بشرتي؟',
                'question_en' => 'How can I know my skin type?',
                'answer_ar'   => 'يمكنك معرفة نوع بشرتك عبر إكمال التقييم (الكويز) المخصص في تطبيقنا.',
                'answer_en'   => 'You can determine your skin type by completing the assessment (quiz) in our application.',
                'sort_order'  => 1,
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
