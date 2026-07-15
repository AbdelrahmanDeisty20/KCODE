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
        Schema::create('assessment_answers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('assessment_id')->constrained('assessments')->cascadeOnDelete();
            $table->foreignId('question_id')->constrained('quiz_questions')->cascadeOnDelete();
            $table->foreignId('answer_id')->constrained('quiz_options')->cascadeOnDelete();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('assessment_answers');
    }
};
/*
{
    "status": true,
    "message": "تم إنشاء الروتين المخصص بنجاح",
    "data": {
        "is_routine_added": true,
        "questions": [
            {
                "question_id": 1,
                "question_title": "روتينات KCODE حسب هدف بشرتك",
                "selected_options": [
                    {
                        "option_id": 1,
                        "option_title": "ترطيب وحماية"
                    }
                ]
            },
            {
                "question_id": 2,
                "question_title": "ما نوع بشرتك؟",
                "selected_options": [
                    {
                        "option_id": 6,
                        "option_title": "دهنية"
                    }
                ]
            },
            {
                "question_id": 3,
                "question_title": "هل ترغبين بمعالجة مشكلة إضافية؟",
                "selected_options": [
                    {
                        "option_id": 11,
                        "option_title": "حبوب"
                    }
                ]
            }
        ],
        "routine": [
            {
                "step_id": 5,
                "step_name": "غسول",
                "step_order": 3,
                "is_core": true,
                "is_addon": false,
                "morning": true,
                "night": true,
                "product": {
                    "id": 5,
                    "name": "غسول بيوتي أوف جوسون بتركيبة البرقوق الأخضر لتنظيف لطيف ومنعش للبشرة (100ml)",
                    "sku": "KCODE-P005",
                    "price": 36,
                    "image": "http://127.0.0.1:8000/uploads/products/http://127.0.0.1:8000/uploads/products/beauty-of-joseon-green-plum-refreshing-cleanser-100ml.jpg",
                    "average_rating": 4,
                    "num_reviews": 3,
                    "brand": {
                        "id": 18,
                        "name": "بيوتي أوف جوسون"
                    }
                },
                "alternatives": [
                    {
                        "id": 25,
                        "name": "غسول أرينسيا بتركيبة الأرز لتنظيف لطيف ومنعش للبشرة (120g)",
                        "price": 41.7,
                        "image": "http://127.0.0.1:8000/uploads/products/http://127.0.0.1:8000/uploads/products/arencia-fresh-green-rice-mochi-cleanser-120g.jpg",
                        "average_rating": 4,
                        "num_reviews": 3,
                        "brand": {
                            "id": 26,
                            "name": "أرينسيا"
                        }
                    }
                ]
            },
            {
                "step_id": 1,
                "step_name": "تونر",
                "step_order": 5,
                "is_core": true,
                "is_addon": false,
                "morning": true,
                "night": true,
                "product": {
                    "id": 40,
                    "name": "تونر ميديكيوب بتركيبة السيكا وPDRN لدعم المرونة ومظهر البشرة المشدود (250ml)",
                    "sku": "KCODE-P040",
                    "price": 32.8,
                    "image": "http://127.0.0.1:8000/uploads/products/http://127.0.0.1:8000/uploads/products/medicube-pdrn-pink-cica-soothing-toner-250ml.jpg",
                    "average_rating": 4,
                    "num_reviews": 3,
                    "brand": {
                        "id": 16,
                        "name": "ميديكيوب"
                    }
                },
                "alternatives": [
                    {
                        "id": 39,
                        "name": "تونر كوزركس بتركيبة أحماض AHA/BHA وأحماض AHA لإشراقة صحية وملمس ناعم (150ml)",
                        "price": 25.2,
                        "image": "http://127.0.0.1:8000/uploads/products/http://127.0.0.1:8000/uploads/products/cosrx-aha-bha-clarifying-treatment-toner-150ml.jpg",
                        "average_rating": 4,
                        "num_reviews": 3,
                        "brand": {
                            "id": 10,
                            "name": "كوزركس"
                        }
                    }
                ]
            },
            {
                "step_id": 7,
                "step_name": "أمبول",
                "step_order": 8,
                "is_core": true,
                "is_addon": false,
                "morning": true,
                "night": true,
                "product": {
                    "id": 11,
                    "name": "أمبول د. ميلاكسين بتركيبة الأرز والأرز الأبيض لتفتيح البقع وتوحيد لون البشرة (50ml)",
                    "sku": "KCODE-P011",
                    "price": 21.9,
                    "image": "http://127.0.0.1:8000/uploads/products/http://127.0.0.1:8000/uploads/products/dr-melaxin-peel-shot-glow-white-rice-peeling-ampoule-50ml.jpg",
                    "average_rating": 4,
                    "num_reviews": 3,
                    "brand": {
                        "id": 21,
                        "name": "د. ميلاكسين"
                    }
                },
                "alternatives": [
                    {
                        "id": 72,
                        "name": "أمبول سكِن1004 بتركيبة السينتيلا لتفتيح البقع وتوحيد لون البشرة (100ml)",
                        "price": 15.8,
                        "image": "http://127.0.0.1:8000/uploads/products/http://127.0.0.1:8000/uploads/products/skin1004-madagascar-centella-tone-brightening-capsule-ampoule-100ml.jpg",
                        "average_rating": 4,
                        "num_reviews": 3,
                        "brand": {
                            "id": 19,
                            "name": "سكِن1004"
                        }
                    }
                ]
            },
            {
                "step_id": 2,
                "step_name": "سيروم",
                "step_order": 9,
                "is_core": true,
                "is_addon": false,
                "morning": true,
                "night": true,
                "product": {
                    "id": 17,
                    "name": "سيروم سيليماكس بتركيبة الريتينول لشد البشرة وتقليل الخطوط الدقيقة (30ml)",
                    "sku": "KCODE-P017",
                    "price": 43.1,
                    "image": "http://127.0.0.1:8000/uploads/products/http://127.0.0.1:8000/uploads/products/celimax-the-vita-a-retinol-shot-tightening-serum-30ml.jpg",
                    "average_rating": 4,
                    "num_reviews": 3,
                    "brand": {
                        "id": 24,
                        "name": "سيليماكس"
                    }
                },
                "alternatives": [
                    {
                        "id": 2,
                        "name": "سيروم أنوا بتركيبة النياسيناميد وحمض الترانيكساميك لتفتيح البقع وتوحيد لون البشرة (30ml)",
                        "price": 31.5,
                        "image": "http://127.0.0.1:8000/uploads/products/http://127.0.0.1:8000/uploads/products/anua-niacinamide-10-plus-txa-4-serum-30ml.jpg",
                        "average_rating": 4,
                        "num_reviews": 3,
                        "brand": {
                            "id": 17,
                            "name": "أنوا"
                        }
                    }
                ]
            },
            {
                "step_id": 8,
                "step_name": "علاج للبشرة",
                "step_order": 11,
                "is_core": true,
                "is_addon": false,
                "morning": true,
                "night": true,
                "product": {
                    "id": 15,
                    "name": "سيروم في تي كوزمتكس بتركيبة ريدل شوت والسيكا لتهدئة البشرة وتقليل الاحمرار (50ml)",
                    "sku": "KCODE-P015",
                    "price": 18.4,
                    "image": "http://127.0.0.1:8000/uploads/products/http://127.0.0.1:8000/uploads/products/vt-cosmetics-cica-reedle-shot-300-50ml.jpg",
                    "average_rating": 4,
                    "num_reviews": 3,
                    "brand": {
                        "id": 23,
                        "name": "في تي كوزمتكس"
                    }
                },
                "alternatives": []
            }
        ]
    }
}
*/