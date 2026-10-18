# دليل التوثيق الشامل للروتينات الجاهزة (KCODE Preset Routines API)

يقدم هذا المستند توثيقاً مرجعياً متكاملاً لنظام **الروتينات الجاهزة المعتمدة (Curated Preset Routines)** في منصة **KCODE**، متضمناً مطابقة الـ API مع تصميم الواجهة الأمامية (HTML Prototype: `KCODE_R422_I_DESKTOP (2).html`)، وتفاصيل كل حقل مضاف ومصدره، بالإضافة إلى الاستجابتين الكاملتين (JSON Responses) لقائمة الروتينات وتفاصيل الروتين الداخلي.

---

## 1. ما الجديد الذي تمت إضافته ومن أين يأتي؟

تم إجراء تحديث هيكلي شامل يربط قاعدة البيانات بلوحة تحكم Filament وبالـ API لتغذية واجهة المستخدم دون أي نواقص:

| الحقل المضاف | مكان ظهوره في الواجهة | مصدره والغرض منه |
| :--- | :--- | :--- |
| **`preview_images`** | شريط الصور المصغرة على كارت الروتين في صفحة القائمة (`#routines`) | مصفوفة روابط صور حقيقية لمنتجات الروتين لتوليد الـ Thumbnail Strip دون الحاجة لطلب تفاصيل كل منتج. |
| **`benefits`** | 4 نقاط مميزات في بطاقة الروتين بالقائمة | 4 نقاط مركّزة تم استخراجها وتحديدها بدقة من النموذج لكل روتين (`cardBenefitIndexes`). |
| **`what_it_offers`** | شبكة الـ 6 بطاقات في صفحة تفاصيل الروتين | قسم *"ما الذي يقدمه هذا الروتين لبشرتك؟"* ويعرض 6 فوائد شاملة يقدمها الروتين للبشرة. |
| **`usage_guide`** | تبويب **«طريقة الاستخدام»** | كائن هيكلي يقسم الروتين زمنياً: <br>• `am_steps`: خطوات الصباح ☀️ مع نص التطبيق والتكرار.<br>• `pm_standard_steps`: خطوات المساء 🌙 (ليلة العناية المعتادة بدون واقي شمس).<br>• `pm_variants`: ليالي التقشير المتغيرة (مثل ليلة BHA أو ليلة Retino-Mela) مع نص التنبيه الإرشادي. |
| **`application`** | داخل كل خطوة في طريقة الاستخدام | نص **«التطبيق»** التوجيهي (مثل: *"كوّن رغوة بكمية مناسبة بين كفّين مبلّلتين..."*). |
| **`starter_guide`** | أسفل المنتجات النشطة والمقشرات | نص **«طريقة البدء»** للمنتجات القوية (مثل Azelaic Acid و BHA للتدرج الأسبوعي). |
| **`complementary_products`** | تبويب **«منتجات مكملة»** | منتجات داعمة اختيارية (مثل لصقات الحبوب وماء ميسيلار)، تشمل روابط الصور الحقيقية وحقل **`why_selected`** (لماذا اخترناه لهذا الروتين؟). |
| **`reason`** | كرت المنتج في تفاصيل الروتين | نص **«لماذا اخترناه لك؟»** يشرح فلسفة KCODE في اختيار المنتج للبشرة. |
| **`image` & `brand.image`** | بطاقات المنتجات والبراندات | تم حل مشكلة طلب `$prod->image_url` غير المعرف واستبداله بالمسار المعتمد `$prod->image_path ?: $prod->image` لمنع ظهور الصور المؤقتة. |

---

## 2. روابط الـ Endpoints في النظام

* **قائمة الروتينات الجاهزة:** `GET /api/preset-routines`
* **تفاصيل روتين محدد:** `GET /api/preset-routines/{id}`
* **إعادة توليد السيدر عبر الـ API:** `GET /api/preset-routines/generate`
* **روتينات الرجال:** `GET /api/men-preset-routines` و `GET /api/men-preset-routines/{id}`

> [!NOTE]
> يدعم الـ API هيدر اللغة `lang: ar` (لإرجاع النصوص والعناوين بالعربية) أو `lang: en` (للإنجليزية).

---

## 3. الاستجابة الأولى: قائمة الروتينات الجاهزة بالكامل (All Preset Routines)

**الطلب:**
```http
GET https://kcodeskin.com/api/preset-routines
Headers:
  lang: ar
  Accept: application/json
```

