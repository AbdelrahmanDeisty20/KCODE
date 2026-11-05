# الدليل التفصيلي الشامل لحزمة تسليم المطور `KCODE_Developer_R479` (شرح بالحرف)

---

## أولاً: إجابة سؤالك المباشر — هل كل حاجة في الحزمة واضحة ومكملة؟

**نعم، الحزمة واضحة بنسبة 100% ومكتملة برمجيًا وبيانيًا وبصريًا كحزمة تسليم (`R479-handoff`)، ولكن من الضروري التفرقة بين "ما هو مكتمل وجاهز داخل الحزمة" وبين "ما تركته الحزمة عمدًا ليربطه المطور بالباك إند الفعلي للمتجر":**

### 1. ما هو مكتمل 100% داخل الحزمة (جاهز تمامًا ولا يحتاج أي تأليف):
* **بيانات ومعايير ودرجات الـ 256 منتجًا بالكامل:** مقسمة بين 248 منتجًا في `master/KCODE_Product_Master_Final.xlsx` (منها 238 تدخل محرك التحليل و10 للمتجر فقط) + 8 منتجات في `master/KCODE_Out_Of_Scope.xlsx` (للمتجر فقط).
* **النصوص العربية المعتمدة للـ 256 منتجًا (`content/products.json` و`content/compact_pages.json`):** تشمل الاسم، الباركود، الحجم، الوصف المعتمد، الملخص، بطاقات طريقة الاستخدام (`usage_tiles`)، قراءة KCODE للتركيبة (`benefits`)، خلاصة KCODE وملاءمة البشرة الحساسة (`summaryHtml`)، والأسئلة الشائعة لكل منتج.
* **محرك التوصيات الخادمي (`runtime/`):** جميع القواعد الحتمية لاختيار الروتين الأساسي والمكملات للـ **728 سيناريو**، شاملة معالجة نفاد المخزون وتعارض الريتينويدين (`KC0122`/`KC0242`) واستثناء الـ 16 حالة للمقشرات.
* **قوالب وتصميم وتفاعلات الواجهة الأمامية (`website/source/` و`frontend/`):** تشمل **271 صفحة** (15 صفحة متجر + 256 صفحة منتج كاملة) و**1,051 صورة وأصل بصري**، بالإضافة إلى نسختي المعاينة الكاملتين للديسكتوب والموبايل في `website/dist/`.

### 2. ما تركته الحزمة عمدًا ليكمله المطور عند الدمج مع الباك إند الحي:
* **الأسعار النهائية للبيع:** الأسعار الموجودة في المعاينة مؤقتة/توضيحية (خصوصًا المنتجات الجديدة)، ويجب قراءة الأسعار الفعلية بالبيسة العمانية من قاعدة بيانات المتجر.
* **مزود المخزون الخادمي الحي (`inventory_provider`):** المحرك يستقبل حالة توفر المنتجات (`SKU -> true/false`) من قاعدة بيانات المتجر الحقيقية عند كل طلب تحليل، ولا يجوز ترك قائمة ثابتة تفترض توفر الجميع في الإنتاج.
* **ربط عمليات التجارة الإلكترونية الحقيقية:** ربط السلة، الدفع، الشحن، الحسابات، والطلبات بقاعدة بيانات المتجر الفعلي في مشروع Laravel الخاص بك.

---

## ثانياً: خريطة الأرقام الكلية في الحزمة (توزيع الـ 256 منتجًا والـ 728 سيناريو)

