# دليل تحديثات API تفاصيل المنتج (PDP & Quick View) — KCODE

> **الهدف من هذا التحديث:**  
> تحويل واجهات تفاصيل المنتج والعرض السريع لتصبح **ديناميكية 100% ومربوطة بلوحة التحكم (Dashboard)**، مع مطابقة التصميم المعتمد **R479** بدقة حرفية.

---

## 1. جدول مقارنة المفاتيح (Old vs New Keys)

| المفتاح القديم (Old Key) | المفتاح الجديد (New Key) | مكانه في الـ JSON | الغرض وطريقة الاستخدام |
| :--- | :--- | :--- | :--- |
| `sensitive_eligible`<br>*(كان يعود `true/false` فقط)* | **`sensitive_status`**<br>+<br>**`sensitive_guidance`** | `data.sensitive_status`<br>`data.quick_view.sensitive_status` | **حالة البشرة الحساسة:**<br>بدل كتابة نص ثابت، خذ الحالة مباشرة من `sensitive_status` (مثل: `"مناسب بشرط"` أو `"مناسب للبشرة الحساسة"` أو `"غير مناسب"`).<br>إذا كان هناك نص شرط وإرشاد، يظهر في `sensitive_guidance` ليُعرض أسفل الشارة. |
| كود `KCODE10` كان مكتوباً ثابتاً في الفرونت إند | **`promo_coupon`** | `data.promo_coupon`<br>`data.quick_view.promo_coupon` | **كوبون الخصم الترويجي الأول:**<br>كائن يحتوي على `{ "code": "...", "text": "..." }`.<br>أصبح يُدار من لوحة التحكم. إذا عطّله الأدمن سيعود `null` (في هذه الحالة قم بإخفاء بانر الكوبون). |
| نصوص الضمانات كانت ثابتة في أكورديون الفرونت | **`trust_badges`** | `data.trust_badges`<br>`data.quick_view.trust_badges` | **الضمانات الأربعة (الأصالة، التوصيل، الدفع، الإرجاع):**<br>مصفوفة جاهزة تحتوي على الضمانات بنصوصها الرسمية المعتمدة وتُدار من لوحة التحكم. خذ منها `{ id, title, icon, text }` واعرضها في الأكورديون. |
| تجميع مواصفات العبوة من عدة حقول متفرقة | **`specifications`** | `data.specifications` | **جدول مواصفات المنتج:**<br>كائن موحد وجاهز لملء جدول مواصفات الـ PDP مباشرة (الماركة، الاسم، الباركود، النوع، الحجم، القوام، العطر، المكونات INCI). |

---

## 2. تفاصيل الحقول الجديدة

### أ) ملاءمة البشرة الحساسة (`sensitive_status` & `sensitive_guidance`):
- **السبب:** منتجات الريتينال ومقشرات الأحماض في KCODE تصنيفها الرسمي هو **`مناسب بشرط`** ومرفق معها شروط استخدام وتحذيرات حول العين.
- **الاستخدام:**
  - اقرأ العنوان من: `product.sensitive_status`
  - اقرأ نص التوجيه/الشرط من: `product.sensitive_guidance` (إن وجد).

### ب) كوبون الخصم الترويجي (`promo_coupon`):
- **السبب:** جعل نسبة الخصم وكود الكوبون قابلين للتغيير من لوحة التحكم دون تعديل كود الفرونت إند.
- **الاستخدام:**
  ```json
  "promo_coupon": {
      "code": "KCODE10",
      "text": "كود خصم KCODE10 الشامل",
      "discount_type": "percentage",
      "discount_value": 10
  }
  ```
  *(إذا كانت القيمة `null`، لا تقم بعرض صندوق الكوبون).*

### ج) ضمانات الشراء (`trust_badges`):
- **السبب:** ضمان تطابق نصوص سياسة الشحن والدفع والأصالة والإرجاع مع لوحة التحكم وتحديثها تلقائياً.
- **الاستخدام:** حلقة تكرار (Loop / Map) على مصفوفة `product.trust_badges`.