**الاستجابة:**
```json
{
    "status": true,
    "message": "تم جلب الروتينات المجهزة بنجاح.",
    "data": [
        {
            "id": 116,
            "routine_id": 116,
            "title": "روتين الحبوب والبثور وآثار الحبوب",
            "description": "روتين متوازن يستهدف الحبوب والاحمرار والدهون الزائدة من دون إهمال ترطيب البشرة. يساعد على العناية بانسداد المسام وآثار الحبوب لتبدو البشرة أكثر صفاءً ونعومة وتوازناً.",
            "badge": "مختار وفق مراجعة KCODE للمركبات",
            "skin_type": "مناسب لـ: البشرة الدهنية والمختلطة المعرّضة للحبوب.",
            "goal": "عناية بالحبوب والآثار",
            "total_price": 582,
            "products_count": 5,
            "benefits": [
                "يساعد على تقليل مظهر الحبوب والبثور.",
                "يساعد على تنظيم الدهون وتقليل اللمعان.",
                "يحسّن مظهر المسام والرؤوس السوداء.",
                "يساعد على تقليل مظهر آثار الحبوب."
            ],
            "preview_images": [
                "https://cdn.shopify.com/s/files/1/0271/7351/9412/products/skin1004-centella-ampoule-foam-main-1.png?v=1653587232",
                "https://cdn.shopify.com/s/files/1/0753/1429/9158/files/anua-us-ampoule-serum-azelaic-acid-10-hyaluron-redness-soothing-serum-1239193732.jpg?v=1779177969",
                "https://cdn.shopify.com/s/files/1/0513/3775/6828/files/bha-blackhead-power-liquid-cosrx-official-1.jpg?v=1689840681",
                "https://cdn.shopify.com/s/files/1/0578/0157/2530/files/7fc8bf9c92f9d5dc7af1e1a8c2d7da6b.png?v=1781505041",
                "https://cdn.shopify.com/s/files/1/0513/3775/6828/files/Ultra_light_invisible_800x1067_e5a058ed-afbb-4517-9516-58a1bf91b1aa.webp?v=1785303430"
            ]
        },
        {
            "id": 117,
            "routine_id": 117,
            "title": "روتين المسام والرؤوس السوداء واللمعان الزائد",
            "description": "روتين مركز للبشرة التي تعاني من الرؤوس السوداء والمسام الواضحة واللمعان السريع. يساعد على العناية بانسداد المسام وتنظيم الدهون وتحسين ملمس البشرة لتبدو أنقى وأكثر نعومة وتوازناً.",
            "badge": "مختار وفق مراجعة KCODE للمركبات",
            "skin_type": "مناسب لـ: البشرة الدهنية والمختلطة.",
            "goal": "تنقية المسام واللمعان",
            "total_price": 600,
            "products_count": 5,
            "benefits": [
                "يساعد على تقليل مظهر المسام.",
                "يعتني بالرؤوس السوداء وانسداد المسام.",
                "يساعد على تنظيم الدهون الزائدة.",
                "يقلل مظهر اللمعان خلال اليوم."
            ],
            "preview_images": [
                "https://cdn.shopify.com/s/files/1/0271/7351/9412/products/skin1004-centella-ampoule-foam-main-1.png?v=1653587232",
                "https://cdn.shopify.com/s/files/1/0513/3775/6828/files/niacin.png?v=1748420816",
                "https://cdn.shopify.com/s/files/1/0513/3775/6828/files/bha-blackhead-power-liquid-cosrx-official-1.jpg?v=1689840681",
                "https://cdn.shopify.com/s/files/1/0558/4135/7989/files/red-bean-water-gel-1-front.webp?v=1770286899",
                "https://cdn.shopify.com/s/files/1/0513/3775/6828/files/Ultra_light_invisible_800x1067_e5a058ed-afbb-4517-9516-58a1bf91b1aa.webp?v=1785303430"
            ]
        },
        {
            "id": 118,
            "routine_id": 118,
            "title": "روتين البشرة الدهنية التي تشعر بالشد بعد التنظيف",
            "description": "إذا كانت بشرتك تلمع لكنها تشعر بالشد بعد التنظيف، فهي لا تحتاج إلى التجفيف أكثر، بل إلى توازن أفضل بين الترطيب والدهون. يمنحها هذا الروتين ترطيباً خفيفاً وراحة أكبر، مع العناية باللمعان من دون ملمس دهني ثقيل.",
            "badge": "مختار وفق مراجعة KCODE للمركبات",
            "skin_type": "مناسب لـ: البشرة الدهنية والمختلطة التي تعاني من الشعور بالشد أو نقص الترطيب.",
            "goal": "ترطيب وتوازن الدهون",
            "total_price": 724,
            "products_count": 5,
            "benefits": [
                "يساعد على تقليل الشعور بالشد بعد التنظيف.",
                "يمنح البشرة ترطيباً خفيفاً من دون إثقالها.",
                "يساعد على تنظيم اللمعان والدهون الزائدة.",
                "يدعم حاجز البشرة ويقلل فقدان الترطيب."
            ],
            "preview_images": [
                "https://cdn.shopify.com/s/files/1/0271/7351/9412/products/torriden-dive-in-cleansing-foam-renewal-main.png?v=1667871297",
                "https://cdn.shopify.com/s/files/1/0753/1429/9158/files/anua-us-toner-rice-70-glow-milky-toner-1239193716.jpg?v=1779178210",
                "https://cdn.shopify.com/s/files/1/0271/7351/9412/files/numbuzin-No.1-pantothenic-B5-active-soothing-serum-cover.jpg?v=1745342617",
                "https://cdn.shopify.com/s/files/1/0459/7779/6766/files/PuritoSeoul_Oat-inCalmingGelCream100ml_1.webp?v=1775459053",
                "https://cdn.shopify.com/s/files/1/0558/4135/7989/files/01_0730___EU.jpg?v=1789608144"
            ]
        },
        {
            "id": 119,
            "routine_id": 119,
            "title": "روتين العناية بمظهر الكلف والتصبغات وتفاوت لون البشرة",
            "description": "روتين مركز متعدد المسارات للعناية بمظهر الكلف والبقع الداكنة وتفاوت لون البشرة، بدلاً من الاعتماد على مكوّن واحد فقط. يجمع بين العناية بالتصبغات والإشراقة والملمس والخطوط الدقيقة، مع الترطيب والحماية اليومية من الشمس.",
            "badge": "مختار وفق مراجعة KCODE للمركبات",
            "skin_type": "مناسب لـ: البشرة الدهنية والمختلطة والعادية والجافة غير الحساسة.",
            "goal": "تفتيح وتوحيد اللون",
            "total_price": 693,
            "products_count": 5,
            "benefits": [
                "يساعد على تقليل مظهر الكلف والتصبغات.",
                "يعتني بالبقع الداكنة وآثار الحبوب.",
                "يساعد على توحيد مظهر لون البشرة.",
                "يدعم حاجز البشرة ويوفر حماية يومية من الشمس."
            ],
            "preview_images": [
                "https://cdn.shopify.com/s/files/1/0271/7351/9412/products/skin1004-centella-ampoule-foam-main-1.png?v=1653587232",
                "https://cdn.shopify.com/s/files/1/0753/1429/9158/files/anua-global-ampoule-serum-niacinamide-10-txa-4-serum-for-brightening-and-dark-spots-1239193722.jpg?v=1779177130",
                "https://cdn.shopify.com/s/files/1/0271/7351/9412/files/dr.reju-advanced-retino-mela-serum-texture.webp?v=1779998411",
                "https://cdn.shopify.com/s/files/1/0111/9309/0106/files/Purito-Seoul-Mighty-Bamboo-Panthenol-Cream-senti-senti.jpg?v=1769210789",
                "https://cdn.shopify.com/s/files/1/0111/9309/0106/files/sun_essence_main.jpg?v=1762461344"
            ]
        },
        {
            "id": 120,
            "routine_id": 120,
            "title": "روتين تهدئة الاحمرار ودعم حاجز البشرة",
            "description": "روتين لطيف للبشرة التي يظهر عليها الاحمرار أو تشعر بالجفاف والانزعاج بسهولة. يركز على التهدئة والترطيب ودعم حاجز البشرة بخطوات مريحة تساعدها على استعادة مظهر أكثر نعومة وراحة وتوازناً.",
            "badge": "مختار وفق مراجعة KCODE للمركبات",
            "skin_type": "مناسب لـ: البشرة الحساسة، سواء كانت دهنية أو مختلطة أو عادية أو جافة.",
            "goal": "تهدئة ودعم الحاجز",
            "total_price": 649,
            "products_count": 5,
            "benefits": [
                "يساعد على تهدئة مظهر الاحمرار.",
                "يقلل الشعور بالجفاف والانزعاج.",
                "يمنح البشرة ترطيباً مكثفاً ومتوازناً.",
                "يدعم حاجز البشرة ويقلل فقدان الترطيب."
            ],
            "preview_images": [
                "https://cdn.shopify.com/s/files/1/0271/7351/9412/products/torriden-dive-in-cleansing-foam-renewal-main.png?v=1667871297",
                "https://cdn.shopify.com/s/files/1/0513/3775/6828/files/james_800x1067_1_1_4e9750cc-2cd6-4817-ace5-be2305a85806.jpg?v=1763111577",
                "https://cdn.shopify.com/s/files/1/0271/7351/9412/files/numbuzin-No.1-pantothenic-B5-active-soothing-serum-cover.jpg?v=1745342617",
                "https://cdn.shopify.com/s/files/1/0459/7779/6766/files/PuritoSeoul_Oat-inCalmingGelCream100ml_1.webp?v=1775459053",
                "https://cdn.shopify.com/s/files/1/0111/9309/0106/files/sun_essence_main.jpg?v=1762461344"
            ]
        },
        {
            "id": 121,
            "routine_id": 121,
            "title": "روتين البشرة الجافة والباهتة للترطيب والإشراقة",
            "description": "روتين غني للبشرة التي تبدو جافة أو خشنة وتفتقد إلى الحيوية والإشراقة. يساعد على تعويض الترطيب ودعم حاجز البشرة وتحسين الملمس، لتبدو أكثر نعومة وامتلاءً وإشراقاً.",
            "badge": "مختار وفق مراجعة KCODE للمركبات",
            "skin_type": "مناسب لـ: البشرة الجافة والعادية المائلة إلى الجفاف.",
            "goal": "ترطيب إضافي وإشراقة",
            "total_price": 842,
            "products_count": 5,
            "benefits": [
                "يساعد على تقليل مظهر الجفاف والخشونة.",
                "يمنح البشرة ترطيباً غنياً ومريحاً.",
                "يدعم حاجز البشرة ويقلل فقدان الترطيب.",
                "يقلل مظهر البهتان ويعزز الإشراقة."
            ],
            "preview_images": [
                "https://cdn.shopify.com/s/files/1/0271/7351/9412/products/torriden-dive-in-cleansing-foam-renewal-main.png?v=1667871297",
                "https://cdn.shopify.com/s/files/1/0753/1429/9158/files/anua-us-toner-rice-70-glow-milky-toner-1239193716.jpg?v=1779178210",
                "https://cdn.shopify.com/s/files/1/0459/7779/6766/products/thumb-7IS46583_405x405_0f7e5da7-0308-4c86-ad38-2a5023906a6d.jpg?v=1680055232",
                "https://cdn.shopify.com/s/files/1/0271/7351/9412/files/biodance-skin-glow-essence-cream-cover.webp?v=1768494903",
                "https://cdn.shopify.com/s/files/1/0558/4135/7989/files/01_0730___EU.jpg?v=1789608144"
            ]
        },
        {
            "id": 122,
            "routine_id": 122,
            "title": "روتين الخطوط الدقيقة والتجاعيد وفقدان المرونة والعناية بمحيط العين",
            "description": "روتين متكامل للعناية بعلامات التقدم في مظهر البشرة، من الخطوط الدقيقة وفقدان المرونة إلى الهالات والانتفاخ حول العين. يجمع بين الببتيدات والعناية الليلية والترطيب ليمنح الوجه ومحيط العين مظهراً أكثر نعومة وامتلاءً وحيوية.",
            "badge": "مختار وفق مراجعة KCODE للمركبات",
            "skin_type": "مناسب لـ: البشرة الدهنية والمختلطة والعادية والجافة غير الحساسة.",
            "goal": "مرونة ومكافحة التجاعيد",
            "total_price": 573,
            "products_count": 5,
            "benefits": [
                "يساعد على تحسين مظهر الخطوط الدقيقة والتجاعيد.",
                "يدعم مظهر مرونة البشرة وتماسكها.",
                "يمنح البشرة ترطيباً يساعدها على الظهور أكثر امتلاءً.",
                "يعتني بالخطوط الدقيقة حول العين."
            ],
            "preview_images": [
                "https://cdn.shopify.com/s/files/1/0271/7351/9412/products/skin1004-centella-ampoule-foam-main-1.png?v=1653587232",
                "https://cdn.shopify.com/s/files/1/0513/3775/6828/files/Peptide_Serum_50ml_97fb9dc5-4937-4073-a862-c030bc637fd0.jpg?v=1784857291",
                "https://cdn.shopify.com/s/files/1/0558/4135/7989/files/01_0325.jpg?v=1774440555",
                "https://cdn.shopify.com/s/files/1/0271/7351/9412/files/torriden-cellmazing-firming-cream-hover.png?v=1721408817",
                "https://cdn.shopify.com/s/files/1/0111/9309/0106/files/sun_essence_main.jpg?v=1762461344"
            ]
        },
        {
            "id": 123,
            "routine_id": 123,
            "title": "روتين العناية اليومية للحفاظ على بشرة متوازنة ومشرقة",
            "description": "لا تحتاج بشرتك إلى مشكلة واضحة حتى تبدأ بالاهتمام بها. يمنحك هذا الروتين أساساً يومياً متوازناً للمحافظة على الترطيب والنعومة والإشراقة، مع العناية بمظهر البقع الخفيفة وتفاوت اللون وحماية البشرة من الشمس.",
            "badge": "مختار وفق مراجعة KCODE للمركبات",
            "skin_type": "مناسب لـ: جميع أنواع البشرة.",
            "goal": "عناية يومية متوازنة",
            "total_price": 610,
            "products_count": 5,
            "benefits": [
                "يساعد على المحافظة على ترطيب البشرة ونعومتها.",
                "يعزز مظهر الإشراقة والحيوية.",
                "يعتني بتفاوت اللون ومظهر البقع الخفيفة.",
                "يوفر حماية يومية من أشعة الشمس."
            ],
            "preview_images": [
                "https://cdn.shopify.com/s/files/1/0271/7351/9412/products/skin1004-centella-ampoule-foam-main-1.png?v=1653587232",
                "https://cdn.shopify.com/s/files/1/0753/1429/9158/files/anua-us-toner-rice-70-glow-milky-toner-1239193716.jpg?v=1779178210",
                "https://cdn.shopify.com/s/files/1/0558/4135/7989/files/02_0916__30ml_1.jpg?v=1789546505",
                "https://cdn.shopify.com/s/files/1/0513/3775/6828/files/advanced-snail-92-all-in-one-cream-cosrx-official-15.jpg?v=1757571879",
                "https://cdn.shopify.com/s/files/1/0111/9309/0106/files/sun_essence_main.jpg?v=1762461344"
            ]
        }
    ],
    "pagination": {
        "current_page": 1,
        "per_page": 10,
        "total": 8,
        "last_page": 1,
        "has_more_pages": false
    }
}
```