* **إجمالي منتجات المتجر المعتمدة للعرض:** **256 منتجًا** (الأرقام من `KC0001` إلى `KC0259` باستثناء 3 أرقام غير مستخدمة هي `KC0056`, `KC0106`, `KC0135`).
* **توزيع الـ 256 منتجًا:**
  1. **238 منتجًا في كتالوج محرك التحليل (`runtime/catalog.json` و`runtime/routing_policy.json`):** هي المنتجات المؤهلة لدخول حسابات الكويز (أساسي أو مكملات).
  2. **10 منتجات في الماستر للمتجر فقط (`store-only`):** موجودة في `KCODE_Product_Master_Final.xlsx` ولكنها غير مفعلة في محرك الكويز وهي: `KC0231`, `KC0232`, `KC0233`, `KC0234`, `KC0240`, `KC0241`, `KC0244`, `KC0246`, `KC0248`, `KC0254`.
  3. **8 منتجات خارج نطاق التحليل (`master/KCODE_Out_Of_Scope.xlsx`):** تُعرض في المتجر فقط وهي: `KC0044`, `KC0054`, `KC0130`, `KC0178`, `KC0196`, `KC0206`, `KC0245`, `KC0253`.
* **المنتجات الـ 43 الفائزة عند توفر كامل المخزون (`content/usage_policies.json` و`content/usage_short.json`):** هي المنتجات الـ 43 التي تظهر فعليًا في نتائج الـ 728 سيناريو القياسية ولها ملخصات استخدام قصيرة وسياسات ترتيب زمني (`morning` / `evening`).

---

## ثالثاً: تشريح المجلدات والملفات ملفًا بملف (بالحرف)

### 1. الجذر الرئيسي للحزمة (`KCODE_Developer_R479/`)
* `README_AR.txt`: تعليمات التسليم الأساسية وخطوات التحقق الـ 8 قبل الدمج.
* `MANIFEST.json`: سجل يضم **2,265 ملفًا** مع بصمة `SHA-256` لكل ملف للتحقق من سلامة الحزمة عبر `tools/check_manifest.py`.
* `requirements-build.txt`: الحزم اللازمة لأدوات الفحص والبناء (`openpyxl>=3.1,<4` و`lxml>=5,<7`).

### 2. مجلد `master/` (مصدر الحقيقة للدرجات والأهلية والباركود)
* `KCODE_Product_Master_Final.xlsx`: يضم ورقة `PRODUCT MASTER` (248 صفًا) وورقة `PRODUCT DETAILS`. يحتوي على الدرجات الـ 12 للأهداف (`Acne`, `Oil Control`, `Pores`, `Pigmentation PIH`, `Redness PIE`, `Radiance`, `Hydration`, `Barrier`, `Lines`, `Eye Dark Circles`, `Eye Puffiness`, `Blackheads`)، ودرجات ملاءمة البشرة الـ 4 (`Fit Oily`, `Fit Combination`, `Fit Normal`, `Fit Dry`)، ودرجة التحمل (`Tolerance`).
* `KCODE_Out_Of_Scope.xlsx`: يضم ورقة `OUT OF SCOPE` (8 صفوف).

### 3. مجلد `content/` (المصدر الوحيد للنصوص العربية المعروضة للعميل)
* `products.json`: بيانات العرض الكاملة للـ 256 منتجًا (`brand`, `name`, `size`, `barcode`, `description`, `brief`, `usage_tiles`, `usage`, `usage_notice_ar`, `benefits`, `summaryHtml`).
* `compact_pages.json`: قوالب HTML الكاملة لنافذة العرض المختصر (Quick View) للـ 256 منتجًا.
* `usage_policies.json`: سياسات الاستخدام والترتيب الزمني للـ 43 منتجًا الفائزة في نتائج الكويز.
* `usage_short.json`: النصوص المختصرة لطريقة الاستخدام (`usage_short_ar`) للـ 43 منتجًا.
* `display_labels.json`: العناوين العربية المعتمدة لأدوار المنتجات في بطاقات النتائج.
* `product_routes.json`: خريطة ربط كل `SKU` باسم صفحته (`product` لـ `KC0001` و`product-KCxxxx` للبقية).
* `image_map.csv`: ربط الـ 256 منتجًا بملفات الصور والنصوص البديلة (`alt`).

