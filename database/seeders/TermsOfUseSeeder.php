<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TermsOfUseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('terms_of_use')->truncate();

        $contentAr = [
            'sections' => [
                [
                    'title'     => '1. القبول بالشروط',
                    'paragraph' => 'باستخدامك لموقع KCODE أو الشراء منه، فإنك توافق على الالتزام بكافة الشروط والأحكام الموضحة هنا. إذا كنت لا توافق على هذه الشروط، يرجى عدم استخدام الموقع.'
                ],
                [
                    'title'     => '2. حساب المستخدم والأمان',
                    'paragraph' => 'أنت مسؤول عن الحفاظ على سرية معلومات حسابك وكلمة المرور الخاصة بك، وعن جميع الأنشطة التي تحدث تحت حسابك. يحتفظ الموقع بحق رفض الخدمة أو إنهاء الحسابات عند الحاجة.'
                ],
                [
                    'title'     => '3. الأسعار وتوفر المنتجات',
                    'paragraph' => 'جميع الأسعار المعروضة على الموقع هي بالريال العُماني وشاملة للضرائب المطبقة إن وجدت. نحتفظ بحق تعديل الأسعار أو تحديث المنتجات المتاحة في أي وقت دون إشعار مسبق.'
                ],
                [
                    'title'     => '4. الملكية الفكرية',
                    'paragraph' => 'جميع المحتويات المعروضة على هذا الموقع بما في ذلك النصوص، الشعارات، الصور، والتصميم هي ملك حصري لـ KCODE وتخضع لقوانين الملكية الفكرية.'
                ]
            ]
        ];

        $contentEn = [
            'sections' => [
                [
                    'title'     => '1. Acceptance of Terms',
                    'paragraph' => 'By using or purchasing from the KCODE website, you agree to be bound by all the terms and conditions outlined here. If you do not agree to these terms, please do not use the website.'
                ],
                [
                    'title'     => '2. User Account and Security',
                    'paragraph' => 'You are responsible for maintaining the confidentiality of your account information and password, and for all activities that occur under your account. The website reserves the right to refuse service or terminate accounts as needed.'
                ],
                [
                    'title'     => '3. Pricing and Product Availability',
                    'paragraph' => 'All prices displayed on the website are in Omani Rials and include applicable taxes, if any. We reserve the right to modify prices or update available products at any time without prior notice.'
                ],
                [
                    'title'     => '4. Intellectual Property',
                    'paragraph' => 'All content displayed on this website, including text, logos, images, and design, is the exclusive property of KCODE and is subject to intellectual property laws.'
                ]
            ]
        ];

        DB::table('terms_of_use')->insert([
            'title_ar'   => 'شروط الاستخدام',
            'title_en'   => 'Terms of Use',
            'content_ar' => json_encode($contentAr, JSON_UNESCAPED_UNICODE),
            'content_en' => json_encode($contentEn, JSON_UNESCAPED_UNICODE),
            'is_active'  => true,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }
}
