# 🚀 خطوات تشغيل التحديثات على السيرفر

> **تاريخ التحديث:** 2026-10-13
> **المطوّر:** Backend Team
> **الخطر:** ⚠️ لا تشغّل `db:seed` الكاملة — ستمسح بيانات الإنتاج

---

## ⚠️ ملاحظة مهمة قبل أي خطوة

- **الـ Seeders الكاملة محظورة على الإنتاج** — `CategorySeeder` و`ProductSeeder` يحتويان على `truncate()` ويمسحان كل البيانات.
- الخطوات أدناه آمنة تماماً ولا تمس أي بيانات موجودة.

---

## الخطوة 1: pull الكود

```bash
git pull origin main
```

---

## الخطوة 2: تشغيل الـ Migration (آمن)

يضيف عمودي `description_ar` و`description_en` لجدول `categories` فقط إذا لم يكونا موجودين، ولا يعدّل أي صف فيه قيمة مسبقاً.

```bash
php artisan migrate --force
```

**المتوقع:**

```
INFO Running migrations.
 2026_10_13_110000_add_description_to_categories_table .. DONE
```

> إذا ظهر `Nothing to migrate` معناه سبق تشغيله — لا مشكلة.

---

## الخطوة 3: ملء أوصاف التصنيفات

يعدّل حقلَي `description_ar` و`description_en` للتصنيفات العشرة فقط.
**لا يمس أي بيانات منتجات أو تصنيفات أخرى.**

```bash
php storage/app/sync_category_desc.php
```

**المتوقع:**

```
Cleanser     updated=1
Toner        updated=1
Essence      updated=1
Serum        updated=1
Moisturizer  updated=1
Sunscreen    updated=1
Eye Care     updated=1
Exfoliator   updated=1
Mask         updated=1
Body Care    updated=1
```

---

## الخطوة 4: إصلاح `sensitive_eligible` للمنتجات

يعيد حساب `sensitive_eligible` لكل منتج بناءً على القواعد الرسمية من ملف `homepage.config.json`، ويزامن جدول الـ Pivot مع نوع البشرة «Sensitive».
**لا يمس أسماء المنتجات أو أسعارها أو صورها أو أي بيانات أخرى.**

```bash
php storage/app/fix_sensitive_eligible.php
```

**المتوقع:**

```
JSON products: 218

Eligible:     134
Not eligible: 84
No JSON data: 0
Sensitive pivot synced: 134 products linked

=== Verification against official reference_counts ===
Oily         | بالنوع: 167 (expect 167) ✅ | مع الحساسية: 104 (expect 104) ✅
Combination  | بالنوع: 205 (expect 204) ❌ | مع الحساسية: 128 (expect 128) ✅
Normal       | بالنوع: 206 (expect 206) ✅ | مع الحساسية: 133 (expect 133) ✅
Dry          | بالنوع: 170 (expect 170) ✅ | مع الحساسية: 117 (expect 117) ✅
```

> الـ ❌ في عدد «مختلطة بالنوع» (205 بدل 204) سبق فحصه — هو منتج واحد KC0169 مرتبط بالـ Fallback، ولا يؤثر على الحساسية.
> أرقام الحساسية الأربعة ✅ مطابقة 100% للمرجع الرسمي.

---

## ترتيب التشغيل الكامل (نسخة سريعة)

```bash
git pull origin main
php artisan migrate --force
php storage/app/sync_category_desc.php
php storage/app/fix_sensitive_eligible.php
```

---

## التحقق بعد التشغيل

```bash
# تأكد إن الـ API شغال
curl -H "Accept-Language: ar" https://your-domain.com/api/categories | python -m json.tool

# تأكد إن description موجود في الـ response
curl -H "Accept-Language: ar" https://your-domain.com/api/products/filter?skin_type_id=1&sensitive_eligible=1
```

---

## الملفات المتأثرة بالتحديث (للمراجعة)

| الملف | النوع | التأثير |
|---|---|---|
| `database/migrations/2026_10_13_110000_add_description_to_categories_table.php` | Migration | يضيف columns جديدة فقط |
| `app/Models/Category.php` | Model | `$fillable` + accessor |
| `app/Http/Resources/API/CATEGORY/CategoryResource.php` | Resource | يضيف `description` للـ response |
| `app/Filament/Resources/CategoryResource.php` | Admin Panel | خانتا وصف في لوحة الأدمن |
| `app/Http/Requests/API/GENERAL/ProductFilterRequest.php` | Request | قاعدة `sensitive_eligible` |
| `app/Services/ProductService.php` | Service | فلتر `sensitive_eligible` |
| `database/seeders/CategorySeeder.php` | Seeder | أوصاف رسمية (للـ seeding المستقبلي) |
| `database/seeders/ProductSeeder.php` | Seeder | منطق حساسية صحيح (للـ seeding المستقبلي) |
| `database/seeders/KcodePdpFieldsSeeder.php` | Seeder | أُزيل `sensitive_eligible => true` الخاطئ |
| `storage/app/sync_category_desc.php` | Script | يُشغَّل مرة واحدة على السيرفر |
| `storage/app/fix_sensitive_eligible.php` | Script | يُشغَّل مرة واحدة على السيرفر |