### 4. مجلد `runtime/` (المحرك الخادمي وقواعد الـ 728 سيناريو بالحرف)
* `quiz_contract.json`: عقد الأسئلة الـ 4:
  * `primary` (9 أهداف: `acne`, `pores`, `blackheads`, `dark_spots`, `dullness`, `redness`, `dryness`, `lines`, `maintain`).
  * `secondary` (10 أهداف: نفس الـ 8 الأولى بدون `maintain` + `oil` + `eyes` + `null`، مع منع تكرار `primary`).
  * `skin` (4 أنواع: `oily`, `combination`, `normal`, `dry`).
  * `sensitive` (`boolean`: `true` أو `false`).
  * المجموع: `91 زوج أهداف × 4 أنواع بشرة × 2 حساسية = 728 سيناريو`.
* `engine.py`: خوارزمية اختيار الروتين الأساسي (`select`):
  * الأهلية: `fit[skin] >= 60`، وعند الحساسية `tolerance >= 60`.
  * تطبيع `maintain`: إذا اختير مع مشكلة ثانوية، يتحول الهدف الفعال إلى المشكلة الثانوية.
  * دمج الدورين: إذا تصدر منتج واحد للهدفين معًا يخدم الدورين ويُعرض مرة واحدة، وكذلك إن كان العلاج تونرًا.
  * استثناء الزوج الريتينويدي (`KC0122` و`KC0242`): عند اجتماعهما يحتفظ العلاج الأول بمكانه ويُستبدل العلاج الثاني بأعلى بديل مؤهل غير ريتينويدي (`KC0145` أو `KC0001`)، وإذا نفدت جميع البدائل تبقى `treatment2` في `missing`.
* `supplements.py`: خوارزمية اختيار المكملات (`choose`):
  * مستقلة تمامًا عن درجات الأساسي، بحد أقصى 3 مكملات.
  * أولوية الحجز لـ `mask`, `eye_patch`, `acne_patch` ثم المنافسة بين `essence`, `exfoliator`, `mist`.
  * منع الجمع بين `essence` و`mist` معًا.
  * استبعاد المقشرات في الـ 16 حالة المحددة في `approved_exfoliator_exclusions.json` دون تعويض.
* `service.py`: المدخل العام الوحيد `recommend(answers, unavailable=())`.
* `http_adapter.py`: محول WSGI لنقطتي النهاية `GET /api/kcode/quiz-contract` و`POST /api/kcode/recommend`.

### 5. مجلد `frontend/` (منطق العرض والكويز وجدول الاستخدام)
* `quiz.mjs`: دوال `options`, `changePrimary`, `validate`.
* `client.mjs`: دوال `loadContract`, `recommend`.
* `presentation.js`: بناء جدول الاستخدام (`صباحًا` و`مساءً` فقط بدون تقويم 14/28 يومًا)، وترتيب الخطوات حسب `usage_policy.order`، وإظهار تنبيه الليالي المنفصلة عند اجتماع مقشر وريتينويد، وإدارة `RoutineSelection` و`QuizState`.
* `renderer.js`: رسم واجهة الكويز والنتائج والتنقل والسلة وإبراز تنبيه `KC0169`.

### 6. مجلد `website/` (مصدر الموقع الكامل ونسخ المعاينة)
* `website/source/`: يضم `desktop-shell.html`, `mobile-shell.html`, `boot.json`, `meta.json`, `assets.json` + **271 صفحة** في `pages/` + **880 مورد بيانات** في `data/` + **1,051 أصل صورة** في `assets/`.
* `website/dist/`: المعاينات النهائية `KCODE_DESKTOP_R479.html` و`KCODE_MOBILE_ON_DESKTOP_R479.html`.

