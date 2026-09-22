<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>404 — الصفحة غير موجودة | KCODE</title>
    
    <!-- Instant Theme Detection Script to Prevent Flash -->
    <script>
        (function() {
            var theme = localStorage.getItem('theme') || localStorage.getItem('filament::theme') || localStorage.getItem('theme-mode');
            var isDark = theme === 'dark' || (!theme && window.matchMedia && window.matchMedia('(prefers-color-scheme: dark)').matches);
            if (isDark) {
                document.documentElement.classList.add('dark');
            } else {
                document.documentElement.classList.remove('dark');
            }
        })();
    </script>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
    
    <style>
        /* Light Theme Tokens */
        :root {
            --kcode-canvas: #fbfaf7;
            --kcode-primary: #2e1633;
            --kcode-primary-hover: #43214a;
            --kcode-text-primary: #2e1633;
            --kcode-text-secondary: #5f565c;
            --kcode-berry: #93254e;
            --kcode-badge-bg: #f8edf2;
            --kcode-border-badge: rgba(147, 37, 78, 0.18);
            --kcode-line-soft: rgba(147, 37, 78, 0.25);
            --kcode-notice-bg: rgba(46, 22, 51, 0.04);
            --kcode-btn-bg: #2e1633;
            --kcode-btn-text: #ffffff;
            --kcode-btn-hover: #43214a;
            --kcode-btn-shadow: rgba(46, 22, 51, 0.45);
            --kcode-logo-stroke: #2e1633;
            --kcode-logo-dot: #93254e;
            --kcode-font: 'Cairo', system-ui, -apple-system, sans-serif;
        }

        /* Dark Theme Tokens */
        html.dark {
            --kcode-canvas: #1a0c1d;
            --kcode-primary: #ffffff;
            --kcode-primary-hover: #e28aab;
            --kcode-text-primary: #fbfaf7;
            --kcode-text-secondary: #d4c8d2;
            --kcode-berry: #e28aab;
            --kcode-badge-bg: rgba(226, 138, 171, 0.12);
            --kcode-border-badge: rgba(226, 138, 171, 0.3);
            --kcode-line-soft: rgba(226, 138, 171, 0.35);
            --kcode-notice-bg: rgba(255, 255, 255, 0.08);
            --kcode-btn-bg: #93254e;
            --kcode-btn-text: #ffffff;
            --kcode-btn-hover: #b53265;
            --kcode-btn-shadow: rgba(147, 37, 78, 0.55);
            --kcode-logo-stroke: #f7f3f0;
            --kcode-logo-dot: #e28aab;
        }

        *, *::before, *::after {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        html, body {
            height: 100%;
            background-color: var(--kcode-canvas);
            color: var(--kcode-text-primary);
            font-family: var(--kcode-font);
            direction: rtl;
            text-align: center;
            overflow-x: hidden;
            -webkit-font-smoothing: antialiased;
            transition: background-color 0.3s ease, color 0.3s ease;
        }

        .error-container {
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            padding: 2.5rem 1.5rem;
            position: relative;
        }

        /* Subtle Header Logo */
        .brand-logo {
            margin-bottom: 1.75rem;
            display: inline-block;
            transition: transform 0.2s ease;
        }

        .brand-logo:hover {
            transform: scale(1.03);
        }

        .brand-logo svg {
            width: 140px;
            height: auto;
            display: block;
        }

        .brand-logo path {
            stroke: var(--kcode-logo-stroke);
            transition: stroke 0.3s ease;
        }

        .brand-logo circle {
            fill: var(--kcode-logo-dot);
            transition: fill 0.3s ease;
        }

        /* Card Wrap */
        .error-card {
            width: 100%;
            max-width: 680px;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
        }

        /* Top Badge */
        .error-badge {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0.35rem 1rem;
            background-color: var(--kcode-badge-bg);
            color: var(--kcode-berry);
            border: 1px solid var(--kcode-border-badge);
            border-radius: 9999px;
            font-size: 0.875rem;
            font-weight: 600;
            margin-bottom: 1.25rem;
            transition: all 0.3s ease;
        }

        .error-badge-dot {
            width: 7px;
            height: 7px;
            background-color: var(--kcode-berry);
            border-radius: 50%;
            display: inline-block;
        }

        /* Giant 404 Text */
        .error-code {
            font-size: clamp(5rem, 16vw, 9.5rem);
            font-weight: 900;
            line-height: 0.95;
            color: var(--kcode-primary);
            letter-spacing: -0.04em;
            margin-bottom: 1.25rem;
            user-select: none;
            transition: color 0.3s ease;
        }

        /* Subtitle Divider */
        .subtitle-divider {
            width: 100%;
            max-width: 280px;
            display: flex;
            align-items: center;
            gap: 1rem;
            margin-bottom: 1.25rem;
            color: var(--kcode-berry);
            font-size: 0.9rem;
            font-weight: 600;
            transition: color 0.3s ease;
        }

        .subtitle-divider::before,
        .subtitle-divider::after {
            content: '';
            flex: 1;
            height: 1px;
            background-color: var(--kcode-line-soft);
            transition: background-color 0.3s ease;
        }

        /* Main Heading */
        .error-heading {
            font-size: clamp(1.6rem, 4vw, 2.25rem);
            font-weight: 800;
            color: var(--kcode-primary);
            line-height: 1.3;
            margin-bottom: 0.85rem;
            transition: color 0.3s ease;
        }

        /* Description Paragraph */
        .error-description {
            font-size: clamp(0.95rem, 2vw, 1.0625rem);
            font-weight: 400;
            color: var(--kcode-text-secondary);
            line-height: 1.8;
            max-width: 540px;
            margin: 0 auto 1.5rem auto;
            transition: color 0.3s ease;
        }

        /* Auto Redirect Notice */
        .redirect-notice {
            display: inline-flex;
            align-items: center;
            gap: 0.4rem;
            font-size: 0.9rem;
            font-weight: 500;
            color: var(--kcode-text-secondary);
            margin-bottom: 1.5rem;
            background: var(--kcode-notice-bg);
            padding: 0.4rem 1.1rem;
            border-radius: 8px;
            transition: all 0.3s ease;
        }

        .countdown-number {
            font-weight: 800;
            color: var(--kcode-berry);
            font-size: 1.05rem;
        }

        /* Back to Dashboard Button */
        .btn-dashboard {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 0.6rem;
            padding: 0.85rem 2.2rem;
            background-color: var(--kcode-btn-bg);
            color: var(--kcode-btn-text);
            font-family: var(--kcode-font);
            font-size: 1rem;
            font-weight: 600;
            text-decoration: none;
            border-radius: 10px;
            box-shadow: 0 10px 24px -10px var(--kcode-btn-shadow);
            transition: all 0.22s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .btn-dashboard:hover {
            background-color: var(--kcode-btn-hover);
            transform: translateY(-2px);
            box-shadow: 0 14px 28px -8px var(--kcode-btn-shadow);
        }

        .btn-dashboard:active {
            transform: scale(0.98);
        }

        .btn-dashboard svg {
            width: 19px;
            height: 19px;
            fill: none;
            stroke: currentColor;
            stroke-width: 2;
            stroke-linecap: round;
            stroke-linejoin: round;
            transition: transform 0.2s ease;
        }

        .btn-dashboard:hover svg {
            transform: translateX(4px);
        }

        @media (max-width: 480px) {
            .error-container {
                padding: 1.5rem 1rem;
            }
            .brand-logo svg {
                width: 115px;
            }
            .btn-dashboard {
                width: 100%;
            }
        }
    </style>
</head>
<body>
    <main class="error-container">
        <!-- Logo -->
        <a href="/admin" class="brand-logo" aria-label="KCODE Dashboard">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="-8 -8 506 116" role="img" aria-label="KCODE">
                <path d="M6.3 6.3 V93.7 M6.3 52 L56 6.3 M7.3 52 L56 93.7" fill="none" stroke-width="12.6" stroke-linecap="round" stroke-linejoin="round"/>
                <path d="M166.84 22 A40.7 43.7 0 1 0 166.84 78" fill="none" stroke-width="12.6" stroke-linecap="round" stroke-linejoin="round"/>
                <path d="M248 6.3 A41.7 43.7 0 1 1 247.99 6.3 Z" fill="none" stroke-width="12.6" stroke-linecap="round" stroke-linejoin="round"/>
                <path d="M322.3 6.3 V93.7 M322.3 6.3 H343.6 A43.7 43.7 0 0 1 343.6 93.7 H322.3" fill="none" stroke-width="12.6" stroke-linecap="round" stroke-linejoin="round"/>
                <path d="M434.3 6.3 H490 M434.3 50 H480 M434.3 93.7 H490 M434.3 6.3 V93.7" fill="none" stroke-width="12.6" stroke-linecap="round" stroke-linejoin="round"/>
                <circle cx="248" cy="50" r="11"/>
            </svg>
        </a>

        <!-- Content Card -->
        <article class="error-card">
            <!-- Badge -->
            <div class="error-badge">
                <span class="error-badge-dot"></span>
                <span>خطأ في المسار 404</span>
            </div>

            <!-- Giant 404 -->
            <h1 class="error-code">404</h1>

            <!-- Subtitle Divider -->
            <div class="subtitle-divider">
                <span>صفحة غير متوفرة</span>
            </div>

            <!-- Heading -->
            <h2 class="error-heading">عذراً! الصفحة غير موجودة</h2>

            <!-- Description -->
            <p class="error-description">
                نعتذر، يبدو أن الرابط الذي تحاول الوصول إليه غير متوفر حالياً أو تم نقله لعنوان آخر.
            </p>

            <!-- Auto Redirect Notice -->
            <div class="redirect-notice">
                <span>سيتم إعادة توجيهك تلقائياً للوحة التحكم خلال</span>
                <span id="countdown" class="countdown-number">5</span>
                <span>ثوانٍ...</span>
            </div>

            <!-- Back to Dashboard Button -->
            <a href="/admin" class="btn-dashboard">
                <svg viewBox="0 0 24 24">
                    <path d="M19 12H5M12 19l-7-7 7-7"/>
                </svg>
                <span>العودة إلى لوحة التحكم</span>
            </a>
        </article>
    </main>

    <script>
        (function() {
            let seconds = 5;
            const countdownEl = document.getElementById('countdown');
            const targetUrl = '/admin';

            const timer = setInterval(function() {
                seconds--;
                if (countdownEl) {
                    countdownEl.textContent = seconds;
                }
                if (seconds <= 0) {
                    clearInterval(timer);
                    window.location.href = targetUrl;
                }
            }, 1000);
        })();
    </script>
</body>
</html>
