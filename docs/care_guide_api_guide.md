# دليل التوثيق الشامل لنظام دليل العناية (KCODE Care Guide API & Dashboard)

يقدم هذا المستند توثيقاً مرجعياً متكاملاً لموديل ونافذة **«دليل العناية» (Care Guide Modal)** في منصة **KCODE**، متطابقاً مع التصميم المعتمد في النموذج (`KCODE_R422_I_DESKTOP (2).html#routines`)، مع الالتزام الصارم بمعمارية المشروع المتبعة (Architecture).

---

## 1. هيكل قاعدة البيانات (Database Schema)

تم إنشاء جدولين مرتبطين بعلاقة `1-to-Many` لضمان المرونة الكاملة وسهولة التعديل والإضافة من لوحة التحكم:

### أ) جدول دليل العناية الرئيسي: `care_guides`
| الحقل | النوع | الوصف |
| :--- | :--- | :--- |
| `id` | `bigint unsigned` | المعرف الفريد. |
| `title_ar` | `string` | العنوان بالعربية (مثل: `دليل العناية`). |
| `title_en` | `string, nullable` | العنوان بالإنجليزية (مثل: `Care Guide`). |
| `description_ar` | `text` | المقدمة التعريفية بالعربية. |
| `description_en` | `text, nullable` | المقدمة التعريفية بالإنجليزية. |
| `note_ar` | `text, nullable` | الملاحظة الختامية/التنبيه بالعربية (مثل: *المنتج الجيد قد لا يناسب الجميع...*). |
| `note_en` | `text, nullable` | الملاحظة الختامية بالإنجليزية. |
| `button_quiz_text_ar` | `string, nullable` | نص زر اختبار البشرة (مثل: `ابدأ تحليل بشرتك`). |
| `button_quiz_text_en` | `string, nullable` | نص زر اختبار البشرة بالإنجليزية. |
| `button_quiz_action` | `string, nullable` | وجهة/أكشن زر الاختبار (افتراضي: `quiz`). |
| `button_routines_text_ar`| `string, nullable` | نص زر استكشاف الروتينات (مثل: `اكتشف الروتينات`). |
| `button_routines_text_en`| `string, nullable` | نص زر استكشاف الروتينات بالإنجليزية. |
| `button_routines_action` | `string, nullable` | وجهة/أكشن زر الروتينات (افتراضي: `routines`). |
| `is_active` | `boolean` | حالة التفعيل (افتراضي: `true`). |
| `created_at` / `updated_at` | `timestamp` | طوابع الوقت. |

### ب) جدول بنود وخطوات الدليل: `care_guide_items`
| الحقل | النوع | الوصف |
| :--- | :--- | :--- |
| `id` | `bigint unsigned` | المعرف الفريد للبند. |
| `care_guide_id` | `foreignId` | ربط بالدليل الرئيسي مع `cascadeOnDelete`. |
| `title_ar` | `string` | عنوان البند بالعربية (مثل: `نفهم تركيبته`, `نوضّح لمن يناسب`, `نرشدك لاستخدامه`). |
| `title_en` | `string, nullable` | عنوان البند بالإنجليزية. |
| `description_ar` | `text` | شرح وتفاصيل البند بالعربية. |
| `description_en` | `text, nullable` | شرح وتفاصيل البند بالإنجليزية. |
| `icon` | `string, nullable` | رمز الأيقونة (مثل: `flask`, `user-check`, `sparkles`). |
| `sort_order` | `integer` | ترتيب العرض (افتراضي: `0`). |
| `is_active` | `boolean` | حالة تفعيل البند. |
| `created_at` / `updated_at` | `timestamp` | طوابع الوقت. |

---

## 2. لوحة التحكم (Filament Admin Dashboard)