### 7. مجلد `docs/`, `tools/`, `tests/`
* `docs/`: ملفات التوثيق العربية (`MIGRATION_AR.txt`, `INTEGRATION_AR.txt`, `RULES_AR.txt`, `ACCEPTANCE_AR.txt`, `QA_RESULT_AR.txt`) وسجلات المقارنة (`master_comparison.json`, `master_cell_changes.csv`, `old_file_migration.csv`, `provenance.json`).
* `tools/`: سكربتات `export_master.py`, `build_site.py`, `check_manifest.py`.
* `tests/`: المراجع الذهبية (`golden_728.json`, `ui_results_728.json`, `approved_changes.json`) وسكربتات الفحص (`verify.py`, `handoff.py`, `import_isolation.py`, `stock_exhaustion.py`, `verify_website.py`, `quiz.test.mjs`, `presentation.cjs`).

---

## رابعاً: المقارنة الشاملة الكاملة بين مشروعك الحالي (`c:\myProjects\KCODE`) وحزمة `KCODE_Developer_R479`

بعد فحص مشروعك الحالي بالكامل (`app/`, `database/migrations/`, `database/seeders/`, `routes/api.php`, `exicel/`) ومقارنته بالحزمة الجديدة `KCODE_Developer_R479`، إليك النتيجة الدقيقة لكل طبقة في المشروع:

### 1. جدول المقارنة الشامل (طبقة بطبقة)

