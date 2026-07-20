<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 1. FAQs (الأسئلة الشائعة)
        Schema::create('faqs', function (Blueprint $table) {
            $table->id();
            $table->string('question_ar');
            $table->string('question_en');
            $table->text('answer_ar');
            $table->text('answer_en');
            $table->integer('sort_order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // دالة مساعدة لإنشاء جداول السياسات المتشابهة في البنية
        $createPolicyTable = function (string $tableName) {
            Schema::create($tableName, function (Blueprint $table) {
                $table->id();
                $table->string('title_ar');
                $table->string('title_en');
                $table->text('content_ar');
                $table->text('content_en');
                $table->boolean('is_active')->default(true);
                $table->timestamps();
            });
        };

        // 2. سياسة الشحن (Shipping Policy)
        $createPolicyTable('shipping_policies');

        // 3. سياسة الإرجاع (Return Policy)
        $createPolicyTable('return_policies');

        // 4. شروط الاستخدام (Terms of Use)
        $createPolicyTable('terms_of_use');

        // 5. سياسة الخصوصية (Privacy Policy)
        $createPolicyTable('privacy_policies');

        // 6. سياسة الكوبونات (Coupons Policy)
        $createPolicyTable('coupon_policies');

        // 7. برنامج النقاط (Points Program)
        $createPolicyTable('points_program_policies');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('faqs');
        Schema::dropIfExists('shipping_policies');
        Schema::dropIfExists('return_policies');
        Schema::dropIfExists('terms_of_use');
        Schema::dropIfExists('privacy_policies');
        Schema::dropIfExists('coupon_policies');
        Schema::dropIfExists('points_program_policies');
    }
};