تم إنشاء Resource مخصص في لوحة التحكم يتبع معايير النظام:
* **المسار:** `App\Filament\Resources\CareGuideResource`
* **المجموعة:** `المحتوى والصفحات التعريفية` (`Content & Static Pages`)
* **العنوان:** `دليل العناية` (`Care Guide`)
* **الأيقونة:** `heroicon-o-book-open`
* **المميزات:**
  1. تعديل البيانات الأساسية (العنوان العربي والإنجليزي، المقدمة، الملاحظة الختامية، والتفعيل).
  2. قسم منسدل لأزرار الإجراءات (تعديل نصوص وأكشن أزرار الاختبار والروتينات).
  3. حقل تكراري مدمج (`Repeater`) لإدارة بنود الدليل بحرية كاملة: إضافة بنود جديدة، إعادة الترتيب، التعديل أو الحذف المباشر.

---

## 3. نقطة نهاية الـ API (API Endpoint)

### رابط الـ Endpoint:
* **الرابط:** `GET /api/care-guide`
* **الهيدرز المدعومة:**
  * `lang: ar` (لإرجاع النصوص باللغة العربية)
  * `lang: en` (لإرجاع النصوص باللغة الإنجليزية)
  * `Accept: application/json`

### مثال الاستجابة (باللغة العربية `lang: ar`):
```json
{
    "status": true,
    "message": "تم استرجاع دليل العناية بنجاح",
    "data": {
        "id": 1,
        "title": "دليل العناية",
        "description": "يراجع فريق KCODE مكونات المنتج ودورها في تركيبته، ويحوّل هذه القراءة إلى إرشادات واضحة تساعدك على الاختيار.",
        "items": [
            {
                "id": 1,
                "title": "نفهم تركيبته",
                "description": "نشرح لك فائدة مكوناته، وما يمكن أن يقدّمه لبشرتك.",
                "icon": "flask",
                "sort_order": 1
            },
            {
                "id": 2,
                "title": "نوضّح لمن يناسب",
                "description": "نحدّد أنواع البشرة الأنسب له، وما يجب مراعاته إذا كانت بشرتك حساسة.",
                "icon": "user-check",
                "sort_order": 2
            },
            {
                "id": 3,
                "title": "نرشدك لاستخدامه",
                "description": "نوضّح متى تستخدمه، وترتيبه في روتينك، وكيف تبدأ به.",
                "icon": "sparkles",
                "sort_order": 3
            }
        ],
        "note": "المنتج الجيد قد لا يناسب الجميع. لذلك نذكر المزايا والملاحظات التي قد تغيّر قرارك."
    }
}
```

---

## 4. ملفات النظام التي تم إنشاؤها وتعديلها

1. **التهجيرات (Migrations):**
   - `database/migrations/2026_10_20_143000_create_care_guides_table.php`
   - `database/migrations/2026_10_20_143001_create_care_guide_items_table.php`
2. **الموديلات (Eloquent Models):**
   - `app/Models/CareGuide.php`
   - `app/Models/CareGuideItem.php`
3. **السيدر (Seeders):**
   - `database/seeders/CareGuideSeeder.php`
   - `database/seeders/DatabaseSeeder.php` (إضافة استدعاء السيدر)
4. **طبقة الخدمات (Service Layer):**
   - `app/Services/CareGuideService.php`
5. **طبقة تحويل المخرجات (API Resources):**
   - `app/Http/Resources/API/CareGuide/CareGuideResource.php`
   - `app/Http/Resources/API/CareGuide/CareGuideItemResource.php`
6. **متحكم الـ API (Controller):**
   - `app/Http/Controllers/API/Genral/CareGuideController.php`
7. **المسارات (Routes):**
   - `routes/api.php` (`GET /care-guide`)
8. **ملفات الترجمة (Localization):**
   - `lang/ar/messages.php`
   - `lang/en/messages.php`
9. **لوحة التحكم (Filament v5 Resource):**
   - `app/Filament/Resources/CareGuideResource.php`
   - `app/Filament/Resources/CareGuideResource/Pages/ListCareGuides.php`
   - `app/Filament/Resources/CareGuideResource/Pages/CreateCareGuide.php`
   - `app/Filament/Resources/CareGuideResource/Pages/EditCareGuide.php`