| المحور / الطبقة | الوضع الحالي في مشروعك (`c:\myProjects\KCODE`) | الوضع في حزمة التسليم الجديدة (`KCODE_Developer_R479`) | ما يجب تحديثه في مشروعك |
| :--- | :--- | :--- | :--- |
| **الإصدار المرجعي للبيانات** | خليط من 3 إصدارات قديمة: `v16.2/v16.5` (`exicel/kcode_products.json`) + `V24_FINAL` (`BrandSeeder.php`) + `R422` (`QuizQuestionSeeder.php`). | إصدار واحد موحد ونهائي معتمد **`R479-handoff`** يجمع الكتالوج والمحرك والنصوص والواجهة. | توحيد كافة الـ Seeders والخدمات لتقرأ من ملفات `KCODE_Developer_R479`. |
| **عدد المنتجات الإجمالي** | **`218` منتجًا** في `exicel/kcode_products.json` و**`224` منتجًا** في `SyncClientPricesAndSettingsSeeder.php`. | **`256` منتجًا** (`KC0001` إلى `KC0259` باستثناء `KC0056, KC0106, KC0135`): `238` لمحرك التحليل + `10` ماستر للمتجر فقط + `8` خارج النطاق للمتجر فقط. | إضافة الـ **32 منتجًا الجديد** (`KC0228`–`KC0259`) وتحديث بيانات الـ 224 منتجًا الحالية من `content/products.json` والماستر. |
| **قواعد أهلية البشرة والحساسية** | في `ProductSeeder.php`: حد قبول نوع البشرة `fitScore >= 68`، ومعادلة حساسية قديمة (`60` مع تعديلات `-15` للغسول و`+8` للعين و`-10` للبقع). | في `runtime/engine.py`: حد ملاءمة البشرة الموحد **`fit[skin] >= 60`**، وحد الحساسية الموحد **`tolerance >= 60`** بعد تحديث 115 خلية في الماستر (`docs/master_cell_changes.csv`). | تحديث درجات المنتجات الـ 12 ودرجات `Fit` و`Tolerance` من `runtime/catalog.json` وتطبيق شرط `>= 60`. |
| **بيانات صفحات المنتج (PDP) والـ Quick View** | في `KcodePdpFieldsSeeder.php`: يضع **نفس الـ 4 منتجات المكملة الثابتة (`KC0140, KC0016, KC0223, KC0077`)** ونفس **الـ 4 أسئلة شائعة الثابتة** ونفس نصوص طريقة الاستخدام العامة لجميع المنتجات! | في `content/products.json` و`content/compact_pages.json` و`website/source/pages/product-*.txt`: **كل منتج من الـ 256 له نصوص عربية معتمدة خاصة به فرديًا** (بطاقات `usage_tiles`، خطوات الاستخدام، قراءة التركيبة `benefits`، خلاصة KCODE `summaryHtml`، والأسئلة الشائعة والمنتجات المكملة الخاصة به). | تحديث `KcodePdpFieldsSeeder.php` ليستورد البيانات الفردية المعتمدة لكل منتج بدلاً من القيم المكررة الثابتة. |
| **أسئلة الكويز (`QuizQuestionSeeder.php`)** | السؤال الأول يضم **7 خيارات مدمجة**، والسؤال الرابع يضم 9 خيارات + `لا يوجد` دون قواعد منع تكرار إجابة السؤال الأول أو استثناء `maintain`. | في `runtime/quiz_contract.json`: السؤال الأول **9 أهداف منفصلة**، والسؤال الرابع **10 أهداف ثانوية + `null`** (بإضافة `oil` و`eyes`) مع منع تكرار هدف السؤال الأول = **`728` سيناريو**. | تحديث `QuizQuestionSeeder.php` ليطابق عقد `quiz_contract.json` بالحرف. |
| **محرك التوصيات الخادمي (`QuizService.php`)** | في `QuizService.php`: يبحث فقط عن منتجات `is_best_seller = true` ويعطي نقاطًا مبسطة (`+100` للهدف و`+200` للمشكلة و`average_rating * 2`)! **لا يطبق خوارزمية KCODE المعتمدة أصلاً**. | في `runtime/engine.py` و`runtime/supplements.py`: خوارزمية حتمية تطبق أوزان `70%/30%`، التخصص `spec`، دمج الأدوار، منع اجتماع الريتينويدين (`KC0122`/`KC0242`)، استثناء الـ 16 حالة للمقشرات، والتعويض الديناميكي عند نفاد المخزون. | إعادة بناء منطق الاختيار في `QuizService.php` ليطابق منطق `runtime/engine.py` و`runtime/supplements.py` بنسبة 100% (واجتياز اختبار الـ 728 سيناريو في `golden_728.json`). |
| **جدول استخدام الروتين (الصباح والمساء)** | يعتمد على حقول `morning` و`night` عامة في جدول `product_routines`. | في `content/usage_policies.json` و`frontend/presentation.js`: جدول من فترتين فقط (`صباحًا` و`مساءً`) مرتب حسب `order`، مع وضع العلاجات التدريجية المزدوجة صباحًا، وتنبيه الليالي المنفصلة عند اجتماع مقشر وريتينويد. | إرجاع بيانات `usage_policy` و`usage_short_ar` و`notice_ar` (`KC0169`) ضمن استجابة الكويز. |
| **الروتينات الـ 8 الجاهزة وروتينات الرجال الـ 5** | موجودة في `PresetRoutineSeeder.php` و`MenPresetRoutineSeeder.php`، لكن تنقصها **صور الأغلفة الجماعية الـ 13** (8 للروتينات + 5 للرجال)، وفي روتين الرجال الثالث (`tightness`) المرطب `KC0009` مضبوط `مساءً فقط` بينما في `R479` هو `صباحًا ومساءً`، مع تحرير محدّث لنصوص الرجال في `#menData`. | في `website/source/data/615737bab3da422c09fc.txt` (`#curatedData`) و`29329431bbd6ca075eb8.txt` (`#menData`): نصوص نهائية معتمدة مع صور غلاف جماعية (`cover.src` و`cover.srcset`) لكل روتين. | تحديث نصوص روتينات الرجال وتوقيت `KC0009` في `MenPresetRoutineSeeder.php`، وربط صور الأغلفة الجماعية الـ 13 بالروتينات. |
| **صور المنتجات والأصول البصرية** | في `database/seeders/assets/products/`: يوجد **225 ملفًا فقط**، وكثير من المنتجات حتى `KC0224` تستخدم صورًا مؤقتة `KCxxxx_placeholder.svg` (مثل `KC0177, KC0184, KC0185, KC0189, KC0192, KC0198, KC0207, KC0218, KC0219, KC0223, KC0224`). | في `website/source/assets/`: يوجد **`1,051` ملفًا** محسنًا بصيغة `.webp`؛ **جميع الـ 224 منتجًا من `KC0001` حتى `KC0227` لها صور حقيقية عالية الدقة** بـ 4 مقاسات متجاوبة ومعرض صور متعدد الزوايا، وفقط الـ 32 منتجًا الجديدة (`KC0228`–`KC0259`) تستخدم الصورة المؤقتة الموحدة. | استبدال صور المنتجات في المشروع بالصور الحقيقية الجديدة من `R479` (باستخدام خريطة `externalProductMedia` في `boot.json`). |

