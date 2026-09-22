<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>404 — الصفحة غير موجودة | KCODE</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
    <style>
        :root {
            --kcode-primary: #2e1633;
            --kcode-berry: #93254e;
            --kcode-berry-light: #e28aab;
            --kcode-canvas: #fbfaf7;
            --kcode-surface: #ffffff;
            --kcode-text-primary: #2e1633;
            --kcode-text-secondary: #5f565c;
            --kcode-badge-bg: #f8edf2;
            --kcode-border-soft: rgba(46, 22, 51, 0.12);
            --kcode-line-soft: rgba(147, 37, 78, 0.25);
            --kcode-font: 'Cairo', system-ui, -apple-system, sans-serif;
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
            margin-bottom: 2rem;
            display: inline-block;
            transition: transform 0.2s ease;
        }

        .brand-logo img, .brand-logo svg {
            width: 140px;
            height: auto;
            display: block;
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
            border: 1px solid rgba(147, 37, 78, 0.18);
            border-radius: 9999px;
            font-size: 0.875rem;
            font-weight: 600;
            margin-bottom: 1.25rem;
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
            margin-bottom: 1.5rem;
            user-select: none;
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
        }

        .subtitle-divider::before,
        .subtitle-divider::after {
            content: '';
            flex: 1;
            height: 1px;
            background-color: var(--kcode-line-soft);
        }

        /* Main Heading */
        .error-heading {
            font-size: clamp(1.6rem, 4vw, 2.25rem);
            font-weight: 800;
            color: var(--kcode-primary);
            line-height: 1.3;
            margin-bottom: 1rem;
        }

        /* Description Paragraph */
        .error-description {
            font-size: clamp(0.95rem, 2vw, 1.0625rem);
            font-weight: 400;
            color: var(--kcode-text-secondary);
            line-height: 1.8;
            max-width: 540px;
            margin: 0 auto;
        }

        @media (max-width: 480px) {
            .error-container {
                padding: 1.5rem 1rem;
            }
            .brand-logo img, .brand-logo svg {
                width: 115px;
            }
        }
    </style>
</head>
<body>
    <main class="error-container">
        <!-- Logo -->
        <a href="/" class="brand-logo" aria-label="KCODE Homepage">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="-8 -8 506 116" role="img" aria-label="KCODE">
                <path d="M6.3 6.3 V93.7 M6.3 52 L56 6.3 M7.3 52 L56 93.7" fill="none" stroke="#2E1633" stroke-width="12.6" stroke-linecap="round" stroke-linejoin="round"/>
                <path d="M166.84 22 A40.7 43.7 0 1 0 166.84 78" fill="none" stroke="#2E1633" stroke-width="12.6" stroke-linecap="round" stroke-linejoin="round"/>
                <path d="M248 6.3 A41.7 43.7 0 1 1 247.99 6.3 Z" fill="none" stroke="#2E1633" stroke-width="12.6" stroke-linecap="round" stroke-linejoin="round"/>
                <path d="M322.3 6.3 V93.7 M322.3 6.3 H343.6 A43.7 43.7 0 0 1 343.6 93.7 H322.3" fill="none" stroke="#2E1633" stroke-width="12.6" stroke-linecap="round" stroke-linejoin="round"/>
                <path d="M434.3 6.3 H490 M434.3 50 H480 M434.3 93.7 H490 M434.3 6.3 V93.7" fill="none" stroke="#2E1633" stroke-width="12.6" stroke-linecap="round" stroke-linejoin="round"/>
                <circle cx="248" cy="50" r="11" fill="#93254E"/>
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
        </article>
    </main>
</body>
</html>
