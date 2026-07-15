# دليل فهم كود ودورة حياة تقييم الكويز (Quiz Assessment Flow Guide)

هذا الدليل يشرح بالتفصيل كيف تتدفق بيانات تقييم الكويز (Quiz Assessment) من واجهة المستخدم حتى يتم حفظ التقييم والروتين بالكامل بنجاح في قاعدة البيانات وإرجاع الروتين الموصى به مباشرة.

---

## 1. إرسال البيانات من العميل (Postman / Frontend)

يقوم الـ Frontend أو Postman بإرسال المعرفات الأساسية مباشرة كحقول في الـ request body:
`POST /api/quiz/submit-answers`

### شكل البيانات المرسلة:

```json
{
    "skin_type_id": 1,
    "routine_goal_id": 5,
    "concern_ids": [1, 2]
}
```

---

## 2. مرحلة التحقق من صحة البيانات (Validation)

يمر الطلب أولاً على ملف التحقق `EvaluateQuizRequest.php` للتأكد من وجود نوع البشرة والهدف وسلامة البيانات:

```php
class EvaluateQuizRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'skin_type_id' => ['required', 'exists:skin_types,id'],
            'routine_goal_id' => ['required', 'exists:routine_goals,id'],
            'concern_ids' => ['nullable', 'array'],
            'concern_ids.*' => ['exists:concerns,id'],
        ];
    }
}
```

---

## 3. مرحلة الحفظ وحفظ الروتين في قاعدة البيانات (`QuizService.php`)

يقوم السيرفر بإنشاء السجلات وربطها بالروتين المقترح:

1. **إنشاء التقييم (`assessments`):** يتم إنشاء سجل التقييم الرئيسي وربطه بـ `skin_type_id`.
2. **حفظ الأهداف والمشاكل (`assessment_goals` & `assessment_concerns`):** يتم حفظ الهدف والمشاكل المحددة وربطها بمعرف التقييم.
3. **إنشاء الروتين (`routines`):** يتم إنشاء سجل في جدول `routines`.
4. **تشغيل محرك الاقتراح وحفظ المنتجات (`routine_products`):**
   * يتم البحث **فقط عن المنتجات الأكثر مبيعاً** (`is_best_seller = true`) المتوافقة مع نوع البشرة في كل خطوة من خطوات الروتين.
   * يتم تقييم وحساب نقاط (Score) لكل منتج، مع إعطاء **أولوية قصوى وبونص كبير (+200 نقطة)** للمنتجات التي تعالج المشاكل المحددة في `concern_ids`.
   * يتم ترتيب الخطوات المقترحة حسب تقييم منتجاتها تنازلياً واختيار **أعلى 5 منتجات كحد أقصى (5 Products Limit)** لضمان تقديم روتين فعال ومباشر للمشكلة.
   * يتم إعادة ترتيب الخطوات الخمسة المختارة تصاعدياً حسب ترتيب خطوات الروتين لضمان تسلسل الاستخدام الصحيح للبشرة.
   * يتم تخزين المنتجات المقترحة مباشرة في جدول `routine_products`.

---

## 4. شكل الاستجابة البسيط (API Response) بعد الحفظ بنجاح

تقوم الـ API بإرجاع الأسئلة المحددة بالإجابات بالإضافة إلى مصفوفة الروتين المقترحة (الحد الأقصى 5 منتجات الأكثر مبيعاً):

```json
{
    "status": true,
    "message": "Personalized routine recommended successfully.",
    "data": {
        "questions": [
            {
                "question_id": 1,
                "question_title": "KCODE routines based on your skin goal",
                "selected_options": [
                    {
                        "option_id": 1,
                        "option_title": "Hydration & Protection"
                    }
                ]
            },
            {
                "question_id": 2,
                "question_title": "What is your skin type?",
                "selected_options": [
                    {
                        "option_id": 6,
                        "option_title": "Oily"
                    }
                ]
            },
            {
                "question_id": 3,
                "question_title": "Would you like to treat an additional concern?",
                "selected_options": [
                    {
                        "option_id": 11,
                        "option_title": "Acne"
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
                    "id": 6,
                    "name": "غسول كوزركس لتنظيف لطيف ومنعش للبشرة (150ml)",
                    "sku": "KCODE-P006",
                    "price": 31.2,
                    "image": "http://127.0.0.1:8000/uploads/products/cosrx-low-ph-good-morning-gel-cleanser-150ml.jpg",
                    "average_rating": 4,
                    "num_reviews": 3,
                    "brand": {
                        "id": 10,
                        "name": "كوزركس"
                    }
                },
                "alternatives": []
            }
        ]
    }
}
```
"
