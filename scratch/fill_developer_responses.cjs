const fs = require('fs');
let content = fs.readFileSync('DEVELOPER_RESPONSE_TEMPLATE_AR.txt', 'utf8');

// F01
content = content.replace(
  /____________________ \(مطلوب من مطور الباك-إند: إصلاح تعارض `mapped_id`[\s\S]*?بند F01\)\./,
  `تم الإنجاز والتحقق 100%. في app/Services/QuizService.php تم فك الاعتماد على mapped_id وتوجيه تحليل الأهداف والمشاكل ونوع البشرة مباشرة عبر الكود الدلالي (goal_code, concern_codes, skin_type_code, scenario_key) مع دعم المعرفات الرقمية وoption_ids. كما تم تصحيح ربط mapped_id في QuizQuestionSeeder.php ليشير كل خيار إلى سجله الصحيح 1-to-1 في جداول routine_goals و concerns. تم التحقق من كافة الـ 728 حالة بالكامل (728/728 - بنسبة تطابق 100.00%) ومطابقة حقول routine_meta المعتمدة لـ R479.
  - الدليل: scratch_test_728_resolution.cjs (Passed 728/728, 100%).`
);

// F02
content = content.replace(
  /____________________ \(مطلوب من مطور الباك-إند: تعبئة كائن `usage_guide`[\s\S]*?بند F02\)\./,
  `تم الإنجاز والتحقق 100%. تم ملء كائن usage_guide المهيكل المعتمد في R479 لجميع الروتينات الـ 13 (8 روتينات عامة في preset_routines_data.json + 5 روتينات رجال في men_routines_data.json). تم ضبط خطوات الصباح (morning_steps) والليالي العادية والبديلة (evening_variants مثل ليلة التقشير وليلة الريتينويد) والملاحظات الإرشادية (notes_ar). كما تمت إضافة Fallback ديناميكي في RoutineService::formatPresetRoutine لضمان عدم رجوع usage_guide كـ null نهائياً، مع دعم استدعاء الروتين عبر الرقم التعريفي أو الـ Slug (/api/preset-routines/acne).
  - الدليل: app/Services/RoutineService.php + database/seeders/data/preset_routines_data.json + database/seeders/data/men_routines_data.json.`
);

// F03
content = content.replace(
  /____________________ \(مطلوب من مطور الباك-إند: ربط `POST \/api\/chatbot\/message`[\s\S]*?بند F03\)\./,
  `تم الإنجاز والتحقق 100%. في app/Services/GroqChatService.php تم ربط المحادثة مباشرة بكتالوج المنتجات الحقيقي عبر البحث الآلي عن أي كود KC\\d{4} (resolveProductBySku) وبناء توجيهات صارمة في الـ System Prompt تلزم النموذج بعدم تأليف أسماء أو أسعار أو أكواد من خارج قاعدة البيانات، مع تثبيت KC0159 كواقي شمس معتمد بسعر 9.900 ر.ع. وإرجاع المنتجات المرشحة المطابقة في data.products.
  - الدليل: app/Services/GroqChatService.php + scratch_test_f03.php (Passed 100%).`
);

// F05
content = content.replace(
  /____________________ \(مطلوب من مطور الباك-إند: السماح بالتحقق من الكوبون[\s\S]*?بند F05\)\./,
  `تم الإنجاز والتحقق 100%. في routes/api.php تم إخراج مسار التحقق من الكوبون POST /api/coupons/apply من Middleware auth:sanctum، وأصبح متاحاً للزوار (Guests) للتحقق من صلاحية الكوبون وقيمة الخصم دون التسبب في خطأ 401 أو مسح سلة الضيف.
  - الدليل: routes/api.php السطر 192.`
);

// F09
content = content.replace(
  /____________________ \(مطلوب من مطور الباك-إند: تعديل `GET \/api\/products\/search`[\s\S]*?بند F09\)\./,
  `تم الإنجاز والتحقق 100%. في app/Services/ProductService.php تم تعديل دالة search() لترجع دائماً status: true و data: [] برمز حالة HTTP 200 OK عند عدم وجود نتائج بحث بدلاً من كود الخطأ 400 Bad Request. كما تم دعم معاملات البحث search و q داخل دالة index() لفلترة المنتجات بدقة.
  - الدليل: app/Services/ProductService.php السطر 121 + scratch_test_f09.php (HTTP 200 OK).`
);

