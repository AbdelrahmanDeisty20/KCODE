<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ReturnPolicySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('return_policies')->truncate();

        $contentAr = [
            'cards' => [
                [
                    'icon'     => 'check',
                    'title'    => 'مهلة 14 يوماً',
                    'subtitle' => 'يمكنك طلب الاسترجاع خلال 14 يوماً من استلام الطلب.'
                ],
                [
                    'icon'     => 'shield',
                    'title'    => 'الغلاف الأصلي',
                    'subtitle' => 'يجب أن يكون المنتج غير مستخدم وفي تغليفه الأصلي المغلق.'
                ]
            ],
            'sections' => [
                [
                    'title'     => '1. شروط قبول الاسترجاع',
                    'paragraph' => 'حرصاً على سلامة عملائنا وشروط الصحة العامة لمنتجات العناية والتجميل، يُشترط أن تكون المنتجات المراد استرجاعها غير مفتوحة، ولم يتم استخدامها، وبحالتها الأصلية تماماً عند الشراء.'
                ],
                [
                    'title'     => '2. المنتجات التالفة أو الخاطئة',
                    'paragraph' => 'في حال استلامك لمنتج تالف أو مغاير لطلبك، يرجى التواصل معنا خلال 48 ساعة وسنقوم باستبداله فوراً مع تحمل كامل تكاليف الشحن.'
                ],
                [
                    'title'     => '3. آلية استرداد الأموال',
                    'paragraph' => 'يتم فحص المنتج المسترجع فور وصوله لمخازننا، ويتم تحويل المبلغ لنفس طريقة الدفع المستخدمة خلال 3 إلى 7 أيام عمل من فحص الطلب.'
                ]
            ]
        ];

        $contentEn = [
            'cards' => [
                [
                    'icon'     => 'check',
                    'title'    => '14-Day Window',
                    'subtitle' => 'You can request a return within 14 days of receiving the order.'
                ],
                [
                    'icon'     => 'shield',
                    'title'    => 'Original Packaging',
                    'subtitle' => 'The product must be unused and in its original closed packaging.'
                ]
            ],
            'sections' => [
                [
                    'title'     => '1. Conditions for Accepting Returns',
                    'paragraph' => 'For the safety of our customers and general health standards for care and beauty products, it is required that the products to be returned are unopened, unused, and in their exact original condition at the time of purchase.'
                ],
                [
                    'title'     => '2. Damaged or Incorrect Products',
                    'paragraph' => 'In case you receive a damaged or different product than your order, please contact us within 48 hours and we will replace it immediately, bearing all shipping costs.'
                ],
                [
                    'title'     => '3. Refund Mechanism',
                    'paragraph' => 'The returned product is inspected immediately upon arrival at our warehouses, and the amount is refunded to the same payment method used within 3 to 7 business days of inspecting the order.'
                ]
            ]
        ];

        DB::table('return_policies')->insert([
            'title_ar'   => 'سياسة الإرجاع',
            'title_en'   => 'Return Policy',
            'content_ar' => json_encode($contentAr, JSON_UNESCAPED_UNICODE),
            'content_en' => json_encode($contentEn, JSON_UNESCAPED_UNICODE),
            'is_active'  => true,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }
}
