const fs = require('fs');

const rawFindings = JSON.parse(fs.readFileSync('scratch_all_findings.json', 'utf8'));

// Detailed backend mapping and action plan
const backendMapping = {
  F01: {
    category: 'backend',
    categoryLabel: 'باك إند (مهمتنا الأساسية)',
    filePaths: [
      'app/Services/QuizService.php',
      'app/Services/R479EngineService.php',
      'app/Http/Controllers/API/QuizController.php'
    ],
    backendAction: `1. فحص دالة حل الإجابات (resolveAnswers / resolveQuiz) والتأكد من مطابقة الـ Option ID مع الكود الدلالي للهدف والمشكلة (dullness, dryness, acne, oiliness, ... إلخ).
2. إزالة أي تداخل بين معرّف الخيار (option_id) ومعرّف الهدف (goal_id / concern_id).
3. التأكد من أن المفتاح الناتج scenario_key يطابق تماماً المفاتيح الـ 728 في ملف QUIZ_EXPECTED_728.json.
4. التأكد من أن المنتجات الأساسية والمكملة وترتيب الخطوات 1، 2، 3، 4، 5 تخرج مطابقة 100% للمرجع.`,
    testGuide: `تشغيل سكريبت فحص آلي على ملف QUIZ_EXPECTED_728.json والتأكد من نجاح 728/728 سيناريو دون أي اختلاف.`
  },
  F02: {
    category: 'backend',
    categoryLabel: 'باك إند (مهمتنا الأساسية)',
    filePaths: [
      'app/Services/R479EngineService.php',
      'app/Http/Resources/API/Routines/RoutineDetailResource.php',
      'database/seeders/RoutineSeeder.php'
    ],
    backendAction: `1. فحص جداول الروتينات الجاهزة الثمانية (8 Preset Routines) في قاعدة البيانات وخدمة الروتينات.
2. إلغاء توحيد التوقيت (am: true, pm: true, daily) لجميع الـ 40 منتجاً.
3. استعادة جدول توقيتات R479 المعتمد:
   - واقيات الشمس (Sunscreen) تظهر في الصباح فقط (AM فقط) وممنوع منعاً باتاً ظهورها في المساء (PM).
   - منتجات التقشير القوية والأحماض والريتينول تظهر في المساء فقط (PM فقط) أو بتكرار 2-3 مرات أسبوعياً، وليس يومياً صباحاً ومساءً.`,
    testGuide: `استدعاء API الروتينات الجاهزة والتأكد من خلو المساء من أي واقي شمس، وعدم ظهور مقشرات قوية صباحاً بشكل يومي.`
  },
  F03: {
    category: 'backend',
    categoryLabel: 'باك إند (مهمتنا الأساسية)',
    filePaths: [
      'app/Services/ChatbotService.php',
      'app/Http/Controllers/API/ChatbotController.php'
    ],
    backendAction: `1. ربط الـ AI Chatbot بقاعدة بيانات المنتجات الفعلية (Product Master) بناءً على كود الـ SKU.
2. تعديل System Prompt للشات بوت لمنعه منعاً باتاً من تأليف أو اختراع وظائف للمنتجات أو طريقة استعمال من خياله.
3. تعريف المنتج KC0159 بشكل صريح كـ واقي شمس كيميائي خفيف (COSRX Ultra Light Invisible Sunscreen SPF50) بدلاً من وصفه كغسول رغوي يُشطف!
4. عند السؤال عن كود غير موجود أو منتج غير متوفر، يرد البوت بصراحة: "البيانات غير متوفرة لهذا المنتج" ولا يخترع تعليمات.`,
    testGuide: `اختبار الشات بوت بسؤال: "ما هو منتج KC0159 وكيف أستخدمه؟" والتأكد من رده بأنه واقي شمس وخطوات استخدامه الصباحية.`
  },
  F04: {
    category: 'backend_shared',
    categoryLabel: 'باك إند + فرونت إند',
    filePaths: [
      'app/Services/R479EngineService.php',
      'app/Http/Resources/API/Quiz/QuizResultResource.php'
    ],
    backendAction: `1. في نتيجة الكويز، التأكد من إرجاع بيانات الاستخدام والجدول الزمني المخصص لكل منتج مكمّل (Support Product) ضمن الـ Payload.
2. عند إضافة مكمل مثل KC0185 (ماسك الترطيب)، يتم توفير بيانات توقيته (مثلاً: أسبوعياً مساءً) وتنبيهاته لتمكين الواجهة من تحديث الجدول على الفور.`,
    testGuide: `التحقق من أن استجابة API الكويز تحتوي على schedule و warnings لكل منتج مكمل.`
  },
  F05: {
    category: 'frontend_shared',
    categoryLabel: 'فرونت إند (مع دعم الباك)',
    filePaths: [
      'app/Http/Controllers/API/CouponController.php',
      'routes/api.php'
    ],
    backendAction: `1. التأكد من أن مسار فحص وتطبيق الكوبون POST /api/v1/coupons/apply يقبل سلة الضيف (Guest Cart عبر guest_token أو session) ولا يطلب auth:sanctum إجباري.
2. في حال كان الكوبون خاطئاً أو خاصاً بالأعضاء فقط، يتم إرجاع كود 422 مع رسالة واضحة بدلاً من 401 Unauthorized، حتى لا يقوم الفرونت إند بتفريغ السلة.`,
    testGuide: `إرسال طلب فحص كوبون خاطئ كضيف والتأكد من استلام 422 وعدم حدوث 401.`
  },
  F06: {
    category: 'frontend_shared',
    categoryLabel: 'فرونت إند (مع دعم الباك)',
    filePaths: [
      'app/Http/Controllers/API/CheckoutController.php',
      'routes/api.php'
    ],
    backendAction: `1. التأكد من أن مسارات الشراء وإنشاء الطلب تدعم خيار زائر (guest_checkout: true) ببيانات الاسم ورقم الهاتف والعنوان دون إجبار العميل على التسجيل.`,
    testGuide: `إنشاء طلب تجريبي كامل بدون Bearer Token بنجاح.`
  },
  F07: {
    category: 'backend_shared',
    categoryLabel: 'باك إند + فرونت إند',
    filePaths: [
      'app/Services/CartService.php',
      'app/Http/Controllers/API/CartController.php'
    ],
    backendAction: `1. في دالة إضافة الروتين إلى السلة (addRoutineToCart)، تطبيق مبدأ (Idempotency / Upsert):
2. إذا تم استدعاء إضافة الروتين وكان المنتج موجوداً بالفعل في السلة من الروتين نفسه، يتم تثبيت الكمية على 1 وليس جمعها (1+1=2)، إلا إذا طلب المستخدم صراحة زيادة الكمية يدوياً من السلة.`,
    testGuide: `استدعاء دالة إضافة الروتين مرتين متتاليتين والتأكد من أن الكميات الإجمالية للمنتجات 6 وليست 12.`
  },
  F08: {
    category: 'frontend',
    categoryLabel: 'فرونت إند فقط',
    filePaths: ['React: QuickViewModal.tsx'],
    backendAction: `مسؤولية مطور الواجهة (Frontend): تصفير حالة الـ Modal وعرض Skeleton Loader أثناء جلب بيانات المنتج الجديد بدلاً من عرض المنتج السابق.`,
    testGuide: `ملاحظة انتقال المعاينة السريعة بين منتجين مختلفين بسرعة.`
  },
  F09: {
    category: 'backend',
    categoryLabel: 'باك إند (مهمتنا)',
    filePaths: [
      'app/Http/Controllers/API/ProductController.php',
      'app/Services/ProductService.php'
    ],
    backendAction: `1. فحص دالة البحث عن المنتجات في الـ API.
2. عندما لا يوجد أي منتج يطابق كلمة البحث، يجب أن تُرجع الـ API مصفوفة فارغة data: [] مع total: 0 ورسالة مناسبة.
3. منع السيرفر تماماً من تطبيق أي Fallback عشوائي يُرجع منتجات عشوائية (زي منتجات ANUA) عند فشل البحث.`,
    testGuide: `البحث عن كلمة عشوائية مثل 'xyznonexistent123' والتأكد من أن النتيجة data: [] والعدد صفر.`
  },
  F10: {
    category: 'backend',
    categoryLabel: 'باك إند (مهمتنا)',
    filePaths: [
      'database/seeders/ReviewSeeder.php',
      'database/migrations/...'
    ],
    backendAction: `1. تنظيف قاعدة البيانات من الـ 2,928 تقييماً الوهمية والمكررة (الأسماء الأجنبية والمكررة مثل John Doe وسارة علي).
2. الإبقاء فقط على التقييمات الحقيقية المعتمدة أو تصفير جدول reviews والبدء بسيدر تقييمات واقعي معتمد.
3. التأكد من أن ReviewResource لا يعرض تقييمات مكررة لنفس المستخدم.`,
    testGuide: `فحص عدد السجلات في جدول reviews والتحقق من أن الأسماء والنصوص واقعية وغير مكررة.`
  },
  F11: {
    category: 'owner',
    categoryLabel: 'قرار مالك + إعدادات باك',
    filePaths: ['config/company.php', 'database/seeders/SettingSeeder.php'],
    backendAction: `طلب بيانات السجل التجاري (CR) والرقم الضريبي الحقيقيين ورقم الهاتف الرسمي من المالك، واستبدال الأصفار 0000000000 بالبيانات الصحيحة في إعدادات النظام.`,
    testGuide: `ظهور الأرقام الحقيقية في الفوتر ونهاية الفاتورة.`
  },
  F12: {
    category: 'owner',
    categoryLabel: 'قرار مالك + إعدادات باك',
    filePaths: ['database/seeders/SettingSeeder.php', 'app/Services/ShippingService.php'],
    backendAction: `1. تعديل سياسة الاسترجاع في قاعدة البيانات والمحتوى لتكون 7 أيام طبقاً لمرجع R479 (وليس 14 يوماً).
2. إدخال تسعيرة الشحن الرسمية لسلطنة عمان والإمارات والسعودية في جدول الشحن (Shipping Rates).`,
    testGuide: `فحص رد الـ API لشروط الشحن وسياسة الاسترجاع.`
  },
  F13: {
    category: 'backend',
    categoryLabel: 'باك إند (مهمتنا)',
    filePaths: [
      'database/seeders/HomePageSeeder.php',
      'app/Http/Controllers/API/HomeController.php'
    ],
    backendAction: `1. مطابقة الـ 8 منتجات المعروضة في الصفحة الرئيسية بالمنتجات المعتمدة في ملف R479 (home.txt).
2. تحديث جدول الأسئلة الشائعة (FAQs) في الداتابيز لتطابق الـ 6 أسئلة المعتمدة وإجاباتها الرسمية.`,
    testGuide: `استدعاء GET /api/v1/home ومقارنة المنتجات والأسئلة بالملف المعتمد.`
  },
  F14: {
    category: 'frontend_shared',
    categoryLabel: 'فرونت إند + باك إند',
    filePaths: [
      'app/Http/Resources/API/Products/ProductDetailResource.php'
    ],
    backendAction: `1. التأكد من أن ProductDetailResource يُرجع تفاصيل طريقة الاستخدام مع عناوينها المخصصة (label و text) لكل خطوة (مثلاً: "طريقة الاستخدام"، "التكرار"، "نصيحة إضافية")، لتمكين الفرونت من عرضها ديناميكياً بدلاً من العناوين الثابتة.`,
    testGuide: `فحص JSON تفاصيل المنتج وملاحظة حقول usage_steps.`
  },
  F15: {
    category: 'backend',
    categoryLabel: 'باك إند (مهمتنا)',
    filePaths: [
      'database/seeders/ProductSeeder.php',
      'app/Services/R479EngineService.php'
    ],
    backendAction: `1. تحديث حقل ملاءمة البشرة الحساسة لـ 22 منتجاً فعالاً في قاعدة البيانات.
2. تعيين الحالة إلى "مناسب بشرط" (suitable_with_caution) بدلاً من "غير مناسب"، مع إضافة تعليمات التدرج المطلوبة في R479.`,
    testGuide: `فحص المنتجات الـ 22 والتأكد من مطابقة حالة الحساسية للمرجع.`
  },
  F16: {
    category: 'backend',
    categoryLabel: 'باك إند (مهمتنا)',
    filePaths: [
      'database/seeders/ProductSpecificationSeeder.php',
      'app/Http/Resources/API/Products/ProductDetailResource.php'
    ],
    backendAction: `1. استعادة مواصفات واقيات الشمس في جدول المواصفات (تحديد نوع الفلتر: كيميائي / فيزيائي معدني / هجين).
2. استعادة ملاحظات قائمة المكونات (INCI notes) للمنتجات المحددة في التقرير.`,
    testGuide: `فحص مواصفات KC0159 وواقيات الشمس الأخرى في الـ API.`
  },
  F17: {
    category: 'frontend_shared',
    categoryLabel: 'فرونت إند + باك إند',
    filePaths: [
      'app/Http/Resources/API/Products/ProductDetailResource.php'
    ],
    backendAction: `1. فحص حقل gallery أو sub_images في ProductDetailResource: التأكد من أنه لا يحتوي على الصورة الرئيسية مكررة مرة أخرى داخله، بل يحتوي فقط على الصور الإضافية الفرعية.`,
    testGuide: `فحص استجابة تفاصيل أي منتج والتأكد من عدم تكرار رابط الصورة الرئيسية داخل مصفوفة الجاليري.`
  },
  F18: {
    category: 'frontend',
    categoryLabel: 'فرونت إند فقط',
    filePaths: ['React: ProductImage.tsx'],
    backendAction: `مهمة فرونت إند: الباك إند بالفعل يرسل حقل seo.image_alt في الـ API، والمطلوب من الفرونت استخدام هذا الحقل في وسم <img alt="..."> بدلاً من الـ slug الإنجليزي.`,
    testGuide: `فحص كود الـ HTML في المتصفح والتأكد من وجود النص البديل العربي.`
  },
  F19: {
    category: 'backend_shared',
    categoryLabel: 'باك إند + فرونت إند',
    filePaths: [
      'app/Http/Controllers/SitemapController.php',
      'routes/web.php'
    ],
    backendAction: `1. تحديث دالة توليد خريطة الموقع sitemap.xml لتستخدم رابط الدومين الرسمي المعتمد (https://kcodeskin.com) لجميع الصفحات والمنتجات والمدونات.
2. تضمين وسوم الـ Canonical الصحيحة.`,
    testGuide: `فتح /sitemap.xml وفحص الدومينات وروابط المنتجات.`
  },
  F20: {
    category: 'frontend',
    categoryLabel: 'فرونت إند فقط',
    filePaths: ['React: FooterNewsletter.tsx'],
    backendAction: `مهمة فرونت إند: تصحيح الرابط التشعبي في فوتر النشرة البريدية ليوجه إلى /privacy-policy بدلاً من مسار مكسور يعطي 404.`,
    testGuide: `الضغط على رابط سياسة الخصوصية بالفوتر.`
  },
  F21: {
    category: 'backend',
    categoryLabel: 'باك إند (مهمتنا)',
    filePaths: [
      'app/Services/R479EngineService.php',
      'app/Http/Resources/API/Quiz/QuizResultResource.php'
    ],
    backendAction: `1. في نتائج التحليل والروتينات، استخدام نص support_copy المعتمد في R479 للمنتجات المكملة بدلاً من استبداله بالنص التسويقي العام للمنتج.
2. توضيح دور المكمل بدقة للمستخدم (مثلاً: "ماسك ترطيب عام اختياري؛ ليس علاجاً للمشكلة المختارة").`,
    testGuide: `فحص حقل reason / support_copy للمكملات في نتيجة الكويز.`
  },
  F22: {
    category: 'frontend',
    categoryLabel: 'فرونت إند فقط',
    filePaths: ['React: SupportProductCard.tsx'],
    backendAction: `مهمة فرونت إند: تحسين التمييز البصري لزر اختيار المكمل (Active State) وضبط خاصية aria-pressed="true" عند التحديد.`,
    testGuide: `معاينة شكل الزر عند اختياره وعدم اختياره.`
  },
  F23: {
    category: 'owner_frontend',
    categoryLabel: 'فرونت إند + قرار مالك',
    filePaths: ['React: locales/en.json'],
    backendAction: `مهمة مشتركة: إما ترجمة المحتوى بالكامل بشكل احترافي معتمد، أو بناءً على قرار المالك تعطيل مبدل اللغة الإنجليزية مؤقتاً لحين اعتماد الترجمة.`,
    testGuide: `التحقق من عدم وجود نصوص عربية مختلطة في النسخة الإنجليزية.`
  },
  F24: {
    category: 'frontend',
    categoryLabel: 'فرونت إند فقط',
    filePaths: ['React: MobileDrawer.tsx'],
    backendAction: `مهمة فرونت إند: إضافة مستمع لحدث الكيبورد (KeyboardEvent: Escape) لإغلاق القائمة الجانبية للموبايل وإرجاع التركيز (Focus) لزر الفتح.`,
    testGuide: `فتح القائمة والضغط على زر ESC من الكيبورد.`
  },
  F25: {
    category: 'frontend',
    categoryLabel: 'فرونت إند فقط',
    filePaths: ['React: MenRoutineCard.tsx'],
    backendAction: `مهمة فرونت إند: ربط حقل skinLabel بدلاً من audience في بطاقة روتين الرجال في الصفحة الرئيسية.`,
    testGuide: `معاينة بطاقة روتين الرجال في الصفحة الرئيسية.`
  },
  F26: {
    category: 'backend',
    categoryLabel: 'باك إند (مهمتنا)',
    filePaths: [
      'database/seeders/BlogSeeder.php',
      'public/storage/blog/...'
    ],
    backendAction: `1. فحص روابط الصور الخاصة بالمقالات السبعة في جدول blogs/articles وتصحيحها لتشير إلى صور موجودة فعلياً في public storage بدلاً من روابط مكسورة 404.
2. مراجعة محتوى ونصوص المقالات والمؤلفين واعتمادها.`,
    testGuide: `فتح صفحة المقالات والدليل والتأكد من تحميل صور المقالات السبعة بنجاح دون أي خطأ 404.`
  },
  N01: {
    category: 'backend_shared',
    categoryLabel: 'باك إند + فرونت إند',
    filePaths: [
      'app/Http/Resources/API/Products/ProductResource.php',
      'app/Services/PricingService.php'
    ],
    backendAction: `1. توحيد صيغة ومعادلة احتساب الخصومات عبر جميع نقاط الاتصال (المنتج الفردي، الروتين، السلة).
2. التأكد من عدم تطبيق الخصم مرتين (مثلاً نسبة 50% تظهر كـ 75% خصم).
3. توحيد صيغة رمز العملة (ريال عماني / OMR) في جميع مخرجات الـ API.`,
    testGuide: `التحقق من حساب منتج سعره 10 وخصمه 50% أنه يخرج 5.000 OMR في كل الصفحات.`
  }
};

