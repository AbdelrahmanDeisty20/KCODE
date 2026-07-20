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
        Schema::create('loyalty_levels', function (Blueprint $table) {
            $table->id();
            $table->string('name_ar');                       // e.g. "الوردي الجميل"
            $table->string('name_en');                       // e.g. "Beautiful Pink"
            $table->text('description_ar')->nullable();
            $table->text('description_en')->nullable();
            $table->integer('min_points')->default(0);       // الحد الأدنى لهذا المستوى
            $table->integer('max_points')->nullable();       // null = أعلى مستوى بلا سقف    
            $table->string('icon')->nullable();              // أيقونة اختيارية
            $table->integer('sort_order')->default(0);       // ترتيب العرض
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('loyalty_levels');
    }
};