### 2. ما هو موجود ومكتمل في مشروعك الحالي (`c:\myProjects\KCODE`) ولا يحتاج استبدالاً (لأن `R479` يعتمد عليه كباك إند):
1. **لوحة تحكم الإدارة الكاملة (Filament Admin Panel)** في `app/Filament/Resources/*`.
2. **نظام المصادقة والحسابات (`AuthController`, `ForgetPasswordController`, `Sanctum`, `OTP`).**
3. **نظام السلة والطلبات والشحن (`CartService`, `CheckoutService`, `OrderService`, `LocationSeeder`, `CityShippingFeeSeeder`).**
4. **نظام الولاء والنقاط والكوبونات والعروض (`LoyaltyService`, `CouponService`, `OfferService`).**
5. **المدونة، الشات بوت الذكي، وإشعارات التطبيقات (`BlogService`, `ChatbotService`, `FirebaseNotificationService`).**
6. **الفئات الـ 10 والماركات الـ 22 ودليل العناية (`CategorySeeder`, `BrandSeeder`, `CareGuideSeeder`):** متطابقة بالفعل في العدد والأسماء مع حزمة `R479`.

---

## خامساً: خريطة الصور والأصول البصرية الكاملة (`1,051` ملف — المنتجات وجميع أقسام الموقع)

### 1. كيف تُخزن الصور داخل الحزمة وكيف تُربط بالصفحات؟
* جميع الملفات البصرية الفعلية موجودة داخل المجلد `website/source/assets/` وعددها **`1,051` ملفًا** بصيغ `.webp`, `.png`, `.svg`, `.woff`, `.woff2`.
* كل ملف في مجلد `assets/` مُسمى ببصمة سداسية عشرية مكونة من **20 حرفًا** (مثل `96ef71fc1eb210d99972.webp`).
* الملف `website/source/assets.json` يربط كل بصمة (`<20-hex-hash>`) باسم الملف ونوع الـ MIME الخاص به.
* داخل ملفات الصفحات (`website/source/pages/*.txt`) وملفات البيانات (`website/source/data/*.txt`)، تُستدعى أي صورة بالرمز: `KCODE_ASSET_<20-hex-hash>`.

### 2. هل كل صورة مربوطة بمنتجها بالاسم والـ SKU؟ وكيف أعرف كل صورة لأي منتج؟
**نعم بنسبة 100%!** على الرغم من أن أسماء الملفات في مجلد `assets/` مشفرة بـ Hash، فإن الحزمة تحتوي على **5 مصادر خرائط رسمية** تكشف اسم كل صورة، والمنتج أو القسم التابع لها، والزاوية، والدقة:

