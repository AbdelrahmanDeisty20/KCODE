<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PrivacyPolicySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('privacy_policies')->truncate();

        $contentAr = [
            'sections' => [
                [
                    'title'     => '1. البيانات التي نجمعها',
                    'paragraph' => 'نجمع المعلومات الضرورية لإتمام طلباتك وتوفير تجربة مخصصة لك، مثل: الاسم الكامل، البريد الإلكتروني، رقم الهاتف، عنوان التوصيل، ونوع البشرة المفضل لتقديم التوصيات.'
                ],
                [
                    'title'     => '2. استخدام وحماية البيانات',
                    'paragraph' => 'تُستخدم بياناتك فقط لمعالجة الشحنات، تفعيل الحسابات، وتحسين خدماتنا. نحن نطبق أحدث بروتوكولات التشفير والأمان الإلكتروني لمنع أي وصول غير مصرح به لبياناتك الشخصية.'
                ],
                [
                    'title'     => '3. مشاركة البيانات مع طرف ثالث',
                    'paragraph' => 'نحن لا نبيع أو نؤجر بياناتك الشخصية لأي طرف ثالث. نقوم فقط بمشاركة المعلومات الضرورية مع شركات الشحن المعتمدة وبوابات الدفع المشفرة لإتمام معاملتك بنجاح.'
                ],
                [
                    'title'     => '4. ملفات تعريف الارتباط (Cookies)',
                    'paragraph' => 'يستخدم الموقع ملفات تعريف الارتباط لتحسين وتخصيص تجربة التصفح وتذكر تفضيلاتك مثل اللغة وسلة التسوق.'
                ]
            ]
        ];

        $contentEn = [
            'sections' => [
                [
                    'title'     => '1. Data We Collect',
                    'paragraph' => 'We collect the necessary information to complete your orders and provide a customized experience for you, such as: full name, email, phone number, delivery address, and preferred skin type to provide recommendations.'
                ],
                [
                    'title'     => '2. Use and Protection of Data',
                    'paragraph' => 'Your data is only used to process shipments, activate accounts, and improve our services. We apply the latest encryption and cyber security protocols to prevent any unauthorized access to your personal data.'
                ],
                [
                    'title'     => '3. Data Sharing with Third Parties',
                    'paragraph' => 'We do not sell or rent your personal data to any third party. We only share the necessary information with certified shipping companies and encrypted payment gateways to complete your transaction successfully.'
                ],
                [
                    'title'     => '4. Cookies',
                    'paragraph' => 'The website uses cookies to improve and customize your browsing experience and remember your preferences such as language and shopping cart.'
                ]
            ]
        ];

        DB::table('privacy_policies')->insert([
            'title_ar'   => 'سياسة الخصوصية',
            'title_en'   => 'Privacy Policy',
            'content_ar' => json_encode($contentAr, JSON_UNESCAPED_UNICODE),
            'content_en' => json_encode($contentEn, JSON_UNESCAPED_UNICODE),
            'is_active'  => true,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }
}
