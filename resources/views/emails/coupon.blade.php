<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>كوبون خصم خاص بك من KCODE</title>
    <style>
        body {
            background-color: #fcfbfa;
            font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
            margin: 0;
            padding: 0;
            direction: rtl;
            text-align: right;
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
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.04);
        }
        .email-header {
            padding: 30px 20px 20px 20px;
            text-align: center;
            background-color: #ffffff;
        }
        .logo {
            max-width: 140px;
            height: auto;
        }
        .email-body {
            padding: 30px 40px;
            text-align: center;
            color: #1d1e20;
        }
        h1 {
            font-size: 22px;
            font-weight: 700;
            color: #10b981;
            margin-top: 0;
            margin-bottom: 12px;
        }
        p {
            font-size: 15px;
            line-height: 1.6;
            color: #4b5563;
            margin-bottom: 20px;
        }
        .coupon-box {
            background: linear-gradient(135deg, #ecfdf5 0%, #d1fae5 100%);
            border: 2px dashed #10b981;
            border-radius: 12px;
            padding: 20px;
            margin: 20px 0;
            text-align: center;
        }
        .coupon-code {
            font-size: 28px;
            font-weight: 800;
            letter-spacing: 3px;
            color: #065f46;
            margin: 8px 0;
        }
        .coupon-details {
            font-size: 14px;
            color: #047857;
            margin-top: 6px;
        }
        .email-footer {
            padding: 24px 40px;
            text-align: center;
            font-size: 12px;
            color: #9ca3af;
            background-color: #f9fafb;
        }
    </style>
</head>
<body>
    <div class="email-wrapper">
        <div class="email-container">
            <div class="email-header">
                <img src="{{ asset('images/logo-BfbQ1CpO.svg') }}" alt="KCODE Logo" class="logo">
            </div>

            <div class="email-body">
                <h1>🎁 كود خصم خاص بك!</h1>
                <p>مرحباً {{ $userName }}، يسعدنا تقديم كود خصم حصري لك لاستخدامه في طلبيتك القادمة!</p>

                <div class="coupon-box">
                    <div style="font-size: 13px; color: #047857;">كود الخصم الحصري</div>
                    <div class="coupon-code">{{ $coupon->code }}</div>
                    <div class="coupon-details">
                        @if($coupon->discount_type === 'percentage')
                            خصم {{ (float) $coupon->discount_value }}% على طلبك!
                        @else
                            خصم بقيمة {{ (float) $coupon->discount_value }} ج.م على طلبك!
                        @endif
                    </div>
                </div>

                @if($coupon->end_date)
                    <p style="font-size: 13px; color: #6b7280;">ينتهي العرض بتاريخ: {{ \Carbon\Carbon::parse($coupon->end_date)->format('Y-m-d') }}</p>
                @endif

                <p style="font-size: 14px; color: #374151;">استخدم الكود عند الدفع للاستفادة من الخصم.</p>
            </div>

            <div class="email-footer">
                <div style="font-weight: bold; color: #374151;">KCODE</div>
                <div>Curated Korean Skincare</div>
                <div style="margin-top: 12px;">© {{ date('Y') }} KCODE. All rights reserved.</div>
            </div>
        </div>
    </div>
</body>
</html>
