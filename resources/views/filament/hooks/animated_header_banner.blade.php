@php
    $path = request()->path();
    $type = null;

    if (str_contains($path, 'admin/app-notifications')) {
        $type = 'app_notifications';
    } elseif (str_contains($path, 'admin/coupons')) {
        $type = 'coupons';
    } elseif (str_contains($path, 'admin/offers')) {
        $type = 'offers';
    } elseif (str_contains($path, 'admin/orders')) {
        $type = 'orders';
    } elseif (str_contains($path, 'admin/products')) {
        $type = 'products';
    } elseif (str_contains($path, 'admin/categories')) {
        $type = 'categories';
    } elseif (str_contains($path, 'admin/sub-categories')) {
        $type = 'sub_categories';
    } elseif (str_contains($path, 'admin/brands')) {
        $type = 'brands';
    } elseif (str_contains($path, 'admin/users')) {
        $type = 'users';
    } elseif (str_contains($path, 'admin/roles')) {
        $type = 'roles';
    } elseif (str_contains($path, 'admin/activity-logs')) {
        $type = 'activity_logs';
    } elseif (str_contains($path, 'admin/assessments')) {
        $type = 'assessments';
    } elseif (str_contains($path, 'admin/concerns')) {
        $type = 'concerns';
    } elseif (str_contains($path, 'admin/quiz-questions')) {
        $type = 'quiz_questions';
    } elseif (str_contains($path, 'admin/skin-types')) {
        $type = 'skin_types';
    } elseif (str_contains($path, 'admin/blog-categories')) {
        $type = 'blog_categories';
    } elseif (str_contains($path, 'admin/blog-tags')) {
        $type = 'blog_tags';
    } elseif (str_contains($path, 'admin/blogs')) {
        $type = 'blogs';
    } elseif (str_contains($path, 'admin/chatbot-messages')) {
        $type = 'chatbot_messages';
    } elseif (str_contains($path, 'admin/chatbot-suggestions')) {
        $type = 'chatbot_suggestions';
    } elseif (str_contains($path, 'admin/newsletter-subscriptions')) {
        $type = 'newsletter_subscriptions';
    } elseif (str_contains($path, 'admin/loyalty-levels')) {
        $type = 'loyalty_levels';
    } elseif (str_contains($path, 'admin/faqs')) {
        $type = 'faqs';
    } elseif (str_contains($path, 'admin/pages')) {
        $type = 'pages';
    } elseif (str_contains($path, 'admin/settings')) {
        $type = 'settings';
    } elseif ($path === 'admin' || $path === 'admin/' || str_contains($path, 'admin/dashboard')) {
        $type = 'dashboard';
    }
@endphp

