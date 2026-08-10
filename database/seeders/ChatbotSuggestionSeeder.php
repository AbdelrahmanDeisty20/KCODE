<?php

namespace Database\Seeders;

use App\Models\ChatbotSuggestion;
use Illuminate\Database\Seeder;

class ChatbotSuggestionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $suggestions = [
            [
                'question_ar' => 'ما هو الروتين المناسب للبشرة الجافة في الشتاء؟',
                'question_en' => 'What is the best routine for dry skin in winter?',
                'is_active' => true,
                'sort_order' => 1,
            ],
            [
                'question_ar' => 'أفضل سيروم لتفتيح التصبغات والبقع الداكنة؟',
                'question_en' => 'Top recommended serum for hyperpigmentation and dark spots?',
                'is_active' => true,
                'sort_order' => 2,
            ],
            [
                'question_ar' => 'طريقة استخدام واقي الشمس بالشكل الصحيح؟',
                'question_en' => 'How to apply sunscreen correctly?',
                'is_active' => true,
                'sort_order' => 3,
            ],
            [
                'question_ar' => 'علاج حب الشباب والحد من إفرازات الدهون؟',
                'question_en' => 'How to control acne and excess oil?',
                'is_active' => true,
                'sort_order' => 4,
            ],
            [
                'question_ar' => 'ترتيب خطوات الروتين المسائي قبل النوم؟',
                'question_en' => 'What is the correct order for a night skincare routine?',
                'is_active' => true,
                'sort_order' => 5,
            ],
        ];

        foreach ($suggestions as $s) {
            ChatbotSuggestion::firstOrCreate(
                ['question_ar' => $s['question_ar']],
                $s
            );
        }
    }
}
