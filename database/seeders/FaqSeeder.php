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
            [
                'question_ar' => 'ما هي مدة التوصيل داخل سلطنة عُمان؟',
                'question_en' => 'What is the delivery time within the Sultanate of Oman?',
                'answer_ar'   => 'توصيل سريع خلال 1-3 أيام عمل لكافة الولايات والمدن.',
                'answer_en'   => 'Fast delivery within 1-3 business days to all states and cities.',
                'sort_order'  => 2,
                'is_active'   => true,
            ],
            [
                'question_ar' => 'هل يتوفر شحن لدول الخليج العربي؟ وما هي المدة؟',
                'question_en' => 'Is shipping available to Arab Gulf countries? What is the delivery time?',
                'answer_ar'   => 'نعم، يتوفر شحن دولي سريع خلال 4-7 أيام عمل مع التتبع المباشر.',
                'answer_en'   => 'Yes, fast international shipping is available within 4-7 business days with live tracking.',
                'sort_order'  => 3,
                'is_active'   => true,
            ],
            [
                'question_ar' => 'هل توجد خدمة شحن مجاني؟',
                'question_en' => 'Is free shipping available?',
                'answer_ar'   => 'نعم، يتوفر شحن مجاني للطلبات التي تتجاوز قيمتها 25 ريال عُماني.',
                'answer_en'   => 'Yes, free shipping is available for orders over 25 OMR.',
                'sort_order'  => 4,
                'is_active'   => true,
            ],
            [
                'question_ar' => 'كم يستغرق تجهيز الطلب؟',
                'question_en' => 'How long does it take to prepare the order?',
                'answer_ar'   => 'يتم تجهيز الشحنات خلال 24 ساعة من تأكيد الطلب، وتسليمها لشريك الشحن المعتمد لدينا.',
                'answer_en'   => 'Shipments are prepared within 24 hours of order confirmation and handed over to our certified shipping partner.',
                'sort_order'  => 5,
                'is_active'   => true,
            ],
            [
                'question_ar' => 'كيف يمكنني تتبع شحنتي؟',
                'question_en' => 'How can I track my shipment?',
                'answer_ar'   => 'فور شحن طلبك، ستصلك رسالة نصية وبريد إلكتروني يحتوي على رقم التتبع المباشر للطلب لمتابعة خط سير الشحنة حتى وصولها لباب منزلك.',
                'answer_en'   => 'As soon as your order is shipped, you will receive an SMS and an email containing the direct tracking number to follow the progress of the shipment until it reaches your doorstep.',
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