### د) جدول المواصفات (`specifications`):
- يجمع لك كل ما يخص مواصفات العبوة في كائن واحد:
  ```json
  "specifications": {
      "brand": "بيوتي أوف جوسون",
      "full_name": "Beauty of Joseon Revive Eye Serum: Ginseng + Retinal",
      "barcode": "8809738316146",
      "type": "العناية بالعين",
      "size": "30ml",
      "texture": "متوسط",
      "fragrance": "دون عطر مضاف",
      "ingredients": "Water, Glycerin, Dipropylene Glycol..."
  }
  ```

---

## 3. الرد النموذجي الكامل من الـ API (JSON Response)

> **المسار:** `GET /api/products/{id}` أو `GET /api/products/{slug}`  
> **مثال للمنتج 77 (`KC0078`):**

```json
{
    "status": true,
    "message": "تم جلب المنتج بنجاح",
    "data": {
        "id": 77,
        "name": "Beauty of Joseon Revive Eye Serum: Ginseng + Retinal",
        "description": "سيروم للعين يساعد على تقليل مظهر الخطوط الدقيقة ودعم مرونة الجلد حول العين وتخفيف الجفاف، بتركيبة تجمع بين الريتينال بتركيز 200ppm والأدينوزين والسيراميد والكوليسترول.",
        "sku": "KC0078",
        "price": 9.9,
        "stock": 80,
        "image": "https://kcodeskin.com/storage/products/KC0078.webp",
        "status": "active",
        "sales_count": 348,
        "is_favorite": false,
        "short_name": "Revive Eye Serum: Ginseng + Retinal",
        "ingredients": "Water, Glycerin, Dipropylene Glycol, Caprylic/Capric Triglyceride, 1,2-Hexanediol, Butylene Glycol Dicaprylate/Dicaprate, Niacinamide, Pentaerythrityl Tetraethylhexanoate, Cetearyl Alcohol, Cetearyl Olivate, Sorbitan Olivate, Butylene Glycol, Hydrogenated Lecithin, Panax Ginseng Root Extract, Carbomer, Glyceryl Stearate, Tromethamine, Macadamia Ternifolia Seed Oil, Ethylhexylglycerin, Adenosine, Cholesterol, Dextrin, Polyglyceryl-10 Oleate, Theobroma Cacao (Cocoa) Extract, Disodium EDTA, Brassica Campestris (Rapeseed) Sterols, Phytosteryl/Behenyl/Octyldodecyl Lauroyl Glutamate, Retinal, Silica, Aluminum/Magnesium Hydroxide Stearate, Sodium Hyaluronate, Tocopherol, Potassium Cetyl Phosphate, Pentaerythrityl Tetra-di-t-butyl Hydroxyhydrocinnamate, Ceramide NP, Palmitoyl Tripeptide-5",
        "how_to_use": "التوقيت والتكرار — استخدمه مساءً: ليلة واحدة، ثم اترك ليلتين دون استخدامه حول العين إذا استخدمته على الوجه، فاترك ليلة راحة بين كل استخدامين.\nالكمية والتوزيع — استخدم ضغطة إلى ضغطتين؛ وابدأ بكمية أقل إذا كانت بشرتك حساسة ربّت برفق بإصبع البنصر تحت العينين وحولهما، وتجنّب ملامسة العينين.\nالترتيب والترطيب — ضعه بعد التنظيف والتونر إن كنت تستخدمه، ثم ضع المرطب للبشرة الجافة أو الحساسة، ضع مرطبًا لطيفًا قبل السيروم. اتركه دون شطف.\nالعناية المصاحبة — لا تستخدم معه مقشّرًا أو منتج ريتينول أو ريتينال آخر في المساء نفسه في صباح اليوم التالي، ضع واقي الشمس بعد المرطب. لا يُستخدم أثناء الحمل أو الرضاعة.",
        "review_rating": 4,
        "num_reviews": 24,
        "brand": {
            "id": 18,
            "name": "Beauty of Joseon",
            "image": "https://kcodeskin.com/storage/brands/beauty-of-joseon.png"
        },
        "sub_category": {
            "id": 24,
            "name": "العناية بالعين",
            "image": "https://kcodeskin.com/storage/sub_categories/eye-serum.webp",
            "products_count": 4
        },
        "size": "30ml",
        "barcode": "8809738316146",
        "sensitive_eligible": true,
        "sensitive_status": "مناسب بشرط",
        "sensitive_guidance": "مناسب بشرط ألا يكون الجلد حول العين محمرًّا أو متقشّرًا أو متهيّجًا. ابدأ بكمية قليلة فوق مرطب لطيف، واترك ليلتين من الراحة بين الاستخدامات؛ فالريتينال قد يسبّب جفافًا أو تهيّجًا. أوقفه إذا استمر الحرقان أو الاحمرار.",
        "brief_insight_ar": "سيروم للعين لتقليل مظهر الخطوط الدقيقة وترطيب محيط العين",
        "country_of_origin_ar": "كوريا الجنوبية",
        "role_ar": "سيروم للعين لتقليل مظهر الخطوط الدقيقة وترطيب محيط العين",
        "limitations_notes_ar": "مناسب بشرط ألا يكون الجلد حول العين محمرًّا أو متقشّرًا أو متهيّجًا. ابدأ بكمية قليلة فوق مرطب لطيف، واترك ليلتين من الراحة بين الاستخدامات؛ فالريتينال قد يسبّب جفافًا أو تهيّجًا. أوقفه إذا استمر الحرقان أو الاحمرار.",
        "texture": "متوسط",
        "fragrance": "دون عطر مضاف",
        "why_kcode": "سيروم للعين يساعد على تقليل مظهر الخطوط الدقيقة ودعم مرونة الجلد حول العين وتخفيف الجفاف، بتركيبة تجمع بين الريتينال بتركيز 200ppm والأدينوزين والسيراميد والكوليسترول.",
        "usage_frequency_ar": "مساءً؛ لمحيط العين ليلة استخدام تليها ليلتان من الراحة",
        "active_strength_level": "Medium",
        "safety_notes": "مناسب بشرط — مناسب بشرط ألا يكون الجلد حول العين محمرًّا أو متقشّرًا أو متهيّجًا. ابدأ بكمية قليلة فوق مرطب لطيف، واترك ليلتين من الراحة بين الاستخدامات؛ فالريتينال قد يسبّب جفافًا أو تهيّجًا. أوقفه إذا استمر الحرقان أو الاحمرار.",
        "key_benefits": "عناية بالخطوط الدقيقة: الريتينال من مشتقات فيتامين أ، ويساند مع الأدينوزين تحسين مظهر الخطوط الدقيقة ومرونة الجلد. نرشّحه لهذه الفائدة مع الاستخدام المتدرّج، دون وعد بمحو التجاعيد العميقة.\n\nتقليل الجفاف حول العين: يساعد الغليسرين وهيالورونات الصوديوم على جذب الرطوبة، بينما يساند السيراميد والكوليسترول حاجز البشرة ويقلّلان فقدانها. هذه المكوّنات تدعم راحة الجلد، لكنها لا تمنع احتمال التهيّج من الريتينال.\n\nتركيبة مساندة للنعومة: يضيف النياسيناميد ومستخلص الجينسنغ عناية مرطّبة، مع مكوّنات دهنية تمنح الجلد نعومة. نقيّم هذا الدعم مع الريتينال، ولا نعتمد على اسم الجينسنغ وحده للحكم على فاعلية السيروم.",
        "trust_badges": [
            {
                "id": "authenticity",
                "title": "أصالة مضمونة",
                "icon": "shield",
                "text": "نوفر منتجاتنا من العلامات التجارية ومن قنوات توريد موثوقة، ثم نفحص كل منتج وعبوته قبل اعتماده للبيع ولا نكتفي بالباركود وحده للتحقق من الأصالة."
            },
            {
                "id": "delivery",
                "title": "توصيل سريع",
                "icon": "truck",
                "text": "نفس اليوم داخل مسقط للطلبات المؤكَّدة قبل الساعة الواحدة ظهرًا بتوقيت عُمان. من 24 إلى 48 ساعة لباقي عُمان."
            },
            {
                "id": "payment",
                "title": "دفع مرن وآمن",
                "icon": "card",
                "text": "ادفع بالبطاقة أو عند الاستلام."
            },
            {
                "id": "return_policy",
                "title": "إرجاع خلال 7 أيام",
                "icon": "return",
                "text": "يمكنك إرجاع المنتج خلال 7 أيام من استلامه، بشرط أن يكون غير مستخدم وغير مفتوح وفي عبوته الأصلية."
            }
        ],
        "promo_coupon": {
            "code": "KCODE10",
            "text": "كود خصم KCODE10 الشامل",
            "discount_type": "percentage",
            "discount_value": 10
        },
        "specifications": {
            "brand": "بيوتي أوف جوسون",
            "full_name": "Beauty of Joseon Revive Eye Serum: Ginseng + Retinal",
            "barcode": "8809738316146",
            "type": "العناية بالعين",
            "size": "30ml",
            "texture": "متوسط",
            "fragrance": "دون عطر مضاف",
            "ingredients": "Water, Glycerin, Dipropylene Glycol, Caprylic/Capric Triglyceride, 1,2-Hexanediol, Butylene Glycol Dicaprylate/Dicaprate, Niacinamide, Pentaerythrityl Tetraethylhexanoate, Cetearyl Alcohol, Cetearyl Olivate, Sorbitan Olivate, Butylene Glycol, Hydrogenated Lecithin, Panax Ginseng Root Extract, Carbomer, Glyceryl Stearate, Tromethamine, Macadamia Ternifolia Seed Oil, Ethylhexylglycerin, Adenosine, Cholesterol, Dextrin, Polyglyceryl-10 Oleate, Theobroma Cacao (Cocoa) Extract, Disodium EDTA, Brassica Campestris (Rapeseed) Sterols, Phytosteryl/Behenyl/Octyldodecyl Lauroyl Glutamate, Retinal, Silica, Aluminum/Magnesium Hydroxide Stearate, Sodium Hyaluronate, Tocopherol, Potassium Cetyl Phosphate, Pentaerythrityl Tetra-di-t-butyl Hydroxyhydrocinnamate, Ceramide NP, Palmitoyl Tripeptide-5"
        },
        "skin_types": [
            { "id": 1, "name": "دهنية" },
            { "id": 2, "name": "جافة" },
            { "id": 3, "name": "مختلطة" },
            { "id": 6, "name": "عادية" },
            { "id": 4, "name": "حساسة" }
        ],
        "concerns": [
            { "id": 3, "name": "التصبغات وآثار الحبوب" },
            { "id": 5, "name": "ضعف حاجز البشرة" },
            { "id": 6, "name": "الجفاف والترطيب" },
            { "id": 7, "name": "البهتان والإشراقة" },
            { "id": 8, "name": "الخطوط الدقيقة والتجاعيد" },
            { "id": 9, "name": "العناية بمحيط العين" }
        ],
        "quick_view": {
            "role": "سيروم للعين لتقليل مظهر الخطوط الدقيقة وترطيب محيط العين",
            "best_for_skin_types": [
                "دهنية",
                "جافة",
                "مختلطة",
                "عادية",
                "حساسة"
            ],
            "timing": "مساءً؛ لمحيط العين ليلة استخدام تليها ليلتان من الراحة",
            "texture": "متوسط",
            "fragrance": "دون عطر مضاف",
            "actives": "ريتينال · نياسيناميد · أدينوزين · كوليسترول · سيراميد",
            "sensitive_eligible": true,
            "sensitive_status": "مناسب بشرط",
            "sensitive_guidance": "مناسب بشرط ألا يكون الجلد حول العين محمرًّا أو متقشّرًا أو متهيّجًا. ابدأ بكمية قليلة فوق مرطب لطيف، واترك ليلتين من الراحة بين الاستخدامات؛ فالريتينال قد يسبّب جفافًا أو تهيّجًا. أوقفه إذا استمر الحرقان أو الاحمرار.",
            "note": "سيروم للعين لتقليل مظهر الخطوط الدقيقة وترطيب محيط العين",
            "promo_coupon": {
                "code": "KCODE10",
                "text": "كود خصم KCODE10 الشامل",
                "discount_type": "percentage",
                "discount_value": 10
            },
            "trust_badges": [
                {
                    "id": "authenticity",
                    "title": "أصالة مضمونة",
                    "icon": "shield",
                    "text": "نوفر منتجاتنا من العلامات التجارية ومن قنوات توريد موثوقة، ثم نفحص كل منتج وعبوته قبل اعتماده للبيع ولا نكتفي بالباركود وحده للتحقق من الأصالة."
                },
                {
                    "id": "delivery",
                    "title": "توصيل سريع",
                    "icon": "truck",
                    "text": "نفس اليوم داخل مسقط للطلبات المؤكَّدة قبل الساعة الواحدة ظهرًا بتوقيت عُمان. من 24 إلى 48 ساعة لباقي عُمان."
                },
                {
                    "id": "payment",
                    "title": "دفع مرن وآمن",
                    "icon": "card",
                    "text": "ادفع بالبطاقة أو عند الاستلام."
                },
                {
                    "id": "return_policy",
                    "title": "إرجاع خلال 7 أيام",
                    "icon": "return",
                    "text": "يمكنك إرجاع المنتج خلال 7 أيام من استلامه، بشرط أن يكون غير مستخدم وغير مفتوح وفي عبوته الأصلية."
                }
            ],
            "verdict": {
                "note": "سيروم للعين يساعد على تقليل مظهر الخطوط الدقيقة ودعم مرونة الجلد حول العين وتخفيف الجفاف، بتركيبة تجمع بين الريتينال بتركيز 200ppm والأدينوزين والسيراميد والكوليسترول.",
                "skin_fit_value": "الدهنية · المختلطة · العادية · الجافة",
                "skin_fit_guidance": "",
                "sensitive_status": "مناسب بشرط",
                "sensitive_guidance": "مناسب بشرط ألا يكون الجلد حول العين محمرًّا أو متقشّرًا أو متهيّجًا. ابدأ بكمية قليلة فوق مرطب لطيف، واترك ليلتين من الراحة بين الاستخدامات؛ فالريتينال قد يسبّب جفافًا أو تهيّجًا. أوقفه إذا استمر الحرقان أو الاحمرار.",
                "fragrance": "دون عطر مضاف",
                "actives": "ريتينال · نياسيناميد · أدينوزين · كوليسترول · سيراميد",
                "summary_html": "<article class=\"kcode-verdict\" aria-labelledby=\"verdict-title\">\n<div class=\"verdict-top\"><h2 id=\"verdict-title\" class=\"verdict-label\">خلاصة <bdi>KCODE</bdi></h2></div>\n<div class=\"verdict-body\"><p class=\"verdict-note master-copy\">سيروم للعين يساعد على تقليل مظهر الخطوط الدقيقة ودعم مرونة الجلد حول العين وتخفيف الجفاف، بتركيبة تجمع بين الريتينال بتركيز 200ppm والأدينوزين والسيراميد والكوليسترول.</p>\n<div class=\"verdict-audience\" aria-label=\"ملاءمة المنتج للبشرة\">\n<div class=\"fit-item\"><span class=\"fit-label\">يناسب أنواع البشرة</span><strong class=\"fit-value\">الدهنية · المختلطة · العادية · الجافة</strong></div>\n<section class=\"fit-item fit-sensitive\" aria-label=\"ملاءمة البشرة الحساسة\"><div class=\"sensitive-head\"><span class=\"fit-label\">للبشرة الحساسة</span><strong class=\"fit-value fit-status\">مناسب بشرط</strong></div><p class=\"fit-guidance\">مناسب بشرط ألا يكون الجلد حول العين محمرًّا أو متقشّرًا أو متهيّجًا. ابدأ بكمية قليلة فوق مرطب لطيف، واترك ليلتين من الراحة بين الاستخدامات؛ فالريتينال قد يسبّب جفافًا أو تهيّجًا. أوقفه إذا استمر الحرقان أو الاحمرار.</p></section>\n</div></div></article>"
            }
        },
        "routine_position": {
            "step_number": 3,
            "total_steps": 5,
            "step_title": "سيروم العين",
            "steps": [
                { "step": 1, "title": "غسول", "subtitle": null, "is_current": false },
                { "step": 2, "title": "تونر أو سيروم للوجه", "subtitle": "اختياري", "is_current": false },
                { "step": 3, "title": "سيروم العين", "subtitle": "هذا المنتج", "is_current": true },
                { "step": 4, "title": "مرطب", "subtitle": null, "is_current": false },
                { "step": 5, "title": "واقي شمس", "subtitle": "صباح اليوم التالي", "is_current": false }
            ]
        },
        "usage_instructions": {
            "timing_label": "التوقيت والتكرار",
            "timing": "استخدمه مساءً: ليلة واحدة، ثم اترك ليلتين دون استخدامه حول العين",
            "timing_note": "إذا استخدمته على الوجه، فاترك ليلة راحة بين كل استخدامين.",
            "amount_label": "الكمية والتوزيع",
            "amount": "استخدم ضغطة إلى ضغطتين؛ وابدأ بكمية أقل إذا كانت بشرتك حساسة",
            "amount_note": "ربّت برفق بإصبع البنصر تحت العينين وحولهما، وتجنّب ملامسة العينين.",
            "application_label": "الترتيب والترطيب",
            "application_method": "ضعه بعد التنظيف والتونر إن كنت تستخدمه، ثم ضع المرطب",
            "application_note": "للبشرة الجافة أو الحساسة، ضع مرطبًا لطيفًا قبل السيروم. اتركه دون شطف.",
            "gradual_start_label": "العناية المصاحبة",
            "gradual_start": "لا تستخدم معه مقشّرًا أو منتج ريتينول أو ريتينال آخر في المساء نفسه",
            "gradual_start_note": "في صباح اليوم التالي، ضع واقي الشمس بعد المرطب. لا يُستخدم أثناء الحمل أو الرضاعة.",
            "caution": "",
            "tiles": [
                {
                    "label": "التوقيت والتكرار",
                    "value": "استخدمه مساءً: ليلة واحدة، ثم اترك ليلتين دون استخدامه حول العين",
                    "note": "إذا استخدمته على الوجه، فاترك ليلة راحة بين كل استخدامين."
                },
                {
                    "label": "الكمية والتوزيع",
                    "value": "استخدم ضغطة إلى ضغطتين؛ وابدأ بكمية أقل إذا كانت بشرتك حساسة",
                    "note": "ربّت برفق بإصبع البنصر تحت العينين وحولهما، وتجنّب ملامسة العينين."
                },
                {
                    "label": "الترتيب والترطيب",
                    "value": "ضعه بعد التنظيف والتونر إن كنت تستخدمه، ثم ضع المرطب",
                    "note": "للبشرة الجافة أو الحساسة، ضع مرطبًا لطيفًا قبل السيروم. اتركه دون شطف."
                },
                {
                    "label": "العناية المصاحبة",
                    "value": "لا تستخدم معه مقشّرًا أو منتج ريتينول أو ريتينال آخر في المساء نفسه",
                    "note": "في صباح اليوم التالي، ضع واقي الشمس بعد المرطب. لا يُستخدم أثناء الحمل أو الرضاعة."
                }
            ],
            "benefits": [
                {
                    "title": "عناية بالخطوط الدقيقة",
                    "text": "الريتينال من مشتقات فيتامين أ، ويساند مع الأدينوزين تحسين مظهر الخطوط الدقيقة ومرونة الجلد. نرشّحه لهذه الفائدة مع الاستخدام المتدرّج، دون وعد بمحو التجاعيد العميقة."
                },
                {
                    "title": "تقليل الجفاف حول العين",
                    "text": "يساعد الغليسرين وهيالورونات الصوديوم على جذب الرطوبة، بينما يساند السيراميد والكوليسترول حاجز البشرة ويقلّلان فقدانها. هذه المكوّنات تدعم راحة الجلد، لكنها لا تمنع احتمال التهيّج من الريتينال."
                },
                {
                    "title": "تركيبة مساندة للنعومة",
                    "text": "يضيف النياسيناميد ومستخلص الجينسنغ عناية مرطّبة، مع مكوّنات دهنية تمنح الجلد نعومة. نقيّم هذا الدعم مع الريتينال، ولا نعتمد على اسم الجينسنغ وحده للحكم على فاعلية السيروم."
                }
            ]
        },
        "complementary_routine": [
            {
                "id": 103,
                "sku": "KC0104",
                "brand": "سكِن1004",
                "name": "SKIN1004 Madagascar Centella Ampoule Foam",
                "size": "125ml",
                "category": "غسول",
                "reason": "غسول رغوي ينظف البشرة من الشوائب والدهون اليومية",
                "optional": false,
                "price": 9.9,
                "image": "https://kcodeskin.com/storage/products/KC0104.webp"
            },
            {
                "id": 25,
                "sku": "KC0025",
                "brand": "ميديكيوب",
                "name": "Medicube PDRN Pink Cica Soothing Toner",
                "size": "250ml",
                "category": "تونر",
                "reason": "تونر خفيف يساعد على تهدئة البشرة وتقليل مظهر الاحمرار وترطيبها بعد التنظيف",
                "optional": true,
                "price": 9.9,
                "image": "https://kcodeskin.com/storage/products/KC0025.webp"
            },
            {
                "id": 9,
                "sku": "KC0009",
                "brand": "أنوا",
                "name": "ANUA 3 Ceramide Panthenol Moisture Barrier Cream",
                "size": "100ml",
                "category": "مرطب",
                "reason": "مرطب كريمي يساعد على تقوية حاجز البشرة وتقليل فقدان الترطيب والشعور بالجفاف",
                "optional": false,
                "price": 9.9,
                "image": "https://kcodeskin.com/storage/products/KC0009.webp"
            },
            {
                "id": 76,
                "sku": "KC0077",
                "brand": "بيوتي أوف جوسون",
                "name": "Beauty of Joseon Relief Sun: Rice + Probiotics SPF50+ PA++++",
                "size": "50ml",
                "category": "واقي شمس",
                "reason": "واقي شمس كيميائي كريمي للحماية اليومية وترطيب البشرة",
                "optional": false,
                "price": 9.9,
                "image": "https://kcodeskin.com/storage/products/KC0077.webp"
            }
        ],
        "product_faqs": [
            {
                "id": "faq_1",
                "question": "كيف نقيّم التركيبة في KCODE؟",
                "answer": "نراجع قائمة المكونات كاملة، ونربط دورها باحتياج البشرة. ثم نوضّح الفائدة الأساسية، والفوائد المساندة، وشروط الملاءمة؛ لتختار وأنت تعرف سبب توصيتنا."
            },
            {
                "id": "faq_2",
                "question": "هل يحتوي على ريتينال بتركيز 2% أم 0.02%؟",
                "answer": "تركيز الريتينال في هذا السيروم هو 0.02%، أي 200 جزء في المليون. أما نسبة 2% التي تذكرها العلامة فتخصّ المزيج الذي يحمل الريتينال ومكوّنات أخرى، وليست نسبة الريتينال وحده."
            },
            {
                "id": "faq_3",
                "question": "هل يمكن استخدامه على الوجه أيضًا؟",
                "answer": "نعم، تسمح العلامة باستخدامه على الوجه. استخدمه مساءً واترك ليلة راحة بين كل استخدامين للوجه. أمّا حول العين، فاترك ليلتين من الراحة بين الاستخدامات، ولا تجمعه مع منتج ريتينول أو ريتينال آخر في المساء نفسه."
            },
            {
                "id": "faq_4",
                "question": "هل يمكن استخدامه أثناء الحمل أو الرضاعة؟",
                "answer": "لا؛ توصي العلامة بتجنّبه أثناء الحمل والرضاعة لأنه يحتوي على الريتينال. اختر بدلًا منه عناية مرطّبة لمحيط العين لا تحتوي على مشتقات فيتامين أ."
            }
        ],
        "formula_evaluation_info": {
            "title_ar": "كيف نقيّم التركيبة في KCODE؟",
            "subtitle_ar": "وراء كل خلاصة، مراجعة تبدأ من تركيبة المنتج نفسه.",
            "steps": [
                {
                    "title_ar": "نبدأ من العبوة",
                    "text_ar": "نراجع قائمة المكونات المدوّنة على المنتج، ونقرأها كاملة لفهم ما تحتويه التركيبة فعلًا، بدل الاكتفاء بالمكوّن الذي يبرزه الإعلان."
                },
                {
                    "title_ar": "وجود المكوّن وحده لا يكفي",
                    "text_ar": "ندرس وظيفة المكوّن وترتيبه في القائمة، ونأخذ النسب المعلنة في الحسبان. ثم نقيّم دوره ضمن التركيبة: هل يدعم الهدف الأساسي للمنتج، أم يؤدي دورًا مساندًا؟"
                },
                {
                    "title_ar": "نقرأ التركيبة ككل",
                    "text_ar": "نربط المكونات ببعضها وباحتياجات البشرة، ونوازن بين ما تدعمه التركيبة وما يستدعي الانتباه في ملاءمتها وتحمّلها. ثم نحوّل هذه القراءة إلى خلاصة واضحة تساعدك على فهم المنتج."
                }
            ],
            "footer_note_ar": "نقيّم كل منتج بمعاييرنا نفسها؛ شهرته ووجوده في المتجر لا يمنحانه توصية تلقائية."
        },
        "seo": {
            "product_title_seo": "Beauty of Joseon Revive Eye Serum: Ginseng + Retinal | KCODE",
            "en_short_hook": "Revive Eye Serum: Ginseng + Retinal",
            "seo_meta_title_ar": "Beauty of Joseon Revive Eye Serum: Ginseng + Retinal | KCODE",
            "meta_description": "سيروم للعين يساعد على تقليل مظهر الخطوط الدقيقة ودعم مرونة الجلد حول العين وتخفيف الجفاف، بتركيبة تجمع بين الريتينال بتركيز 200ppm والأدينوزين والسيراميد والكوليسترول.",
            "primary_keyword": "ريتينال · نياسيناميد · أدينوزين · كوليسترول · سيراميد",
            "secondary_keywords": null,
            "final_url_slug": "kc0078-beauty-of-joseon-revive-eye-serum-ginseng-retinal",
            "image_alt": "سيروم بيوتي أوف جوسون ريفايف للعين بالجينسنغ والريتينال، 30 مل — الواجهة الأمامية",
            "og_title_ar": "Beauty of Joseon Revive Eye Serum: Ginseng + Retinal | KCODE",
            "og_description": "سيروم للعين لتقليل مظهر الخطوط الدقيقة وترطيب محيط العين",
            "pdp_headline_en": null,
            "above_fold_hook_en": null,
            "keywords": null
        },
        "category": {
            "id": 7,
            "name": "العناية بالعين",
            "description": "تركيبات ألطف لمحيط العين الرقيق",
            "image": "https://kcodeskin.com/storage/categories/eye-care.webp"
        },
        "product_images": [
            {
                "id": 94,
                "image": "https://kcodeskin.com/storage/products/KC0078_gallery_1.webp"
            }
        ]
    }
}
```

