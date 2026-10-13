# 📂 دليل API: إضافة `description` لتصنيفات المنتجات

> **Endpoint:** `GET /api/categories`
> **التغيير:** إضافة حقل `description` ثنائي اللغة لكل تصنيف

---

## ✅ ما الذي تغيّر؟

قبل هذا التحديث كانت الاستجابة هكذا:

```json
{
  "id": 1,
  "name": "غسول",
  "image": "https://..."
}
```

**بعد التحديث:**

```json
{
  "id": 1,
  "name": "غسول",
  "description": "ينظّف الوجه ويزيل الزيوت والأوساخ",
  "image": "https://..."
}
```

---

## 🌐 سلوك اللغة

الحقل `description` يتغيّر **تلقائياً** بحسب `Accept-Language` تماماً مثل `name`:

| Header | قيمة `name` | قيمة `description` |
|---|---|---|
| `Accept-Language: ar` | `غسول` | `ينظّف الوجه ويزيل الزيوت والأوساخ` |
| `Accept-Language: en` | `Cleanser` | `Cleanses the face and removes oil and dirt` |

> [!IMPORTANT]
> ما في حاجة تتغير في طريقة استدعاء الـ API. نفس الـ endpoint، نفس الـ headers — الحقل الجديد بيجي تلقائياً.

---

## 📦 الاستجابة الكاملة — عربي

```json
[
    {
        "id": 1,
        "name": "غسول",
        "description": "ينظّف الوجه ويزيل الزيوت والأوساخ",
        "image": "https://.../storage/app/public/categories/cleanser.webp"
    },
    {
        "id": 2,
        "name": "تونر",
        "description": "يوازن البشرة بعد التنظيف ويهيّئها لما بعده",
        "image": "https://.../storage/app/public/categories/toner.webp"
    },
    {
        "id": 3,
        "name": "إسنس",
        "description": "طبقة ترطيب خفيفة قبل السيروم",
        "image": "https://.../storage/app/public/categories/essence.webp"
    },
    {
        "id": 4,
        "name": "سيروم وأمبول",
        "description": "تركيز عالٍ لمعالجة مشكلة محدّدة",
        "image": "https://.../storage/app/public/categories/serum-ampoule.webp"
    },
    {
        "id": 5,
        "name": "مرطب",
        "description": "يحبس الترطيب ويحمي حاجز البشرة",
        "image": "https://.../storage/app/public/categories/moisturizer.webp"
    },
    {
        "id": 6,
        "name": "واقي شمس",
        "description": "الحماية اليومية من أشعة الشمس",
        "image": "https://.../storage/app/public/categories/sunscreen.webp"
    },
    {
        "id": 7,
        "name": "العناية بالعين",
        "description": "تركيبات ألطف لمحيط العين الرقيق",
        "image": "https://.../storage/app/public/categories/eye-care.webp"
    },
    {
        "id": 8,
        "name": "مقشر",
        "description": "يزيل الخلايا الميتة ويحسّن الملمس",
        "image": "https://.../storage/app/public/categories/exfoliator.webp"
    },
    {
        "id": 9,
        "name": "ماسكات",
        "description": "عناية مركّزة أسبوعية",
        "image": "https://.../storage/app/public/categories/mask.webp"
    },
    {
        "id": 10,
        "name": "العناية بالجسم",
        "description": "تنظيف الجسم والعناية بالحبوب والملمس",
        "image": "https://.../storage/app/public/categories/body-care.webp"
    }
]
```

---

## 📦 الاستجابة الكاملة — English

```json
[
    {
        "id": 1,
        "name": "Cleanser",
        "description": "Cleanses the face and removes oil and dirt",
        "image": "https://.../storage/app/public/categories/cleanser.webp"
    },
    {
        "id": 2,
        "name": "Toner",
        "description": "Rebalances skin after cleansing and preps it for the next step",
        "image": "https://.../storage/app/public/categories/toner.webp"
    },
    {
        "id": 3,
        "name": "Essence",
        "description": "A lightweight hydration layer before serum",
        "image": "https://.../storage/app/public/categories/essence.webp"
    },
    {
        "id": 4,
        "name": "Serum",
        "description": "High concentration to target a specific concern",
        "image": "https://.../storage/app/public/categories/serum-ampoule.webp"
    },
    {
        "id": 5,
        "name": "Moisturizer",
        "description": "Locks in hydration and protects the skin barrier",
        "image": "https://.../storage/app/public/categories/moisturizer.webp"
    },
    {
        "id": 6,
        "name": "Sunscreen",
        "description": "Daily protection against the sun",
        "image": "https://.../storage/app/public/categories/sunscreen.webp"
    },
    {
        "id": 7,
        "name": "Eye Care",
        "description": "Gentler formulas for the delicate eye area",
        "image": "https://.../storage/app/public/categories/eye-care.webp"
    },
    {
        "id": 8,
        "name": "Exfoliator",
        "description": "Removes dead skin cells and refines texture",
        "image": "https://.../storage/app/public/categories/exfoliator.webp"
    },
    {
        "id": 9,
        "name": "Mask",
        "description": "Concentrated weekly treatment",
        "image": "https://.../storage/app/public/categories/mask.webp"
    },
    {
        "id": 10,
        "name": "Body Care",
        "description": "Body cleansing and care for texture and blemishes",
        "image": "https://.../storage/app/public/categories/body-care.webp"
    }
]
```

---

## 🔧 ملاحظات للفرونت

- `description` قد يكون `null` في حالة أضاف الأدمن تصنيفاً جديداً من لوحة التحكم ولم يكتب وصفه بعد — تعاملوا معه كـ optional.
- النصوص العربية مأخوذة حرفياً من ملف `homepage.config.json` الرسمي (`types.cards[].desc`).
- النصوص الإنجليزية مترجمة من العربي.
- الأدمن يقدر يعدّل الوصف في أي وقت من لوحة التحكم (Filament ← Categories).

---

## 📐 التغييرات في الكود (للمرجعية)

| الملف | التغيير |
|---|---|
| `database/migrations/..._add_description_to_categories_table.php` | أضاف عمودي `description_ar` و`description_en` إلى جدول `categories` |
| `app/Models/Category.php` | أضاف الحقلين لـ `$fillable` وأضاف accessor `description` يختار اللغة تلقائياً |
| `app/Http/Resources/API/CATEGORY/CategoryResource.php` | أضاف `'description' => $this->description` للـ response |
| `app/Filament/Resources/CategoryResource.php` | أضاف خانتَي textarea للأدمن |
| `database/seeders/CategorySeeder.php` | يحتوي على الأوصاف الرسمية للتصنيفات العشرة |