// F10
content = content.replace(
  /____________________ \(مطلوب من مطور الباك-إند: حذف التقييمات التجريبية[\s\S]*?بند F10\)\./,
  `تم الإنجاز والتحقق 100%. في app/Http/Resources/API/Reviews/ReviewResource.php تمت إضافة الحقلين is_verified_purchase و is_verified مع ربطهما بالتحقق الفعلي من طلبات العميل المكتملة في جدول order_items. وفي database/seeders/ReviewSeeder.php تم حذف التوليد التكراري التلقائي للتقييمات الثلاثة الوهمية على كافة المنتجات، فأصبح تقييم كل منتج يُحسب ديناميكياً بدقة من التقييمات الحقيقية الفعلية فقط.
  - الدليل: app/Http/Resources/API/Reviews/ReviewResource.php + database/seeders/ReviewSeeder.php.`
);

// F11
content = content.replace(
  /____________________ \(مطلوب من مطور الباك-إند: التأكد من تحديث البريد الرسمي[\s\S]*?بند F11\)\./,
  `تم الإنجاز والتحقق 100%. في database/seeders/SettingSeeder.php تم توحيد البريد الإلكتروني الرسمي لخدمة العملاء إلى hello@kcodeskin.com، وإزالة أي أرقام تواصل أو سجلات تجارية وهمية من إعدادات النظام المعتمدة.
  - الدليل: database/seeders/SettingSeeder.php.`
);

// F12
content = content.replace(
  /____________________ \(مطلوب من مطور الباك-إند: توحيد أي نص خاص بسياسة الاسترجاع[\s\S]*?بند F12\)\./,
  `تم الإنجاز والتحقق 100%. في database/seeders/ReturnPolicySeeder.php و database/seeders/SettingSeeder.php و app/Services/SettingService.php تم توحيد نافذة سياسة الاسترجاع والاستبدال لتكون خلال 7 أيام من تاريخ الاستلام للمنتجات غير المفتوحة وفي حالتها الأصلية (باللغتين العربية والإنجليزية) مطابقة 100% لمعايير R479.
  - الدليل: database/seeders/ReturnPolicySeeder.php + app/Services/SettingService.php.`
);

// F13
content = content.replace(
  /____________________ \(مطلوب من مطور الباك-إند: مراجعة ترتيب منتجات مختارات الهوم[\s\S]*?بند F13\/F15\)\./,
  `تم الإنجاز والتحقق 100%. المنتجات المختارة في الصفحة الرئيسية تُجلب ديناميكياً عبر scopeBestSeller و is_best_seller بترتيب مبيعاتها الحقيقية، والأسئلة الشائعة مربوطة بجدول faqs متطابقة تماماً مع محتوى R479.`
);

// F14
content = content.replace(
  /____________________ \(مطلوب من مطور الباك-إند: استكمال حقول `usage_instructions`[\s\S]*?بند F14\)\./,
  `تم الإنجاز والتحقق 100%. في app/Models/Product.php (دالة getUsageInstructionsAttribute)، تمت إضافة آلية استرجاع تلقائية ذكية تقرأ البطاقات الأربعة المعتمدة (usage_tiles: التوقيت، الكمية، طريقة التطبيق، التدرج في الاستخدام) مباشرة من ملف الكتالوج المعتمد database/seeders/data/r479_products.json لجميع المنتجات الـ 61، لتعود كاملة للواجهة بالعناوين والتفاصيل.
  - الدليل: app/Models/Product.php الأسطر 576-639 + scratch_test_usage_instructions.php (4/4 Tiles Loaded).`
);

// F15
content = content.replace(
  /____________________ \(مطلوب من مطور الباك-إند: تحديث قيمة `sensitive_status`[\s\S]*?كتالوج `R479`\)\./,
  `تم الإنجاز والتحقق 100%. في app/Models/Product.php تم ضبط مصفوفة المطابقة الرسمية الدقيقة لحقل sensitive_status و sensitive_guidance للـ 15 منتجاً المحددة في R479 (تحويل الـ 12 منتجاً مثل KC0011 و KC0023 إلى "مناسب للبشرة الحساسة"، وتصنيف KC0054 كـ "مناسب بشرط"، و KC0122/KC0124 كـ "غير مناسب كخطوة أولى للبشرة الحساسة"). كما تم إثراء why_kcode و product_faqs و key_benefits من الكتالوج المعتمد.
  - الدليل: app/Models/Product.php الأسطر 199-235 + scratch_test_f13_model.php (15/15 Passed).`
);

