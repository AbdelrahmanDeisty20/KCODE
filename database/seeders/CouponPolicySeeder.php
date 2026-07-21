<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CouponPolicySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('coupon_policies')->truncate();

        $contentAr = [
            'header_card' => [
                'title'    => 'خصومات مستمرة على مجموعات العناية',
                'subtitle' => 'تابعي حساباتنا الرسمية للحصول على أكواد الخصم الموسمية وكوبونات الترحيب للعملاء الجدد.'
            ],
            'sections' => [
                [
                    'title'     => '1. شروط إدخال الكوبون',
                    'paragraph' => 'يمكن تطبيق كود الخصم في صفحة الدفع (Checkout) في الحقل المخصص قبل إتمام الطلب. يرجى التثبت من صحة أحرف الكود والالتزام بالحد الأدنى للطلب إن وجد.'
                ],
                [
                    'title'     => '2. دمج الكوبونات مع العروض',
                    'paragraph' => 'كقاعدة عامة، لا يمكن دمج أكثر من كود خصم واحد في الطلب الواحد، كما قد لا تسري بعض الكوبونات على المنتجات المخفضة مسبقاً خلال فترات التخفيضات الكبرى.'
                ],
                [
                    'title'     => '3. صلاحية الكوبون والإنهاء',
                    'paragraph' => 'لكل كود خصم تاريخ صلاحية محدد مسبقاً. يحتفظ موقع KCODE بحق تعديل أو إلغاء أي كود خصم في حال وجود سوء استخدام أو انتهاء الحملة الترويجية.'
                ]
            ]
        ];

        $contentEn = [
            'header_card' => [
                'title'    => 'Continuous Discounts on Care Sets',
                'subtitle' => 'Follow our official accounts to get seasonal discount codes and welcome coupons for new customers.'
            ],
            'sections' => [
                [
                    'title'     => '1. Coupon Entry Conditions',
                    'paragraph' => 'The discount code can be applied in the payment page (Checkout) in the designated field before completing the order. Please verify the correct characters of the code and adhere to the minimum order limit, if any.'
                ],
                [
                    'title'     => '2. Combining Coupons with Offers',
                    'paragraph' => 'As a general rule, it is not possible to combine more than one discount code in a single order, and some coupons may not apply to products that are already discounted during major sale periods.'
                ],
                [
                    'title'     => '3. Coupon Validity and Termination',
                    'paragraph' => 'Each discount code has a pre-defined validity date. The KCODE website reserves the right to modify or cancel any discount code in the event of misuse or the end of the promotional campaign.'
                ]
            ]
        ];

        DB::table('coupon_policies')->insert([
            'title_ar'   => 'سياسة الكوبونات',
            'title_en'   => 'Coupons Policy',
            'content_ar' => json_encode($contentAr, JSON_UNESCAPED_UNICODE),
            'content_en' => json_encode($contentEn, JSON_UNESCAPED_UNICODE),
            'is_active'  => true,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }
}
