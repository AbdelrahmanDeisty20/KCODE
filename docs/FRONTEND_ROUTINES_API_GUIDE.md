# دليل ربط واجهات الروتينات (الروتينات المختارة & روتينات الرجال) — KCODE R479

هذا الدليل مخصص لمبرمج الفرونت (Frontend Developer) لربط قسمين رئيسيين معتمدين من نسخة العميل (R479):
1. **الروتينات المختارة بعناية (Curated Routines)** الخاصة بصفحة الروتينات `#routines`.
2. **روتينات الرجال (Men's Routines)** الخاصة بقسم الرجال بالصفحة الرئيسية `#home#men` وصفحة تفاصيل روتين الرجل `#men?routine={slug}`.

---

## 1. الروتينات المختارة (General Curated Routines)

### نقاط النهاية (Endpoints):
- **قائمة الروتينات (List):** `GET /api/preset-routines`
- **تفاصيل الروتين (Details):** `GET /api/preset-routines/{id}`

### الروتينات الثمانية المعتمدة (8 Routines):
| المعرف (Slug) | عنوان الروتين | عدد المنتجات الأساسية | المنتجات المكملة | صورة الغلاف المعتمدة (Cover) |
| :--- | :--- | :---: | :---: | :--- |
| `acne` | روتين الحبوب والبثور وآثار الحبوب | 5 منتجات | 2 منتج | `routines/0d9d479ecb1f709ed0e7.webp` |
| `pores` | روتين المسام والرؤوس السوداء واللمعان الزائد | 5 منتجات | 2 منتج | `routines/72d3caff33e95e8abddf.webp` |
| `oily-hydration` | روتين البشرة الدهنية التي تشعر بالشد بعد التنظيف | 5 منتجات | 2 منتج | `routines/aa0c60ac3efa6db8532b.webp` |
| `pigmentation` | روتين العناية بمظهر الكلف والتصبغات وتفاوت لون البشرة | 5 منتجات | 2 منتج | `routines/a9560f582fd173ff8169.webp` |
| `barrier` | روتين تهدئة الاحمرار ودعم حاجز البشرة | 5 منتجات | 2 منتج | `routines/48056dbb8bed1d0b5ee2.webp` |
| `dry-hydration` | روتين البشرة الجافة والباهتة للترطيب والإشراقة | 5 منتجات | 2 منتج | `routines/92fe8ab38583b477736e.webp` |
| `lines` | روتين الخطوط الدقيقة والتجاعيد وفقدان المرونة والعناية بمحيط العين | 5 منتجات | 2 منتج | `routines/a23337aa0ce55a21aad8.webp` |
| `daily` | روتين العناية اليومية للحفاظ على بشرة متوازنة ومشرقة | 5 منتجات | 2 منتج | `routines/44b1bd5050eebbfa693e.webp` |

### شكل الاستجابة (Response Example - List):
```json
{
  "status": true,
  "message": "تم جلب الروتينات المجهزة بنجاح.",
  "data": [
    {
      "id": 14,
      "routine_id": 14,
      "slug": "acne",
      "title": "روتين الحبوب والبثور وآثار الحبوب",
      "description": "روتين متوازن يستهدف الحبوب والاحمرار والدهون الزائدة من دون إهمال ترطيب البشرة...",
      "badge": "مختارة وفق مراجعة KCODE للمركبات",
      "skin_type": "البشرة الدهنية والمختلطة المعرّضة للحبوب.",
      "goal": "الحبوب والبثور وآثارها",
      "image": "https://kcodeskin.com/storage/routines/0d9d479ecb1f709ed0e7.webp",
      "cover": {
        "src": "https://kcodeskin.com/storage/routines/0d9d479ecb1f709ed0e7.webp",
        "srcset": "https://kcodeskin.com/storage/routines/f85aae499d0314db8fa9.webp 320w, https://kcodeskin.com/storage/routines/0d9d479ecb1f709ed0e7.webp 640w, https://kcodeskin.com/storage/routines/9a4255b24d7dac8ddda5.webp 960w, https://kcodeskin.com/storage/routines/d2fe199efd45d6fbeb04.webp 1024w",
        "alt": "منتجات روتين الحبوب والبثور وآثارها...",
        "width": 1024,
        "height": 1536
      },
      "total_price": 48.4,
      "products_count": 5,
      "benefits": [
        "يساعد على تقليل مظهر الحبوب والبثور.",
        "يعتني بالاحمرار المصاحب للحبوب.",
        "يساعد على تنظيم الدهون وتقليل اللمعان.",
        "يحسّن مظهر المسام والرؤوس السوداء."
      ],
      "preview_images": [
        "https://kcodeskin.com/storage/products/KC0104.webp",
        "https://kcodeskin.com/storage/products/KC0002.webp",
        "https://kcodeskin.com/storage/products/KC0177.webp",
        "https://kcodeskin.com/storage/products/KC0071.webp",
        "https://kcodeskin.com/storage/products/KC0159.webp"
      ]
    }
  ]
}
```

### شكل الاستجابة (Response Example - Details):
عند طلب تفاصيل الروتين عبر `GET /api/preset-routines/{id}`، يتم إرجاع كل ما سبق بالإضافة إلى:
- `items`: مصفوفة الخطوات الـ 5 الأساسية، وكل خطوة فيها:
  - `display_order`: ترتيب الخطوة (1، 2، 3...).
  - `step_name`: اسم الخطوة المعتمد (غسول، سيروم، مقشر، مرطب، واقي شمس).
  - `morning` / `night`: استخدام صباحي ومسائي.
  - `use_time_ar`: توقيت الاستخدام المعتمد.
  - `application`: طريقة التطبيق بالتفصيل.
  - `frequency`: تكرار الاستخدام (مثال: "يومياً" أو "مساءً 2-3 مرات أسبوعياً مع التدرج").
  - `product`: كائن المنتج ببياناته وصورته وسعره وماركته.
- `complementary_products`: المنتجات المكملة (مثل ماء ميسيلار أو لصقات الحبوب).
- `what_it_offers`: النقاط الست التفصيلية لفوائد الروتين.

---

## 2. روتينات الرجال (Men's Preset Routines)

### نقاط النهاية (Endpoints):
- **قائمة روتينات الرجال (List):** `GET /api/men-preset-routines`
- **تفاصيل روتين الرجل (Details):** `GET /api/men-preset-routines/{id}`

### الروتينات الخمسة المعتمدة للرجال (5 Routines):
| المعرف (Slug) | عنوان الروتين | العنوان الفرعي (Subtitle) | المنتجات الأساسية | التوقيت (Timing) | غلاف البطاقة المعتمد |
| :--- | :--- | :--- | :---: | :--- | :--- |
| `daily` | العناية اليومية الأساسية | تنظيف وترطيب وحماية من الشمس بخطوات بسيطة. | 3 منتجات (KC0104, KC0223, KC0076) | صباحاً: غسول + واقي شمس<br>مساءً: غسول + مرطب جل | `routines/a2348ed5482efe93275b.webp` |
| `shine` | عناية باللمعان الزائد | عناية خفيفة للوجه الذي يزداد لمعانه خلال اليوم. | 3 منتجات (KC0163, KC0088, KC0159) | صباحاً: غسول + واقي شمس<br>مساءً: غسول + مرطب جل | `routines/6fdd38f3bc19a2c8f020.webp` |
| `tightness` | ترطيب وراحة بعد الغسل | عناية مرطّبة للجفاف والشدّ بعد غسل الوجه. | 3 منتجات (KC0140, KC0009, KC0121) | صباحاً: غسول + مرطب + واقي شمس<br>مساءً: غسول + مرطب | `routines/1478f81f1c74d6bd8771.webp` |
| `dullness` | نضارة للبشرة الباهتة | عناية للبشرة الباهتة تساعد على النضارة وتجانس اللون. | 4 منتجات (KC0104, KC0095, KC0223, KC0076) | صباحاً: غسول + واقي شمس<br>مساءً: غسول + سيروم + مرطب | `routines/ba6e16c17e3b6b4ac757.webp` |
| `shaving` | عناية بعد الحلاقة | عناية لطيفة بالجفاف والانزعاج بعد الحلاقة. | 4 منتجات (KC0104, KC0144, KC0223, KC0076) | صباحاً: غسول وحلاقة + أمبول + واقي شمس<br>مساءً: غسول + مرطب | `routines/5ecf41d45afaa2941a8d.webp` |

### شكل الاستجابة (Response Example - Men List):
```json
{
  "status": true,
  "message": "تم جلب روتينات الرجال المجهزة بنجاح.",
  "data": [
    {
      "id": 22,
      "routine_id": 22,
      "slug": "daily",
      "title": "العناية اليومية الأساسية",
      "subtitle": "تنظيف وترطيب وحماية من الشمس بخطوات بسيطة.",
      "description": "بداية بسيطة لوجه أنظف وبشرة أنعم وأكثر نضارة. يجمع هذا الروتين التنظيف والترطيب والحماية من الشمس...",
      "skin_type": "جميع أنواع البشرة",
      "timing": {
        "am": "غسول ثم واقي شمس",
        "pm": "غسول ثم مرطب جل"
      },
      "card_note": "ترطيب صباحي عند الحاجة، قبل واقي الشمس.",
      "audience": "كنت تبحث عن روتين يومي بسيط لتنظيف بشرتك وترطيبها وحمايتها من الشمس.",
      "image": "https://kcodeskin.com/storage/routines/a2348ed5482efe93275b.webp",
      "cover": {
        "src": "https://kcodeskin.com/storage/routines/a2348ed5482efe93275b.webp",
        "srcset": "https://kcodeskin.com/storage/routines/2961d9530f85f8e10905.webp 320w, https://kcodeskin.com/storage/routines/a2348ed5482efe93275b.webp 640w, https://kcodeskin.com/storage/routines/ba8931a284654cf5c413.webp 960w, https://kcodeskin.com/storage/routines/66abed861a2f9d58d1c3.webp 1024w",
        "alt": "منتجات روتين العناية اليومية الأساسية للرجال: غسول سكين 1004 سنتيلا، مرطب بوريتو أوت إن وواقي شمس بيوتي أوف جوسون أكوا فريش"
      },
      "total_price": 29.7,
      "products_count": 3
    }
  ]
}
```

---

## 3. كيفية استخدام صور الغلاف (Cover Images):
1. **استخدام مباشر وبسيط:**
   - يمكنك قراءة حقل `routine.image` ووضعه مباشرة داخل الوسم:
   ```html
   <img src={routine.image} alt={routine.title} />
   ```
2. **استخدام متجاوب (Responsive باحترافية R479):**
   - استخدم كائن `routine.cover`:
   ```html
   <picture>
     <img 
       src={routine.cover.src} 
       srcset={routine.cover.srcset} 
       sizes="(max-width: 960px) 40vw, 260px"
       alt={routine.cover.alt} 
       loading="lazy" 
       width={routine.cover.width} 
       height={routine.cover.height} 
     />
   </picture>
   ```