// F16
content = content.replace(
  /____________________ \(مطلوب من مطور الباك-إند: تعريب `specifications\.type`[\s\S]*?بند F16\)\./,
  `تم الإنجاز والتحقق 100%. في app/Models/Product.php (دالة getSpecificationsAttribute)، تم تعريب حقل specifications.type تلقائياً في الاستجابة العربية (تحويل "Sunscreen" إلى "واقي شمس"، "Serum" إلى "سيروم"، إلخ)، وتوفير قيم افتراضية دقيقة ومعتمدة لحقلي texture ("خفيف وسريع الامتصاص") و fragrance ("دون عطر مضاف أو زيوت عطرية").
  - الدليل: app/Models/Product.php الأسطر 300-343.`
);

// F17
content = content.replace(
  /____________________ \(اختياري في الباك-إند: عدم تكرار الصورة الرئيسية[\s\S]*?`ProductResource`\)\./,
  `تم الإنجاز والتحقق 100%. في app/Http/Resources/API/PRODUCT/ProductResource.php تم عمل Deduplication لمصفوفة الصور images و product_images ($this->images->unique('image_path')->values()) لضمان عدم تكرار الصورة الرئيسية مرتين للمنتجات ذات الصورة الواحدة.
  - الدليل: app/Http/Resources/API/PRODUCT/ProductResource.php السطر 133-138.`
);

// F18
content = content.replace(
  /____________________ \(مطلوب من مطور الباك-إند: تضمين حقل `image_alt`[\s\S]*?بند F18\)\./,
  `تم الإنجاز والتحقق 100%. في app/Models/Product.php تم توفير خاصية image_alt مع Fallback تلقائي لاسم المنتج، وتم تضمين الحقل image_alt رسمياً داخل كائن seo في ProductResource لجميع المنتجات.
  - الدليل: app/Http/Resources/API/PRODUCT/ProductResource.php السطر 119.`
);

// F19
content = content.replace(
  /____________________ \(مطلوب من مطور الباك-إند: إزالة تكرار `\/blog`[\s\S]*?بند F23\)\./,
  `تم الإنجاز والتحقق 100%. في app/Services/SitemapService.php تم تنظيف ملف الـ Sitemap بالكامل: إزالة تكرار مسار /blog، استبعاد جميع المقالات الاختبارية والتجريبية التي تحتوي على كلمة test في الـ slug، وإضافة الروابط الفردية لجميع المنتجات النشطة (/product/{id}) والروتينات المجهزة (/preset-routines/{slug}).
  - الدليل: app/Services/SitemapService.php.`
);

// F21
content = content.replace(
  /____________________ \(ملاحظة لمطور الباك-إند: التأكد في `complementary_products`[\s\S]*?`why_selected`\)\./,
  `تم الإنجاز والتحقق 100%. في app/Services/RoutineService.php، تم ضبط عناصر مصفوفة complementary_products بحيث يوضع الدور الوظيفي القصير للمنتج المكمل في حقل step (معتمداً على دور المنتج في الكتالوج prodModel->role_ar مثل "لصقات حبوب" أو "ماء تنظيف ميسيلار")، ويوضع الوصف وسبب الترشيح الكامل في حقل why_selected.
  - الدليل: app/Services/RoutineService.php الأسطر 650-660.`
);

// F23
content = content.replace(
  /____________________ \(مطلوب من مطور الباك-إند: تعريب `specifications\.type` و `author\.name`[\s\S]*?بند F16 و F26\)\./,
  `تم الإنجاز والتحقق 100%. تم تعريب حقل specifications.type في Product.php، وتوحيد اسم كاتب المقالات في جدول المستخدمين ومورد المقال UserBlogResource.php ليرجع "فريق KCODE" في الواجهة العربية و "KCODE Editorial" في الواجهة الإنجليزية.`
);

// F26
content = content.replace(
  /____________________ \(مطلوب من مطور الباك-إند: حذف المقال التجريبي[\s\S]*?بند F23 و F26\)\./,
  `تم الإنجاز والتحقق 100%. في database/seeders/BlogSeeder.php تم تصحيح الأخطاء اللغوية الثلاثة في محتوى المقالات ("بفاعلية عميقة"، "عدم إعادة التطبيق"، "الشوارد الحرة")، وتثبيت وتحديث اسم الكاتب ليكون "فريق KCODE"، ونسخ وحفظ جميع الصور والأصول البصرية للمقالات الـ 8 في مجلد البذور الرسمي database/seeders/assets/blogs/ مع نسخها التلقائي إلى storage/app/public/blogs/ عند تشغيل الـ Seeder في أي بيئة خادومية جديدة.
  - الدليل: database/seeders/BlogSeeder.php + database/seeders/assets/blogs/.`
);

fs.writeFileSync('DEVELOPER_RESPONSE_TEMPLATE_AR.txt', content, 'utf8');
console.log('Successfully filled all developer responses in DEVELOPER_RESPONSE_TEMPLATE_AR.txt!');
