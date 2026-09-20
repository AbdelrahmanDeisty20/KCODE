<?php

namespace App\Http\Resources\API\CART;

use App\Http\Resources\API\AUHT\UserResource;
use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CartResource extends JsonResource
{
    /**
     * Transform the resource into an array matching KCODE Cart Prototype.
     */
    public function toArray(Request $request): array
    {
        $items = $this->whenLoaded('items');

        $subtotal = 0.0;
        $itemsCount = 0;
        $hasBlockedItems = false;

        if ($items && !($items instanceof \Illuminate\Http\Resources\MissingValue)) {
            foreach ($items as $item) {
                $subtotal += (float) ($item->total_price ?? 0.0);
                $itemsCount += (int) ($item->quantity ?? 0);
                if ($item->product && (int) ($item->product->stock ?? 0) <= 0) {
                    $hasBlockedItems = true;
                }
            }
        }

        // Read Free Shipping Threshold dynamically from Settings table (Filament Admin managed)
        $minSetting = Setting::where('key_en', 'free_shipping_min_amount')->first();
        $freeShippingThreshold = $minSetting ? (float) ($minSetting->value_en ?? 25.00) : 25.00;

        $isFreeShipping = $subtotal >= $freeShippingThreshold;
        $remainingForFreeShipping = max(0, round($freeShippingThreshold - $subtotal, 3));

        $couponDiscount = (float) ($this->coupon_discount ?? 0.0);
        $grandTotal = max(0, round($subtotal - $couponDiscount, 3));

        $shippingMessageAr = $isFreeShipping
            ? 'الشحن مجاني لطلبك'
            : 'أنت على بُعد ' . number_format($remainingForFreeShipping, 3) . ' ر.ع من الشحن المجاني';

        return [
            'id'                          => $this->id,
            'session_id'                  => $this->session_id ?? null,
            'user'                        => new UserResource($this->whenLoaded('user')),
            'items'                       => CartItemResource::collection($items),
            'summary'                     => [
                'items_count'                 => $itemsCount,
                'subtotal'                    => round($subtotal, 3),
                'coupon_code'                 => $this->coupon_code ?? null,
                'coupon_discount'             => round($couponDiscount, 3),
                'free_shipping_threshold'     => $freeShippingThreshold,
                'is_free_shipping'            => $isFreeShipping,
                'remaining_for_free_shipping' => $remainingForFreeShipping,
                'shipping_message_ar'         => $shippingMessageAr,
                'shipping_cost'               => $isFreeShipping ? 0.0 : null,
                'shipping_cost_label_ar'      => $isFreeShipping ? 'مجاني' : 'يُحسب عند إتمام الطلب',
                'grand_total'                 => $grandTotal,
                'has_blocked_items'           => $hasBlockedItems,
            ],
            // Assurances from HTML prototype / policies
            'assurances' => [
                [
                    'title' => 'أصالة مضمونة',
                    'icon'  => 'shield',
                    'text'  => 'نورد منتجاتنا مباشرةً من العلامات التجارية أو موزعيها المعتمدين، ثم نفحص كل منتج وعبوته قبل اعتماده للبيع.'
                ],
                [
                    'title' => 'التوصيل داخل عُمان',
                    'icon'  => 'truck',
                    'text'  => 'نفس اليوم داخل مسقط للطلبات المؤكَّدة قبل الساعة الواحدة ظهرًا. ومن 24 إلى 48 ساعة لباقي عُمان.'
                ],
                [
                    'title' => 'إرجاع خلال 7 أيام',
                    'icon'  => 'return',
                    'text'  => 'يمكنك إرجاع المنتج خلال 7 أيام من استلامه، بشرط أن يكون غير مستخدم وغير مفتوح وفي عبوته الأصلية.'
                ],
            ],
        ];
    }
}