1. **قاموس الأسماء الأصلية المقروءة في `website/source/boot.json` (`externalProductMedia` — الأسطر `106271` إلى `107284`):**
   * يترجم **`1,012` بصمة Hash** إلى **اسم الملف الإنجليزي الأصلي المقروء بالكامل**، والذي يبدأ مباشرة بـ `SKU` المنتج متبوعًا بالماركة واسم المنتج والحجم وزاوية التصوير (`front`, `with-box`, `open-jar`, `open-applicator`, `four-sachets`) والدقة (`160`, `480`, `768`, `1200`, أو الدقة الكاملة).
   * مثال لمنتج `KC0001`:
     * `"49f3230149a2469d6af7"`: `"kc0001-anua-niacinamide-10-txa-4-serum-30ml-front-160.webp"`
     * `"96ef71fc1eb210d99972"`: `"kc0001-anua-niacinamide-10-txa-4-serum-30ml-front-480.webp"`
     * `"90ee9f1f6787ee11e9bf"`: `"kc0001-anua-niacinamide-10-txa-4-serum-30ml-front-768.webp"`
     * `"454dce4665f3100ef843"`: `"kc0001-anua-niacinamide-10-txa-4-serum-30ml-front.webp"`
     * `"3ac6d0cfa2584565c648"`: `"kc0001-anua-niacinamide-10-txa-4-serum-30ml-with-box-160.webp"`
     * `"f73b371f9411e8f01c3d"`: `"kc0001-anua-niacinamide-10-txa-4-serum-30ml-with-box-480.webp"`
     * `"d348a3399bcb557b1c5a"`: `"kc0001-anua-niacinamide-10-txa-4-serum-30ml-with-box-768.webp"`
     * `"dbcd68fac403081a8c36"`: `"kc0001-anua-niacinamide-10-txa-4-serum-30ml-with-box.webp"`
2. **كائن بيانات الكتالوج ومعرض الصور الكامل في `website/source/data/d744fcce8be90ef861ba.txt` (`#catalogData`):**
   * يحتوي لكل منتج (`id: "KC0001"` إلى `KC0259`) على الحقول الجاهزة: `image` (صورة البطاقة الأساسية `480w`)، `imageSrcset`، `imageAlt`، `imageAltEn`، `imageApproval`، ومصفوفة `gallery` التي تضم كافة صور المعرض (`src` بالدقة الكاملة، `thumb` بمقاس `160w`، `card` بمقاس `480w`، و`srcset`).
   * المنتجات التي لم تُصور بعد (مثل `KC0228`–`KC0259`) تستخدم الصورة المؤقتة `e3baa5876dd50fcccd3f.svg` (`"صورة المنتج قيد التجهيز"`).
3. **قاموس صور الكويز في `website/source/data/6d67bb1c24268760319e.txt` (`#quizImages`):**
   * مفهرس مباشرة برقم `SKU` لكل منتج مصور لاستخدامه المباشر في صفحة نتائج الكويز.
4. **توضيح بخصوص `content/image_map.csv`:**
   * هذا الملف استخرج فقط وسوم `<img>` الثابتة في HTML الصفحات (والتي تمثل في صفحة المنتج شعار الهيدر/الفوتر + الـ 4 منتجات المقترحة في قسم "أكمل روتينك" أسفل الصفحة)، بينما معرض المنتج نفسه يُركّب ديناميكيًا عبر `<div class="kp-gallery-mount" data-gallery-product="KCxxxx"></div>` من `#catalogData`.

### 3. خريطة جميع الصور غير المنتجات (الأقسام، الماركات، الروتينات، الرجالي، والشعارات)
1. **صور فئات المنتجات الـ 10 (`#navigationAssets` في `website/source/data/748594284f75d558d3b6.txt`):**
   * غسول (`cleanser`): `91bfd16c48cbbafe47fd.webp`
   * تونر (`toner`): `657d4ec9cb752023e5e5.webp`
   * إسنس (`essence`): `7fc55473581b0bf59ecc.webp`
   * سيروم وأمبول (`serum`): `704c920cb29d05a9fb3e.webp`
   * مرطب (`moisturizer`): `a7d2c74453f7a65d065c.webp`
   * واقي شمس (`sunscreen`): `808d9f086770b1087dc6.webp`
   * العناية بالعين (`eye`): `62df44eeba7201fc41cb.webp`
   * مقشر (`exfoliator`): `457640250a025a68ff99.webp`
   * ماسكات (`mask`): `6801de33d076a43403b8.webp`
   * العناية بالجسم (`body`): `49ffc92ca55d00736b33.webp`
