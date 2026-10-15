# 💬 سيكشن «ما يقوله عملاؤنا بعد التجربة»: شرح التعديلات والـ API

> الهدف: كروت تجارب العملاء في الصفحة الرئيسية تبقى **ديناميكية 100%** من غير أي بيانات ثابتة في الفرونت.
> المطلوب كان إضافة حقلين: **`tag`** (وسم التجربة) و **`city`** (مدينة العميل) في رد `GET /api/reviews/general`.

---

## 📌 الخلاصة السريعة

| المطلوب | الحالة |
|---|---|
| حقل `tag` (بالعربي والإنجليزي حسب لغة الطلب) | ✅ |
| حقل `city` (جوه `user` وكمان في المراجعة نفسها) | ✅ |
| فلتر `featured` عشان الصفحة الرئيسية | ✅ |
| `limit` عشان تجيب عدد محدد من غير pagination | ✅ |
| إدارة الوسم والتمييز من لوحة الأدمن (Filament) | ✅ |
| بيانات تجريبية واقعية (12 تجربة) | ✅ |
| أي بيانات قديمة اتمسحت أو اتعدّلت | ❌ مفيش خالص |

---

## 🗄️ 1. قاعدة البيانات (Migration)

**الملف:** `database/migrations/2026_10_13_140000_add_tag_and_featured_to_reviews_city_to_users.php`

| الجدول | العمود | النوع | الوصف |
|---|---|---|---|
| `reviews` | `tag_ar` | `string` nullable | وسم التجربة بالعربي |
| `reviews` | `tag_en` | `string` nullable | وسم التجربة بالإنجليزي |
| `reviews` | `featured` | `boolean` default `false` | يظهر في الصفحة الرئيسية ولا لأ |
| `users` | `city` | `string` nullable | مدينة العميل |

> الـ migration بيتأكد إن العمود مش موجود قبل ما يضيفه (`hasColumn`)، فتشغيله آمن حتى لو اتشغل قبل كدا.

---

## 🧩 2. الكود اللي اتعدّل

| الملف | التعديل |
|---|---|
| `app/Models/Review.php` | أضفت `tag_ar` و `tag_en` و `featured` في `$fillable`، و cast لـ `featured` كـ boolean، و accessor اسمه `tag` بيرجع العربي أو الإنجليزي حسب `Accept-Language` |
| `app/Models/User.php` | أضفت `city` في `Fillable` |
| `app/Http/Resources/API/Reviews/ReviewResource.php` | الرد بقى فيه `tag` و `tag_ar` و `tag_en` و `city` |
| `app/Http/Resources/API/Reviews/ReviewUserResource.php` | أضفت `city` جوه كائن `user` |
| `app/Services/ReviewService.php` | `genralReview()` بقت بتدعم `featured` و `limit` |
| `app/Http/Controllers/API/Genral/ReviewController.php` | validation للبراميترز، ولو فيه `limit` بيرجع list عادية، ولو مفيش بيرجع paginated |
| `app/Filament/Resources/ReviewResource.php` | خانات الوسم والتمييز في الفورم، وعمودين في الجدول (التمييز بيتغيّر بضغطة toggle)، وفلاتر |
| `lang/ar/messages.php` + `lang/en/messages.php` | أضفت `reviewsRetrievedSuccessfully` و `noReviewsFound` (كانوا بيظهروا كـ key من غير ترجمة) |

### 🐞 مشاكل اتصلحت في نفس الوقت
1. **الـ Resource كان بيتطبق مرتين:** الـ service كان بيرجع `ReviewResource::collection` والـ controller بيلفّه تاني.
2. **الـ endpoint كان بيقع لو مفيش تقييمات:** الـ service كان بيرجع `[]`، والـ controller بينادي `->items()` عليها. دلوقتي بيرجع رد سليم وفيه `data: []`.

---

## 🌐 3. الـ API

### Endpoint
```
GET /api/reviews/general
```

### Query Parameters

| البراميتر | النوع | إجباري | الوصف |
|---|---|---|---|
| `featured` | `0` / `1` | لا | `1` = التقييمات المميزة بس |
| `limit` | `int` (من 1 لـ 50) | لا | عدد محدد **من غير pagination**، مترتب بالأعلى تقييماً ثم الأحدث |
| `page` | `int` | لا | رقم الصفحة (لما مفيش `limit`) |

