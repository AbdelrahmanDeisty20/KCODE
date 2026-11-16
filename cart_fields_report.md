# 🛒 دليل الـ API المعتمد لسلة المشتريات والضمانات (Frontend Cart & Assurances Reference)

---

## 📡 نقاط النهاية (API Endpoints)

| اسم الشاشة / الخدمة | نوع الطلب | الرابط (Endpoint) | الوصف |
| :--- | :--- | :--- | :--- |
| **عرض السلة (Get Cart)** | `GET` / `POST` | `/api/cart` | جلب سلة المستخدم بعناصرها وحسابات الإجمالي والشحن بدون الضمانات. |
| **ضمانات الشراء (Assurances)** 🆕 | `GET` | `/api/assurances` | **نقطة نهاية مستقلة** لجلب كروت الضمانات والتعهدات (أصالة، توصيل، إرجاع). |
| **إضافة منتج فردي للسلة** | `POST` | `/api/cart/add-single` | إضافة منتج واحد للسلة أو زيادة كميته مع التأكد من المخزون. |
| **إضافة روتين كامل (Bulk Add)** | `POST` | `/api/cart/add-bulk` | إضافة مصفوفة من منتجات الروتين دفعة واحدة للسلة. |
| **تعديل كمية عنصر** | `POST` | `/api/cart/update-quantity` | زيادة أو تقليل كمية منتج في السلة (`quantity`). |
| **حذف عنصر من السلة** | `POST` | `/api/cart/remove-item` | حذف منتج معين بالكامل من السلة. |
| **تطبيق كود الخصم (Apply Coupon)** | `POST` | `/api/coupons/apply` | تطبيق كوبون خصم على السلة وحساب قيمة الخصم. |

---

## 📋 1. استجابة سلة المشتريات المعتمدة (Cart JSON Response: /api/cart)

```json
{
    "status": true,
    "message": "تم جلب السلة بنجاح",
    "data": {
        "id": 40,
        "user": {
            "id": 9,
            "name": "Blog Author",
            "email": "author@kcode.com",
            "phone": "01000000000",
            "birth_date": "2008-08-17T00:00:00.000000Z",
            "quote": "ششششش",
            "image": "https://kcodeskin.com/storage/users/1786878628.webp",
            "type": "blog_author",
            "status": "active",
            "created_at": "2026-07-27T09:11:49.000000Z"
        },
        "items": [
            {
                "id": 363,
                "item_type": "single",
                "routine_id": null,
                "quantity": 2,
                "unit_price": 119,
                "discount_amount": 0,
                "discount_percentage": 0,
                "price_after_discount": 0,
                "total_price": 238,
                "product": {
                    "id": 1,
                    "name": "ANUA Niacinamide 10 TXA 4 Serum (30ml)",
                    "sku": "KC0001",
                    "short_name": "ANUA Niacinamide 10 TXA 4 Serum (30ml)",
                    "price": 119,
                    "image": "https://images.unsplash.com/photo-1620916566398-39f1143ab7be?auto=format&fit=crop&q=80&w=800",
                    "brand": {
                        "id": 17,
                        "name": "ANUA",
                        "image": "https://kcodeskin.com/storage/app/public/brands/01M1EC0936V3NPPQ22HDCKR8TS.png"
                    },
                    "offers": [],
                    "is_favorite": false,
                    "sales_count": 440,
                    "review_rating": 4,
                    "num_reviews": 24,
                    "size": "30ml",
                    "barcode": "8809640734526",
                    "sensitive_eligible": true,
                    "brief_insight_ar": "سيروم مهدئ يعزز مرونة البشرة ويقلل الاحمرار والتهيج",
                    "country_of_origin_ar": "كوريا الجنوبية",
                    "role_ar": "Daily Treatment"
                },
                "routine": null
            }
        ],
        "summary": {
            "items_count": 2,
            "subtotal": 238,
            "coupon_code": null,
            "coupon_discount": 0,
            "free_shipping_threshold": 25,
            "is_free_shipping": true,
            "remaining_for_free_shipping": 0,
            "shipping_message_ar": "الشحن مجاني لطلبك",
            "shipping_cost": 0,
            "shipping_cost_label_ar": "مجاني",
            "grand_total": 238,
            "has_blocked_items": false
        }
    }
}
```

---

## 🛡️ 2. استجابة الضمانات المستقلة (Assurances JSON Response: /api/assurances)