2. **شعارات الـ 22 ماركة كورية (`brand_logos` في `website/source/data/748594284f75d558d3b6.txt`):**
   * `ABIB` (`30261dc815400d510ff7.png`)، `ANUA` (`b6cff0dcea1657b7d776.svg`)، `Arencia` (`2771070d21474d539bd3.webp`)، `AXIS-Y` (`fac75d639c35d519d469.png`)، `Beauty of Joseon` (`152c570e844d5d3514e7.png`)، `BIODANCE` (`66876bb4d7d1ba50298d.png`)، `celimax` (`6bce599fd56b4451ef1b.png`)، `COSRX` (`95675988c9252005450a.svg`)، `Dr.Althea` (`bd1d1cdac4d893c2a1d7.png`)، `Dr.Melaxin` (`bb00a8956732065474ef.webp`)، `Dr.Reju-All` (`d5209a09c597dde1f6ee.svg`)، `EQQUALBERRY` (`73007e225b37727e94d9.png`)، `ISNTREE` (`20dac568152be7e46480.png`)، `Mary&May` (`87582af74717a8e63f18.png`)، `Medicube` (`79396a25f30e16866766.png`)، `MIXSOON` (`2320ff54b1268480b31a.png`)، `NUMBUZIN` (`a3cf2668d0f0d0886121.png`)، `PURITO` (`8cff45d7431439509357.svg`)، `SKIN1004` (`afc9fce5ed38d764a5d3.svg`)، `SOME BY MI` (`6954989d6c12981bd514.svg`)، `TORRIDEN` (`33a52f0cfe1924117f9d.png`)، `VT COSMETICS` (`28598c6d8616e3b1cc6e.svg`).
3. **الصور الجماعية للروتينات الجاهزة الـ 8 (`#curatedData` في `website/source/data/615737bab3da422c09fc.txt`):**
   * `acne` (`0d9d479ecb1f709ed0e7.webp`)، `pores` (`72d3caff33e95e8abddf.webp`)، `oily-hydration` (`aa0c60ac3efa6db8532b.webp`)، `pigmentation` (`a9560f582fd173ff8169.webp`)، `barrier` (`48056dbb8bed1d0b5ee2.webp`)، `dry-hydration` (`92fe8ab38583b477736e.webp`)، `lines` (`a23337aa0ce55a21aad8.webp`)، `daily` (`44b1bd5050eebbfa693e.webp`) — وكل منها متوفر بـ 4 مقاسات متجاوبة.
4. **الصور الجماعية لبطاقات قسم العناية بالرجل الـ 5 (`#men` في `home.txt` و`men.txt`):**
   * `daily` (`a2348ed5482efe93275b.webp`)، `shine` (`6fdd38f3bc19a2c8f020.webp`)، `tightness` (`1478f81f1c74d6bd8771.webp`)، `dullness` (`ba6e16c17e3b6b4ac757.webp`)، `shaving` (`5ecf41d45afaa2941a8d.webp`) — وكل منها متوفر بـ 4 مقاسات متجاوبة.
5. **شعارات الهوية والصورة الافتراضية:**
   * شعار الهيدر (`98fd1dde252d16e10d36.svg`)، شعار الفوتر (`296ec700cdf31888e79b.svg`)، شعار نواة التحليل الذكي (`c0be2ad7d7eea5377a4e.png`)، والصورة المؤقتة للمنتجات قيد التجهيز (`e3baa5876dd50fcccd3f.svg`).


