<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title }} - KCODE</title>
    <style>
        body {
            background-color: #F5F2ED;
            font-family: 'Segoe UI', Tahoma, Arial, sans-serif;
            margin: 0;
            padding: 0;
            direction: rtl;
            text-align: right;
            -webkit-font-smoothing: antialiased;
            -moz-osx-font-smoothing: grayscale;
        }
        .email-wrapper {
            width: 100%;
            padding: 40px 15px;
            background-color: #F5F2ED;
            box-sizing: border-box;
        }
        .email-container {
            max-width: 540px;
            margin: 0 auto;
            background-color: #FBFAF7;
            border: 1px solid #EDE8E4;
            border-radius: 20px;
            overflow: hidden;
            box-shadow: 0 10px 30px rgba(46, 22, 51, 0.05);
        }
        .email-header {
            padding: 32px 24px 20px 24px;
            text-align: center;
            background-color: #FBFAF7;
            border-bottom: 1px solid #EDE8E4;
        }
        .logo-text {
            font-size: 26px;
            font-weight: 800;
            letter-spacing: 3px;
            color: #2E1633;
            text-decoration: none;
            display: inline-block;
        }
        .logo-dot {
            color: #93254E;
        }
        .email-body {
            padding: 36px 32px;
            text-align: right;
            color: #2E1633;
        }
        .badge {
            display: inline-block;
            padding: 6px 14px;
            font-size: 12px;
            font-weight: 600;
            color: #93254E;
            background-color: #FDF4F7;
            border: 1px solid #FBCFE8;
            border-radius: 9999px;
            margin-bottom: 18px;
        }
        h1 {
            font-size: 21px;
            font-weight: 700;
            margin-top: 0;
            margin-bottom: 16px;
            color: #2E1633;
            line-height: 1.4;
        }
        .content-box {
            font-size: 15px;
            line-height: 1.8;
            color: #5F565C;
            margin-bottom: 28px;
            white-space: pre-line;
            background-color: #ffffff;
            padding: 20px 22px;
            border-radius: 14px;
            border: 1px solid #EDE8E4;
        }
        .btn-wrapper {
            text-align: center;
            margin: 30px 0 10px 0;
        }
        .btn-action {
            display: inline-block;
            background-color: #2E1633;
            color: #FFFFFF !important;
            text-decoration: none;
            font-size: 14px;
            font-weight: 600;
            padding: 14px 34px;
            border-radius: 9999px;
            box-shadow: 0 4px 14px rgba(46, 22, 51, 0.2);
            transition: all 0.2s ease;
        }
        .email-footer {
            padding: 24px 30px 30px 30px;
            text-align: center;
            background-color: #FBFAF7;
            border-top: 1px solid #EDE8E4;
            font-size: 12px;
            color: #85787F;
            line-height: 1.6;
        }
        .footer-links a {
            color: #93254E;
            text-decoration: none;
            margin: 0 8px;
            font-weight: 500;
        }
    </style>
</head>
<body>
    <div class="email-wrapper">
        <div class="email-container">
            <div class="email-header">
                <a href="{{ config('app.url', 'https://kcodeskin.com') }}" class="logo-text">
                    KCODE<span class="logo-dot">.</span>
                </a>
            </div>

            <div class="email-body">
                <div class="badge">
                    @if($type === 'promotion')
                        🎁 عرض حصري وخاص
                    @elseif($type === 'general_coupon' || $type === 'private_coupon')
                        🏷️ كوبون خصم
                    @elseif($type === 'order')
                        📦 تحديث الطلب
                    @elseif($type === 'blog')
                        📰 مقالة جديدة
                    @else
                        📣 إشعار جديد
                    @endif
                </div>

                <h1>{{ $title }}</h1>

                <div class="content-box">
                    {{ $body }}
                </div>

                @if(!empty($actionUrl))
                    <div class="btn-wrapper">
                        <a href="{{ str_starts_with($actionUrl, 'http') ? $actionUrl : url($actionUrl) }}" class="btn-action">
                            {{ $actionText }} &larr;
                        </a>
                    </div>
                @endif
            </div>

            <div class="email-footer">
                <p style="margin: 0 0 10px 0;">
                    وصلتك هذه الرسالة لأنك مسجل في متجر <strong>KCODE</strong> للعناية بالبشرة الكورية.
                </p>
                <div class="footer-links">
                    <a href="{{ config('app.url', 'https://kcodeskin.com') }}">زيارة المتجر</a> &bull;
                    <a href="{{ config('app.url', 'https://kcodeskin.com') }}/privacy-policy">سياسة الخصوصية</a>
                </div>
                <p style="margin: 14px 0 0 0; color: #A89FA4; font-size: 11px;">
                    &copy; {{ date('Y') }} KCODE Skincare. جميع الحقوق محفوظة.
                </p>
            </div>
        </div>
    </div>
</body>
</html>