```json
{
    "status": true,
    "message": "تم جلب الصفحة بنجاح",
    "data": [
        {
            "title": "أصالة مضمونة",
            "icon": "shield",
            "text": "نورد منتجاتنا مباشرةً من العلامات التجارية أو موزعيها المعتمدين، ثم نفحص كل منتج وعبوته قبل اعتماده للبيع."
        },
        {
            "title": "التوصيل داخل عُمان",
            "icon": "truck",
            "text": "نفس اليوم داخل مسقط للطلبات المؤكَّدة قبل الساعة الواحدة ظهرًا. ومن 24 إلى 48 ساعة لباقي عُمان."
        },
        {
            "title": "إرجاع خلال 7 أيام",
            "icon": "return",
            "text": "يمكنك إرجاع المنتج خلال 7 أيام من استلامه، بشرط أن يكون غير مستخدم وغير مفتوح وفي عبوته الأصلية."
        }
    ]
}
```

---

## 🗂️ 3. تحليل حقول استجابة السلة (Cart Fields Analysis)

| اسم الحقل | نوع البيانات | الشرح والاستخدام في الواجهة (UI) |
| :--- | :--- | :--- |
| **`id`** | `number` | معرّف سلة المشتريات في قاعدة البيانات. |
| **`user`** | `object` | كائن بيانات المستخدم المسجل صاحب السلة. |
| **`items`** | `array` | مصفوفة المنتجات المضافة في السلة. |
| **`items[].id`** | `number` | معرّف عنصر السلة (Cart Item ID) لتعديل الكمية أو الإزالة. |
| **`items[].item_type`** | `string` | نوع العنصر (`single` منتج فردي أو `routine` روتين كامل). |
| **`items[].quantity`** | `number` | الكمية الحالية المطلوبة من المنتج. |
| **`items[].unit_price`** | `number` | سعر القطعة الواحدة من المنتج. |
| **`items[].total_price`** | `number` | الإجمالي لسطر المنتج (`unit_price * quantity`). |
| **`items[].product`** | `object` | تفاصيل المنتج الكاملة (`id`, `name`, `price`, `image`, `brand`...). |
| **`summary.items_count`** | `number` | مجموع عدد القطع المضافة في السلة (`2` قطعة). |
| **`summary.subtotal`** | `number` | مجموع أسعار المنتجات قبل الخصم والشحن. |
| **`summary.coupon_code`** | `string\|null` | كود الخصم المطبق (`null` عند عدم وجود كود). |
| **`summary.coupon_discount`** | `number` | قيمة الخصم المالية المخصومة بالكوبون. |
| **`summary.free_shipping_threshold`**| `number` | حد الشحن المجاني المحدد في النظام (`25 ر.ع`). |
| **`summary.is_free_shipping`** | `boolean` | هل حقق الطلب الشحن المجاني (`true` / `false`). |
| **`summary.remaining_for_free_shipping`** | `number` | المبلغ المتبقي للوصول للشحن المجاني. |
| **`summary.shipping_message_ar`** | `string` | نص رسالة الشحن المشجعة للمستخدم ("الشحن مجاني لطلبك"). |
| **`summary.shipping_cost`** | `number` | قيمة الشحن المضافة (تكون `0` عند الشحن المجاني). |
| **`summary.shipping_cost_label_ar`** | `string` | نص كلفة الشحن المظهورة بالعربية ("مجاني" أو "يُحسب عند إتمام الطلب"). |
| **`summary.grand_total`** | `number` | المبلغ الإجمالي النهائي المطلوب دفعه. |
| **`summary.has_blocked_items`** | `boolean` | `true` في حال وجود منتجات منتهية المخزون تمنع العميل من الدفع. |

---

## 🚚 4. منطق حسابات الشحن المجاني (Free Shipping Logic)

1. **الحد الأدنى للشحن المجاني:** المحدّد في النظام هو **`25 ر.ع`**.
2. **عندما يكون `subtotal >= 25` (مثل حالة الطلب الحالية `238 ر.ع`):**
   - يكون الشحن **مجاني بالكامل بدون أي رسوم إضافية (`shipping_cost = 0`)**.
   - تظهر حالة `is_free_shipping: true` والنص: `"الشحن مجاني لطلبك"`.
3. **عندما يكون `subtotal < 25` (مثلاً طلب بـ `15 ر.ع`):**
   - يكون الشحن **غير مجاني ويُحسب عند إتمام الطلب**.
   - يظهر المبلغ المتبقي: `remaining_for_free_shipping = 25 - subtotal`.
