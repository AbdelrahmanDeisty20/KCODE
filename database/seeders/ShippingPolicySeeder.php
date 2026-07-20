<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ShippingPolicySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('shipping_policies')->truncate();

        $contentAr = [
            'cards' => [
                [
                    'icon'     => 'location',
                    'title'    => 'سلطنة عُمان',
                    'subtitle' => 'توصيل سريع خلال 1-3 أيام عمل لكافة الولايات والمدن.'
                ],
                [
                    'icon'     => 'clock',
                    'title'    => 'دول الخليج العربي',
                    'subtitle' => 'شحن دولي سريع خلال 4-7 أيام عمل مع التتبع المباشر.'
                ],
                [
                    'icon'     => 'shield',
                    'title'    => 'شحن مجاني',
                    'subtitle' => 'شحن مجاني للطلبات التي تتجاوز قيمة 25 ريال عُماني.'
                ]
            ],
            'sections' => [
                [
                    'title'     => '1. رسوم ومواعيد التوصيل',
                    'paragraph' => 'نسعى دائماً لتجهيز طلبك بأسرع وقت ممكن. يتم تجهيز الشحنات خلال 24 ساعة من تأكيد الطلب، وتسليمها لشريك الشحن المعتمد لدينا.'
                ],
                [
                    'title'     => '2. تتبع الشحنة',
                    'paragraph' => 'فور شحن طلبك، ستصلك رسالة نصية وبريد إلكتروني يحتوي على رقم التتبع المباشر للطلب لمتابعة خط سير الشحنة حتى وصولها لباب منزلك.'
                ],
                [
                    'title'     => '3. ملاحظات هامة',
                    'paragraph' => 'يرجى التأكد من إدخال عنوان التسليم ورقم الهاتف بدقة لتجنب أي تأخير. قد تختلف مواعيد التوصيل خلال فترات العروض الموسمية والأعياد.'
                ]
            ]
        ];

        $contentEn = [
            'cards' => [
                [
                    'icon'     => 'location',
                    'title'    => 'Sultanate of Oman',
                    'subtitle' => 'Fast delivery within 1-3 business days to all states and cities.'
                ],
                [
                    'icon'     => 'clock',
                    'title'    => 'Arab Gulf Countries',
                    'subtitle' => 'Fast international shipping within 4-7 business days with live tracking.'
                ],
                [
                    'icon'     => 'shield',
                    'title'    => 'Free Shipping',
                    'subtitle' => 'Free shipping for orders over 25 OMR.'
                ]
            ],
            'sections' => [
                [
                    'title'     => '1. Fees and Delivery Times',
                    'paragraph' => 'We always strive to prepare your order as quickly as possible. Shipments are prepared within 24 hours of order confirmation and handed over to our certified shipping partner.'
                ],
                [
                    'title'     => '2. Order Tracking',
                    'paragraph' => 'As soon as your order is shipped, you will receive an SMS and an email containing the direct tracking number to follow the progress of the shipment until it reaches your doorstep.'
                ],
                [
                    'title'     => '3. Important Notes',
                    'paragraph' => 'Please make sure to enter the delivery address and phone number accurately to avoid any delays. Delivery times may vary during seasonal promotion periods and holidays.'
                ]
            ]
        ];

        DB::table('shipping_policies')->insert([
            'title_ar'   => 'معلومات المتجر - سياسة الشحن',
            'title_en'   => 'Store Information - Shipping Policy',
            'content_ar' => json_encode($contentAr, JSON_UNESCAPED_UNICODE),
            'content_en' => json_encode($contentEn, JSON_UNESCAPED_UNICODE),
            'is_active'  => true,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }
}