### Headers
```
Accept-Language: ar   // أو en: بيتحكم في قيمة "tag" و "message"
```

### 🏠 للصفحة الرئيسية (الاستخدام المقترح)
```
GET /api/reviews/general?featured=1&limit=4
```

### ✅ الرد (`limit` موجود، فالـ data بترجع list من غير pagination)
```json
{
  "status": true,
  "message": "تم استرجاع التقييمات بنجاح",
  "data": [
    {
      "id": 1396,
      "tag": "الطلب والتغليف",
      "tag_ar": "الطلب والتغليف",
      "tag_en": "Order & Packaging",
      "user": {
        "name": "مريم البلوشية",
        "image": "http://localhost/storage/users/default.png",
        "city": "مسقط"
      },
      "rating": 5,
      "comment": "الطلب وصلني خلال يوم واحد والتغليف كان أنيق جداً ومحكم، حسّيت إني فاتحة هدية مش طلب أونلاين. المنتجات أصلية وتواريخها حديثة.",
      "city": "مسقط",
      "created_at": "2026-10-12T13:33:35.000000Z"
    },
    {
      "id": 1397,
      "tag": "خدمة العملاء",
      "tag_ar": "خدمة العملاء",
      "tag_en": "Customer Service",
      "user": {
        "name": "سالم الحارثي",
        "image": "http://localhost/storage/users/default.png",
        "city": "السيب"
      },
      "rating": 5,
      "comment": "تواصلت مع خدمة العملاء عشان أسأل عن منتج مناسب لبشرة زوجتي الحساسة، الرد كان سريع وفيه اهتمام حقيقي بالتفاصيل. تعامل راقي.",
      "city": "السيب",
      "created_at": "2026-10-11T13:33:35.000000Z"
    },
    {
      "id": 1398,
      "tag": "الروتين المقترَح",
      "tag_ar": "الروتين المقترَح",
      "tag_en": "Suggested Routine",
      "user": {
        "name": "نوف الهنائية",
        "image": "http://localhost/storage/users/default.png",
        "city": "نزوى"
      },
      "rating": 5,
      "comment": "جربت الروتين المقترح بعد اختبار البشرة، وبعد ثلاث أسابيع لاحظت فرق واضح في الحبوب والمسام. أخيراً روتين بسيط ومفهوم وفعّال.",
      "city": "نزوى",
      "created_at": "2026-10-10T13:33:35.000000Z"
    },
    {
      "id": 1399,
      "tag": "تجربة KCODE",
      "tag_ar": "تجربة KCODE",
      "tag_en": "KCODE Experience",
      "user": {
        "name": "خلود المعمرية",
        "image": "http://localhost/storage/users/default.png",
        "city": "صحار"
      },
      "rating": 5,
      "comment": "تجربة KCODE من أحلى تجارب التسوق اللي مرت علي، الموقع سهل والشرح لكل منتج واضح، وصرت أعرف بالضبط إيش يناسب بشرتي.",
      "city": "صحار",
      "created_at": "2026-10-09T13:33:35.000000Z"
    }
  ]
}
```

### 📄 الرد (من غير `limit`، فبيرجع paginated زي الأول)
```
GET /api/reviews/general?page=1
```
```json
{
  "status": true,
  "message": "تم استرجاع التقييمات بنجاح",
  "data": [ { "...": "نفس شكل العنصر اللي فوق" } ],
  "pagination": {
    "current_page": 1,
    "per_page": 10,
    "total": 21,
    "last_page": 3
  }
}
```

### 🚫 لو مفيش تقييمات
```json
{
  "status": true,
  "message": "لا توجد تقييمات",
  "data": []
}
```

### 📝 ملاحظات للفرونت
- استخدموا **`tag`** على طول، لأنه بيتغيّر لوحده حسب اللغة. `tag_ar` و `tag_en` موجودين لو احتجتوهم.
- **`city`** موجودة في مكانين، `item.city` و `item.user.city`، واستخدموا أي واحد فيهم.
- ممكن `tag` أو `city` يرجعوا `null` لو الأدمن ماحطهمش، فاعملوا fallback وخبّوا العنصر لو فاضي.