// Merge into structured list
const enrichedFindings = rawFindings.map(f => {
  const map = backendMapping[f.id] || {
    category: 'other',
    categoryLabel: 'أخرى',
    filePaths: [],
    backendAction: f.fields['الإصلاح المحدد'] || '',
    testGuide: f.fields['إعادة الاختبار'] || ''
  };
  return {
    ...f,
    ...map
  };
});

// Write HTML template
const html = `<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>خطة عمل الباك إند الشاملة | KCODE Audit R479</title>
  <style>
    :root {
      --primary: #762d48;
      --primary-dark: #541d32;
      --primary-light: #f7eff3;
      --accent: #d97706;
      --danger: #dc2626;
      --success: #16a34a;
      --info: #2563eb;
      --bg: #f8fafc;
      --card-bg: #ffffff;
      --text: #1e293b;
      --text-muted: #64748b;
      --border: #e2e8f0;
      --border-dark: #cbd5e1;
    }

    * {
      box-sizing: border-box;
      margin: 0;
      padding: 0;
    }

    body {
      font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, Tahoma, sans-serif;
      background-color: var(--bg);
      color: var(--text);
      line-height: 1.7;
      padding-bottom: 80px;
    }

    header {
      background: linear-gradient(135deg, #762d48 0%, #4a1529 100%);
      color: white;
      padding: 35px 20px;
      box-shadow: 0 4px 20px rgba(118, 45, 72, 0.25);
    }

    .container {
      max-width: 1240px;
      margin: 0 auto;
      padding: 0 20px;
    }

    .header-content {
      display: flex;
      justify-content: space-between;
      align-items: center;
      flex-wrap: wrap;
      gap: 20px;
    }

    .header-title h1 {
      font-size: 28px;
      font-weight: 800;
      margin-bottom: 6px;
      letter-spacing: -0.5px;
    }

    .header-title p {
      color: #f3d7e2;
      font-size: 15px;
    }

    .header-badge {
      background: rgba(255, 255, 255, 0.15);
      border: 1px solid rgba(255, 255, 255, 0.3);
      padding: 8px 16px;
      border-radius: 999px;
      font-size: 13px;
      backdrop-filter: blur(5px);
    }

    /* Stats Grid */
    .stats-grid {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
      gap: 16px;
      margin: -25px auto 30px;
    }

    .stat-card {
      background: var(--card-bg);
      border-radius: 12px;
      padding: 18px 20px;
      box-shadow: 0 4px 12px rgba(0,0,0,0.05);
      border: 1px solid var(--border);
      border-top: 4px solid var(--primary);
      transition: transform 0.2s;
    }

    .stat-card:hover {
      transform: translateY(-2px);
    }

    .stat-card.p0 { border-top-color: var(--danger); }
    .stat-card.backend { border-top-color: var(--primary); }
    .stat-card.frontend { border-top-color: var(--info); }
    .stat-card.owner { border-top-color: var(--accent); }

    .stat-number {
      font-size: 32px;
      font-weight: 800;
      line-height: 1.1;
      margin-bottom: 4px;
    }

    .stat-label {
      color: var(--text-muted);
      font-size: 13px;
      font-weight: 600;
    }

    /* Filter Controls */
    .controls-wrapper {
      background: white;
      padding: 16px 20px;
      border-radius: 12px;
      border: 1px solid var(--border);
      box-shadow: 0 2px 8px rgba(0,0,0,0.03);
      margin-bottom: 25px;
      display: flex;
      flex-wrap: wrap;
      justify-content: space-between;
      align-items: center;
      gap: 15px;
    }

    .filter-tabs {
      display: flex;
      flex-wrap: wrap;
      gap: 8px;
    }

    .tab-btn {
      background: #f1f5f9;
      border: 1px solid var(--border);
      color: var(--text-muted);
      padding: 8px 16px;
      border-radius: 8px;
      font-size: 14px;
      font-weight: 600;
      cursor: pointer;
      transition: all 0.2s;
    }

    .tab-btn:hover {
      background: #e2e8f0;
      color: var(--text);
    }

    .tab-btn.active {
      background: var(--primary);
      color: white;
      border-color: var(--primary);
      box-shadow: 0 2px 8px rgba(118, 45, 72, 0.3);
    }

    .search-box {
      flex: 1;
      min-width: 250px;
      max-width: 350px;
      position: relative;
    }

    .search-box input {
      width: 100%;
      padding: 9px 14px;
      border-radius: 8px;
      border: 1px solid var(--border-dark);
      font-size: 14px;
      outline: none;
    }

    .search-box input:focus {
      border-color: var(--primary);
      box-shadow: 0 0 0 3px rgba(118, 45, 72, 0.15);
    }

    /* Findings List */
    .findings-container {
      display: flex;
      flex-direction: column;
      gap: 18px;
    }

    .finding-card {
      background: var(--card-bg);
      border-radius: 12px;
      border: 1px solid var(--border);
      box-shadow: 0 2px 8px rgba(0,0,0,0.04);
      overflow: hidden;
      transition: all 0.2s;
    }

    .finding-card:hover {
      border-color: #cbd5e1;
      box-shadow: 0 6px 16px rgba(0,0,0,0.07);
    }

    .finding-card.P0 {
      border-right: 6px solid var(--danger);
    }
    .finding-card.P1 {
      border-right: 6px solid #d97706;
    }
    .finding-card.P2 {
      border-right: 6px solid #94a3b8;
    }
    .finding-card.TECH {
      border-right: 6px solid #6366f1;
    }

    .card-header {
      padding: 16px 20px;
      background: #ffffff;
      border-bottom: 1px solid var(--border);
      display: flex;
      justify-content: space-between;
      align-items: flex-start;
      gap: 15px;
      cursor: pointer;
    }

    .card-header-left {
      display: flex;
      align-items: center;
      gap: 12px;
      flex-wrap: wrap;
    }

    .badge-id {
      background: #1e293b;
      color: white;
      font-weight: 700;
      font-size: 13px;
      padding: 4px 10px;
      border-radius: 6px;
      font-family: monospace;
    }

    .badge-p0 {
      background: #fee2e2;
      color: #991b1b;
      font-size: 12px;
      font-weight: 700;
      padding: 4px 8px;
      border-radius: 6px;
      border: 1px solid #fecaca;
    }

    .badge-p1 {
      background: #fef3c7;
      color: #92400e;
      font-size: 12px;
      font-weight: 700;
      padding: 4px 8px;
      border-radius: 6px;
      border: 1px solid #fde68a;
    }

    .badge-p2 {
      background: #f1f5f9;
      color: #475569;
      font-size: 12px;
      font-weight: 700;
      padding: 4px 8px;
      border-radius: 6px;
    }

    .badge-category {
      font-size: 12px;
      font-weight: 700;
      padding: 4px 10px;
      border-radius: 6px;
    }

    .badge-category.backend {
      background: #fdf2f8;
      color: #9d174d;
      border: 1px solid #fbcfe8;
    }

    .badge-category.backend_shared {
      background: #eff6ff;
      color: #1e40af;
      border: 1px solid #bfdbfe;
    }

    .badge-category.frontend, .badge-category.frontend_shared {
      background: #f0fdf4;
      color: #166534;
      border: 1px solid #bbf7d0;
    }

    .badge-category.owner, .badge-category.owner_frontend {
      background: #fefce8;
      color: #854d0e;
      border: 1px solid #fef08a;
    }

    .card-title {
      font-size: 17px;
      font-weight: 700;
      color: var(--text);
      margin-top: 4px;
    }

    .card-subtitle {
      font-size: 13px;
      color: var(--text-muted);
    }

    .card-body {
      padding: 20px;
      display: grid;
      grid-template-columns: 1fr;
      gap: 16px;
    }

    @media(min-width: 900px) {
      .card-body.with-sidebar {
        grid-template-columns: 1.6fr 1fr;
      }
    }

    .section-title {
      font-size: 13px;
      font-weight: 700;
      text-transform: uppercase;
      letter-spacing: 0.5px;
      color: var(--primary);
      margin-bottom: 6px;
      display: flex;
      align-items: center;
      gap: 6px;
    }

    .highlight-box {
      background: #fafaf9;
      border: 1px solid #e7e5e4;
      border-radius: 8px;
      padding: 14px;
      font-size: 14px;
      line-height: 1.6;
    }

    .highlight-box.action {
      background: #fff1f2;
      border-color: #fecdd3;
      border-right: 4px solid #e11d48;
    }

    .highlight-box.action strong {
      color: #9f1239;
    }

    .highlight-box.files {
      background: #f8fafc;
      border-color: #cbd5e1;
      font-family: monospace;
      font-size: 13px;
    }

    .file-tag {
      display: inline-block;
      background: #e2e8f0;
      color: #0f172a;
      padding: 3px 8px;
      border-radius: 4px;
      margin: 3px 2px;
      direction: ltr;
      unicode-bidi: embed;
    }

    .acceptance-box {
      background: #f0fdf4;
      border: 1px solid #bbf7d0;
      border-right: 4px solid #16a34a;
      padding: 12px 14px;
      border-radius: 8px;
      font-size: 13px;
      color: #14532d;
    }

    .acceptance-box strong {
      color: #166534;
      display: block;
      margin-bottom: 3px;
    }

    .checklist-toggle {
      display: flex;
      align-items: center;
      gap: 8px;
      margin-top: 10px;
      font-size: 13px;
      font-weight: 600;
      cursor: pointer;
    }

    .checklist-toggle input {
      width: 18px;
      height: 18px;
      cursor: pointer;
      accent-color: var(--primary);
    }

    /* Action Plan Banner */
    .action-banner {
      background: #ffffff;
      border-radius: 12px;
      border: 2px solid var(--primary);
      padding: 24px;
      margin-bottom: 25px;
      box-shadow: 0 4px 15px rgba(118, 45, 72, 0.08);
    }

    .action-banner h2 {
      font-size: 20px;
      color: var(--primary);
      margin-bottom: 12px;
      font-weight: 800;
    }

    .action-banner ol {
      padding-right: 20px;
      font-size: 15px;
      color: #334155;
    }

    .action-banner ol li {
      margin-bottom: 8px;
    }

    footer {
      text-align: center;
      color: var(--text-muted);
      font-size: 13px;
      margin-top: 40px;
    }
  </style>
</head>
<body>

  <header>
    <div class="container">
      <div class="header-content">
        <div class="header-title">
          <h1>خطة تنفيذ وإصلاح الباك إند (Backend Action Plan)</h1>
          <p>مشروع KCODE — مراجعة تدقيق الحزمة المعتمدة R479 وتحديد المهام بدقة</p>
        </div>
        <div class="header-badge">
          تاريخ الفحص: 15 نوفمبر 2026 | حزمة التدقيق: 13 نوفمبر 2026
        </div>
      </div>
    </div>
  </header>

  <main class="container">
    
    <!-- Stats Cards -->
    <div class="stats-grid">
      <div class="stat-card">
        <div class="stat-number" style="color: var(--text)">27</div>
        <div class="stat-label">إجمالي البنود المفحوصة (F01–F26 + N01)</div>
      </div>
      <div class="stat-card p0">
        <div class="stat-number" style="color: var(--danger)">3</div>
        <div class="stat-label">حواجب إطلاق حرجة (P0 Release Blockers)</div>
      </div>
      <div class="stat-card backend">
        <div class="stat-number" style="color: var(--primary)">13</div>
        <div class="stat-label">مهمتنا كـ Backend (Laravel / DB / API)</div>
      </div>
      <div class="stat-card frontend">
        <div class="stat-number" style="color: var(--info)">11</div>
        <div class="stat-label">مسؤولية Frontend (React / Vercel)</div>
      </div>
      <div class="stat-card owner">
        <div class="stat-number" style="color: var(--accent)">3</div>
        <div class="stat-label">قرارات خاصة بالمالك (CR, Shipping)</div>
      </div>
    </div>

    <!-- Quick Backend Summary Banner -->
    <div class="action-banner">
      <h2>🚀 خارطة الطريق المباشرة للباك إند (Backend Roadmap):</h2>
      <ol>
        <li><strong>المرحلة الأولى (حواجب الإطلاق P0):</strong>
          إصلاح منطق الكويز في <code>QuizService.php</code> لمطابقة الـ 728 حالة في <code>QUIZ_EXPECTED_728.json</code>، استعادة جداول التوقيت في <code>R479EngineService.php</code> (منع الواقي ليلاً)، وربط الشات بوت بـ SKU الفعلي لمنع التأليف في <code>ChatbotService.php</code>.
        </li>
        <li><strong>المرحلة الثانية (تنظيف البيانات وسلامة الـ APIs):</strong>
          تنظيف قاعدة البيانات من الـ 2,928 تقييماً مكرراً (F10)، منع المنتجات العشوائية عند فشل البحث (F09)، تثبيت كمية إضافة الروتين (F07)، ودعم سلة الضيف في الكوبونات دون 401 (F05).
        </li>
        <li><strong>المرحلة الثالثة (المطابقة المرجعية R479):</strong>
          استعادة الـ 8 منتجات والـ 6 أسئلة الشائعة في الهوم بيج (F13)، ضبط تصنيف حساسية الـ 22 منتجاً كـ "مناسب بشرط" (F15)، تصحيح روابط صور المقالات السبعة (F26)، وتوحيد معادلة الخصم (N01).
        </li>
      </ol>
      <div style="margin-top: 12px; font-size: 13px; color: #b91c1c; font-weight: bold; background: #fff1f2; padding: 8px 12px; border-radius: 6px;">
        ⚠️ تنبيه الالتزام الصارم: لا يتم رفع أي كود للـ Remote Repository (No Git Push) نهائياً إلا بعد الفحص والتجربة وموافقتك.
      </div>
    </div>

    <!-- Database & Fields Analysis Section -->
    <div class="action-banner" style="border-color: #0284c7; background: #f0f9ff; margin-bottom: 25px;">
      <h2 style="color: #0369a1;">🗄️ تحليل قاعدة البيانات والحقول: هل نحتاج حقولاً أو جداول جديدة؟</h2>
      <p style="font-size: 15px; color: #0c4a6e; margin-bottom: 14px;">
        <strong>الخلاصة المباشرة: لا نحتاج إنشاء أي Migrations أو حقول جديدة في قاعدة البيانات!</strong><br>
        قاعدة البيانات الحالية في المشروع مجهزة بالفعل بنسبة 100% بكافة الأعمدة وبنية الـ JSON المطلوبة، ولوحة تحكم Filament Dashboard تدعمها وترتبط بها بالكامل. الخلل المرصود في تقرير التدقيق سببه <strong>بيانات في الـ Seeders</strong> أو <strong>منطق في الـ Services</strong> وليس نقصاً في بنية الجداول.
      </p>

      <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 14px; margin-top: 15px;">
        
        <div style="background: white; border-radius: 8px; padding: 14px; border: 1px solid #bae6fd;">
          <div style="font-weight: 700; color: #0369a1; margin-bottom: 6px;">1. الكويز وحالات الـ 728 (F01)</div>
          <div style="font-size: 13px; color: #334155; line-height: 1.6;">
            • <strong>الحقول الحالية:</strong> <code>key</code> في <code>quiz_questions</code>، و <code>code</code> في <code>quiz_options</code>، و <code>scenario_key</code> في <code>assessments</code>.<br>
            • <strong>حقول جديدة؟:</strong> ❌ <strong>لا نحتاج إطلاقاً</strong>.<br>
            • <strong>المطلوب:</strong> تصحيح ربط معرف الخيار (option ID) بالهدف والمشكلة داخل <code>QuizService.php</code>.
          </div>
        </div>

        <div style="background: white; border-radius: 8px; padding: 14px; border: 1px solid #bae6fd;">
          <div style="font-weight: 700; color: #0369a1; margin-bottom: 6px;">2. جداول الروتينات العامة (F02)</div>
          <div style="font-size: 13px; color: #334155; line-height: 1.6;">
            • <strong>الحقول الحالية:</strong> <code>morning</code> و <code>night</code> و <code>frequency_ar</code> و <code>usage_guide_json</code> في جداول <code>preset_routines</code>.<br>
            • <strong>حقول جديدة؟:</strong> ❌ <strong>لا نحتاج</strong>.<br>
            • <strong>المطلوب:</strong> تصحيح بيانات الـ Seeder لمنع الواقي ليلاً وضبط تكرار المقشرات.
          </div>
        </div>

        <div style="background: white; border-radius: 8px; padding: 14px; border: 1px solid #bae6fd;">
          <div style="font-weight: 700; color: #0369a1; margin-bottom: 6px;">3. البشرة الحساسة لـ 22 منتجاً (F15)</div>
          <div style="font-size: 13px; color: #334155; line-height: 1.6;">
            • <strong>الحقول الحالية:</strong> حقول <code>usage_instructions_json.verdict.sensitive_status</code> و <code>sensitive_guidance</code> مدعومة وجاهزة ومربوطة في Filament Dashboard.<br>
            • <strong>حقول جديدة؟:</strong> ❌ <strong>لا نحتاج</strong>.<br>
            • <strong>المطلوب:</strong> ضبط قيمة 'مناسب بشرط' ونص التدرج داخل الـ JSON للمنتجات الـ 22.
          </div>
        </div>

        <div style="background: white; border-radius: 8px; padding: 14px; border: 1px solid #bae6fd;">
          <div style="font-weight: 700; color: #0369a1; margin-bottom: 6px;">4. مواصفات واقيات الشمس والـ INCI (F16)</div>
          <div style="font-size: 13px; color: #334155; line-height: 1.6;">
            • <strong>الحقول الحالية:</strong> <code>role_ar</code>، <code>ingredients_ar</code>، <code>texture_ar</code> في جدول <code>products</code>.<br>
            • <strong>حقول جديدة؟:</strong> ❌ <strong>لا نحتاج</strong>.<br>
            • <strong>المطلوب:</strong> إرجاع نوع الواقي كيميائي/معدني في <code>role_ar</code> وملاحظات الـ INCI الملحقة.
          </div>
        </div>

        <div style="background: white; border-radius: 8px; padding: 14px; border: 1px solid #bae6fd;">
          <div style="font-weight: 700; color: #0369a1; margin-bottom: 6px;">5. التقييمات والبحث والسلة (F07, F09, F10)</div>
          <div style="font-size: 13px; color: #334155; line-height: 1.6;">
            • <strong>الحقول الحالية:</strong> جداول <code>reviews</code> و <code>carts</code> و <code>products</code> مكتملة بكافة العلاقات.<br>
            • <strong>حقول جديدة؟:</strong> ❌ <strong>لا نحتاج</strong>.<br>
            • <strong>المطلوب:</strong> حذف التقييمات الوهمية من الداتابيز، وتعديل منطق الـ Controllers.
          </div>
        </div>

        <div style="background: white; border-radius: 8px; padding: 14px; border: 1px solid #bae6fd;">
          <div style="font-weight: 700; color: #0369a1; margin-bottom: 6px;">6. الشات بوت والذكاء الاصطناعي (F03)</div>
          <div style="font-size: 13px; color: #334155; line-height: 1.6;">
            • <strong>الحقول الحالية:</strong> جدول <code>products</code> يحتوي على الـ SKU والتفاصيل، وجدول <code>chatbot_messages</code>.<br>
            • <strong>حقول جديدة؟:</strong> ❌ <strong>لا نحتاج</strong>.<br>
            • <strong>المطلوب:</strong> ربط كويري جلب بيانات المنتج بالـ SKU في <code>ChatbotService.php</code> لمنع اختراع معلومات.
          </div>
        </div>

      </div>
    </div>

    <!-- Filter & Search Controls -->
    <div class="controls-wrapper">
      <div class="filter-tabs">
        <button class="tab-btn active" onclick="filterCategory('all', this)">عرض الكل (27)</button>
        <button class="tab-btn" onclick="filterCategory('backend', this)" style="background: #fdf2f8; color: #9d174d; border-color: #fbcfe8;">مهمتي: الباك إند (13)</button>
        <button class="tab-btn" onclick="filterCategory('p0', this)" style="background: #fee2e2; color: #991b1b; border-color: #fecaca;">حواجب الإطلاق P0 (3)</button>
        <button class="tab-btn" onclick="filterCategory('frontend', this)">الفرونت إند (11)</button>
        <button class="tab-btn" onclick="filterCategory('owner', this)">قرارات المالك (3)</button>
      </div>
      <div class="search-box">
        <input type="text" id="searchInput" placeholder="ابحث برقم البند أو الكلمة (مثلاً F01، كويز، كود)..." onkeyup="searchCards()">
      </div>
    </div>

    <!-- Findings List -->
    <div class="findings-container" id="findingsContainer">
      ${enrichedFindings.map(f => {
        const pBadgeClass = f.sev === 'P0' ? 'badge-p0' : (f.sev === 'P1' ? 'badge-p1' : 'badge-p2');
        const pBadgeText = f.sev === 'P0' ? 'حاجب إطلاق — P0' : (f.sev === 'P1' ? 'أولوية مرتفعة — P1' : (f.sev === 'TECH' ? 'بند حسابي' : 'أولوية متوسطة — P2'));
        const catClass = f.category.includes('backend') ? 'backend' : (f.category.includes('frontend') ? 'frontend' : 'owner');

        const filesHtml = (f.filePaths && f.filePaths.length > 0)
          ? f.filePaths.map(p => `<span class="file-tag">${p}</span>`).join(' ')
          : '<span style="color: #94a3b8; font-size: 13px;">لا يتطلب تعديل ملفات باك إند مباشرة</span>';

        return `
        <div class="finding-card ${f.sev}" data-category="${f.category}" data-sev="${f.sev}" data-id="${f.id}" data-text="${f.title} ${f.id} ${f.fields['المشاهد'] || ''}">
          <div class="card-header" onclick="toggleCardBody(this)">
            <div>
              <div class="card-header-left">
                <span class="badge-id">${f.id}</span>
                <span class="${pBadgeClass}">${pBadgeText}</span>
                <span class="badge-category ${catClass}">${f.categoryLabel}</span>
                ${f.muted ? `<span style="font-size: 12px; color: #64748b;">${f.muted}</span>` : ''}
              </div>
              <div class="card-title">${f.title}</div>
            </div>
            <div>
              <label class="checklist-toggle" onclick="event.stopPropagation()">
                <input type="checkbox" id="chk_${f.id}" onchange="saveCheckState('${f.id}', this.checked)">
                <span>تم الإصلاح</span>
              </label>
            </div>
          </div>

          <div class="card-body with-sidebar">
            <!-- Main Content Area -->
            <div>
              <div style="margin-bottom: 14px;">
                <div class="section-title">🔍 المشاهد والملاحظ في الفحص:</div>
                <div class="highlight-box">
                  ${f.fields['المشاهد'] || 'لا يوجد تفصيل'}
                </div>
              </div>

              <div>
                <div class="section-title">⚡ المطلوب مني تنفيذه كـ Backend بالتحديد:</div>
                <div class="highlight-box action">
                  <div style="white-space: pre-line;">${f.backendAction}</div>
                </div>
              </div>
            </div>

            <!-- Sidebar Info -->
            <div style="display: flex; flex-direction: column; gap: 12px;">
              <div>
                <div class="section-title">📂 الملفات المعنية بالتعديل:</div>
                <div class="highlight-box files">
                  ${filesHtml}
                </div>
              </div>

              <div>
                <div class="section-title">🎯 شرط القبول والاعتماد من العميل:</div>
                <div class="acceptance-box">
                  <strong>شرط القبول الرسمي:</strong>
                  ${f.fields['شرط القبول'] || 'مطابقة مواصفات R479'}
                </div>
              </div>

              ${f.testGuide ? `
              <div>
                <div class="section-title">🧪 طريقة التحقق وإعادة الاختبار:</div>
                <div class="highlight-box" style="font-size: 13px; background: #f1f5f9;">
                  ${f.testGuide}
                </div>
              </div>
              ` : ''}
            </div>
          </div>
        </div>
        `;
      }).join('\n')}
    </div>

    <footer>
      <p>تم إعداد هذه الوثيقة التفاعلية لمشروع KCODE | مطابقة كاملة لمرجع R479 وحزمة تدقيق 13 نوفمبر 2026</p>
    </footer>

  </main>

  <script>
    // Category Filtering
    function filterCategory(cat, btn) {
      document.querySelectorAll('.tab-btn').forEach(b => b.classList.remove('active'));
      btn.classList.add('active');

      const cards = document.querySelectorAll('.finding-card');
      cards.forEach(card => {
        const itemCat = card.getAttribute('data-category');
        const itemSev = card.getAttribute('data-sev');

        if (cat === 'all') {
          card.style.display = 'block';
        } else if (cat === 'p0') {
          card.style.display = (itemSev === 'P0') ? 'block' : 'none';
        } else if (cat === 'backend') {
          card.style.display = (itemCat.includes('backend')) ? 'block' : 'none';
        } else if (cat === 'frontend') {
          card.style.display = (itemCat.includes('frontend')) ? 'block' : 'none';
        } else if (cat === 'owner') {
          card.style.display = (itemCat.includes('owner')) ? 'block' : 'none';
        }
      });
    }

    // Search Filtering
    function searchCards() {
      const q = document.getElementById('searchInput').value.trim().toLowerCase();
      const cards = document.querySelectorAll('.finding-card');

      cards.forEach(card => {
        const text = card.getAttribute('data-text').toLowerCase();
        if (!q || text.includes(q)) {
          card.style.display = 'block';
        } else {
          card.style.display = 'none';
        }
      });
    }

    // Local Storage for Checkboxes
    function saveCheckState(id, isChecked) {
      localStorage.setItem('kcode_check_' + id, isChecked ? '1' : '0');
    }

    function loadCheckStates() {
      const cards = document.querySelectorAll('.finding-card');
      cards.forEach(card => {
        const id = card.getAttribute('data-id');
        const saved = localStorage.getItem('kcode_check_' + id);
        if (saved === '1') {
          const chk = document.getElementById('chk_' + id);
          if (chk) chk.checked = true;
        }
      });
    }

    // Collapsible
    function toggleCardBody(header) {
      const body = header.nextElementSibling;
      if (body.style.display === 'none') {
        body.style.display = 'grid';
      } else {
        body.style.display = 'none';
      }
    }

    window.onload = loadCheckStates;
  </script>
</body>
</html>
`;

fs.writeFileSync('KCODE_BACKEND_ACTION_PLAN.html', html, 'utf8');
console.log('Successfully created KCODE_BACKEND_ACTION_PLAN.html');
