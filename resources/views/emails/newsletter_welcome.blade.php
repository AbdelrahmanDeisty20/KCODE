<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}" dir="{{ app()->getLocale() == 'ar' ? 'rtl' : 'ltr' }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ __('messages.newsletter_welcome_subject') }} - KCODE</title>
    <style>
        body {
            background-color: #fcfbfa;
            font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
            margin: 0;
            padding: 0;
            -webkit-font-smoothing: antialiased;
            -moz-osx-font-smoothing: grayscale;
        }
        .email-wrapper {
            width: 100%;
            padding: 40px 0;
            background-color: #fcfbfa;
        }
        .email-container {
            max-width: 500px;
            margin: 0 auto;
            background-color: #ffffff;
            border: 1px solid #f1eded;
            border-radius: 16px;
            overflow: hidden;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.02);
        }
        .email-header {
            padding: 30px 20px 20px 20px;
            text-align: center;
            background-color: #ffffff;
        }
        .logo {
            max-width: 150px;
            height: auto;
        }
        .email-body {
            padding: 20px 40px 40px 40px;
            text-align: center;
            color: #1d1e20;
        }
        h1 {
            font-size: 22px;
            font-weight: 600;
            margin-top: 0;
            margin-bottom: 16px;
            color: #1d1e20;
        }
        p {
            font-size: 15px;
            line-height: 1.6;
            margin-top: 0;
            margin-bottom: 24px;
            color: #65676b;
        }
        .coupon-container {
            background-color: #fef7f8;
            border: 1px dashed #f48c96;
            border-radius: 12px;
            padding: 20px;
            margin: 25px 0;
            display: inline-block;
            min-width: 220px;
        }
        .coupon-code {
            font-size: 28px;
            font-weight: bold;
            letter-spacing: 4px;
            color: #e57b88;
            margin: 0;
            font-family: monospace, sans-serif;
        }
        .coupon-text {
            font-size: 13px;
            color: #a0a0a0;
            margin-top: 8px;
        }
        .divider {
            height: 1px;
            background-color: #f1eded;
            margin: 0 40px;
        }
        .email-footer {
            padding: 30px 40px;
            text-align: center;
            font-size: 12px;
            color: #a0a0a0;
            background-color: #ffffff;
        }
        .footer-logo-text {
            font-weight: bold;
            color: #1d1e20;
            letter-spacing: 2px;
            margin-bottom: 8px;
        }
    </style>
</head>
<body>
    <div class="email-wrapper">
        <div class="email-container">
            <!-- Header with Logo -->
            <div class="email-header">
                <img src="{{ isset($message) ? $message->embed(public_path('images/logo-BfbQ1CpO.svg')) : asset('images/logo-BfbQ1CpO.svg') }}" alt="KCODE Logo" class="logo">
            </div>

            <div class="divider"></div>

            <!-- Body Content -->
            <div class="email-body">
                <h1>{{ __('messages.newsletter_welcome_title') }}</h1>
                <p>{{ __('messages.newsletter_welcome_body') }}</p>
                
                @if(!empty($couponCode))
                <p style="font-weight: 600; color: #1d1e20; margin-bottom: 8px;">{{ __('messages.newsletter_welcome_coupon_text') }}</p>
                <div class="coupon-container">
                    <div class="coupon-code">{{ $couponCode }}</div>
                    <div class="coupon-text">{{ __('messages.newsletter_welcome_coupon_hint') }}</div>
                </div>
                @endif
            </div>

            <div class="divider"></div>

            <!-- Footer -->
            <div class="email-footer">
                <div class="footer-logo-text">KCODE</div>
                <div>Curated Korean Skincare</div>
                <div style="margin-top: 16px;">© {{ date('Y') }} KCODE. All rights reserved.</div>
            </div>
        </div>
    </div>
</body>
</html>