---

## 4. الاستجابة الثانية: تفاصيل الروتين من الداخل (Single Routine Details)

**الطلب:**
```http
GET https://kcodeskin.com/api/preset-routines/120
Headers:
  Accept: application/json
```

**الاستجابة:**
```json
{
    "status": true,
    "message": "Routine details retrieved successfully.",
    "data": {
        "id": 120,
        "routine_id": 120,
        "title": "Soothing Redness & Barrier Support Routine",
        "description": "روتين لطيف للبشرة التي يظهر عليها الاحمرار أو تشعر بالجفاف والانزعاج بسهولة. يركز على التهدئة والترطيب ودعم حاجز البشرة بخطوات مريحة تساعدها على استعادة مظهر أكثر نعومة وراحة وتوازناً.",
        "badge": "Selected by KCODE Formulation Review",
        "skin_type": "Suitable for: البشرة الحساسة، سواء كانت دهنية أو مختلطة أو عادية أو جافة.",
        "goal": "Soothing & Barrier Support",
        "total_price": 649,
        "products_count": 5,
        "benefits": [
            "يساعد على تهدئة مظهر الاحمرار.",
            "يقلل الشعور بالجفاف والانزعاج.",
            "يمنح البشرة ترطيباً مكثفاً ومتوازناً.",
            "يدعم حاجز البشرة ويقلل فقدان الترطيب."
        ],
        "preview_images": [
            "https://cdn.shopify.com/s/files/1/0271/7351/9412/products/torriden-dive-in-cleansing-foam-renewal-main.png?v=1667871297",
            "https://cdn.shopify.com/s/files/1/0513/3775/6828/files/james_800x1067_1_1_4e9750cc-2cd6-4817-ace5-be2305a85806.jpg?v=1763111577",
            "https://cdn.shopify.com/s/files/1/0271/7351/9412/files/numbuzin-No.1-pantothenic-B5-active-soothing-serum-cover.jpg?v=1745342617",
            "https://cdn.shopify.com/s/files/1/0459/7779/6766/files/PuritoSeoul_Oat-inCalmingGelCream100ml_1.webp?v=1775459053",
            "https://cdn.shopify.com/s/files/1/0111/9309/0106/files/sun_essence_main.jpg?v=1762461344"
        ],
        "what_it_offers": [
            "يساعد على تهدئة مظهر الاحمرار.",
            "يقلل الشعور بالجفاف والانزعاج.",
            "يمنح البشرة ترطيباً مكثفاً ومتوازناً.",
            "يدعم حاجز البشرة ويقلل فقدان الترطيب.",
            "يحسّن مظهر النعومة والامتلاء.",
            "يوفر حماية يومية من الشمس بتركيبة ملائمة للبشرة الحساسة."
        ],
        "how_to_use": null,
        "usage_guide": {
            "am_title": "خطوات الصباح",
            "am_steps": [
                {
                    "sku": "KC0140",
                    "role": "الغسول",
                    "application": "كوّن رغوة بكمية مناسبة بين كفّين مبلّلتين، دلّك الوجه بلطف، ثم اشطفه بالماء الفاتر.",
                    "frequency": "يوميًا.",
                    "starter_guide": ""
                },
                {
                    "sku": "KC0154",
                    "role": "الإسنس",
                    "application": "بعد الغسول والتونر إن استخدمته، وزّع كمية صغيرة، نحو ضغطتين إلى ثلاث، على الوجه وربّت بلطف حتى الامتصاص؛ تجنّب منطقة العينين.",
                    "frequency": "صباحًا ومساءً.",
                    "starter_guide": ""
                },
                {
                    "sku": "KC0123",
                    "role": "السيروم",
                    "application": "بعد التنظيف والتونر إن استخدمته، وزّع كمية مناسبة على الوجه وربّت بلطف، ثم اتبع بالمرطب.",
                    "frequency": "يوميًا.",
                    "starter_guide": ""
                },
                {
                    "sku": "KC0223",
                    "role": "المرطب",
                    "application": "وزّع كمية مناسبة على الوجه في خطوة الترطيب صباحًا ومساءً، ثم ربّت حتى الامتصاص.",
                    "frequency": "صباحًا ومساءً، في خطوة الترطيب.",
                    "starter_guide": ""
                },
                {
                    "sku": "KC0121",
                    "role": "واقي الشمس",
                    "application": "وزّعه على الوجه والرقبة والأجزاء المكشوفة قبل الخروج بنحو 15 دقيقة.",
                    "frequency": "صباحًا؛ أعد التطبيق كل ساعتين عند البقاء خارج المنزل، وبعد السباحة أو التعرّق.",
                    "starter_guide": ""
                }
            ],
            "pm_title": "خطوات المساء",
            "pm_standard_label": "ليلة العناية المعتادة",
            "pm_standard_steps": [
                {
                    "sku": "KC0140",
                    "role": "الغسول",
                    "application": "كوّن رغوة بكمية مناسبة بين كفّين مبلّلتين، دلّك الوجه بلطف، ثم اشطفه بالماء الفاتر.",
                    "frequency": "يوميًا.",
                    "starter_guide": ""
                },
                {
                    "sku": "KC0154",
                    "role": "الإسنس",
                    "application": "بعد الغسول والتونر إن استخدمته، وزّع كمية صغيرة، نحو ضغطتين إلى ثلاث، على الوجه وربّت بلطف حتى الامتصاص؛ تجنّب منطقة العينين.",
                    "frequency": "صباحًا ومساءً.",
                    "starter_guide": ""
                },
                {
                    "sku": "KC0123",
                    "role": "السيروم",
                    "application": "بعد التنظيف والتونر إن استخدمته، وزّع كمية مناسبة على الوجه وربّت بلطف، ثم اتبع بالمرطب.",
                    "frequency": "يوميًا.",
                    "starter_guide": ""
                },
                {
                    "sku": "KC0223",
                    "role": "المرطب",
                    "application": "وزّع كمية مناسبة على الوجه في خطوة الترطيب صباحًا ومساءً، ثم ربّت حتى الامتصاص.",
                    "frequency": "صباحًا ومساءً، في خطوة الترطيب.",
                    "starter_guide": ""
                }
            ],
            "pm_variants": [],
            "night_notes": ""
        },
        "complementary_products": [
            {
                "product_id": 91,
                "sku": "KC0094",
                "name": "Dr.Althea Pure Grinding Cleansing Balm (50ml)",
                "name_ar": "Dr.Althea Pure Grinding Cleansing Balm (50ml)",
                "brand": "Dr.Althea",
                "image": "https://cdn.shopify.com/s/files/1/0459/7779/6766/files/1_a791a049-1299-4c1f-8528-9c188b89f1f4.webp?v=1751437786",
                "price": 113,
                "size": "50ml",
                "why_selected": "يساعد على إزالة المكياج والواقي الشمسي مساءً قبل الغسول، عند الحاجة فقط.",
                "note": "يساعد على إزالة المكياج والواقي الشمسي مساءً قبل الغسول، عند الحاجة فقط.",
                "step": "الغسول"
            },
            {
                "product_id": 177,
                "sku": "KC0184",
                "name": "BIODANCE Hydro Cera-nol Real Deep Mask (34g (4EA))",
                "name_ar": "BIODANCE Hydro Cera-nol Real Deep Mask (34g (4EA))",
                "brand": "BIODANCE",
                "image": "https://cdn.shopify.com/s/files/1/0111/9309/0106/files/Biodance-Hydro-Cera-nol-Real-Deep-Mask-sentisenti.jpg?v=1762461345",
                "price": 92,
                "size": "34g (4EA)",
                "why_selected": "عناية أسبوعية مكثفة لدعم الترطيب وحاجز البشرة، ويُستخدم بدلاً من المرطب الليلي في ليلة استخدامه.",
                "note": "عناية أسبوعية مكثفة لدعم الترطيب وحاجز البشرة، ويُستخدم بدلاً من المرطب الليلي في ليلة استخدامه.",
                "step": "قناع ليلي"
            }
        ],
        "items": [
            {
                "display_order": 1,
                "step_name": "Step 1",
                "morning": true,
                "night": true,
                "use_time_ar": "صباحاً ومساءً",
                "reason": "غسول رغوي ينظف البشرة من الشوائب اليومية، مع الغليسرين والبانثينول والألانتوين للمساعدة على تقليل الشعور بالشد والجفاف بعد الغسيل.",
                "frequency": "يوميًا.",
                "application": "كوّن رغوة بكمية مناسبة بين كفّين مبلّلتين، دلّك الوجه بلطف، ثم اشطفه بالماء الفاتر.",
                "starter_guide": "",
                "product": {
                    "id": 134,
                    "name": "TORRIDEN DIVE IN Low Molecular Hyaluronic Acid Cleansing Foam (150ml)",
                    "name_ar": "TORRIDEN DIVE IN Low Molecular Hyaluronic Acid Cleansing Foam (150ml)",
                    "short_name": "TORRIDEN DIVE IN Low Molecular Hyaluronic Acid Cleansing Foam",
                    "sku": "KC0140",
                    "price": 162,
                    "size": "150ml",
                    "image": "https://cdn.shopify.com/s/files/1/0271/7351/9412/products/torriden-dive-in-cleansing-foam-renewal-main.png?v=1667871297",
                    "average_rating": 0,
                    "num_reviews": 0,
                    "brand": {
                        "id": 40,
                        "name": "TORRIDEN",
                        "image": "https://kcodeskin.com/storage/app/public/brands/01M1EC093DQ9WS8B2JQ1CWQVKP.png"
                    }
                }
            },
            {
                "display_order": 2,
                "step_name": "Step 2",
                "morning": true,
                "night": true,
                "use_time_ar": "صباحاً ومساءً",
                "reason": "إيسنس بقوام جل يساعد على زيادة ترطيب البشرة وتقليل مظهر الجفاف وتحسين نعومتها، بتركيبة تجمع بين إفرازات الحلزون المصفّاة والبيتايين وهيالورونات الصوديوم والبانثينول.",
                "frequency": "صباحًا ومساءً.",
                "application": "بعد الغسول والتونر إن استخدمته، وزّع كمية صغيرة، نحو ضغطتين إلى ثلاث، على الوجه وربّت بلطف حتى الامتصاص؛ تجنّب منطقة العينين.",
                "starter_guide": "",
                "product": {
                    "id": 148,
                    "name": "COSRX Advanced Snail 96 Mucin Power Essence (100ml)",
                    "name_ar": "COSRX Advanced Snail 96 Mucin Power Essence (100ml)",
                    "short_name": "COSRX Advanced Snail 96 Mucin Power Essence",
                    "sku": "KC0154",
                    "price": 171,
                    "size": "100ml",
                    "image": "https://cdn.shopify.com/s/files/1/0513/3775/6828/files/james_800x1067_1_1_4e9750cc-2cd6-4817-ace5-be2305a85806.jpg?v=1763111577",
                    "average_rating": 0,
                    "num_reviews": 0,
                    "brand": {
                        "id": 10,
                        "name": "COSRX",
                        "image": "https://kcodeskin.com/storage/app/public/brands/01M1EC0939BQEE09XD1SYSR7GB.png"
                    }
                }
            },
            {
                "display_order": 3,
                "step_name": "Step 3",
                "morning": true,
                "night": true,
                "use_time_ar": "صباحاً ومساءً",
                "reason": "سيروم خفيف يساعد على تهدئة البشرة وتقليل الشعور بالجفاف والشد وتحسين مظهر البهتان، بتركيبة تجمع بين البانثينول والبيتايين والنياسيناميد.",
                "frequency": "يوميًا.",
                "application": "بعد التنظيف والتونر إن استخدمته، وزّع كمية مناسبة على الوجه وربّت بلطف، ثم اتبع بالمرطب.",
                "starter_guide": "",
                "product": {
                    "id": 119,
                    "name": "NUMBUZIN No.1 Pantothenic B5 Active Soothing Serum (50ml)",
                    "name_ar": "NUMBUZIN No.1 Pantothenic B5 Active Soothing Serum (50ml)",
                    "short_name": "NUMBUZIN No.1 Pantothenic B5 Active Soothing Serum",
                    "sku": "KC0123",
                    "price": 99,
                    "size": "50ml",
                    "image": "https://cdn.shopify.com/s/files/1/0271/7351/9412/files/numbuzin-No.1-pantothenic-B5-active-soothing-serum-cover.jpg?v=1745342617",
                    "average_rating": 4,
                    "num_reviews": 24,
                    "brand": {
                        "id": 28,
                        "name": "NUMBUZIN",
                        "image": "https://kcodeskin.com/storage/app/public/brands/01M1EC093CN4AEDB1NQQSE12R0.png"
                    }
                }
            },
            {
                "display_order": 4,
                "step_name": "Step 4",
                "morning": true,
                "night": true,
                "use_time_ar": "صباحاً ومساءً",
                "reason": "مرطب بقوام جل يساعد على تهدئة البشرة وتقليل الشعور بالجفاف وزيادة نعومتها، بتركيبة تجمع بين ماء الشوفان والبانثينول وبيتا غلوكان والسكوالان.",
                "frequency": "صباحًا ومساءً، في خطوة الترطيب.",
                "application": "وزّع كمية مناسبة على الوجه في خطوة الترطيب صباحًا ومساءً، ثم ربّت حتى الامتصاص.",
                "starter_guide": "",
                "product": {
                    "id": 214,
                    "name": "PURITO Oat-In Calming Gel Cream (100ml)",
                    "name_ar": "PURITO Oat-In Calming Gel Cream (100ml)",
                    "short_name": "PURITO Oat-In Calming Gel Cream",
                    "sku": "KC0223",
                    "price": 114,
                    "size": "100ml",
                    "image": "https://cdn.shopify.com/s/files/1/0459/7779/6766/files/PuritoSeoul_Oat-inCalmingGelCream100ml_1.webp?v=1775459053",
                    "average_rating": 0,
                    "num_reviews": 0,
                    "brand": {
                        "id": 31,
                        "name": "PURITO",
                        "image": "https://kcodeskin.com/storage/app/public/brands/01M1EC093CN4AEDB1NQQSE12R1.png"
                    }
                }
            },
            {
                "display_order": 5,
                "step_name": "Step 5",
                "morning": true,
                "night": false,
                "use_time_ar": "صباحاً",
                "reason": "واقي شمس خفيف بدرجة حماية SPF 50+ وPA++++ بفلاتر متعددة للحماية من الأشعة فوق البنفسجية، مع السنتيلا وعرق السوس لتهدئة البشرة وتقليل مظهر الاحمرار. لا يترك أثراً أبيض.",
                "frequency": "صباحًا؛ أعد التطبيق كل ساعتين عند البقاء خارج المنزل، وبعد السباحة أو التعرّق.",
                "application": "وزّعه على الوجه والرقبة والأجزاء المكشوفة قبل الخروج بنحو 15 دقيقة.",
                "starter_guide": "",
                "product": {
                    "id": 117,
                    "name": "NUMBUZIN No.1 Clear Filter Sun Essence SPF50+ PA++++ (50ml)",
                    "name_ar": "NUMBUZIN No.1 Clear Filter Sun Essence SPF50+ PA++++ (50ml)",
                    "short_name": "NUMBUZIN No.1 Clear Filter Sun Essence SPF50+ PA++++",
                    "sku": "KC0121",
                    "price": 103,
                    "size": "50ml",
                    "image": "https://cdn.shopify.com/s/files/1/0111/9309/0106/files/sun_essence_main.jpg?v=1762461344",
                    "average_rating": 4,
                    "num_reviews": 24,
                    "brand": {
                        "id": 28,
                        "name": "NUMBUZIN",
                        "image": "https://kcodeskin.com/storage/app/public/brands/01M1EC093CN4AEDB1NQQSE12R0.png"
                    }
                }
            }
        ]
    }
}