---

## 🛠️ 4. لوحة الأدمن (Filament)

**المسار:** تقييمات وآراء المنتجات

- **في الفورم:**
  - خانة **المنتج** بقت اختيارية، ولو فاضية يبقى ده **تقييم عام على الموقع** وبيظهر في سيكشن تجارب العملاء.
  - **وسم التجربة (عربي)** و **Experience Tag (EN)**.
  - **مميز (يظهر في الصفحة الرئيسية)**: Toggle.
- **في الجدول:**
  - عمود **الوسم** (badge).
  - عمود **مميز**، وتقدر تغيّره بضغطة toggle من الجدول على طول.
- **الفلاتر:**
  - **مميز** (أيوه / لأ).
  - **نوع التقييم** (تقييم منتج / تقييم عام للموقع).

> مدينة العميل (`city`) بتتحفظ في جدول `users`.

---

## 🌱 5. الـ Seeders

### أ) `ReviewTagCitySeeder`: بيملى البيانات القديمة
**الملف:** `database/seeders/ReviewTagCitySeeder.php`

- بيدوّر على التقييمات العامة القديمة (`product_id = null`) ويحط وسم **للفاضي منها بس**، والوسوم بتتوزع بالترتيب.
- بيحط مدينة **للعملاء اللي مدينتهم فاضية بس**.
- لو مفيش ولا تقييم مميز، بيعلّم أعلى 4 كمميزين.
- **مش بيكتب فوق أي قيمة موجودة.**

### ب) `CustomerTestimonialsSeeder`: بيضيف 12 تجربة جديدة
**الملف:** `database/seeders/CustomerTestimonialsSeeder.php`

- بيضيف **12 عميل** بأسماء عُمانية، ولكل واحد **تجربة واحدة** بتعليق عربي واقعي.
- **4 مميزين** وهما اللي بيظهروا في الصفحة الرئيسية، و **8 عاديين** للصفحات.
- التقييمات 4 و 5 نجوم، وتواريخها خلال آخر 20 يوم.
- إيميلات الحسابات من `testimonial1@kcode.om` لـ `testimonial12@kcode.om`، والباسورد عشوائي (محدش يقدر يدخل بيها).
- **Idempotent:** لو اتشغل أكتر من مرة مش بيكرر حاجة (اتجرب: المرة التانية `0 new testimonials added`).

| # | العميل | المدينة | الوسم | ⭐ | مميز |
|---|---|---|---|---|---|
| 1 | مريم البلوشية | مسقط | الطلب والتغليف | 5 | ✅ |
| 2 | سالم الحارثي | السيب | خدمة العملاء | 5 | ✅ |
| 3 | نوف الهنائية | نزوى | الروتين المقترَح | 5 | ✅ |
| 4 | خلود المعمرية | صحار | تجربة KCODE | 5 | ✅ |
| 5 | أحمد الرواحي | بوشر | الطلب والتغليف | 5 | — |
| 6 | شيخة الكندية | صلالة | خدمة العملاء | 4 | — |
| 7 | عائشة الشكيلية | العامرات | الروتين المقترَح | 5 | — |
| 8 | يوسف العبري | صور | تجربة KCODE | 5 | — |
| 9 | ريم الزدجالية | مسقط | الطلب والتغليف | 4 | — |
| 10 | حمد السعدي | الرستاق | خدمة العملاء | 5 | — |
| 11 | منى الغافرية | عبري | الروتين المقترَح | 5 | — |
| 12 | بدر الفارسي | صحار | تجربة KCODE | 4 | — |

---

## 🚀 6. أوامر التشغيل على السيرفر (بالترتيب)

```bash
php artisan migrate --force
php artisan db:seed --class=ReviewTagCitySeeder --force
php artisan db:seed --class=CustomerTestimonialsSeeder --force
php artisan optimize:clear
```

> ⚠️ الترتيب مهم: الـ migration الأول عشان الأعمدة تكون موجودة قبل الـ seeders.