@if ($type)
    <style>
        .kcode-page-banner {
            position: relative;
            background: linear-gradient(135deg, rgba(16, 185, 129, 0.09) 0%, rgba(15, 23, 42, 0.03) 100%);
            border: 1px solid rgba(16, 185, 129, 0.22);
            border-radius: 0.85rem;
            padding: 0.85rem 1.4rem;
            margin-bottom: 1.2rem;
            overflow: hidden;
            display: flex;
            align-items: center;
            justify-content: space-between;
            box-shadow: 0 4px 15px -3px rgba(0, 0, 0, 0.05);
            backdrop-filter: blur(6px);
        }

        .kcode-banner-info h2 {
            font-size: 1.1rem;
            font-weight: 700;
            color: #10b981;
            margin: 0 0 0.2rem 0;
            display: flex;
            align-items: center;
            gap: 0.4rem;
        }

        .kcode-banner-info p {
            font-size: 0.8rem;
            color: #64748b;
            margin: 0;
        }

        /* --- RESPONSIVE MEDIA QUERIES --- */
        @media (max-width: 640px) {
            .kcode-page-banner {
                flex-direction: column;
                align-items: center;
                text-align: center;
                gap: 0.8rem;
                padding: 1rem 0.8rem;
            }

            .kcode-banner-info h2 {
                justify-content: center;
                font-size: 1rem;
            }

            .kcode-banner-info p {
                font-size: 0.75rem;
            }

            .kcode-banner-anim {
                display: flex;
                justify-content: center;
                width: 100%;
                margin-top: 0.2rem;
            }

            .orders-stage {
                width: 150px;
            }
        }

        /* --- 1. ORDERS ANIMATION: Sleek Delivery Van --- */
        @keyframes driveVan {
            0% { transform: translateX(160px); }
            50% { transform: translateX(-160px); }
            100% { transform: translateX(160px); }
        }

        @keyframes wheelSpin {
            100% { transform: rotate(-360deg); }
        }

        @keyframes vanBounce {
            0% { transform: translateY(0px); }
            100% { transform: translateY(-1.5px); }
        }

        .orders-stage {
            position: relative;
            width: 200px;
            height: 44px;
            overflow: hidden;
            border-bottom: 2px dashed rgba(16, 185, 129, 0.35);
        }

        .van-wrapper {
            position: absolute;
            bottom: 0px;
            animation: driveVan 7s ease-in-out infinite, vanBounce 0.35s ease-in-out infinite alternate;
        }

        .wheel-rotate {
            transform-box: fill-box;
            transform-origin: center;
            animation: wheelSpin 0.5s linear infinite;
        }

        /* --- 2. PRODUCTS ANIMATION: 3D Shopping Box & Sparkles --- */
        @keyframes boxFloat {
            0%, 100% { transform: translateY(0) rotate(0deg); }
            50% { transform: translateY(-6px) rotate(3deg); }
        }

        @keyframes sparkleRotate {
            0% { transform: rotate(0deg) scale(0.7); opacity: 0.4; }
            50% { transform: rotate(180deg) scale(1.2); opacity: 1; }
            100% { transform: rotate(360deg) scale(0.7); opacity: 0.4; }
        }

        .products-stage {
            position: relative;
            width: 80px;
            height: 50px;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .box-svg {
            animation: boxFloat 3s ease-in-out infinite;
            filter: drop-shadow(0 6px 10px rgba(16, 185, 129, 0.3));
        }

        .sparkle-icon {
            position: absolute;
            animation: sparkleRotate 2.5s linear infinite;
        }

        .sp-1 { top: -2px; right: 5px; color: #fbbf24; font-size: 0.9rem; }
        .sp-2 { bottom: 0px; left: 5px; color: #10b981; font-size: 0.8rem; animation-delay: 1.2s; }

        /* --- 3. USERS ANIMATION: Radar Pulse & Avatars --- */
        @keyframes radarWave1 {
            0% { transform: scale(0.5); opacity: 1; }
            100% { transform: scale(1.6); opacity: 0; }
        }

        @keyframes greenBlink {
            0%, 100% { opacity: 1; transform: scale(1); }
            50% { opacity: 0.4; transform: scale(0.85); }
        }

        .users-stage {
            position: relative;
            width: 70px;
            height: 48px;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .radar-wave {
            position: absolute;
            width: 40px;
            height: 40px;
            border-radius: 50%;
            border: 2px solid #10b981;
            animation: radarWave1 2.2s cubic-bezier(0.25, 0.46, 0.45, 0.94) infinite;
        }

        .online-dot {
            position: absolute;
            top: 6px;
            right: 18px;
            width: 8px;
            height: 8px;
            background: #10b981;
            border-radius: 50%;
            box-shadow: 0 0 8px #10b981;
            animation: greenBlink 1.5s ease-in-out infinite;
        }

        /* --- 4. COUPONS ANIMATION: Floating Ticket & Rising % --- */
        @keyframes ticketBounce {
            0%, 100% { transform: translateY(0) rotate(-3deg); }
            50% { transform: translateY(-5px) rotate(3deg); }
        }

        @keyframes risePercent {
            0% { opacity: 0; transform: translateY(8px) scale(0.6); }
            50% { opacity: 1; }
            100% { opacity: 0; transform: translateY(-22px) scale(1.1); }
        }

        .coupons-stage {
            position: relative;
            width: 80px;
            height: 50px;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .ticket-svg {
            animation: ticketBounce 2.6s ease-in-out infinite;
            filter: drop-shadow(0 4px 8px rgba(245, 158, 11, 0.3));
        }

        .rising-pct {
            position: absolute;
            font-weight: 800;
            font-size: 0.85rem;
            color: #f59e0b;
            animation: risePercent 2.2s ease-out infinite;
        }

        .pct-a { left: 8px; animation-delay: 0s; }
        .pct-b { right: 8px; animation-delay: 1.1s; }

        /* --- 5. SKINCARE ANIMATION: Serum Bottle & Liquid Drop Ripple --- */
        @keyframes dropFall {
            0% { opacity: 0; transform: translateY(-10px) scale(0.5); }
            40% { opacity: 1; transform: translateY(8px) scale(1); }
            80% { opacity: 0; transform: translateY(18px) scale(1.2); }
            100% { opacity: 0; }
        }

        @keyframes rippleExpand {
            0% { width: 4px; height: 2px; opacity: 1; }
            100% { width: 32px; height: 10px; opacity: 0; border-width: 1px; }
        }

        .skincare-stage {
            position: relative;
            width: 70px;
            height: 52px;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .serum-svg {
            filter: drop-shadow(0 4px 10px rgba(16, 185, 129, 0.4));
        }

        .drop-particle {
            position: absolute;
            top: 8px;
            width: 6px;
            height: 9px;
            background: #10b981;
            border-radius: 50% 50% 50% 50% / 60% 60% 40% 40%;
            animation: dropFall 2.2s cubic-bezier(0.4, 0, 0.6, 1) infinite;
        }

        .liquid-ripple {
            position: absolute;
            bottom: 4px;
            border: 2px solid #10b981;
            border-radius: 50%;
            animation: rippleExpand 2.2s ease-out infinite;
            animation-delay: 0.8s;
        }

        /* --- 6. SETTINGS ANIMATION: Interlocking Gears --- */
        @keyframes gearRotateClockwise {
            from { transform: rotate(0deg); }
            to { transform: rotate(360deg); }
        }

        @keyframes gearRotateCounter {
            from { transform: rotate(0deg); }
            to { transform: rotate(-360deg); }
        }

        .settings-stage {
            position: relative;
            width: 70px;
            height: 50px;
        }

        .gear-main {
            position: absolute;
            top: 2px;
            right: 8px;
            transform-origin: center;
            animation: gearRotateClockwise 7s linear infinite;
        }

        .gear-sub {
            position: absolute;
            bottom: 2px;
            left: 8px;
            transform-origin: center;
            animation: gearRotateCounter 4.5s linear infinite;
        }
    </style>

    <div class="kcode-page-banner">
        <div class="kcode-banner-info">
            @if ($type === 'orders')
                <h2>📦 {{ app()->getLocale() === 'en' ? 'Orders & Delivery Management' : 'إدارة ومتابعة الطلبات' }}</h2>
                <p>{{ app()->getLocale() === 'en' ? 'Track order status, delivery, and update shipments in real-time' : 'تتبع حالات الشحن والتسليم وتحديث الطلبات لحظة بلحظة' }}</p>

            @elseif ($type === 'products')
                <h2>🛍️ {{ app()->getLocale() === 'en' ? 'Products & Catalog Management' : 'كتالوج المنتجات والأسعار' }}</h2>
                <p>{{ app()->getLocale() === 'en' ? 'Manage products, prices, stock levels, and store inventory' : 'إضافة وتعديل المنتجات، الأسعار، والمخزون بكتالوج المتجر' }}</p>

            @elseif ($type === 'categories')
                <h2>📁 {{ app()->getLocale() === 'en' ? 'Main Categories' : 'الأقسام الرئيسية للمتجر' }}</h2>
                <p>{{ app()->getLocale() === 'en' ? 'Organize and structure main product categories' : 'تنظيم وترتيب أقسام وتصنيفات المتجر الرئيسية' }}</p>

            @elseif ($type === 'sub_categories')
                <h2>📂 {{ app()->getLocale() === 'en' ? 'Sub Categories' : 'الأقسام الفرعية' }}</h2>
                <p>{{ app()->getLocale() === 'en' ? 'Manage sub categories linked to main product categories' : 'إدارة الأقسام الفرعية التابعة للأقسام الرئيسية' }}</p>

            @elseif ($type === 'brands')
                <h2>🏷️ {{ app()->getLocale() === 'en' ? 'Brands & Manufacturers' : 'العلامات التجارية (الماركات)' }}</h2>
                <p>{{ app()->getLocale() === 'en' ? 'Manage store brand catalog and featured manufacturers' : 'إدارة وتصنيف الماركات والعلامات التجارية لمنتجات المتجر' }}</p>

            @elseif ($type === 'users')
                <h2>👤 {{ app()->getLocale() === 'en' ? 'Customers & User Accounts' : 'المستخدمون والعملاء' }}</h2>
                <p>{{ app()->getLocale() === 'en' ? 'Monitor customer accounts, details, and activity' : 'متابعة وإدارة حسابات العملاء، البيانات، والنشاط بالمتجر' }}</p>

            @elseif ($type === 'roles')
                <h2>🔑 {{ app()->getLocale() === 'en' ? 'Roles & Permissions' : 'الصلاحيات والأدوار' }}</h2>
                <p>{{ app()->getLocale() === 'en' ? 'Manage admin panel roles and user access permissions' : 'إدارة صلاحيات الوصول وأدوار المشرفين باللوحة' }}</p>

            @elseif ($type === 'activity_logs')
                <h2>📜 {{ app()->getLocale() === 'en' ? 'Activity & System Audit Logs' : 'سجلات العمليات والأحداث' }}</h2>
                <p>{{ app()->getLocale() === 'en' ? 'Monitor admin actions, model changes, logins, and system audit trails' : 'متابعة حركة المشرفين، التعديلات، تسجيلات الدخول وسجل تتبع النظام' }}</p>

            @elseif ($type === 'app_notifications')
                <h2>🔔 {{ app()->getLocale() === 'en' ? 'App Notifications (Push)' : 'إشعارات التطبيق (Push Notifications)' }}</h2>
                <p>{{ app()->getLocale() === 'en' ? 'Send and monitor real-time push notifications and store alerts' : 'إرسال ومتابعة الإشعارات اللحظية والتنبيهات المباشرة للتطبيق' }}</p>

            @elseif ($type === 'coupons')
                <h2>🏷️ {{ app()->getLocale() === 'en' ? 'Discount Coupons' : 'كوبونات الخصم' }}</h2>
                <p>{{ app()->getLocale() === 'en' ? 'Create and manage discount codes and store promo vouchers' : 'إنشاء وتفعيل كودات الخصم وقسائم الشراء الخاصة بالمتجر' }}</p>

            @elseif ($type === 'offers')
                <h2>⚡ {{ app()->getLocale() === 'en' ? 'Product Offers & Discounts' : 'عروض المنتجات والخصومات' }}</h2>
                <p>{{ app()->getLocale() === 'en' ? 'Manage promotional offers and direct product discounts' : 'إدارة العروض الترويجية والخصومات المباشرة على المنتجات' }}</p>

            @elseif ($type === 'assessments')
                <h2>✨ {{ app()->getLocale() === 'en' ? 'Skin Quiz Results & Assessments Log' : 'نتائج اختبارات البشرة (Skin Quiz)' }}</h2>
                <p>{{ app()->getLocale() === 'en' ? 'Track customer skin quiz results and routine recommendations' : 'متابعة نتائج اختبارات البشرة والتوصيات الطبية والروتينات المقترحة للعملاء' }}</p>

            @elseif ($type === 'concerns')
                <h2>⚠️ {{ app()->getLocale() === 'en' ? 'Skin Concerns' : 'مشاكل البشرة' }}</h2>
                <p>{{ app()->getLocale() === 'en' ? 'Define skin concerns and map products to skin conditions' : 'تعريف وإدارة مشاكل البشرة وتصنيف المنتجات حسب الحالة العلاجية' }}</p>

            @elseif ($type === 'quiz_questions')
                <h2>❓ {{ app()->getLocale() === 'en' ? 'Quiz Questions & Options' : 'أسئلة اختبار البشرة (Quiz)' }}</h2>
                <p>{{ app()->getLocale() === 'en' ? 'Manage interactive skin quiz questions and answer options' : 'إدارة أسئلة اختبار البشرة والخيارات التفاعلية للتقييم' }}</p>

            @elseif ($type === 'skin_types')
                <h2>😊 {{ app()->getLocale() === 'en' ? 'Skin Types' : 'أنواع البشرة' }}</h2>
                <p>{{ app()->getLocale() === 'en' ? 'Manage skin types and routine guidelines' : 'تعريف أنواع البشرة المختلفة والروتين الخاص بكل نوع' }}</p>

            @elseif ($type === 'blogs')
                <h2>📝 {{ app()->getLocale() === 'en' ? 'Blog & Articles' : 'المقالات والمدونة' }}</h2>
                <p>{{ app()->getLocale() === 'en' ? 'Manage skincare blog articles, tips, and content' : 'إدارة مقالات المدونة، النصائح الطبية، والمحتوى التوعوي' }}</p>

            @elseif ($type === 'blog_categories')
                <h2>📁 {{ app()->getLocale() === 'en' ? 'Blog Categories' : 'أقسام المدونة' }}</h2>
                <p>{{ app()->getLocale() === 'en' ? 'Organize blog articles into thematic categories' : 'تصنيف وتعميق مقالات المدونة حسب المواضيع' }}</p>

            @elseif ($type === 'blog_tags')
                <h2>🏷️ {{ app()->getLocale() === 'en' ? 'Blog Tags' : 'تاجات ووسوم المدونة' }}</h2>
                <p>{{ app()->getLocale() === 'en' ? 'Manage blog search tags and keywords' : 'إدارة الكلمات المفتاحية والوسوم المسهلة للبحث بالمقالات' }}</p>

            @elseif ($type === 'chatbot_messages')
                <h2>💬 {{ app()->getLocale() === 'en' ? 'AI Consultation Log & Chat Messages' : 'سجل محادثات العملاء (المستشار الذكي)' }}</h2>
                <p>{{ app()->getLocale() === 'en' ? 'Monitor customer conversations with AI Skincare Assistant' : 'متابعة واستعراض محادثات واستفسارات العملاء مع المستشار الذكي' }}</p>

            @elseif ($type === 'chatbot_suggestions')
                <h2>💡 {{ app()->getLocale() === 'en' ? 'AI Quick Suggestion Questions' : 'الأسئلة المقترحة للشات بوت' }}</h2>
                <p>{{ app()->getLocale() === 'en' ? 'Manage quick pre-defined suggestion questions for AI chatbot' : 'إدارة أسئلة الشات بوت الجاهزة والإجابات التلقائية السريعة' }}</p>

            @elseif ($type === 'newsletter_subscriptions')
                <h2>📧 {{ app()->getLocale() === 'en' ? 'Newsletter Subscribers' : 'المشتركون بالنشرة البريدية' }}</h2>
                <p>{{ app()->getLocale() === 'en' ? 'Manage newsletter subscribers list and export emails' : 'متابعة قائمة إيميلات المشتركين بالنشرة وتصدير البيانات' }}</p>

            @elseif ($type === 'loyalty_levels')
                <h2>💎 {{ app()->getLocale() === 'en' ? 'Loyalty Levels & Rewards' : 'مستويات برنامج الولاء' }}</h2>
                <p>{{ app()->getLocale() === 'en' ? 'Configure customer loyalty tiers and reward program' : 'إدارة مستويات ودرجات برنامج الولاء ومكافآت النقاط' }}</p>

            @elseif ($type === 'faqs')
                <h2>❓ {{ app()->getLocale() === 'en' ? 'Frequently Asked Questions' : 'الأسئلة الشائعة' }}</h2>
                <p>{{ app()->getLocale() === 'en' ? 'Manage store FAQs and customer support answers' : 'إدارة الأسئلة والإجابات الشائعة لمستخدمي التطبيق والمتجر' }}</p>

            @elseif ($type === 'pages')
                <h2>📄 {{ app()->getLocale() === 'en' ? 'Static Pages' : 'الصفحات الثابتة' }}</h2>
                <p>{{ app()->getLocale() === 'en' ? 'Manage terms, privacy policy, and static store pages' : 'إدارة الشروط والظروف، سياسة الخصوصية، والصفحات التعريفية' }}</p>

            @elseif ($type === 'settings')
                <h2>⚙️ {{ app()->getLocale() === 'en' ? 'System Settings & Configuration' : 'إعدادات النظام العامة' }}</h2>
                <p>{{ app()->getLocale() === 'en' ? 'Manage store settings, shipping rates, and WhatsApp contact' : 'التحكم في إعدادات المتجر، أسعار الشحن، والواتساب والمعلومات العامة' }}</p>

            @elseif ($type === 'dashboard')
                <h2>🏠 {{ app()->getLocale() === 'en' ? 'Welcome to KCODE Skincare Admin Panel' : 'مرحباً بك في لوحة تحكم KCODE للعناية بالبشرة' }}</h2>
                <p>{{ app()->getLocale() === 'en' ? 'Monitor sales revenue, active customers, orders analytics, and AI skincare consultation logs' : 'تتبع إحصائيات المبيعات، الطلبات، العملاء المسجلين واستشارات الذكاء الاصطناعي لحظة بلحظة' }}</p>
            @endif
        </div>

        <div class="kcode-banner-anim">
            @if ($type === 'orders')
                <!-- 1. ORDERS: Delivery Van -->
                <div class="orders-stage">
                    <div class="van-wrapper">
                        <svg width="56" height="32" viewBox="0 0 56 32" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <rect x="2" y="4" width="34" height="20" rx="3" fill="#10b981"/>
                            <path d="M36 10H46L52 17V24H36V10Z" fill="#059669"/>
                            <path d="M39 12H44L48 17H39V12Z" fill="#e0f2fe"/>
                            <rect x="51" y="19" width="3" height="3" rx="1" fill="#fbbf24"/>
                            <line x1="13" y1="4" x2="13" y2="24" stroke="#047857" stroke-width="1.5"/>
                            <g class="wheel-rotate">
                                <circle cx="13" cy="24" r="5" fill="#1e293b" stroke="#10b981" stroke-width="2"/>
                                <circle cx="13" cy="24" r="2" fill="#cbd5e1"/>
                            </g>
                            <g class="wheel-rotate">
                                <circle cx="42" cy="24" r="5" fill="#1e293b" stroke="#10b981" stroke-width="2"/>
                                <circle cx="42" cy="24" r="2" fill="#cbd5e1"/>
                            </g>
                        </svg>
                    </div>
                </div>

            @elseif ($type === 'products' || $type === 'categories' || $type === 'sub_categories' || $type === 'brands')
                <!-- 2. PRODUCTS / CATALOG: 3D Box & Floating Stars -->
                <div class="products-stage">
                    <span class="sparkle-icon sp-1">✦</span>
                    <svg class="box-svg" width="46" height="42" viewBox="0 0 46 42" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M23 4L42 13V29L23 38L4 29V13L23 4Z" fill="#10b981" opacity="0.85"/>
                        <path d="M23 4L42 13L23 22L4 13L23 4Z" fill="#34d399"/>
                        <path d="M23 22V38L4 29V13L23 22Z" fill="#059669"/>
                        <path d="M23 22L42 13V29L23 38V22Z" fill="#047857"/>
                    </svg>
                    <span class="sparkle-icon sp-2">✦</span>
                </div>

            @elseif ($type === 'users' || $type === 'roles')
                <!-- 3. USERS: Avatar Radar Wave & Online Dot -->
                <div class="users-stage">
                    <div class="radar-wave"></div>
                    <div class="online-dot"></div>
                    <svg width="42" height="42" viewBox="0 0 42 42" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <circle cx="21" cy="14" r="7" fill="#10b981"/>
                        <path d="M7 34C7 27.3726 13.268 22 21 22C28.732 22 35 27.3726 35 34V36H7V34Z" fill="#059669"/>
                    </svg>
                </div>

            @elseif ($type === 'coupons' || $type === 'offers' || $type === 'app_notifications')
                <!-- 4. COUPONS / OFFERS / NOTIFICATIONS: Ticket & Floating Percent -->
                <div class="coupons-stage">
                    <span class="rising-pct pct-a">%</span>
                    <svg class="ticket-svg" width="46" height="34" viewBox="0 0 46 34" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <rect x="2" y="4" width="42" height="26" rx="4" fill="#f59e0b"/>
                        <circle cx="2" cy="17" r="4" fill="#0f172a"/>
                        <circle cx="44" cy="17" r="4" fill="#0f172a"/>
                        <line x1="14" y1="7" x2="14" y2="27" stroke="#b45309" stroke-width="1.5" stroke-dasharray="3 3"/>
                        <circle cx="28" cy="17" r="5" fill="#fef3c7"/>
                    </svg>
                    <span class="rising-pct pct-b">%</span>
                </div>

            @elseif ($type === 'skincare' || $type === 'assessments' || $type === 'concerns' || $type === 'quiz_questions' || $type === 'skin_types')
                <!-- 5. SKINCARE / QUIZ: Serum Bottle & Liquid Drop Ripple -->
                <div class="skincare-stage">
                    <div class="drop-particle"></div>
                    <svg class="serum-svg" width="36" height="42" viewBox="0 0 36 42" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <rect x="14" y="2" width="8" height="6" rx="2" fill="#34d399"/>
                        <path d="M12 8H24V13H12V8Z" fill="#10b981"/>
                        <rect x="8" y="13" width="20" height="26" rx="4" fill="#059669"/>
                        <rect x="11" y="17" width="14" height="18" rx="2" fill="#ecfdf5" opacity="0.3"/>
                    </svg>
                    <div class="liquid-ripple"></div>
                </div>

            @elseif ($type === 'settings' || $type === 'pages' || $type === 'faqs' || $type === 'chatbot_messages' || $type === 'chatbot_suggestions' || $type === 'newsletter_subscriptions' || $type === 'loyalty_levels' || $type === 'blogs' || $type === 'blog_categories' || $type === 'blog_tags' || $type === 'dashboard')
                <!-- 6. SETTINGS / DASHBOARD / PAGES / LOGS: Interlocking Gears -->
                <div class="settings-stage">
                    <svg class="gear-main" width="34" height="34" viewBox="0 0 34 34" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M14.5 2H19.5V5.5C20.7 5.9 21.8 6.5 22.8 7.3L25.5 5L29 8.5L26.7 11.2C27.5 12.2 28.1 13.3 28.5 14.5H32V19.5H28.5C28.1 20.7 27.5 21.8 26.7 22.8L29 25.5L25.5 29L22.8 26.7C21.8 27.5 20.7 28.1 19.5 28.5V32H14.5V28.5C13.3 28.1 12.2 27.5 11.2 26.7L8.5 29L5 25.5L7.3 22.8C6.5 21.8 5.9 20.7 5.5 19.5H2V14.5H5.5C5.9 13.3 6.5 12.2 7.3 11.2L5 8.5L8.5 5L11.2 7.3C12.2 6.5 13.3 5.9 14.5 5.5V2Z" fill="#10b981"/>
                        <circle cx="17" cy="17" r="6" fill="#0f172a"/>
                    </svg>
                    <svg class="gear-sub" width="22" height="22" viewBox="0 0 22 22" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M9.5 1H12.5V3.3C13.3 3.6 14 4 14.7 4.5L16.5 3L19 5.5L17.5 7.3C18 8 18.4 8.7 18.7 9.5H21V12.5H18.7C18.4 13.3 18 14 17.5 14.7L19 16.5L16.5 19L14.7 17.5C14 18 13.3 18.4 12.5 18.7V21H9.5V18.7C8.7 18.4 8 18 7.3 17.5L5.5 19L3 16.5L4.5 14.7C4 14 3.6 13.3 3.3 12.5H1V9.5H3.3C3.6 8.7 4 8 4.5 7.3L3 5.5L5.5 3L7.3 4.5C8 4 8.7 3.6 9.5 3.3V1Z" fill="#f59e0b"/>
                        <circle cx="11" cy="11" r="4" fill="#0f172a"/>
                    </svg>
                </div>
            @endif
        </div>
    </div>
@endif
